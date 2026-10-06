"use strict";
/* Run: node --test tests/floorplan */
const test = require("node:test");
const assert = require("node:assert/strict");
const C = require("../../assets/js/floorplan/core.js");

const IW = 8852;
const IH = 4252;
const near = (a, b, eps) => assert.ok(Math.abs(a - b) <= (eps || 1e-6), `${a} !~ ${b}`);

function layoutFor(w, h) {
	return { width: w, height: h, rect: C.calculateContainedImageRect(w, h, IW, IH) };
}

/* ---------------- geometry ---------------- */

test("contain: centre of a wide stage — vertical letterboxing", () => {
	const r = C.calculateContainedImageRect(1440, 900, IW, IH);
	near(r.width, 1440);
	near(r.height, 1440 * IH / IW);
	near(r.left, 0);
	near(r.top, (900 - r.height) / 2);
	const c = C.normalizedPointToImagePoint(0.5, 0.5, r);
	near(c.x, 720);
	near(c.y, 450);
});

test("contain: very tall stage (phone) — vertical letterboxing, full width", () => {
	const r = C.calculateContainedImageRect(390, 844, IW, IH);
	near(r.width, 390);
	near(r.left, 0);
	assert.ok(r.top > 300);
	const c = C.normalizedPointToImagePoint(0.5, 0.5, r);
	near(c.x, 195);
	near(c.y, 422);
});

test("contain: ultra-wide stage — horizontal letterboxing", () => {
	const r = C.calculateContainedImageRect(3440, 1000, IW, IH);
	near(r.height, 1000);
	near(r.width, 1000 * IW / IH);
	assert.ok(r.left > 500);
	near(r.top, 0);
	near(C.normalizedPointToImagePoint(0, 0, r).x, r.left);
	near(C.normalizedPointToImagePoint(1, 1, r).x, r.left + r.width);
});

test("contain: zero-size container never produces NaN", () => {
	for (const [w, h] of [[0, 0], [0, 500], [500, 0], [-1, 5]]) {
		const r = C.calculateContainedImageRect(w, h, IW, IH);
		assert.deepEqual(r, { left: 0, top: 0, width: 0, height: 0 });
	}
	assert.deepEqual(C.imagePointToNormalized(10, 10, { left: 0, top: 0, width: 0, height: 0 }), { x: 0, y: 0 });
});

test("normalized <-> image point round-trip and clamping", () => {
	const r = C.calculateContainedImageRect(1366, 768, IW, IH);
	const p = C.normalizedPointToImagePoint(0.1574, 0.4328, r);
	const n = C.imagePointToNormalized(p.x, p.y, r);
	near(n.x, 0.1574);
	near(n.y, 0.4328);
	const out = C.imagePointToNormalized(r.left - 50, r.top + r.height + 90, r);
	assert.equal(out.x, 0);
	assert.equal(out.y, 1);
	const raw = C.imagePointToNormalized(r.left - 50, r.top, r, true);
	assert.ok(raw.x < 0);
});

/* ---------------- hotspot alignment across sizes ---------------- */

const SIZES = [[390, 844], [430, 932], [768, 1024], [1024, 768], [1366, 768], [1440, 900], [1920, 1080], [3440, 1000]];

test("the camera puts the focal point exactly on its anchor at every size", () => {
	const hs = { x: 0.1537, y: 0.4157, desktop: { zoom: 4.5 }, mobile: { zoom: 7.5 } };
	for (const [w, h] of SIZES) {
		const layout = layoutFor(w, h);
		const preset = C.getResponsiveCameraPreset(w, h, false);
		const cam = C.resolveRoomCamera(hs, preset);
		const t = C.calculateCameraTransform(cam, layout);
		const s = C.pointToScreen(hs.x, hs.y, t, layout.rect);
		near(s.x, cam.ax * w, 1e-6);
		near(s.y, cam.ay * h, 1e-6);
		assert.ok(s.x > 0 && s.x < w && s.y > 0 && s.y < h, `${w}x${h} dot on screen`);
	}
});

