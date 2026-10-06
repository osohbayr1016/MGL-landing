// Browser interaction tests for the interactive floor plan.
//
//   npm i playwright-core            (anywhere; point PW_DIR at that folder if it is not this one)
//   php tests/floorplan/e2e/render.php
//   node tests/floorplan/e2e/serve.mjs &            # http://127.0.0.1:8099/about
//   node tests/floorplan/e2e/e2e.mjs
//
// Env: BASE_URL (default http://127.0.0.1:8099/about), BROWSER_PATH (a Chromium/Edge executable;
// default: system Edge on Windows), PW_DIR (folder containing node_modules/playwright-core).
import { createRequire } from 'node:module';
import path from 'node:path';

const require = createRequire(path.join(process.env.PW_DIR || process.cwd(), 'noop.js'));
const { chromium } = require('playwright-core');

const BASE = process.env.BASE_URL || 'http://127.0.0.1:8099/about';
const EXE = process.env.BROWSER_PATH || 'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe';

let failed = 0;
let total = 0;
function ok(cond, msg) {
  total++;
  if (!cond) { failed++; console.log('  FAIL ' + msg); } else { console.log('  ok   ' + msg); }
}

const browser = await chromium.launch({ executablePath: EXE, headless: true });

async function open(opts = {}, url = BASE) {
  const ctx = await browser.newContext({ viewport: opts.viewport || { width: 1440, height: 900 }, deviceScaleFactor: opts.dpr || 1, reducedMotion: opts.reducedMotion || 'no-preference', hasTouch: !!opts.touch, isMobile: !!opts.mobile });
  const page = await ctx.newPage();
  const errors = [];
  page.on('pageerror', (e) => errors.push(e.message));
  await page.route(/fonts\.(googleapis|gstatic)\.com/, (r) => r.abort());
  await page.goto(url, { waitUntil: 'load' });
  await page.waitForSelector('.fp-hero[data-fp-ready="1"]');
  await page.waitForFunction(() => document.querySelector('.fp-floor.is-active').classList.contains('is-ready'));
  return { ctx, page, errors };
}

const phase = (page) => page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.getState().phase);
const slug = (page) => page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.getState().slug);
const waitPhase = (page, p, t = 4000) => page.waitForFunction((x) => document.querySelector('.fp-hero').__fpViewer.getState().phase === x, p, { timeout: t });
const click = (page, s) => page.$eval(`[data-slug="${s}"]`, (e) => e.click());
const visible = (page, sel) => page.$eval(sel, (e) => { const c = getComputedStyle(e); return c.visibility !== 'hidden' && parseFloat(c.opacity) > 0.5; });

