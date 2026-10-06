/*!
 * Interactive floor plan — viewer (DOM engine), one instance per floor.
 *
 * Owns: the stage measurements, the camera animation loop, the hotspot layer
 * (plus the staircase link to the other floor) and the navigation state
 * machine (FloorPlanCore.reduce). UI chrome (card, arrows, floor switching,
 * keyboard, URL) lives in public.js; the CP Admin editor drives the same
 * viewer with `editable: true`.
 *
 * Expected markup:
 *   .fp-stage                     (measured; shared by all floors)
 *     .fp-floor  (options.layer)  (one per floor; defaults to the stage)
 *       .fp-world[data-fp-world] > img
 *       .fp-spots[data-fp-spots]
 */
(function (root) {
	"use strict";

	var C = root.FloorPlanCore;
	if (!C) { return; }

	var P = C.PHASE;

	var STAIR_ICON = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 19h4v-4h4v-4h4V7h4"/></svg>';

	function Viewer(stage, options) {
		this.stage = stage;
		this.opt = options || {};
		this.root = this.opt.layer || stage;
		this.world = this.root.querySelector("[data-fp-world]");
		this.img = this.world.querySelector("img");
		this.layer = this.root.querySelector("[data-fp-spots]");
		this.plan = this.opt.plan;
		this.iw = this.plan.imageWidth || 8852;
		this.ih = this.plan.imageHeight || 4252;
		this.bounds = C.normalizeBounds(this.plan.bounds);
		this.editable = !!this.opt.editable;

		this.hotspots = [];
		this.bySlug = {};
		this.nodes = {};
		this.order = [];
		this.link = null;
		this.linkNode = null;

		this.W = 0;
		this.H = 0;
		this.layoutObj = null;
		this.preset = null;
		this.cam = null;
		this.anim = null;
		this.raf = 0;
		this.measureQueued = false;
		this.listeners = { state: [], select: [], move: [], add: [], layout: [], link: [], linkmove: [], gesture: [] };
		this.addMode = false;
		this.selectedEdit = null;
		this.panX = null;       /* normalized focal x of a pannable (phone) overview */
		this.freeCam = null;    /* camera while the visitor explores freely (drag / zoom) */
		this.freeRaw = null;    /* the same before clamping (elastic edges) */
		this.motion = null;
		this.motionRaf = 0;
		this.zMin = 1;
		this.panRange = null;

		this.mqReduce = root.matchMedia ? root.matchMedia("(prefers-reduced-motion: reduce)") : null;
		this.mqCoarse = root.matchMedia ? root.matchMedia("(pointer: coarse)") : null;

		this.state = C.initialState(null);
		this._onFrame = this._onFrame.bind(this);

		this.setData(this.opt.hotspots || []);
		this.setLink(this.plan.link, this.opt.linkLabel);

		/* deep link / initial selection: start focused, no animation */
		if (this.opt.initialSlug && this.bySlug[this.opt.initialSlug] && this.bySlug[this.opt.initialSlug].enabled) {
			this.state = C.initialState(this.opt.initialSlug);
		}

		this._measure();
		this._applyStateUI(null);
		this._renderStatic();
		if (!this.editable) { this._bindGestures(); }

		var self = this;
		if (typeof ResizeObserver !== "undefined") {
			this.ro = new ResizeObserver(function () { self._queueMeasure(); });
			this.ro.observe(stage);
		} else {
			this._onWinResize = function () { self._queueMeasure(); };
			root.addEventListener("resize", this._onWinResize);
		}

		/* the stage can start at 0x0 (hidden tab, display:none) */
		if (this.W === 0) { this._queueMeasure(); }
	}

	/* ------------------------------------------------------------------ */
	/* events                                                             */
	/* ------------------------------------------------------------------ */

	Viewer.prototype.on = function (type, fn) {
		if (this.listeners[type]) { this.listeners[type].push(fn); }
		return this;
	};

	Viewer.prototype._emit = function (type) {
		var l = this.listeners[type];
		var args = Array.prototype.slice.call(arguments, 1);
		for (var i = 0; i < l.length; i++) { l[i].apply(null, args); }
	};

	/* ------------------------------------------------------------------ */
	/* data                                                               */
	/* ------------------------------------------------------------------ */

	/**
	 * (Re)build hotspots. Disabled ones are only drawn in editor mode.
	 * Safe to call at any time: if the selected hotspot disappeared the
	 * state machine returns to the overview.
	 */
	Viewer.prototype.setData = function (list) {
		this.hotspots = C.normalizeHotspots(list);
		this.bySlug = {};
		for (var i = 0; i < this.hotspots.length; i++) {
			this.bySlug[this.hotspots[i].slug] = this.hotspots[i];
		}
		this.order = C.enabledSlugs(this.hotspots);
		this._buildNodes();

		if (this.W > 0) {
			this._dispatch({ type: "data", order: this.order });
			if (!this.anim) { this._renderStatic(); }
		}
	};

	Viewer.prototype._buildNodes = function () {
		var self = this;
		var visible = this.hotspots.filter(function (h) { return self.editable || h.enabled; });

		/* drop nodes that no longer exist */
		Object.keys(this.nodes).forEach(function (slug) {
			if (!self.bySlug[slug] || (!self.editable && !self.bySlug[slug].enabled)) {
				if (self.nodes[slug].parentNode) { self.nodes[slug].parentNode.removeChild(self.nodes[slug]); }
				delete self.nodes[slug];
			}
		});

		visible.forEach(function (h, index) {
			var node = self.nodes[h.slug];
			if (!node) {
				node = self._createNode(h);
				self.nodes[h.slug] = node;
			}
			/* keep DOM order == configured order (tab order); the stair link stays last */
			if (self.layer.children[index] !== node) {
				self.layer.insertBefore(node, self.layer.children[index] || null);
			}
			node.setAttribute("aria-label", self._label(h));
			node.classList.toggle("is-disabled", !h.enabled);
			var nm = node.querySelector(".fp-spot-name");
			if (nm) {
				nm.firstChild.textContent = h.titleEn || h.titleMn;
				nm.lastChild.textContent = h.titleEn ? h.titleMn : "";
			}
			var lab = node.querySelector(".fp-spot-label");
			if (lab) { lab.textContent = (index + 1) + ". " + (h.titleEn || h.titleMn || h.slug); }
		});
	};

	Viewer.prototype._label = function (h) {
		if (h.titleEn && h.titleMn) { return h.titleEn + " — " + h.titleMn; }
		return h.titleEn || h.titleMn || h.slug;
	};

	Viewer.prototype._createNode = function (h) {
		var self = this;
		var b = document.createElement("button");
		b.type = "button";
		b.className = "fp-spot";
		b.setAttribute("data-slug", h.slug);
		b.innerHTML = '<span class="fp-dot" aria-hidden="true"></span>' +
			(this.editable ? '<span class="fp-spot-label" aria-hidden="true"></span>'
				: '<span class="fp-spot-name" aria-hidden="true"><b></b><i></i></span>');

		if (this.editable) {
			this._bindDrag(b, function (x, y, final) { self._emit("move", h.slug, x, y, final); }, function () { self._emit("select", h.slug, null); });
		} else {
			b.addEventListener("click", function () { self.select(h.slug); });
		}
		return b;
	};

	/**
	 * The staircase to the other floor: { x, y } normalized on this floor's
	 * image, or null. `label` = { text: "21", dir: "up"|"down", aria: "..." }.
	 */
	Viewer.prototype.setLink = function (link, label) {
		var self = this;
		var ok = link && isFinite(link.x) && isFinite(link.y);
		this.link = ok ? { x: C.clamp(+link.x, 0, 1), y: C.clamp(+link.y, 0, 1) } : null;

		if (!this.link) {
			if (this.linkNode && this.linkNode.parentNode) { this.linkNode.parentNode.removeChild(this.linkNode); }
			this.linkNode = null;
			return;
		}

		if (!this.linkNode) {
			var b = document.createElement("button");
			b.type = "button";
			b.className = "fp-link";
			b.setAttribute("data-fp-link", "");
			b.innerHTML = '<span class="fp-link-icon">' + STAIR_ICON + '</span><span class="fp-link-tag" aria-hidden="true"></span>';
			if (this.editable) {
				this._bindDrag(b, function (x, y, final) { self._emit("linkmove", x, y, final); }, function () {});
			} else {
				b.addEventListener("click", function () { self._emit("link"); });
			}
			this.linkNode = b;
		}
		this.layer.appendChild(this.linkNode);

		var lab = label || this.opt.linkLabel || {};
		this.linkNode.setAttribute("aria-label", lab.aria || "Шат / Stairs");
		this.linkNode.setAttribute("data-dir", lab.dir === "down" ? "down" : "up");
		this.linkNode.querySelector(".fp-link-tag").textContent = (lab.text || "") + (lab.dir === "down" ? " ↓" : " ↑");

		if (this.cam) { this._render(this.cam); }
	};

	/* ------------------------------------------------------------------ */
	/* layout                                                             */
	/* ------------------------------------------------------------------ */

	Viewer.prototype._queueMeasure = function () {
		if (this.measureQueued) { return; }
		this.measureQueued = true;
		var self = this;
		root.requestAnimationFrame(function () {
			self.measureQueued = false;
			var had = self.W > 0;
			self._measure();
			if (!self.anim) { self._renderStatic(); }
			if (!had && self.W > 0) { self._dispatch({ type: "data", order: self.order }); }
		});
	};

	Viewer.prototype._measure = function () {
		var w = this.stage.clientWidth;
		var h = this.stage.clientHeight;

		if (!(w > 0 && h > 0)) {
			this.W = 0;
			this.H = 0;
			return false;
		}

		var rect = C.calculateContainedImageRect(w, h, this.iw, this.ih);
		this.W = w;
		this.H = h;
		this.layoutObj = { width: w, height: h, rect: rect };
		this.preset = C.getResponsiveCameraPreset(w, h, !!(this.mqCoarse && this.mqCoarse.matches));
		this.panRange = C.overviewPanRange(this.layoutObj, this.preset, this.bounds);
		this.zMin = C.calculateOverviewCamera(this.layoutObj, this.preset, this.bounds).z;
		this.root.classList.toggle("is-pannable", this.panRange.pannable && !this.editable);

		/* the CSS lays out by the hero's own size (also true in the editor frames) */
		var name = this.preset.name;
		this.stage.setAttribute("data-fp-layout", name === "mobilePortrait" || name === "tabletPortrait" ? "portrait" : name === "mobileLandscape" ? "short" : "landscape");
		this.stage.setAttribute("data-fp-size", w < 600 ? "small" : "regular");
		this.stage.style.setProperty("--fp-vh", (h / 100).toFixed(2) + "px");

		this.world.style.width = rect.width + "px";
		this.world.style.height = rect.height + "px";

		this._updateHits();
		this._emit("layout", this.layoutObj, this.preset);
		return true;
	};

	/** Public: force a re-measure (e.g. after the editor resizes the frame). */
	Viewer.prototype.relayout = function () {
		this._measure();
		if (!this.anim) { this._renderStatic(); }
	};

	/** Crowded hotspots (phones) get a smaller hit area so they never overlap. */
	Viewer.prototype._updateHits = function () {
		if (!this.layoutObj) { return; }
		var overview = C.calculateCameraTransform(C.calculateOverviewCamera(this.layoutObj, this.preset, this.bounds), this.layoutObj);
		var pts = [];
		var slugs = [];
		var self = this;
		this.hotspots.forEach(function (h) {
			if (h.enabled || self.editable) {
				slugs.push(h.slug);
				pts.push(C.pointToScreen(h.x, h.y, overview, self.layoutObj.rect));
			}
		});
		var d = C.nearestDistances(pts);
		this.overviewHit = {};
		for (var i = 0; i < slugs.length; i++) {
			this.overviewHit[slugs[i]] = C.clamp(Math.floor(d[i] * 0.9), 16, 44);
		}
		this._applyHits();
	};

	Viewer.prototype._applyHits = function () {
		var overview = this.state.phase === P.OVERVIEW || this.state.phase === P.RETURNING || this.editable;
		for (var slug in this.nodes) {
			if (!Object.prototype.hasOwnProperty.call(this.nodes, slug)) { continue; }
			var size = overview && this.overviewHit && this.overviewHit[slug] ? this.overviewHit[slug] : 44;
			this.nodes[slug].style.setProperty("--fp-hit", size + "px");
		}
	};

	/* ------------------------------------------------------------------ */
	/* camera                                                             */
	/* ------------------------------------------------------------------ */

	Viewer.prototype._descFor = function (slug) {
		if (slug && this.bySlug[slug]) { return { kind: "room", hotspot: this.bySlug[slug] }; }
		return { kind: "overview" };
	};

	/** Camera for the current (settled) state. */
	Viewer.prototype._targetCam = function () {
		if (this.freeCam && this.state.phase === P.OVERVIEW) { return this._clampFree(this.freeCam, false); }
		var desc = this._descFor(this.state.phase === P.OVERVIEW || this.state.phase === P.RETURNING ? null : this.state.slug);
		return C.resolveCamera(desc, this.layoutObj, this.preset, this.bounds, this.panX);
	};

	Viewer.prototype._renderStatic = function () {
		if (!this.layoutObj) { return; }
		this.cam = this._targetCam();
		this._render(this.cam);
	};

	Viewer.prototype._render = function (cam) {
		var L = this.layoutObj;
		if (!L) { return; }
		var t = C.calculateCameraTransform(cam, L);
		this.transform = t;
		this.world.style.transform = "translate3d(" + t.tx.toFixed(2) + "px," + t.ty.toFixed(2) + "px,0) scale(" + t.scale.toFixed(5) + ")";

		var rect = L.rect;
		var inOverview = this.state.phase === P.OVERVIEW;
		/* zoomed in: one finger moves the plan (touch-action none); at the overview
		   vertical swipes keep scrolling the page */
		this.root.classList.toggle("is-zoomed", !inOverview || cam.z > this.zMin * 1.03);
		/* close enough to read: room names appear next to the dots */
		this.root.classList.toggle("is-close", inOverview && cam.z > this.zMin * 2.1);
		if (this.panRange && this.panRange.pannable) {
			this.root.classList.toggle("can-left", cam.fx > this.panRange.min + 0.003);
			this.root.classList.toggle("can-right", cam.fx < this.panRange.max - 0.003);
		}
		for (var slug in this.nodes) {
			if (!Object.prototype.hasOwnProperty.call(this.nodes, slug)) { continue; }
			var h = this.bySlug[slug];
			if (!h) { continue; }
			var px = t.tx + t.scale * h.x * rect.width;
			var py = t.ty + t.scale * h.y * rect.height;
			this.nodes[slug].style.transform = "translate3d(" + px.toFixed(2) + "px," + py.toFixed(2) + "px,0)";
		}
		if (this.linkNode && this.link) {
			var lx = t.tx + t.scale * this.link.x * rect.width;
			var ly = t.ty + t.scale * this.link.y * rect.height;
			this.linkNode.style.transform = "translate3d(" + lx.toFixed(2) + "px," + ly.toFixed(2) + "px,0)";
		}
	};

	Viewer.prototype.reducedMotion = function () {
		return !!(this.mqReduce && this.mqReduce.matches);
	};

	/** Stop a running free camera flight (floor change), settling its promise. */
	Viewer.prototype._cancelFlight = function () {
		if (this.anim && this.anim.resolve) {
			var r = this.anim.resolve;
			this.anim = null;
			this.root.classList.remove("is-moving");
			r(false);
		}
	};

	Viewer.prototype._startAnimation = function (prev, next) {
		this._cancelFlight();
		this._stopMotion();
		this.freeCam = null;
		this.freeRaw = null;
		var kind = next.phase === P.RETURNING ? "back" : next.phase === P.TRANSITIONING_ROOM ? "room" : "focus";
		/* coming back to a pannable overview: show the area we just left */
		if (kind === "back" && next.from && this.bySlug[next.from]) { this.panX = this.bySlug[next.from].x; }
		var from = this.cam || (this.layoutObj ? this._targetCam() : null);
		var id = next.animId;
		var self = this;

		var instant = next.instant || this.reducedMotion() || !this.layoutObj || document.hidden;
		if (instant || !from) {
			this.anim = null;
			if (this.layoutObj) { this._renderStatic(); }
			/* settle on a later tick so listeners see the transition state first */
			Promise.resolve().then(function () { self._dispatch({ type: "animDone", id: id }); });
			return;
		}

		this.anim = {
			id: id,
			kind: kind,
			from: from,
			toSlug: next.slug,
			t0: null,
			dur: C.DURATION[kind],
			rho: kind === "room" ? 0.9 : 1.25,
			ease: kind === "room" ? C.easeInOutCubic : C.easeInOutQuint
		};
		this.root.classList.add("is-moving");
		if (!this.raf) { this.raf = root.requestAnimationFrame(this._onFrame); }
	};

	Viewer.prototype._onFrame = function (ts) {
		this.raf = 0;
		var a = this.anim;
		if (!a) { return; }
		if (!this.layoutObj) {
			/* zero-size stage: wait, do not spin */
			this.raf = root.requestAnimationFrame(this._onFrame);
			return;
		}
		if (a.t0 === null) { a.t0 = ts; }

		var p = Math.min(1, Math.max(0, (ts - a.t0) / a.dur));
		var to;
		if (a.toCam) {
			to = typeof a.toCam === "function" ? a.toCam() : a.toCam;
		} else {
			var isBack = this.state.phase === P.RETURNING;
			to = C.resolveCamera(this._descFor(isBack ? null : a.toSlug), this.layoutObj, this.preset, this.bounds, this.panX);
		}
		this.cam = C.interpolateCamera(a.from, to, a.ease(p), this.layoutObj, a.rho);
		this._render(this.cam);

		if (p < 1) {
			this.raf = root.requestAnimationFrame(this._onFrame);
			return;
		}

		this.cam = to;
		this._render(to);
		this.anim = null;
		this.root.classList.remove("is-moving");
		if (a.resolve) {
			a.resolve(true);
		} else {
			this._dispatch({ type: "animDone", id: a.id });
		}
	};

	/* ---- free camera flights, used for changing floors ---- */

	/** The overview camera of this floor (current pan kept). */
	Viewer.prototype.overviewCamera = function () {
		return this.layoutObj ? C.calculateOverviewCamera(this.layoutObj, this.preset, this.bounds, this.panX) : null;
	};

	/** Camera zoomed into the staircase (`factor` × the overview). */
	Viewer.prototype.linkCamera = function (factor) {
		if (!this.layoutObj || !this.link) { return null; }
		return C.calculatePointCamera(this.layoutObj, this.preset, this.bounds, this.link.x, this.link.y, factor);
	};

	/** Jump the camera without animating. */
	Viewer.prototype.setCamera = function (cam) {
		this._cancelFlight();
		if (!cam) { return; }
		this.cam = cam;
		this._render(cam);
	};

	/**
	 * Animate the camera to `to` (a camera, or a function returning one so it
	 * survives resizes). Resolves true when it lands, false if interrupted.
	 */
	Viewer.prototype.flyTo = function (to, dur, opts) {
		var self = this;
		opts = opts || {};
		this._cancelFlight();
		if (!opts.settle) { this._stopMotion(); }
		if (this.anim) { this.anim = null; }
		return new Promise(function (resolve) {
			var target = typeof to === "function" ? to() : to;
			if (!self.layoutObj || !target || !self.cam || self.reducedMotion() || document.hidden || !(dur > 0)) {
				if (target) { self.cam = target; self._render(target); }
				resolve(true);
				return;
			}
			self.anim = {
				from: self.cam,
				toCam: to,
				t0: null,
				dur: dur,
				rho: opts.rho || 1.1,
				ease: opts.ease || C.easeInOutCubic,
				settle: !!opts.settle,
				resolve: resolve
			};
			self.root.classList.add("is-moving");
			if (!self.raf) { self.raf = root.requestAnimationFrame(self._onFrame); }
		});
	};

	/**
	 * Drop any room selection without animating (used when leaving the floor).
	 * Bumps the animation id so a pending animDone can't land afterwards.
	 */
	Viewer.prototype.resetToOverview = function () {
		var prev = this.state;
		this._cancelFlight();
		this._stopMotion();
		this.anim = null;
		this.freeCam = null;
		this.freeRaw = null;
		this.root.classList.remove("is-moving");
		this.state = { phase: P.OVERVIEW, slug: null, from: null, animId: prev.animId + 1, instant: false };
		this._applyStateUI(prev);
		if (prev.phase !== P.OVERVIEW) { this._emit("state", this.state, prev); }
	};

	/* ------------------------------------------------------------------ */
	/* free exploration: drag, pinch, wheel, double-tap, momentum          */
	/* ------------------------------------------------------------------ */

	function easeOutCubic(t) { t = C.clamp(t, 0, 1); return 1 - Math.pow(1 - t, 3); }

	/** Zoom range for free exploration: the overview .. ~7x closer. */
	Viewer.prototype.zoomLimits = function () {
		var min = this.zMin || 1;
		return { min: min, max: Math.min(C.ZOOM_MAX, Math.max(min * 7, min + 1)) };
	};

	/** Same view, expressed with the focal point at the centre of the stage. */
	Viewer.prototype._centered = function (cam) {
		var L = this.layoutObj;
		var t = C.calculateCameraTransform(cam, L);
		return {
			fx: (L.width / 2 - t.tx) / (t.scale * L.rect.width),
			fy: (L.height / 2 - t.ty) / (t.scale * L.rect.height),
			z: t.scale,
			ax: 0.5,
			ay: 0.5
		};
	};

	/**
	 * Keep a free camera on the drawing. With `rubber` it may stretch past the
	 * edges with resistance (while a finger / the mouse holds it).
	 */
	Viewer.prototype._clampFree = function (cam, rubber) {
		var L = this.layoutObj;
		var b = this.bounds;
		var lim = this.zoomLimits();
		var ov = this._centered(this.overviewCamera());
		var z = cam.z;

		if (rubber) {
			if (z < lim.min) { z = lim.min * Math.pow(z / lim.min, 0.35); }
			if (z > lim.max) { z = lim.max * Math.pow(z / lim.max, 0.35); }
		} else {
			z = C.clamp(z, lim.min, lim.max);
		}

		function axis(v, c0, c1, half, ovc) {
			var lo = c0 + half;
			var hi = c1 - half;
			var a = ovc;
			var bb = ovc;
			if (lo <= hi) { a = Math.min(lo, ovc); bb = Math.max(hi, ovc); }
			if (v < a) { return rubber ? a - (a - v) * 0.35 : a; }
			if (v > bb) { return rubber ? bb + (v - bb) * 0.35 : bb; }
			return v;
		}

		return {
			fx: axis(cam.fx, b.x0, b.x1, L.width / 2 / (z * L.rect.width), ov.fx),
			fy: axis(cam.fy, b.y0, b.y1, L.height / 2 / (z * L.rect.height), ov.fy),
			z: z,
			ax: 0.5,
			ay: 0.5
		};
	};

	function panBy(cam, dx, dy, L) {
		return { fx: cam.fx - dx / (cam.z * L.rect.width), fy: cam.fy - dy / (cam.z * L.rect.height), z: cam.z, ax: 0.5, ay: 0.5 };
	}

	/** Zoom by k keeping the plan point under stage pixel (px, py) where it is. */
	function zoomAt(cam, k, px, py, L) {
		var nx = cam.fx + (px - L.width / 2) / (cam.z * L.rect.width);
		var ny = cam.fy + (py - L.height / 2) / (cam.z * L.rect.height);
		var z = cam.z * k;
		return { fx: nx - (px - L.width / 2) / (z * L.rect.width), fy: ny - (py - L.height / 2) / (z * L.rect.height), z: z, ax: 0.5, ay: 0.5 };
	}

	/** Start (or continue) free exploration from wherever the camera is now. */
	Viewer.prototype._beginFree = function () {
		this._stopMotion();
		if (this.state.phase !== P.OVERVIEW) {
			this.resetToOverview();
		} else if (this.anim) {
			this._cancelFlight();
			this.anim = null;
			this.root.classList.remove("is-moving");
		}
		if (!this.cam) { this.cam = this._targetCam(); }
		this.freeRaw = this._centered(this.cam);
		this.freeCam = this.freeRaw;
		this._emit("gesture");
	};

	Viewer.prototype._applyFree = function (raw, rubber) {
		this.freeRaw = raw;
		this.freeCam = this._clampFree(raw, rubber);
		this.cam = this.freeCam;
		this._render(this.cam);
	};

	Viewer.prototype._stopMotion = function () {
		if (this.motionRaf) { root.cancelAnimationFrame(this.motionRaf); }
		this.motionRaf = 0;
		this.motion = null;
	};

	/** After a gesture: glide back inside the drawing; near the overview, snap to it exactly. */
	Viewer.prototype._settle = function () {
		var self = this;
		if (!this.freeCam || !this.layoutObj) { return; }
		var lim = this.zoomLimits();
		var hard = this._clampFree(this.freeRaw, false);
		var toOverview = hard.z <= lim.min * 1.04;
		/* a phone overview is wider than the screen: keep where the visitor panned to */
		if (toOverview && this.panRange && this.panRange.pannable) { this.panX = hard.fx; }
		var target = toOverview ? this.overviewCamera() : hard;
		var c = this.cam;
		var same = Math.abs(c.z - target.z) < 1e-4 && Math.abs(c.fx - target.fx) < 1e-5 && Math.abs(c.fy - target.fy) < 1e-5 && c.ax === target.ax && c.ay === target.ay;
		if (same) {
			if (toOverview) { this.freeCam = null; this.freeRaw = null; }
			return;
		}
		this.flyTo(target, toOverview ? 420 : 360, { rho: 0.6, ease: easeOutCubic, settle: true }).then(function (landed) {
			if (!landed) { return; }
			if (toOverview) {
				self.freeCam = null;
				self.freeRaw = null;
			} else {
				self.freeRaw = target;
				self.freeCam = target;
			}
		});
	};

	/**
	 * One loop for momentum (after a drag) and smoothed wheel zoom.
	 *   motion = { vx, vy }            px per ms, decaying
	 *   motion = { lz, px, py }        log zoom target + anchor
	 */
	Viewer.prototype._runMotion = function () {
		var self = this;
		if (this.motionRaf) { return; }
		var last = null;
		var step = function (ts) {
			self.motionRaf = 0;
			var m = self.motion;
			if (!m || !self.layoutObj) { return; }
			var dt = last === null ? 16 : Math.min(48, ts - last);
			last = ts;
			var raw = self.freeRaw;
			var done = true;

			if (m.lz !== undefined) {
				var cur = Math.log(raw.z);
				var nz = cur + (m.lz - cur) * (1 - Math.exp(-dt / 70));
				raw = zoomAt(raw, Math.exp(nz - cur), m.px, m.py, self.layoutObj);
				if (Math.abs(m.lz - nz) > 0.002) { done = false; }
			}
			if (m.vx !== undefined) {
				raw = panBy(raw, m.vx * dt, m.vy * dt, self.layoutObj);
				var decay = Math.exp(-dt / 320);
				m.vx *= decay;
				m.vy *= decay;
				var hard = self._clampFree(raw, false);
				/* hitting an edge stops that direction instead of drifting off */
				if (Math.abs(hard.fx - raw.fx) > 1e-6) { m.vx = 0; raw.fx = hard.fx; }
				if (Math.abs(hard.fy - raw.fy) > 1e-6) { m.vy = 0; raw.fy = hard.fy; }
				if (Math.abs(m.vx) + Math.abs(m.vy) > 0.01) { done = false; }
			}

			self._applyFree(raw, false);
			if (done) {
				self.motion = null;
				self._settle();
				return;
			}
			self.motionRaf = root.requestAnimationFrame(step);
		};
		this.motionRaf = root.requestAnimationFrame(step);
	};

	/** Zoom by k at stage pixel (px, py), smoothly. */
	Viewer.prototype.zoomBy = function (k, px, py, animate) {
		if (!this.layoutObj) { return; }
		if (!this.freeCam || this.state.phase !== P.OVERVIEW || (this.anim && !this.anim.settle)) { this._beginFree(); }
		if (this.anim && this.anim.settle) { this._cancelFlight(); this.freeRaw = this._centered(this.cam); }
		var lim = this.zoomLimits();
		var base = this.motion && this.motion.lz !== undefined ? this.motion.lz : Math.log(this.freeRaw.z);
		var lz = C.clamp(base + Math.log(k), Math.log(lim.min), Math.log(lim.max));
		if (px === undefined) { px = this.W / 2; py = this.H / 2; }
		if (animate === false) {
			this._applyFree(zoomAt(this.freeRaw, Math.exp(lz) / this.freeRaw.z, px, py, this.layoutObj), false);
			this._settle();
			return;
		}
		this.motion = { lz: lz, px: px, py: py };
		this._runMotion();
	};

	Viewer.prototype._stagePoint = function (e) {
		var r = this.stage.getBoundingClientRect();
		return { x: e.clientX - r.left, y: e.clientY - r.top };
	};

	/**
	 * Mouse: drag to move, wheel to zoom (at the cursor), double-click to zoom in.
	 * Touch: drag to move, pinch to zoom, double-tap to zoom in. While the plan is
	 * at its overview, vertical swipes still scroll the page (touch-action: pan-y);
	 * once zoomed in, one finger moves the plan in every direction.
	 */
	Viewer.prototype._bindGestures = function () {
		var self = this;
		var el = this.root;
		var pts = {};
		var count = 0;
		var drag = null;
		var pinch = null;
		var lastTap = null;
		var samples = [];

		function midDist() {
			var ids = Object.keys(pts);
			var a = pts[ids[0]];
			var b = pts[ids[1]];
			return { x: (a.x + b.x) / 2, y: (a.y + b.y) / 2, d: Math.max(1, Math.hypot(a.x - b.x, a.y - b.y)) };
		}

		function startPinch() {
			var m = midDist();
			if (!self.freeCam || self.state.phase !== P.OVERVIEW) { self._beginFree(); }
			pinch = { raw: self.freeRaw, x: m.x, y: m.y, d: m.d };
			if (drag) { drag.moved = true; }
		}

		el.addEventListener("pointerdown", function (e) {
			if (e.pointerType === "mouse" && e.button !== 0) { return; }
			if (self.anim && !self.anim.settle && self.anim.resolve) { return; } /* floor change in progress */
			var p = self._stagePoint(e);
			if (!pts[e.pointerId]) { count++; }
			pts[e.pointerId] = p;
			self._stopMotion();
			if (self.anim && self.anim.settle) { self._cancelFlight(); }

			if (count === 1) {
				drag = { id: e.pointerId, sx: p.x, sy: p.y, x: p.x, y: p.y, moved: false, captured: false, type: e.pointerType };
				samples = [{ t: e.timeStamp, x: p.x, y: p.y }];
			} else if (count === 2) {
				try { el.setPointerCapture(e.pointerId); } catch (err) { /* ignore */ }
				if (drag && !drag.captured) { try { el.setPointerCapture(drag.id); drag.captured = true; } catch (err2) { /* ignore */ } }
				startPinch();
			}
		});

		el.addEventListener("pointermove", function (e) {
			if (!pts[e.pointerId]) { return; }
			var p = self._stagePoint(e);
			pts[e.pointerId] = p;
			var L = self.layoutObj;
			if (!L) { return; }

			if (pinch && count >= 2) {
				var m = midDist();
				var k = m.d / pinch.d;
				var raw = zoomAt(pinch.raw, k, pinch.x, pinch.y, L);
				raw = panBy(raw, m.x - pinch.x, m.y - pinch.y, L);
				self._applyFree(raw, true);
				return;
			}

			if (!drag || e.pointerId !== drag.id) { return; }
			if (!drag.moved) {
				if (Math.hypot(p.x - drag.sx, p.y - drag.sy) < 6) { return; }
				drag.moved = true;
				drag.x = p.x;
				drag.y = p.y;
				/* capturing also stops the release from clicking a hotspot */
				try { el.setPointerCapture(drag.id); drag.captured = true; } catch (err) { /* ignore */ }
				el.classList.add("is-panning");
				self._beginFree();
				return;
			}
			self._applyFree(panBy(self.freeRaw, p.x - drag.x, p.y - drag.y, L), true);
			drag.x = p.x;
			drag.y = p.y;
			samples.push({ t: e.timeStamp, x: p.x, y: p.y });
			while (samples.length > 2 && e.timeStamp - samples[0].t > 90) { samples.shift(); }
		});

		var end = function (e) {
			if (!pts[e.pointerId]) { return; }
			delete pts[e.pointerId];
			count = Math.max(0, count - 1);
			try { el.releasePointerCapture(e.pointerId); } catch (err) { /* ignore */ }

			if (pinch) {
				if (count < 2) {
					pinch = null;
					/* continue as a drag with the remaining finger */
					var ids = Object.keys(pts);
					if (ids.length === 1) {
						var q = pts[ids[0]];
						drag = { id: +ids[0], sx: q.x, sy: q.y, x: q.x, y: q.y, moved: true, captured: true, type: "touch" };
						samples = [];
					}
				}
				if (count === 0) { el.classList.remove("is-panning"); drag = null; self._settle(); }
				return;
			}

			if (!drag || e.pointerId !== drag.id) { return; }
			var d = drag;
			drag = null;
			el.classList.remove("is-panning");

			if (d.moved) {
				if (e.type === "pointercancel" || samples.length < 2) { self._settle(); return; }
				var a = samples[0];
				var b = samples[samples.length - 1];
				var dt = Math.max(1, b.t - a.t);
				var vx = (b.x - a.x) / dt;
				var vy = (b.y - a.y) / dt;
				if (e.timeStamp - b.t > 80 || Math.hypot(vx, vy) < 0.05) { self._settle(); return; }
				self.motion = { vx: C.clamp(vx, -4, 4), vy: C.clamp(vy, -4, 4) };
				self._runMotion();
				return;
			}

			/* a tap: double-tap (touch) on the plan zooms in there */
			if (d.type !== "mouse" && e.type !== "pointercancel" && !(e.target.closest && e.target.closest(".fp-spot, .fp-link"))) {
				var now = e.timeStamp;
				if (lastTap && now - lastTap.t < 320 && Math.hypot(d.sx - lastTap.x, d.sy - lastTap.y) < 30) {
					lastTap = null;
					self.zoomBy(2.2, d.sx, d.sy);
				} else {
					lastTap = { t: now, x: d.sx, y: d.sy };
				}
			}
		};
		el.addEventListener("pointerup", end);
		el.addEventListener("pointercancel", end);

		el.addEventListener("dblclick", function (e) {
			if (e.target.closest && e.target.closest(".fp-spot, .fp-link")) { return; }
			var p = self._stagePoint(e);
			self.zoomBy(e.shiftKey ? 1 / 2.2 : 2.2, p.x, p.y);
		});

		/* wheel / trackpad: zoom at the cursor; fully zoomed out, scrolling down
		   is left to the page so visitors are never trapped in the hero */
		el.addEventListener("wheel", function (e) {
			if (!self.layoutObj || (self.anim && !self.anim.settle && self.anim.resolve)) { return; }
			var dy = e.deltaY * (e.deltaMode === 1 ? 16 : e.deltaMode === 2 ? 400 : 1);
			if (!dy) { return; }
			var lim = self.zoomLimits();
			var z = self.motion && self.motion.lz !== undefined ? Math.exp(self.motion.lz) : (self.cam ? self.cam.z : lim.min);
			var atOverview = self.state.phase === P.OVERVIEW && !self.freeCam;
			if (dy > 0 && (atOverview || z <= lim.min * 1.001) && self.state.phase === P.OVERVIEW) { return; }
			if (dy < 0 && z >= lim.max * 0.999) { return; }
			e.preventDefault();
			var p = self._stagePoint(e);
			var k = Math.exp(-C.clamp(dy, -240, 240) * (e.ctrlKey ? 0.012 : 0.0025));
			self.zoomBy(k, p.x, p.y);
		}, { passive: false });
	};

	/* ------------------------------------------------------------------ */
	/* state                                                              */
	/* ------------------------------------------------------------------ */

	Viewer.prototype._dispatch = function (action) {
		var prev = this.state;
		var next = C.reduce(prev, action);
		if (next === prev) { return false; }

		this.state = next;

		if (next.animId !== prev.animId) {
			this._startAnimation(prev, next);
		}
		this._applyStateUI(prev);
		this._emit("state", next, prev);
		if (next.slug && next.slug !== prev.slug) { this._emit("select", next.slug, prev.slug); }
		return true;
	};

	Viewer.prototype._applyStateUI = function () {
		var s = this.state;
		var focused = C.isControlsVisible(s);
		this.root.setAttribute("data-phase", s.phase);

		for (var slug in this.nodes) {
			if (!Object.prototype.hasOwnProperty.call(this.nodes, slug)) { continue; }
			var node = this.nodes[slug];
			var isSel = focused && s.slug === slug;
			var isFrom = s.phase === P.TRANSITIONING_ROOM && s.from === slug;

			node.classList.toggle("is-selected", isSel);
			/* in a room only the chosen hotspot stays; editors always see all */
			node.classList.toggle("is-off", !this.editable && focused && !isSel && !isFrom);
			if (isSel) { node.setAttribute("aria-current", "true"); } else { node.removeAttribute("aria-current"); }
		}
		if (this.linkNode) { this.linkNode.classList.toggle("is-off", !this.editable && focused); }
		this._applyHits();
	};

	/* ------------------------------------------------------------------ */
	/* public commands                                                    */
	/* ------------------------------------------------------------------ */

	Viewer.prototype.select = function (slug) { return this._dispatch({ type: "select", slug: slug, order: this.order }); };
	Viewer.prototype.next = function () { return this._dispatch({ type: "next", order: this.order }); };
	Viewer.prototype.prev = function () { return this._dispatch({ type: "prev", order: this.order }); };
	Viewer.prototype.back = function () { return this._dispatch({ type: "back", order: this.order }); };
	Viewer.prototype.getState = function () { return this.state; };
	Viewer.prototype.getHotspot = function (slug) { return this.bySlug[slug] || null; };
	Viewer.prototype.getNode = function (slug) { return this.nodes[slug] || null; };

	/** Current world->stage transform ({scale,tx,ty}) and contained rect. */
	Viewer.prototype.getGeometry = function () {
		return { transform: this.transform, layout: this.layoutObj, preset: this.preset };
	};

	/** Stage-relative pixel -> normalized image coordinates (clamped). */
	Viewer.prototype.clientToNormalized = function (clientX, clientY) {
		var r = this.stage.getBoundingClientRect();
		/* the editor shows a CSS-scaled frame: convert to unscaled stage pixels */
		var kx = this.W > 0 ? r.width / this.W : 1;
		var ky = this.H > 0 ? r.height / this.H : 1;
		return C.screenToNormalized((clientX - r.left) / kx, (clientY - r.top) / ky, this.transform, this.layoutObj.rect);
	};

	Viewer.prototype.destroy = function () {
		this._cancelFlight();
		this._stopMotion();
		if (this.raf) { root.cancelAnimationFrame(this.raf); }
		if (this.ro) { this.ro.disconnect(); }
		if (this._onWinResize) { root.removeEventListener("resize", this._onWinResize); }
		this.anim = null;
		this.listeners = { state: [], select: [], move: [], add: [], layout: [], link: [], linkmove: [], gesture: [] };
	};

	/* ------------------------------------------------------------------ */
	/* editor hooks (only active with editable: true)                     */
	/* ------------------------------------------------------------------ */

	Viewer.prototype.setSelectedEdit = function (slug) {
		this.selectedEdit = slug;
		for (var s in this.nodes) {
			if (Object.prototype.hasOwnProperty.call(this.nodes, s)) {
				this.nodes[s].classList.toggle("is-edit-selected", s === slug);
			}
		}
	};

	Viewer.prototype.setAddMode = function (on) {
		this.addMode = !!on;
		this.root.classList.toggle("is-add-mode", this.addMode);
		if (this.addMode && !this._addBound) {
			this._addBound = true;
			var self = this;
			this.root.addEventListener("click", function (e) {
				if (!self.addMode || e.target.closest(".fp-spot, .fp-link")) { return; }
				if (!self.layoutObj) { return; }
				var n = self.clientToNormalized(e.clientX, e.clientY);
				self._emit("add", n.x, n.y);
			});
		}
	};

	/** Pointer-drag a node; onMove(x, y, final) gets normalized coordinates. */
	Viewer.prototype._bindDrag = function (node, onMove, onGrab) {
		var self = this;
		var dragging = false;
		var moved = false;
		var startX = 0;
		var startY = 0;

		node.addEventListener("pointerdown", function (e) {
			if (e.button !== undefined && e.button !== 0) { return; }
			dragging = true;
			moved = false;
			startX = e.clientX;
			startY = e.clientY;
			try { node.setPointerCapture(e.pointerId); } catch (err) { /* ignore */ }
			e.preventDefault();
			onGrab();
		});

		node.addEventListener("pointermove", function (e) {
			if (!dragging) { return; }
			if (!moved && Math.hypot(e.clientX - startX, e.clientY - startY) < 3) { return; }
			moved = true;
			var n = self.clientToNormalized(e.clientX, e.clientY);
			onMove(n.x, n.y, false);
		});

		var end = function (e) {
			if (!dragging) { return; }
			dragging = false;
			try { node.releasePointerCapture(e.pointerId); } catch (err) { /* ignore */ }
			if (moved) {
				var n = self.clientToNormalized(e.clientX, e.clientY);
				onMove(n.x, n.y, true);
			}
		};
		node.addEventListener("pointerup", end);
		node.addEventListener("pointercancel", end);
		node.addEventListener("click", function (e) { e.preventDefault(); });
	};

	/** Editor: move a hotspot without re-creating nodes (used while dragging). */
	Viewer.prototype.setHotspotPosition = function (slug, x, y) {
		var h = this.bySlug[slug];
		if (!h) { return; }
		h.x = C.clamp(x, 0, 1);
		h.y = C.clamp(y, 0, 1);
		if (this.anim) { return; }
		if (this.cam) { this._render(this.cam); }
	};

	/** Editor: move the staircase marker. */
	Viewer.prototype.setLinkPosition = function (x, y) {
		if (!this.link) { return; }
		this.link.x = C.clamp(x, 0, 1);
		this.link.y = C.clamp(y, 0, 1);
		if (this.cam && !this.anim) { this._render(this.cam); }
	};

	/** Editor: re-aim the camera after camera settings of the open hotspot change. */
	Viewer.prototype.refreshCamera = function () {
		if (!this.anim) { this._renderStatic(); }
	};

	root.FloorPlanViewer = Viewer;
})(typeof window !== "undefined" ? window : this);