test("overview: a hotspot keeps its place on the drawing when the stage is resized", () => {
	/* non-pannable overviews fit the drawing, so a normalized point sits at
	   the same fraction of the drawing's width at every size */
	const b = C.DEFAULT_CONTENT_BOUNDS;
	const hs = { x: 0.8372, y: 0.4801 };
	let ref = null;
	for (const [w, h] of SIZES) {
		const layout = layoutFor(w, h);
		const preset = C.getResponsiveCameraPreset(w, h, false);
		if (C.overviewPanRange(layout, preset).pannable) { continue; }
		const t = C.calculateCameraTransform(C.calculateOverviewCamera(layout, preset), layout);
		const p = C.pointToScreen(hs.x, hs.y, t, layout.rect);
		const left = C.pointToScreen(b.x0, 0.5, t, layout.rect).x;
		const right = C.pointToScreen(b.x1, 0.5, t, layout.rect).x;
		const frac = (p.x - left) / (right - left);
		if (ref === null) { ref = frac; } else { near(frac, ref, 1e-9); }
	}
	assert.ok(ref !== null);
});

test("overview camera fits the whole drawing inside the stage (desktop / tablet)", () => {
	const b = C.DEFAULT_CONTENT_BOUNDS;
	for (const [w, h] of SIZES) {
		const layout = layoutFor(w, h);
		const preset = C.getResponsiveCameraPreset(w, h, false);
		if (C.overviewPanRange(layout, preset).pannable) { continue; }
		const t = C.calculateCameraTransform(C.calculateOverviewCamera(layout, preset), layout);
		const tl = C.pointToScreen(b.x0, b.y0, t, layout.rect);
		const br = C.pointToScreen(b.x1, b.y1, t, layout.rect);
		assert.ok(tl.x >= -0.5 && tl.y >= -0.5, `${w}x${h} top-left inside`);
		assert.ok(br.x <= w + 0.5 && br.y <= h + 0.5, `${w}x${h} bottom-right inside`);
	}
});

test("phone portrait overview is pannable: the two ends of the drawing both reach the screen edges", () => {
	for (const [w, h] of [[390, 844], [430, 932]]) {
		const layout = layoutFor(w, h);
		const preset = C.getResponsiveCameraPreset(w, h, true);
		const r = C.overviewPanRange(layout, preset);
		assert.ok(r.pannable && r.min < r.max);
		const b = C.DEFAULT_CONTENT_BOUNDS;
		const left = C.calculateCameraTransform(C.calculateOverviewCamera(layout, preset, null, r.min), layout);
		const right = C.calculateCameraTransform(C.calculateOverviewCamera(layout, preset, null, r.max), layout);
		near(C.pointToScreen(b.x0, 0.5, left, layout.rect).x, preset.pad.left, 1e-6);
		near(C.pointToScreen(b.x1, 0.5, right, layout.rect).x, w - preset.pad.right, 1e-6);
		/* out-of-range pan is clamped, never shows blank canvas beyond the drawing */
		const far = C.calculateOverviewCamera(layout, preset, null, -5);
		near(far.fx, r.min);
		/* hotspots are spread far enough apart to tap: nearest pair > 22px */
		const t = C.calculateCameraTransform(C.calculateOverviewCamera(layout, preset, null, r.min), layout);
		const a = C.pointToScreen(0.8014, 0.4735, t, layout.rect);
		const c = C.pointToScreen(0.8372, 0.4801, t, layout.rect);
		assert.ok(Math.hypot(a.x - c.x, a.y - c.y) > 22);
	}
});

test("a drawing that already fits is not pannable (desktop)", () => {
	const layout = layoutFor(1440, 900);
	assert.equal(C.overviewPanRange(layout, C.getResponsiveCameraPreset(1440, 900, false)).pannable, false);
});