// ------------------------------------------------------------------
console.log('overview');
{
  const { ctx, page, errors } = await open();
  ok((await page.$$('.fp-floor.is-active .fp-spot')).length === 11, '20th floor: 11 hotspots');
  ok((await page.$$('.fp-spot')).length === 16, '16 hotspots across both floors');
  ok((await phase(page)) === 'OVERVIEW', 'starts in OVERVIEW');
  ok(!(await visible(page, '[data-fp-info]')), 'info card hidden in overview');
  ok(!(await visible(page, '.fp-nav')), 'arrows hidden in overview');
  ok((await page.textContent('[data-fp-title]')) === '20-Р ДАВХАР', 'floor title shown');
  ok((await page.textContent('[data-fp-title-next]')) === '21', 'title offers the 21st floor');
  ok((await page.$$('.fp-floor.is-active .fp-link')).length === 1, 'staircase marker on the plan');
  ok(await page.$eval('.fp-spot', (e) => e.tagName === 'BUTTON' && /—/.test(e.getAttribute('aria-label'))), 'hotspots are buttons with "EN — MN" labels');
  ok((await page.$$('.fp-sr-only[data-fp-fallback]')).length === 0, 'no-JS fallback list removed once enhanced');
  ok(await page.$eval('#widhas1166', (e) => e.classList.contains('fp-hero')), 'hero kept the section anchor id');
  ok((await page.$$('.pageHeader')).length === 0, 'old photo banner replaced');
  ok(errors.length === 0, 'no page errors ' + errors.join('|'));
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('hotspot alignment with the drawing at every size');
for (const [w, h, touch] of [[390, 844, true], [430, 932, true], [768, 1024, true], [1024, 768, true], [1366, 768], [1440, 900], [1920, 1080], [3440, 1000]]) {
  const { ctx, page } = await open({ viewport: { width: w, height: h }, touch, mobile: !!touch });
  const worst = await page.evaluate(() => {
    const v = document.querySelector('.fp-hero').__fpViewer;
    const wr = document.querySelector('.fp-floor.is-active .fp-world img').getBoundingClientRect();
    let max = 0;
    for (const hs of v.hotspots) {
      const r = v.getNode(hs.slug).getBoundingClientRect();
      const cx = r.left + r.width / 2, cy = r.top + r.height / 2;
      const nx = (cx - wr.left) / wr.width, ny = (cy - wr.top) / wr.height;
      max = Math.max(max, Math.abs(nx - hs.x) * wr.width, Math.abs(ny - hs.y) * wr.height);
    }
    return max;
  });
  ok(worst < 1.0, `${w}x${h}: every dot within ${worst.toFixed(2)}px of its normalized point on the rendered image`);
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('focus / next / previous / back (click + keyboard)');
{
  const { ctx, page } = await open();
  await click(page, 'locker-room');
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'locker-room', 'locker-room focused');
  ok((await page.textContent('[data-fp-en]')) === 'Locker Room', 'English title');
  ok((await page.textContent('[data-fp-mn]')) === 'Хувцас солих өрөө', 'Mongolian title');
  ok((await page.textContent('[data-fp-desc]')).includes('хадгалах'), 'description');
  ok(await visible(page, '[data-fp-info]'), 'info card visible');
  ok(await visible(page, '.fp-nav'), 'arrows visible');
  ok(!(await visible(page, '[data-slug="main-entrance"]')), 'other hotspots hidden while focused');
  ok(new URL(page.url()).searchParams.get('area') === 'locker-room', 'URL has ?area=locker-room');

  // the focused dot sits on the preset anchor
  const d = await page.evaluate(() => {
    const v = document.querySelector('.fp-hero').__fpViewer;
    const r = v.getNode('locker-room').getBoundingClientRect(), s = document.querySelector('.fp-stage').getBoundingClientRect();
    return { x: (r.left + r.width / 2 - s.left) / s.width, y: (r.top + r.height / 2 - s.top) / s.height, ax: v.preset.ax, ay: v.preset.ay };
  });
  ok(Math.abs(d.x - d.ax) < 0.003 && Math.abs(d.y - d.ay) < 0.003, `focused dot on the camera anchor (${d.x.toFixed(3)},${d.y.toFixed(3)})`);

  await page.keyboard.press('ArrowRight');
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'main-entrance', 'ArrowRight -> next');
  await page.keyboard.press('ArrowLeft');
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'locker-room', 'ArrowLeft -> previous');
  await page.keyboard.press('ArrowLeft');
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'terrace-west', 'previous wraps to the last');
  await page.$eval('[data-fp-next]', (e) => e.click());
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'locker-room', 'next button wraps to the first');
  await page.keyboard.press('ArrowDown');
  await waitPhase(page, 'OVERVIEW');
  ok(!new URL(page.url()).searchParams.has('area'), 'URL cleared after returning');
  ok(!(await visible(page, '[data-fp-info]')) && !(await visible(page, '.fp-nav')), 'details + arrows hidden after returning');
  ok((await page.$$eval('.fp-spot.is-off', (n) => n.length)) === 0, 'all hotspots back');
  await click(page, 'lounge');
  await waitPhase(page, 'FOCUSED');
  await page.keyboard.press('Escape');
  await waitPhase(page, 'OVERVIEW');
  ok(true, 'Escape -> overview');
  ok(await page.evaluate(() => document.activeElement && document.activeElement.getAttribute('data-slug') === 'lounge'), 'focus returned to the lounge hotspot');
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('rapid / interrupted interaction');
{
  const { ctx, page } = await open();
  await click(page, 'locker-room');
  await page.waitForTimeout(150);
  await click(page, 'locker-room'); // double click on the same hotspot while zooming
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'locker-room', 'double click is harmless');

  for (let i = 0; i < 5; i++) { await page.keyboard.press('ArrowRight'); }
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'management-team', 'five quick ArrowRight presses land 5 rooms on');

  await page.keyboard.press('ArrowDown');
  await page.waitForTimeout(120);
  await click(page, 'meeting-room'); // select while returning
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'meeting-room', 'selecting while returning re-focuses');

  await page.keyboard.press('ArrowDown');
  await waitPhase(page, 'OVERVIEW');
  await click(page, 'recreation-room');
  await page.waitForTimeout(200);
  await page.keyboard.press('ArrowDown'); // back before the focus animation completes
  await waitPhase(page, 'OVERVIEW');
  ok((await slug(page)) === null, 'Down before focus completes returns to overview');
  ok(!(await page.evaluate(() => document.querySelector('.fp-stage').classList.contains('is-moving'))), 'no stuck "moving" state');

  // typing in an input must not steal the arrow keys
  await click(page, 'lounge');
  await waitPhase(page, 'FOCUSED');
  await page.$eval('#e2e-input', (e) => e.focus());
  await page.keyboard.press('ArrowRight');
  await page.keyboard.press('Escape');
  await page.waitForTimeout(300);
  ok((await slug(page)) === 'lounge' && (await phase(page)) === 'FOCUSED', 'keys ignored while typing in an input');
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('resize / rotation while animating');
{
  const { ctx, page } = await open();
  await click(page, 'civil-engineering-team');
  await page.waitForTimeout(250);
  await page.setViewportSize({ width: 390, height: 844 });
  await waitPhase(page, 'FOCUSED', 5000);
  const d = await page.evaluate(() => {
    const v = document.querySelector('.fp-hero').__fpViewer;
    const r = v.getNode('civil-engineering-team').getBoundingClientRect(), s = document.querySelector('.fp-stage').getBoundingClientRect();
    return { x: (r.left + r.width / 2 - s.left) / s.width, y: (r.top + r.height / 2 - s.top) / s.height, ax: v.preset.ax, ay: v.preset.ay, name: v.preset.name };
  });
  ok(d.name === 'mobilePortrait' && Math.abs(d.x - d.ax) < 0.003 && Math.abs(d.y - d.ay) < 0.003, `after resizing mid-flight the dot is on the phone anchor (${d.name})`);
  await page.setViewportSize({ width: 844, height: 390 });
  await page.waitForTimeout(400);
  const d2 = await page.evaluate(() => { const v = document.querySelector('.fp-hero').__fpViewer; return { name: v.preset.name, layout: document.querySelector('.fp-stage').getAttribute('data-fp-layout') }; });
  ok(d2.name === 'mobileLandscape' && d2.layout === 'short', 'rotating to landscape switches to the landscape layout');
  await page.keyboard.press('ArrowRight');
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'meeting-room', 'navigation still works after rotation');
  await page.setViewportSize({ width: 1440, height: 900 });
  await page.keyboard.press('ArrowDown');
  await waitPhase(page, 'OVERVIEW');
  ok(true, 'back to overview after resizing again');
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('deep link, reduced motion, data refresh');
{
  const { ctx, page } = await open({}, BASE + '?area=kitchen');
  ok((await phase(page)) === 'FOCUSED' && (await slug(page)) === 'kitchen', '?area=kitchen opens focused');
  ok((await page.textContent('[data-fp-title]')) === '21-Р ДАВХАР' && (await page.getAttribute('.fp-floor.is-active', 'data-fp-floor')) === 'office-21f', '...on the 21st floor, where the kitchen is');
  ok((await page.textContent('[data-fp-en]')) === 'Kitchen', 'info filled on deep link');
  await page.keyboard.press('Escape');
  await waitPhase(page, 'OVERVIEW');
  await ctx.close();

  const bad = await open({}, BASE + '?area=does-not-exist');
  ok((await phase(bad.page)) === 'OVERVIEW', 'unknown ?area= ignored');
  await bad.ctx.close();

  const rm = await open({ reducedMotion: 'reduce' });
  await click(rm.page, 'showroom');
  await rm.page.waitForFunction(() => document.querySelector('.fp-hero').__fpViewer.getState().phase === 'FOCUSED', null, { timeout: 500 });
  ok(true, 'reduced motion: focus completes immediately');
  await rm.ctx.close();

  const dr = await open();
  await click(dr.page, 'terrace-west');
  await waitPhase(dr.page, 'FOCUSED');
  await dr.page.evaluate(() => {
    const v = document.querySelector('.fp-hero').__fpViewer;
    v.setData(v.hotspots.map((h) => Object.assign({}, h, { enabled: h.slug !== 'terrace-west' })));
  });
  await waitPhase(dr.page, 'OVERVIEW');
  ok(true, 'selected hotspot disabled by refreshed data -> back to overview');
  ok((await dr.page.$$('.fp-floor.is-active .fp-spot')).length === 10, 'disabled hotspot is no longer drawn');
  await dr.page.evaluate(() => { const v = document.querySelector('.fp-hero').__fpViewer; v.setData(v.hotspots.slice(0, 2)); });
  await dr.page.keyboard.press('ArrowRight');
  ok((await phase(dr.page)) === 'OVERVIEW', 'arrow keys do nothing in the overview');
  await dr.ctx.close();
}

