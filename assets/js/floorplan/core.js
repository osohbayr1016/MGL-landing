/*!
 * Interactive floor plan — pure core (no DOM).
 *
 * Shared by the public hero (assets/js/floorplan/app.js) and the CP Admin
 * editor, and unit-tested with `node --test tests/floorplan`.
 *
 * Coordinate model
 * ----------------
 * Hotspots are stored NORMALIZED against the intrinsic image: x,y in 0..1.
 * The "world" is the object-fit:contain rectangle of the image inside the
 * stage (calculateContainedImageRect). A camera is viewport independent:
 *
 *   { fx, fy, z, ax, ay }
 *     fx,fy  focal point on the image (normalized 0..1)
 *     z      scale of the world; 1 = whole image fits (contain), 4.5 = 4.5x
 *     ax,ay  where the focal point lands in the viewport (fractions of W,H)
 *
 * so a resize/rotation during an animation just re-resolves the same camera.
 */
(function (root, factory) {
	if (typeof module === "object" && module.exports) {
		module.exports = factory();
	} else {
		root.FloorPlanCore = factory();
	}
})(typeof self !== "undefined" ? self : this, function () {
	"use strict";

	/* ---- limits (also enforced server-side in class/floorplan.class.php) ---- */
	var ZOOM_MIN = 1.2;
	var ZOOM_MAX = 16;
	var OFFSET_MAX = 0.4;
	var ANCHOR_MIN = 0.08;
	var ANCHOR_MAX = 0.92;

	/* The drawing itself, normalized — the rest of the image is white canvas. */
	var DEFAULT_CONTENT_BOUNDS = { x0: 0.04, y0: 0.2, x1: 0.97, y1: 0.84 };

	var PHASE = {
		OVERVIEW: "OVERVIEW",
		FOCUSING: "FOCUSING",
		FOCUSED: "FOCUSED",
		TRANSITIONING_ROOM: "TRANSITIONING_ROOM",
		RETURNING: "RETURNING"
	};

	/* ------------------------------------------------------------------ */
	/* numbers                                                            */
	/* ------------------------------------------------------------------ */

	function isNum(v) {
		return typeof v === "number" && isFinite(v);
	}

	function clamp(v, min, max) {
		return Math.min(max, Math.max(min, v));
	}

	/** Number or fallback — never NaN/Infinity. */
	function num(v, fallback) {
		if (typeof v === "string" && v.trim() !== "") {
			v = Number(v);
		}
		return isNum(v) ? v : fallback;
	}

	/** Number clamped to a range; non-numeric -> fallback (also clamped). */
	function clampNum(v, min, max, fallback) {
		return clamp(num(v, fallback), min, max);
	}

	function lerp(a, b, t) {
		return a + (b - a) * t;
	}

	/* ------------------------------------------------------------------ */
	/* geometry                                                           */
	/* ------------------------------------------------------------------ */

	/**
	 * Where an image of iw x ih lands inside a cw x ch box with
	 * object-fit: contain. Zero/invalid sizes give an all-zero rect.
	 */
	function calculateContainedImageRect(cw, ch, iw, ih) {
		if (!(cw > 0 && ch > 0 && iw > 0 && ih > 0)) {
			return { left: 0, top: 0, width: 0, height: 0 };
		}
		var s = Math.min(cw / iw, ch / ih);
		var w = iw * s;
		var h = ih * s;
		return { left: (cw - w) / 2, top: (ch - h) / 2, width: w, height: h };
	}

	/** Normalized (0..1) point -> point in the rect's parent coordinate space. */
	function normalizedPointToImagePoint(x, y, rect) {
		return { x: rect.left + x * rect.width, y: rect.top + y * rect.height };
	}

	/** Inverse of normalizedPointToImagePoint; clamps to 0..1 by default. */
	function imagePointToNormalized(px, py, rect, noClamp) {
		if (!(rect.width > 0 && rect.height > 0)) {
			return { x: 0, y: 0 };
		}
		var x = (px - rect.left) / rect.width;
		var y = (py - rect.top) / rect.height;
		return noClamp ? { x: x, y: y } : { x: clamp(x, 0, 1), y: clamp(y, 0, 1) };
	}

	/**
	 * Camera -> CSS transform of the world element (which is sized to the
	 * contained rect, transform-origin 0 0):
	 *   screen = (tx,ty) + scale * (nx*rect.width, ny*rect.height)
	 * layout = { width, height, rect }  (stage size + contained rect)
	 */
	function calculateCameraTransform(cam, layout) {
		var rect = layout.rect;
		return {
			scale: cam.z,
			tx: cam.ax * layout.width - cam.z * cam.fx * rect.width,
			ty: cam.ay * layout.height - cam.z * cam.fy * rect.height
		};
	}

	/** Normalized image point -> stage pixel under a given transform. */
	function pointToScreen(nx, ny, t, rect) {
		return { x: t.tx + t.scale * nx * rect.width, y: t.ty + t.scale * ny * rect.height };
	}

	/**
	 * Stage pixel -> normalized image point under a given transform. Correct
	 * whatever the camera is, so the editor can drag while zoomed.
	 */
	function screenToNormalized(sx, sy, t, rect, noClamp) {
		if (!(rect.width > 0 && rect.height > 0 && t.scale > 0)) {
			return { x: 0, y: 0 };
		}
		var x = (sx - t.tx) / (t.scale * rect.width);
		var y = (sy - t.ty) / (t.scale * rect.height);
		return noClamp ? { x: x, y: y } : { x: clamp(x, 0, 1), y: clamp(y, 0, 1) };
	}

	/* ------------------------------------------------------------------ */
	/* responsive presets                                                 */
	/* ------------------------------------------------------------------ */

	/**
	 * Classify the STAGE (not the window), so the editor's phone preview
	 * frame behaves exactly like a real phone.
	 */
	function getViewportClass(width, height, coarse) {
		var portrait = height > width * 1.05;
		if (portrait) {
			return width < 600 ? "mobilePortrait" : "tabletPortrait";
		}
		if (height <= 500) {
			return "mobileLandscape";
		}
		if (coarse && width < 1280) {
			return "tabletLandscape";
		}
		return width >= 1700 ? "largeDesktop" : "desktop";
	}

	var PRESETS = {
		/* overviewZoom > 1: the overview is larger than the screen and is panned
		   horizontally. "auto" (phones in portrait): the wider the drawing, the
		   more it is enlarged, so a wide plan never shrinks to an unusable strip
		   while a near-square one still fits the screen whole. */
		largeDesktop: { set: "desktop", zoomMul: 1, ax: 0.54, ay: 0.44, overviewZoom: 1, pad: { top: 84, right: 56, bottom: 56, left: 56 } },
		desktop: { set: "desktop", zoomMul: 1, ax: 0.53, ay: 0.45, overviewZoom: 1, pad: { top: 80, right: 40, bottom: 48, left: 40 } },
		tabletLandscape: { set: "desktop", zoomMul: 0.9, ax: 0.54, ay: 0.42, overviewZoom: 1, pad: { top: 72, right: 28, bottom: 40, left: 28 } },
		tabletPortrait: { set: "mobile", zoomMul: 0.9, ax: 0.5, ay: 0.4, overviewZoom: 1, pad: { top: 80, right: 24, bottom: 40, left: 24 } },
		mobileLandscape: { set: "desktop", zoomMul: 0.85, ax: 0.6, ay: 0.46, overviewZoom: 1, pad: { top: 56, right: 20, bottom: 20, left: 20 } },
		mobilePortrait: { set: "mobile", zoomMul: 1, ax: 0.5, ay: 0.41, overviewZoom: "auto", pad: { top: 72, right: 0, bottom: 24, left: 0 } }
	};

	function getResponsiveCameraPreset(width, height, coarse) {
		var name = getViewportClass(width, height, coarse);
		var p = PRESETS[name];
		return { name: name, set: p.set, zoomMul: p.zoomMul, ax: p.ax, ay: p.ay, overviewZoom: p.overviewZoom, pad: p.pad };
	}

	/* ------------------------------------------------------------------ */
	/* cameras                                                            */
	/* ------------------------------------------------------------------ */

	function overviewFactor(setting, contentAspect) {
		if (setting === "auto") {
			return clamp(contentAspect / 1.35, 1, 2.2);
		}
		return setting > 1 ? setting : 1;
	}

	/** Bounds may come as [x0,y0,x1,y1] (JSON) or {x0,y0,x1,y1}. */
	function normalizeBounds(b) {
		if (Array.isArray(b) && b.length === 4) {
			b = { x0: num(b[0], NaN), y0: num(b[1], NaN), x1: num(b[2], NaN), y1: num(b[3], NaN) };
		}
		if (!b || !(b.x1 > b.x0 && b.y1 > b.y0) || !isNum(b.x0) || !isNum(b.y0)) {
			return DEFAULT_CONTENT_BOUNDS;
		}
		return { x0: clamp(b.x0, 0, 1), y0: clamp(b.y0, 0, 1), x1: clamp(b.x1, 0, 1), y1: clamp(b.y1, 0, 1) };
	}

	function overviewGeometry(layout, preset, bounds) {
		var b = bounds || DEFAULT_CONTENT_BOUNDS;
		var rect = layout.rect;
		var pad = preset.pad;
		var availW = Math.max(1, layout.width - pad.left - pad.right);
		var availH = Math.max(1, layout.height - pad.top - pad.bottom);
		var cw = Math.max(1e-6, (b.x1 - b.x0) * rect.width);
		var ch = Math.max(1e-6, (b.y1 - b.y0) * rect.height);
		var zFit = Math.min(availW / cw, availH / ch);
		var z = zFit * overviewFactor(preset.overviewZoom, cw / ch);
		/* half of the visible width, in image-width units */
		var half = availW / 2 / (z * rect.width);
		var min = b.x0 + half;
		var max = b.x1 - half;
		var pannable = max > min + 1e-6;
		return {
			b: b, z: z, availW: availW, availH: availH, pad: pad,
			pannable: pannable,
			min: pannable ? min : (b.x0 + b.x1) / 2,
			max: pannable ? max : (b.x0 + b.x1) / 2
		};
	}

	/** Horizontal range (normalized focal x) the overview can be panned over. */
	function overviewPanRange(layout, preset, bounds) {
		var g = overviewGeometry(layout, preset, bounds);
		return { min: g.min, max: g.max, pannable: g.pannable };
	}

	/**
	 * Whole drawing, centred inside the padded stage (never crops it). When
	 * the preset zooms the overview (phones) it is wider than the stage and
	 * `panX` (normalized focal x, default: left edge) selects what is visible.
	 */
	function calculateOverviewCamera(layout, preset, bounds, panX) {
		var g = overviewGeometry(layout, preset, bounds);
		var b = g.b;
		var fx = g.pannable
			? clamp(isNum(panX) ? panX : g.min, g.min, g.max)
			: (b.x0 + b.x1) / 2;
		return {
			fx: fx,
			fy: (b.y0 + b.y1) / 2,
			z: g.z,
			ax: (g.pad.left + g.availW / 2) / layout.width,
			ay: (g.pad.top + g.availH / 2) / layout.height
		};
	}

	/** Camera for a selected hotspot under a preset (desktop or mobile set). */
	function resolveRoomCamera(hotspot, preset) {
		var s = hotspot[preset.set] || hotspot.desktop || {};
		var fx = s.focusX === null || s.focusX === undefined ? hotspot.x : s.focusX;
		var fy = s.focusY === null || s.focusY === undefined ? hotspot.y : s.focusY;
		return {
			fx: clampNum(fx, 0, 1, hotspot.x),
			fy: clampNum(fy, 0, 1, hotspot.y),
			z: clamp(clampNum(s.zoom, ZOOM_MIN, ZOOM_MAX, 4.5) * preset.zoomMul, ZOOM_MIN, ZOOM_MAX),
			ax: clamp(preset.ax + clampNum(s.offsetX, -OFFSET_MAX, OFFSET_MAX, 0), ANCHOR_MIN, ANCHOR_MAX),
			ay: clamp(preset.ay + clampNum(s.offsetY, -OFFSET_MAX, OFFSET_MAX, 0), ANCHOR_MIN, ANCHOR_MAX)
		};
	}

	/**
	 * Camera that "walks into" a point (the staircase between floors): the
	 * overview zoomed `factor` times, centred on the point. Used for the floor
	 * change: the leaving floor flies into its stair, the arriving floor starts
	 * at its own stair and pulls back to the overview.
	 */
	function calculatePointCamera(layout, preset, bounds, x, y, factor) {
		var ov = calculateOverviewCamera(layout, preset, bounds);
		return {
			fx: clamp(num(x, 0.5), 0, 1),
			fy: clamp(num(y, 0.5), 0, 1),
			z: clamp(ov.z * (factor > 0 ? factor : 3), ZOOM_MIN * 0.5, ZOOM_MAX),
			ax: 0.5,
			ay: 0.5
		};
	}

	/**
	 * Resolve a camera descriptor — { kind: "overview" } or
	 * { kind: "room", hotspot } — for the current layout.
	 */
	function resolveCamera(desc, layout, preset, bounds, panX) {
		if (desc && desc.kind === "room" && desc.hotspot) {
			return resolveRoomCamera(desc.hotspot, preset);
		}
		return calculateOverviewCamera(layout, preset, bounds, panX);
	}

	/* ------------------------------------------------------------------ */
	/* animation path (van Wijk & Nuij smooth zoom-and-pan)               */
	/* ------------------------------------------------------------------ */

	function cosh(x) {
		return (Math.exp(x) + Math.exp(-x)) / 2;
	}
	function sinh(x) {
		return (Math.exp(x) - Math.exp(-x)) / 2;
	}
	function tanh(x) {
		if (x > 20) { return 1; }
		if (x < -20) { return -1; }
		var a = Math.exp(2 * x);
		return (a - 1) / (a + 1);
	}

	/**
	 * Interpolates between two cameras. p is the (already eased) progress.
	 * Position/size follow the optimal zoom-and-pan path (constant perceived
	 * speed, no overshoot: travelling between rooms eases out a touch and
	 * back in instead of sliding a magnified image). The anchor is a plain
	 * lerp. `rho` is how pronounced the zoom arc is (~0.8 subtle .. 1.4).
	 * p = 0 / 1 return the endpoints exactly.
	 */
	function interpolateCamera(a, b, p, layout, rho) {
		if (p <= 0) { return copyCamera(a); }
		if (p >= 1) { return copyCamera(b); }
		var rect = layout.rect;
		if (!(rect.width > 0 && rect.height > 0 && layout.width > 0)) {
			return copyCamera(b);
		}
		rho = rho > 0 ? rho : 1.2;

		/* units: image widths. visible width w = stageW / (z * baseW). */
		var k = rect.height / rect.width;
		var ux0 = a.fx, uy0 = a.fy * k, w0 = layout.width / (a.z * rect.width);
		var ux1 = b.fx, uy1 = b.fy * k, w1 = layout.width / (b.z * rect.width);

		var rho2 = rho * rho;
		var rho4 = rho2 * rho2;
		var dx = ux1 - ux0;
		var dy = uy1 - uy0;
		var d2 = dx * dx + dy * dy;
		var ux, uy, w;

		if (d2 < 1e-10) {
			var S0 = Math.log(w1 / w0) / rho;
			ux = ux0 + p * dx;
			uy = uy0 + p * dy;
			w = w0 * Math.exp(rho * p * S0);
		} else {
			var d1 = Math.sqrt(d2);
			var b0 = (w1 * w1 - w0 * w0 + rho4 * d2) / (2 * w0 * rho2 * d1);
			var b1 = (w1 * w1 - w0 * w0 - rho4 * d2) / (2 * w1 * rho2 * d1);
			var r0 = Math.log(Math.sqrt(b0 * b0 + 1) - b0);
			var r1 = Math.log(Math.sqrt(b1 * b1 + 1) - b1);
			var S = (r1 - r0) / rho;
			var s = p * S;
			var coshr0 = cosh(r0);
			var u = (w0 / (rho2 * d1)) * (coshr0 * tanh(rho * s + r0) - sinh(r0));
			ux = ux0 + u * dx;
			uy = uy0 + u * dy;
			w = (w0 * coshr0) / cosh(rho * s + r0);
		}

		return {
			fx: ux,
			fy: uy / k,
			z: layout.width / (w * rect.width),
			ax: lerp(a.ax, b.ax, p),
			ay: lerp(a.ay, b.ay, p)
		};
	}

	function copyCamera(c) {
		return { fx: c.fx, fy: c.fy, z: c.z, ax: c.ax, ay: c.ay };
	}

	/* easing: gentle start, long soft landing */
	function easeInOutCubic(t) {
		t = clamp(t, 0, 1);
		return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
	}
	function easeInOutQuint(t) {
		t = clamp(t, 0, 1);
		return t < 0.5 ? 16 * Math.pow(t, 5) : 1 - Math.pow(-2 * t + 2, 5) / 2;
	}

	var DURATION = { focus: 1000, room: 760, back: 900 };

	/* ------------------------------------------------------------------ */
	/* hotspot data                                                       */
	/* ------------------------------------------------------------------ */

	var KINDS = ["room", "team", "entrance", "terrace", "other"];

	function cleanText(v, max) {
		if (v === null || v === undefined) { return ""; }
		var s = String(v).replace(/[\u0000-\u0008\u000B\u000C\u000E-\u001F]/g, "").trim();
		return s.length > max ? s.slice(0, max) : s;
	}

	function cleanSet(s, defZoom) {
		s = s || {};
		var hasFocusX = s.focusX !== null && s.focusX !== undefined && s.focusX !== "";
		var hasFocusY = s.focusY !== null && s.focusY !== undefined && s.focusY !== "";
		return {
			zoom: clampNum(s.zoom, ZOOM_MIN, ZOOM_MAX, defZoom),
			focusX: hasFocusX && isNum(num(s.focusX, NaN)) ? clamp(num(s.focusX, 0), 0, 1) : null,
			focusY: hasFocusY && isNum(num(s.focusY, NaN)) ? clamp(num(s.focusY, 0), 0, 1) : null,
			offsetX: clampNum(s.offsetX, -OFFSET_MAX, OFFSET_MAX, 0),
			offsetY: clampNum(s.offsetY, -OFFSET_MAX, OFFSET_MAX, 0)
		};
	}

	/**
	 * Returns a clean hotspot, or null when it is unusable (no slug, or a
	 * position that is not a finite number). Everything else is clamped.
	 */
	function sanitizeHotspot(raw, index) {
		if (!raw || typeof raw !== "object") { return null; }
		var slug = cleanText(raw.slug, 48).toLowerCase().replace(/[^a-z0-9-]/g, "");
		var x = num(raw.x, NaN);
		var y = num(raw.y, NaN);
		if (slug === "" || !isNum(x) || !isNum(y)) { return null; }
		return {
			slug: slug,
			order: isNum(num(raw.order, NaN)) ? num(raw.order, 0) : index || 0,
			enabled: !(raw.enabled === false || raw.enabled === 0 || raw.enabled === "0"),
			kind: KINDS.indexOf(raw.kind) >= 0 ? raw.kind : "other",
			titleEn: cleanText(raw.titleEn, 80),
			titleMn: cleanText(raw.titleMn, 120),
			descriptionMn: cleanText(raw.descriptionMn, 300),
			descriptionEn: cleanText(raw.descriptionEn, 300),
			x: clamp(x, 0, 1),
			y: clamp(y, 0, 1),
			desktop: cleanSet(raw.desktop, 4.5),
			mobile: cleanSet(raw.mobile, 7.5)
		};
	}

	/** Sanitized, de-duplicated by slug, sorted by order. Disabled kept. */
	function normalizeHotspots(list) {
		var out = [];
		var seen = {};
		if (!Array.isArray(list)) { return out; }
		for (var i = 0; i < list.length; i++) {
			var h = sanitizeHotspot(list[i], i);
			if (h && !seen[h.slug]) {
				seen[h.slug] = true;
				out.push(h);
			}
		}
		out.sort(function (a, b) { return a.order - b.order; });
		return out;
	}

	/** Slugs of the selectable hotspots, in configured order. */
	function enabledSlugs(list) {
		var out = [];
		for (var i = 0; i < list.length; i++) {
			if (list[i].enabled) { out.push(list[i].slug); }
		}
		return out;
	}

	/** Next/previous in the enabled sequence, wrapping around. */
	function neighborSlug(order, current, dir) {
		var n = order.length;
		if (n === 0) { return null; }
		var i = order.indexOf(current);
		if (i < 0) { return dir > 0 ? order[0] : order[n - 1]; }
		return order[(i + (dir > 0 ? 1 : -1) + n) % n];
	}

	/**
	 * Distance (px) from each point to its nearest neighbour, used to cap
	 * the touch hit area so crowded hotspots on a phone stay separable.
	 */
	function nearestDistances(points) {
		var out = [];
		for (var i = 0; i < points.length; i++) {
			var best = Infinity;
			for (var j = 0; j < points.length; j++) {
				if (i === j) { continue; }
				var d = Math.hypot(points[i].x - points[j].x, points[i].y - points[j].y);
				if (d < best) { best = d; }
			}
			out.push(best);
		}
		return out;
	}

	/* ------------------------------------------------------------------ */
	/* state machine                                                      */
	/* ------------------------------------------------------------------ */

	function initialState(slug) {
		return slug
			? { phase: PHASE.FOCUSED, slug: slug, from: null, animId: 0, instant: true }
			: { phase: PHASE.OVERVIEW, slug: null, from: null, animId: 0, instant: false };
	}

	function go(state, phase, slug, from) {
		return { phase: phase, slug: slug, from: from, animId: state.animId + 1, instant: false };
	}

	/**
	 * Pure reducer. `order` (enabled slugs, in sequence) is passed with the
	 * action so the reducer never reads stale data.
	 *   { type: "select", slug, order } | { type: "next"|"prev", order }
	 *   { type: "back" } | { type: "animDone", id } | { type: "data", order }
	 * Impossible combinations cannot be represented: there is one `phase`.
	 */
	function reduce(state, action) {
		var P = PHASE;
		var order = action.order || [];
		var phase = state.phase;
		var active = phase === P.FOCUSING || phase === P.FOCUSED || phase === P.TRANSITIONING_ROOM;
		var target;

		switch (action.type) {
			case "select":
				if (order.indexOf(action.slug) < 0) { return state; }
				if (phase === P.OVERVIEW || phase === P.RETURNING) {
					return go(state, P.FOCUSING, action.slug, null);
				}
				if (action.slug === state.slug) { return state; }
				if (phase === P.FOCUSING) {
					return go(state, P.FOCUSING, action.slug, state.slug);
				}
				return go(state, P.TRANSITIONING_ROOM, action.slug, state.slug);

			case "next":
			case "prev":
				if (!active) { return state; }
				target = neighborSlug(order, state.slug, action.type === "next" ? 1 : -1);
				if (target === null || target === state.slug) { return state; }
				return go(state, phase === P.FOCUSING ? P.FOCUSING : P.TRANSITIONING_ROOM, target, state.slug);

			case "back":
				if (!active) { return state; }
				return go(state, P.RETURNING, null, state.slug);

			case "animDone":
				if (action.id !== state.animId) { return state; }
				if (phase === P.FOCUSING || phase === P.TRANSITIONING_ROOM) {
					return { phase: P.FOCUSED, slug: state.slug, from: null, animId: state.animId, instant: false };
				}
				if (phase === P.RETURNING) {
					return { phase: P.OVERVIEW, slug: null, from: null, animId: state.animId, instant: false };
				}
				return state;

			case "data":
				/* the selected hotspot was disabled/removed under us */
				if (active && order.indexOf(state.slug) < 0) {
					return go(state, P.RETURNING, null, state.slug);
				}
				return state;
		}
		return state;
	}

	function isControlsVisible(state) {
		return state.phase === PHASE.FOCUSING || state.phase === PHASE.FOCUSED || state.phase === PHASE.TRANSITIONING_ROOM;
	}

	return {
		ZOOM_MIN: ZOOM_MIN,
		ZOOM_MAX: ZOOM_MAX,
		OFFSET_MAX: OFFSET_MAX,
		DEFAULT_CONTENT_BOUNDS: DEFAULT_CONTENT_BOUNDS,
		DURATION: DURATION,
		PHASE: PHASE,
		KINDS: KINDS,
		clamp: clamp,
		num: num,
		lerp: lerp,
		calculateContainedImageRect: calculateContainedImageRect,
		normalizedPointToImagePoint: normalizedPointToImagePoint,
		imagePointToNormalized: imagePointToNormalized,
		calculateCameraTransform: calculateCameraTransform,
		pointToScreen: pointToScreen,
		screenToNormalized: screenToNormalized,
		getViewportClass: getViewportClass,
		getResponsiveCameraPreset: getResponsiveCameraPreset,
		calculateOverviewCamera: calculateOverviewCamera,
		overviewPanRange: overviewPanRange,
		normalizeBounds: normalizeBounds,
		calculatePointCamera: calculatePointCamera,
		resolveRoomCamera: resolveRoomCamera,
		resolveCamera: resolveCamera,
		interpolateCamera: interpolateCamera,
		easeInOutCubic: easeInOutCubic,
		easeInOutQuint: easeInOutQuint,
		sanitizeHotspot: sanitizeHotspot,
		normalizeHotspots: normalizeHotspots,
		enabledSlugs: enabledSlugs,
		neighborSlug: neighborSlug,
		nearestDistances: nearestDistances,
		initialState: initialState,
		reduce: reduce,
		isControlsVisible: isControlsVisible
	};
});