test("return-to-overview: interpolation ends exactly on the overview transform", () => {
	const layout = layoutFor(1440, 900);
	const preset = C.getResponsiveCameraPreset(1440, 900, false);
	const room = C.resolveRoomCamera({ x: 0.15, y: 0.41, desktop: { zoom: 4.5 } }, preset);
	const ov = C.calculateOverviewCamera(layout, preset);
	const end = C.interpolateCamera(room, ov, 1, layout, 1.2);
	assert.deepEqual(C.calculateCameraTransform(end, layout), C.calculateCameraTransform(ov, layout));
	const start = C.interpolateCamera(room, ov, 0, layout, 1.2);
	assert.deepEqual(start, room);
});

/* ---------------- screen -> normalized (editor dragging) ---------------- */

test("screenToNormalized inverts pointToScreen under any camera (drag while zoomed)", () => {
	for (const [w, h] of SIZES) {
		const layout = layoutFor(w, h);
		const cams = [
			C.calculateOverviewCamera(layout, C.getResponsiveCameraPreset(w, h, false)),
			{ fx: 0.3, fy: 0.5, z: 6.2, ax: 0.5, ay: 0.4 }
		];
		for (const cam of cams) {
			const t = C.calculateCameraTransform(cam, layout);
			const s = C.pointToScreen(0.2891, 0.6012, t, layout.rect);
			const n = C.screenToNormalized(s.x, s.y, t, layout.rect);
			near(n.x, 0.2891, 1e-9);
			near(n.y, 0.6012, 1e-9);
		}
	}
	const t = C.calculateCameraTransform({ fx: 0.5, fy: 0.5, z: 1, ax: 0.5, ay: 0.5 }, layoutFor(1000, 600));
	const out = C.screenToNormalized(-5000, 99999, t, layoutFor(1000, 600).rect);
	assert.equal(out.x, 0);
	assert.equal(out.y, 1);
});

/* ---------------- presets ---------------- */

test("viewport classes", () => {
	assert.equal(C.getViewportClass(390, 844, true), "mobilePortrait");
	assert.equal(C.getViewportClass(430, 932, true), "mobilePortrait");
	assert.equal(C.getViewportClass(768, 1024, true), "tabletPortrait");
	assert.equal(C.getViewportClass(1024, 768, true), "tabletLandscape");
	assert.equal(C.getViewportClass(844, 390, true), "mobileLandscape");
	assert.equal(C.getViewportClass(1366, 768, false), "desktop");
	assert.equal(C.getViewportClass(1920, 1080, false), "largeDesktop");
	assert.equal(C.getViewportClass(1024, 768, false), "desktop");
});

test("mobile uses the mobile camera settings, not desktop * multiplier", () => {
	const hs = { x: 0.5, y: 0.5, desktop: { zoom: 4 }, mobile: { zoom: 9 } };
	const m = C.resolveRoomCamera(hs, C.getResponsiveCameraPreset(390, 844, true));
	const d = C.resolveRoomCamera(hs, C.getResponsiveCameraPreset(1440, 900, false));
	near(m.z, 9);
	near(d.z, 4);
	assert.ok(m.ay < 0.5, "phone leaves room for the card below the dot");
});

test("camera settings are clamped: zoom=100 / offset=9 cannot break the page", () => {
	const preset = C.getResponsiveCameraPreset(1440, 900, false);
	const cam = C.resolveRoomCamera({ x: 0.3, y: 0.3, desktop: { zoom: 100, offsetX: 9, offsetY: -9, focusX: 5 } }, preset);
	assert.equal(cam.z, C.ZOOM_MAX);
	assert.ok(cam.ax <= 0.92 && cam.ay >= 0.08);
	assert.equal(cam.fx, 1);
	const bad = C.resolveRoomCamera({ x: 0.3, y: 0.3, desktop: { zoom: "abc", offsetX: NaN } }, preset);
	assert.ok(Number.isFinite(bad.z) && Number.isFinite(bad.ax));
});

test("focus override moves the camera target away from the dot", () => {
	const preset = C.getResponsiveCameraPreset(1440, 900, false);
	const cam = C.resolveRoomCamera({ x: 0.3, y: 0.3, desktop: { zoom: 4, focusX: 0.35, focusY: null } }, preset);
	near(cam.fx, 0.35);
	near(cam.fy, 0.3);
});

/* ---------------- animation path ---------------- */