// ------------------------------------------------------------------
console.log('pannable phone overview');
{
  const { ctx, page } = await open({ viewport: { width: 390, height: 844 }, touch: true, mobile: true, dpr: 2 });

  // overview: wider than the screen, can be panned
  const o = await page.evaluate(() => { const v = document.querySelector('.fp-hero').__fpViewer; return { pan: v.panRange, cls: document.querySelector('.fp-floor.is-active').classList.contains('is-pannable'), fx: v.cam.fx }; });
  ok(o.pan.pannable && o.cls, 'phone portrait overview is pannable');
  const visibleCount = () => page.evaluate(() => { const v = document.querySelector('.fp-hero').__fpViewer; const w = innerWidth; return v.hotspots.filter((h) => { const r = v.getNode(h.slug).getBoundingClientRect(); return r.left + r.width / 2 > 0 && r.left + r.width / 2 < w; }).length; });
  const v0 = await visibleCount();
  await page.mouse.move(330, 600); await page.mouse.down(); await page.mouse.move(60, 600, { steps: 12 }); await page.mouse.up();
  const fx1 = await page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.cam.fx);
  ok(fx1 > o.fx + 0.05, `dragging pans the overview (fx ${o.fx.toFixed(3)} -> ${fx1.toFixed(3)})`);
  ok((await phase(page)) === 'OVERVIEW', 'a pan that starts on a hotspot does not select it');
  ok(v0 < 11 && v0 >= 5, `${v0} of the 11 hotspots on the 20th visible at once, the rest reachable by panning`);

  // far room, then back: the overview is panned so that room is on screen
  await page.keyboard.press('ArrowDown');
  await waitPhase(page, 'OVERVIEW');
  await page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.panX = null);
  await click(page, 'terrace-west');
  await waitPhase(page, 'FOCUSED');
  await page.keyboard.press('ArrowDown');
  await waitPhase(page, 'OVERVIEW');
  const on = await page.evaluate(() => { const r = document.querySelector('.fp-hero').__fpViewer.getNode('terrace-west').getBoundingClientRect(); return r.left + r.width / 2 > 0 && r.left + r.width / 2 < innerWidth; });
  ok(on, 'returning from a far room re-centres the pannable overview on it');
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('free exploration: wheel, drag, momentum, double-click');
{
  const { ctx, page } = await open();
  const cam = () => page.evaluate(() => { const v = document.querySelector('.fp-hero').__fpViewer; return { z: v.cam.z, fx: v.cam.fx, fy: v.cam.fy, free: !!v.freeCam, zMin: v.zMin, cls: document.querySelector('.fp-floor.is-active').className }; });
  const c0 = await cam();
  ok(!(await page.$eval('[data-fp-hint]', (e) => e.classList.contains('is-gone'))), 'hint shown before the first gesture');

  // wheel up over the lounge zooms in at the cursor, the page does not scroll
  // whole pixels: browsers report the cursor in integer coordinates
  const lounge = await page.$eval('[data-slug="lounge"]', (e) => { const r = e.getBoundingClientRect(); return { x: Math.round(r.left + r.width / 2), y: Math.round(r.top + r.height / 2) }; });
  const planAt = (x, y) => page.evaluate(([x, y]) => document.querySelector('.fp-hero').__fpViewer.clientToNormalized(x, y), [x, y]);
  const under0 = await planAt(lounge.x, lounge.y);
  await page.mouse.move(lounge.x, lounge.y);
  for (let i = 0; i < 5; i++) { await page.mouse.wheel(0, -120); await page.waitForTimeout(40); }
  await page.waitForTimeout(700);
  const c1 = await cam();
  const under1 = await planAt(lounge.x, lounge.y);
  ok(c1.z > c0.z * 2, `wheel zooms in (${c0.z.toFixed(2)} -> ${c1.z.toFixed(2)})`);
  ok(Math.abs(under1.x - under0.x) < 2e-5 && Math.abs(under1.y - under0.y) < 2e-5, 'the plan point under the cursor stays under the cursor');
  ok((await page.evaluate(() => scrollY)) === 0, 'the page did not scroll while zooming');
  ok(await page.$eval('[data-fp-hint]', (e) => e.classList.contains('is-gone')), 'hint hidden after the first gesture');
  ok(c1.cls.includes('is-close') && (await visible(page, '[data-slug="lounge"] .fp-spot-name')), 'zoomed in close: room names appear next to the dots');
  ok((await page.textContent('[data-slug="lounge"] .fp-spot-name')).includes('Лаунж'), 'the name shows English + Mongolian');
  ok((await phase(page)) === 'OVERVIEW' && !new URL(page.url()).searchParams.has('area'), 'exploring does not open a room');

  // drag moves the plan, momentum carries it a little further after release
  await page.mouse.move(900, 500);
  await page.mouse.down();
  for (let i = 1; i <= 10; i++) { await page.mouse.move(900 - i * 25, 500 - i * 10); await page.waitForTimeout(12); }
  await page.mouse.up();
  const r0 = await cam();
  await page.waitForTimeout(250);
  const r1 = await cam();
  ok(r0.fx > c1.fx, 'dragging left moves the view right');
  ok(r1.fx > r0.fx + 1e-4, 'momentum keeps it gliding after release');
  await page.waitForTimeout(900);

  // click a dot while exploring opens the room from right there
  await click(page, 'lounge');
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'lounge', 'clicking a dot while exploring opens it');

  // dragging inside a room releases you into exploration, without a jump
  const inRoom = await cam();
  await page.mouse.move(700, 400);
  await page.mouse.down();
  await page.mouse.move(650, 380, { steps: 5 });
  await page.mouse.up();
  await page.waitForTimeout(200);
  const out = await cam();
  ok((await phase(page)) === 'OVERVIEW' && out.free, 'dragging in a room switches to free exploration');
  ok(!(await visible(page, '[data-fp-info]')) && !new URL(page.url()).searchParams.has('area'), 'room card closed, ?area= removed');
  ok(Math.abs(out.z - inRoom.z) / inRoom.z < 0.05, 'the camera stays where it was (no jump back to the overview)');

  // zoom all the way out: snaps exactly to the overview, then the wheel scrolls the page
  for (let i = 0; i < 25; i++) { await page.mouse.wheel(0, 120); await page.waitForTimeout(30); if (!(await cam()).free && i > 3) { break; } }
  await page.waitForTimeout(800);
  const back = await cam();
  ok(!back.free && Math.abs(back.z - back.zMin) < 1e-6, 'zooming out lands exactly on the overview');
  const y0 = await page.evaluate(() => scrollY);
  await page.mouse.wheel(0, 300);
  await page.waitForTimeout(400);
  ok((await page.evaluate(() => scrollY)) > y0, 'fully zoomed out, the wheel scrolls the page (never trapped)');
  await page.evaluate(() => scrollTo(0, 0));
  await page.waitForTimeout(200);

  // double-click zooms in there
  await page.mouse.dblclick(720, 520);
  await page.waitForTimeout(800);
  ok((await cam()).z > back.z * 1.8, 'double-click zooms in');
  await ctx.close();
}
{
  // phone: pinch + one-finger drag once zoomed; vertical swipes scroll at the overview
  const { ctx, page } = await open({ viewport: { width: 390, height: 844 }, touch: true, mobile: true, dpr: 2 });
  const ta = () => page.$eval('.fp-floor.is-active', (e) => getComputedStyle(e).touchAction);
  ok((await ta()) === 'pan-y', 'phone overview: touch-action pan-y (vertical swipes scroll the page)');
  const fire = (type, id, x, y) => page.evaluate(([t, i, x, y]) => {
    document.querySelector('.fp-floor.is-active').dispatchEvent(new PointerEvent(t, { pointerType: 'touch', pointerId: i, clientX: x, clientY: y, bubbles: true, isPrimary: i === 1 }));
  }, [type, id, x, y]);
  const z0 = await page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.cam.z);
  await fire('pointerdown', 1, 160, 500);
  await fire('pointerdown', 2, 230, 500);
  for (let i = 1; i <= 8; i++) { await fire('pointermove', 1, 160 - i * 10, 500); await fire('pointermove', 2, 230 + i * 10, 500); }
  await fire('pointerup', 2, 310, 500);
  await fire('pointerup', 1, 80, 500);
  await page.waitForTimeout(600);
  const z1 = await page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.cam.z);
  ok(z1 > z0 * 1.8, `pinch zooms (${z0.toFixed(2)} -> ${z1.toFixed(2)})`);
  ok((await ta()) === 'none', 'zoomed in: touch-action none (one finger moves the plan)');
  const f0 = await page.evaluate(() => { const c = document.querySelector('.fp-hero').__fpViewer.cam; return { fx: c.fx, fy: c.fy }; });
  await fire('pointerdown', 3, 200, 520);
  for (let i = 1; i <= 6; i++) { await fire('pointermove', 3, 200 - i * 8, 520 - i * 12); }
  await fire('pointerup', 3, 152, 448);
  await page.waitForTimeout(500);
  const f1 = await page.evaluate(() => { const c = document.querySelector('.fp-hero').__fpViewer.cam; return { fx: c.fx, fy: c.fy }; });
  ok(f1.fx > f0.fx && f1.fy > f0.fy, 'one-finger drag moves the zoomed plan in both directions');
  // double-tap on empty plan zooms in further
  await fire('pointerdown', 4, 200, 600); await fire('pointerup', 4, 200, 600);
  await page.waitForTimeout(120);
  await fire('pointerdown', 5, 202, 601); await fire('pointerup', 5, 202, 601);
  await page.waitForTimeout(700);
  const z2 = await page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.cam.z);
  ok(z2 > z1 * 1.5, 'double-tap zooms in');
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('changing floors (20 <-> 21)');
{
  const activeFloor = (page) => page.getAttribute('.fp-floor.is-active', 'data-fp-floor');
  const switching = (page) => page.evaluate(() => document.querySelector('.fp-hero').classList.contains('is-switching'));
  const waitSettled = (page) => page.waitForFunction(() => !document.querySelector('.fp-hero').classList.contains('is-switching'), null, { timeout: 5000 });

  const { ctx, page, errors } = await open();

  // the stair walk: the leaving floor's camera zooms into its staircase
  const before = await page.evaluate(() => document.querySelector('.fp-hero').__fpViewer.cam.z);
  await page.click('[data-fp-floor-switch]');
  ok(await switching(page), 'clicking the floor title starts the change');
  await page.waitForTimeout(520); // just before the floors cross (560ms)
  const mid = await page.evaluate(() => { const v = document.querySelector('.fp-hero').__fpViewers[0]; return v.cam.z; });
  ok(mid > before * 1.8, `the camera walks into the 20th-floor stairs (zoom ${before.toFixed(2)} -> ${mid.toFixed(2)})`);
  await page.click('[data-fp-floor-switch]', { force: true }); // a second click while switching is ignored
  await waitSettled(page);
  ok((await activeFloor(page)) === 'office-21f', 'arrived on the 21st floor');
  ok((await page.textContent('[data-fp-title]')) === '21-Р ДАВХАР' && (await page.textContent('[data-fp-title-next]')) === '20', 'title now 21 and offers 20');
  ok((await page.getAttribute('[data-fp-title-go]', 'data-dir')) === 'down', 'the title arrow points down');
  ok(new URL(page.url()).searchParams.get('floor') === '21', 'URL ?floor=21');
  ok((await page.$$('.fp-floor.is-active .fp-spot')).length === 5, '21st floor: 5 hotspots');
  ok(await page.evaluate(() => { const l = document.querySelector('.fp-floor[data-fp-floor="office-20f"]'); const c = getComputedStyle(l); return c.visibility === 'hidden'; }), 'the 20th floor is hidden');
  const settled = await page.evaluate(() => { const v = document.querySelector('.fp-hero').__fpViewer; const ov = v.overviewCamera(); return Math.abs(v.cam.z - ov.z) < 1e-6 && Math.abs(v.cam.fx - ov.fx) < 1e-6; });
  ok(settled, 'camera pulled back exactly to the 21st-floor overview');

  // rooms on 21, then the stairs are hidden; they come back in the overview
  await click(page, 'ceo-office');
  await waitPhase(page, 'FOCUSED');
  ok((await page.textContent('[data-fp-en]')) === 'CEO Office', 'CEO Office card on the 21st');
  ok(!(await visible(page, '.fp-floor.is-active .fp-link')), 'staircase hidden while a room is open');
  await page.keyboard.press('ArrowRight');
  await waitPhase(page, 'FOCUSED');
  ok((await slug(page)) === 'terrace-east', 'arrows stay on the current floor');
  await page.keyboard.press('Escape');
  await waitPhase(page, 'OVERVIEW');

  // back down through the staircase marker
  await page.waitForTimeout(400);
  await page.$eval('.fp-floor.is-active .fp-link', (e) => e.click());
  await waitSettled(page);
  ok((await activeFloor(page)) === 'office-20f', 'the staircase on the 21st leads back down to the 20th');
  ok(!new URL(page.url()).searchParams.has('floor'), 'URL back to plain /about');

  // leaving a floor while a room is open closes the card
  await click(page, 'locker-room');
  await waitPhase(page, 'FOCUSED');
  await page.click('[data-fp-floor-switch]');
  await waitSettled(page);
  ok((await activeFloor(page)) === 'office-21f' && (await phase(page)) === 'OVERVIEW', 'switching from inside a room lands in the next floor overview');
  ok(!(await visible(page, '[data-fp-info]')), 'card closed after the switch');
  ok(!new URL(page.url()).searchParams.has('area'), '?area= cleared');

  // keyboard: PageUp / PageDown while focus is in the hero
  await page.focus('[data-fp-floor-switch]');
  await page.keyboard.press('PageDown');
  await waitSettled(page);
  ok((await activeFloor(page)) === 'office-20f', 'PageDown -> floor below');
  ok(await page.evaluate(() => document.activeElement && document.activeElement.hasAttribute('data-fp-floor-switch')), 'focus stays on the floor switch');
  await page.keyboard.press('PageDown');
  await page.waitForTimeout(200);
  ok((await activeFloor(page)) === 'office-20f' && !(await switching(page)), 'PageDown on the lowest floor does nothing');
  ok(errors.length === 0, 'no page errors ' + errors.join('|'));
  await ctx.close();
}
{
  const { ctx, page } = await open({}, BASE + '?floor=21');
  ok((await page.getAttribute('.fp-floor.is-active', 'data-fp-floor')) === 'office-21f' && (await phase(page)) === 'OVERVIEW', '?floor=21 opens the 21st floor overview');
  await ctx.close();

  const d = await open({}, BASE + '?area=ceo-office');
  ok((await d.page.getAttribute('.fp-floor.is-active', 'data-fp-floor')) === 'office-21f' && (await slug(d.page)) === 'ceo-office', '?area=ceo-office opens the 21st floor, focused');
  await d.ctx.close();

  const rm = await open({ reducedMotion: 'reduce' });
  await rm.page.click('[data-fp-floor-switch]');
  await rm.page.waitForFunction(() => !document.querySelector('.fp-hero').classList.contains('is-switching'), null, { timeout: 300 });
  ok((await rm.page.getAttribute('.fp-floor.is-active', 'data-fp-floor')) === 'office-21f', 'reduced motion: the floor changes immediately');
  await rm.ctx.close();
}
for (const [w, h, touch] of [[390, 844, true], [1440, 900, false]]) {
  const { ctx, page } = await open({ viewport: { width: w, height: h }, touch, mobile: touch });
  await page.click('[data-fp-floor-switch]');
  await page.waitForFunction(() => !document.querySelector('.fp-hero').classList.contains('is-switching'), null, { timeout: 5000 });
  await page.waitForFunction(() => document.querySelector('.fp-floor.is-active').classList.contains('is-ready'));
  const worst = await page.evaluate(() => {
    const v = document.querySelector('.fp-hero').__fpViewer;
    const wr = document.querySelector('.fp-floor.is-active .fp-world img').getBoundingClientRect();
    let max = 0;
    for (const hs of v.hotspots) {
      const r = v.getNode(hs.slug).getBoundingClientRect();
      const cx = r.left + r.width / 2, cy = r.top + r.height / 2;
      max = Math.max(max, Math.abs((cx - wr.left) / wr.width - hs.x) * wr.width, Math.abs((cy - wr.top) / wr.height - hs.y) * wr.height);
    }
    const lr = document.querySelector('.fp-floor.is-active .fp-link').getBoundingClientRect();
    const lx = (lr.left + lr.width / 2 - wr.left) / wr.width, ly = (lr.top + lr.height / 2 - wr.top) / wr.height;
    return { max, link: Math.max(Math.abs(lx - v.link.x) * wr.width, Math.abs(ly - v.link.y) * wr.height), pan: v.panRange.pannable };
  });
  ok(worst.max < 1 && worst.link < 1, `${w}x${h}: 21st-floor dots and stairs aligned (${worst.max.toFixed(2)}px, ${worst.link.toFixed(2)}px)`);
  if (touch) { ok(worst.pan === false, 'phone: the near-square 21st floor fits the screen without panning'); }
  await ctx.close();
}
{
  const { ctx, page } = await open();
  const frames = await page.evaluate(() => new Promise((resolve) => {
    const hero = document.querySelector('.fp-hero');
    const out = [];
    let last = performance.now();
    const tick = (t) => { out.push(t - last); last = t; if (hero.classList.contains('is-switching')) { requestAnimationFrame(tick); } else { resolve(out); } };
    hero.querySelector('[data-fp-floor-switch]').click();
    requestAnimationFrame(tick);
  }));
  const sorted = frames.slice(1).sort((a, b) => a - b);
  console.log(`  floor change: ${frames.length} frames, median=${sorted[Math.floor(sorted.length / 2)].toFixed(1)}ms p95=${sorted[Math.floor(sorted.length * 0.95)].toFixed(1)}ms`);
  ok(frames.length > 60 && sorted[Math.floor(sorted.length * 0.95)] < 40, 'floor change animates smoothly');
  await ctx.close();
}

// ------------------------------------------------------------------
console.log('animation smoothness (informational)');
{
  const { ctx, page } = await open();
  const stats = await page.evaluate(() => new Promise((resolve) => {
    const v = document.querySelector('.fp-hero').__fpViewer;
    const frames = [];
    let last = performance.now();
    const tick = (t) => { frames.push(t - last); last = t; if (v.getState().phase !== 'FOCUSED') { requestAnimationFrame(tick); } else { resolve(frames); } };
    v.select('mep-team');
    requestAnimationFrame(tick);
  }));
  const sorted = stats.slice(1).sort((a, b) => a - b);
  const p95 = sorted[Math.floor(sorted.length * 0.95)];
  console.log(`  frames=${stats.length} median=${sorted[Math.floor(sorted.length / 2)].toFixed(1)}ms p95=${p95.toFixed(1)}ms max=${sorted[sorted.length - 1].toFixed(1)}ms`);
  ok(sorted[sorted.length - 1] < 120, 'no single frame over 120ms during the zoom');
  await ctx.close();
}

await browser.close();
console.log(`\n${total - failed}/${total} passed`);
process.exit(failed ? 1 : 0);