test("interpolation is finite, monotonic in zoom for zoom-in, and hits both ends", () => {
	const layout = layoutFor(1440, 900);
	const preset = C.getResponsiveCameraPreset(1440, 900, false);
	const ov = C.calculateOverviewCamera(layout, preset);
	const room = C.resolveRoomCamera({ x: 0.1537, y: 0.4157, desktop: { zoom: 4.5 } }, preset);
	let prev = ov.z;
	for (let i = 1; i <= 50; i++) {
		const c = C.interpolateCamera(ov, room, i / 50, layout, 1.25);
		for (const k of ["fx", "fy", "z", "ax", "ay"]) { assert.ok(Number.isFinite(c[k]), k); }
		assert.ok(c.z >= prev - 1e-9, "zoom never goes back while zooming in");
		prev = c.z;
	}
	near(prev, room.z, 1e-9);
});

test("room to room: nearby rooms barely change scale; distant ones never zoom below overview", () => {
	const layout = layoutFor(1440, 900);
	const preset = C.getResponsiveCameraPreset(1440, 900, false);
	const ov = C.calculateOverviewCamera(layout, preset);
	const a = C.resolveRoomCamera({ x: 0.1537, y: 0.4157, desktop: { zoom: 4.5 } }, preset);
	const near1 = C.resolveRoomCamera({ x: 0.2372, y: 0.3786, desktop: { zoom: 4.5 } }, preset);
	const far = C.resolveRoomCamera({ x: 0.8609, y: 0.4124, desktop: { zoom: 4.5 } }, preset);
	let minNear = Infinity;
	let minFar = Infinity;
	for (let i = 1; i < 100; i++) {
		minNear = Math.min(minNear, C.interpolateCamera(a, near1, i / 100, layout, 0.9).z);
		minFar = Math.min(minFar, C.interpolateCamera(a, far, i / 100, layout, 0.9).z);
	}
	assert.ok(minNear > 4.5 * 0.6, `short hop stays zoomed in (min z ${minNear})`);
	assert.ok(minFar >= ov.z * 0.99, `long hop never zooms out beyond the overview (min z ${minFar})`);
});

test("identical cameras interpolate without NaN (zero distance, zero zoom change)", () => {
	const layout = layoutFor(1440, 900);
	const c = { fx: 0.3, fy: 0.4, z: 4, ax: 0.5, ay: 0.45 };
	const m = C.interpolateCamera(c, c, 0.5, layout, 1.2);
	for (const k of ["fx", "fy", "z", "ax", "ay"]) { near(m[k], c[k], 1e-9); }
});

test("easing endpoints", () => {
	for (const f of [C.easeInOutCubic, C.easeInOutQuint]) {
		near(f(0), 0);
		near(f(1), 1);
		near(f(0.5), 0.5);
		near(f(-3), 0);
		near(f(9), 1);
	}
});

/* ---------------- hotspot data ---------------- */

test("sanitize: clamps coordinates and drops unusable hotspots", () => {
	const ok = C.sanitizeHotspot({ slug: "Locker-Room", x: 1.4, y: -2, titleEn: "  Locker  ", desktop: { zoom: 100 } }, 0);
	assert.equal(ok.slug, "locker-room");
	assert.equal(ok.x, 1);
	assert.equal(ok.y, 0);
	assert.equal(ok.titleEn, "Locker");
	assert.equal(ok.desktop.zoom, C.ZOOM_MAX);
	assert.equal(ok.descriptionMn, "", "missing description is fine");
	assert.equal(C.sanitizeHotspot({ slug: "a", x: "nope", y: 0.5 }, 0), null);
	assert.equal(C.sanitizeHotspot({ slug: "!!", x: 0.5, y: 0.5 }, 0), null);
	assert.equal(C.sanitizeHotspot(null, 0), null);
});

test("sanitize: extremely long titles are bounded; Cyrillic survives", () => {
	const h = C.sanitizeHotspot({ slug: "a", x: 0.1, y: 0.1, titleEn: "x".repeat(500), titleMn: "Хувцас солих өрөө" }, 0);
	assert.equal(h.titleEn.length, 80);
	assert.equal(h.titleMn, "Хувцас солих өрөө");
});

test("normalizeHotspots: dedupes slugs, sorts by order, filters bad rows", () => {
	const list = C.normalizeHotspots([
		{ slug: "b", order: 2, x: 0.1, y: 0.1 },
		{ slug: "a", order: 1, x: 0.2, y: 0.2 },
		{ slug: "a", order: 9, x: 0.3, y: 0.3 },
		{ slug: "bad", x: NaN, y: 0 }
	]);
	assert.deepEqual(list.map((h) => h.slug), ["a", "b"]);
	assert.deepEqual(C.normalizeHotspots(null), []);
});

/* ---------------- navigation ---------------- */

test("next/previous wrap around and skip disabled hotspots", () => {
	const list = C.normalizeHotspots([
		{ slug: "a", order: 1, x: 0.1, y: 0.1 },
		{ slug: "b", order: 2, x: 0.1, y: 0.1, enabled: false },
		{ slug: "c", order: 3, x: 0.1, y: 0.1 }
	]);
	const order = C.enabledSlugs(list);
	assert.deepEqual(order, ["a", "c"]);
	assert.equal(C.neighborSlug(order, "a", 1), "c");
	assert.equal(C.neighborSlug(order, "c", 1), "a");
	assert.equal(C.neighborSlug(order, "a", -1), "c");
	assert.equal(C.neighborSlug(order, "gone", 1), "a");
	assert.equal(C.neighborSlug(order, "gone", -1), "c");
	assert.equal(C.neighborSlug([], "a", 1), null);
});

/* ---------------- state machine ---------------- */

const ORDER = ["a", "b", "c", "d"];
const act = (s, type, extra) => C.reduce(s, Object.assign({ type, order: ORDER }, extra || {}));

test("state: overview -> focusing -> focused -> transitioning -> returning -> overview", () => {
	let s = C.initialState(null);
	assert.equal(s.phase, "OVERVIEW");
	s = act(s, "select", { slug: "a" });
	assert.equal(s.phase, "FOCUSING");
	s = act(s, "animDone", { id: s.animId });
	assert.equal(s.phase, "FOCUSED");
	s = act(s, "next");
	assert.equal(s.phase, "TRANSITIONING_ROOM");
	assert.equal(s.slug, "b");
	s = act(s, "animDone", { id: s.animId });
	assert.equal(s.phase, "FOCUSED");
	s = act(s, "prev");
	assert.equal(s.slug, "a");
	s = act(s, "animDone", { id: s.animId });
	s = act(s, "back");
	assert.equal(s.phase, "RETURNING");
	assert.equal(s.slug, null);
	s = act(s, "animDone", { id: s.animId });
	assert.equal(s.phase, "OVERVIEW");
});

test("state: double click on the same hotspot is a no-op; stale animDone ignored", () => {
	let s = act(C.initialState(null), "select", { slug: "a" });
	const id = s.animId;
	const again = act(s, "select", { slug: "a" });
	assert.equal(again, s);
	s = act(s, "select", { slug: "c" });
	assert.equal(s.phase, "FOCUSING");
	assert.equal(s.slug, "c");
	assert.notEqual(s.animId, id);
	const stale = act(s, "animDone", { id });
	assert.equal(stale, s, "the first animation finishing late must not complete the second");
});

test("state: rapid next presses retarget instead of corrupting state", () => {
	let s = act(act(C.initialState(null), "select", { slug: "a" }), "animDone", { id: 1 });
	s = act(s, "next");
	s = act(s, "next");
	s = act(s, "next");
	assert.equal(s.phase, "TRANSITIONING_ROOM");
	assert.equal(s.slug, "d");
	s = act(s, "next");
	assert.equal(s.slug, "a", "wraps");
});

test("state: back during focusing returns; back in overview/returning is a no-op", () => {
	let s = act(C.initialState(null), "select", { slug: "b" });
	s = act(s, "back");
	assert.equal(s.phase, "RETURNING");
	assert.equal(act(s, "back"), s);
	assert.equal(act(s, "next"), s, "no room navigation while returning");
	const ov = C.initialState(null);
	assert.equal(act(ov, "back"), ov);
	assert.equal(act(ov, "next"), ov);
});

test("state: selecting while returning re-focuses", () => {
	let s = act(act(C.initialState(null), "select", { slug: "b" }), "back");
	s = act(s, "select", { slug: "c" });
	assert.equal(s.phase, "FOCUSING");
	assert.equal(s.slug, "c");
});

test("state: unknown / disabled slug is ignored", () => {
	const s = C.initialState(null);
	assert.equal(act(s, "select", { slug: "zzz" }), s);
});

test("state: selected hotspot disabled/deleted by a data refresh -> back to overview", () => {
	let s = act(act(C.initialState(null), "select", { slug: "b" }), "animDone", { id: 1 });
	const gone = C.reduce(s, { type: "data", order: ["a", "c", "d"] });
	assert.equal(gone.phase, "RETURNING");
	assert.equal(gone.slug, null);
	assert.equal(C.reduce(s, { type: "data", order: ORDER }), s);
});

test("state: deep link starts focused without an animation flag problem", () => {
	const s = C.initialState("kitchen");
	assert.equal(s.phase, "FOCUSED");
	assert.equal(s.slug, "kitchen");
	assert.equal(s.instant, true);
});

/* ---------------- floors ---------------- */

test("bounds: array / object / garbage all normalize", () => {
	assert.deepEqual(C.normalizeBounds([0.1, 0.2, 0.9, 0.8]), { x0: 0.1, y0: 0.2, x1: 0.9, y1: 0.8 });
	assert.deepEqual(C.normalizeBounds({ x0: 0, y0: 0, x1: 1, y1: 1 }), { x0: 0, y0: 0, x1: 1, y1: 1 });
	assert.equal(C.normalizeBounds([0.9, 0.2, 0.1, 0.8]), C.DEFAULT_CONTENT_BOUNDS, "inverted -> default");
	assert.equal(C.normalizeBounds(null), C.DEFAULT_CONTENT_BOUNDS);
	assert.equal(C.normalizeBounds(["a", 0, 1, 1]), C.DEFAULT_CONTENT_BOUNDS);
});

test("phone overview: a wide floor (20th) pans, a near-square floor (21st) fits whole", () => {
	const w = 390, h = 668;
	const preset = C.getResponsiveCameraPreset(w, h, true);
	const f20 = { width: w, height: h, rect: C.calculateContainedImageRect(w, h, 5288, 3012) };
	const f21 = { width: w, height: h, rect: C.calculateContainedImageRect(w, h, 3296, 2584) };
	const b20 = C.normalizeBounds([0.03707, 0.06507, 0.96293, 0.93493]);
	const b21 = C.normalizeBounds([0.03701, 0.04721, 0.96299, 0.95279]);
	assert.equal(C.overviewPanRange(f20, preset, b20).pannable, true);
	assert.equal(C.overviewPanRange(f21, preset, b21).pannable, false);
});

test("stair camera: centred on the stairs, zoomed relative to the overview", () => {
	const layout = { width: 1440, height: 780, rect: C.calculateContainedImageRect(1440, 780, 5288, 3012) };
	const preset = C.getResponsiveCameraPreset(1440, 780, false);
	const b = C.normalizeBounds([0.03707, 0.06507, 0.96293, 0.93493]);
	const ov = C.calculateOverviewCamera(layout, preset, b);
	const st = C.calculatePointCamera(layout, preset, b, 0.59284, 0.75765, 3.2);
	near(st.z, ov.z * 3.2, 1e-9);
	const t = C.calculateCameraTransform(st, layout);
	const p = C.pointToScreen(0.59284, 0.75765, t, layout.rect);
	near(p.x, 720, 1e-6);
	near(p.y, 390, 1e-6);
	// interpolating stair -> overview lands exactly on the overview
	assert.deepEqual(C.interpolateCamera(st, ov, 1, layout, 1.2), ov);
});
