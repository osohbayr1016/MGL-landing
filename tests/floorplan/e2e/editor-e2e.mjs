// CP Admin editor tests (drag, text, camera, add/duplicate/delete/reorder, save, security).
//
//   FP_DB_PORT=3399 FP_DB_PASS=... php -d extension=mysqli -S 127.0.0.1:8098 -t cpadmin tests/floorplan/e2e/editor-router.php
//   FP_NO_PERM=1 FP_DB_PORT=3399 FP_DB_PASS=... php -d extension=mysqli -S 127.0.0.1:8097 -t cpadmin tests/floorplan/e2e/editor-router.php
//   node tests/floorplan/e2e/editor-e2e.mjs
//
// Env: ED_URL (default http://127.0.0.1:8098), NOPERM_URL (default http://127.0.0.1:8097),
//      BROWSER_PATH, PW_DIR — as in e2e.mjs.
import { createRequire } from 'node:module';
import path from 'node:path';

const require = createRequire(path.join(process.env.PW_DIR || process.cwd(), 'noop.js'));
const { chromium } = require('playwright-core');

const ED = process.env.ED_URL || 'http://127.0.0.1:8098';
const NOPERM = process.env.NOPERM_URL || 'http://127.0.0.1:8097';
const EXE = process.env.BROWSER_PATH || 'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe';

let failed = 0;
let total = 0;
const ok = (c, m) => { total++; if (!c) { failed++; console.log('  FAIL ' + m); } else { console.log('  ok   ' + m); } };

const browser = await chromium.launch({ executablePath: EXE, headless: true });

async function open(base = ED, viewport = { width: 1500, height: 1000 }) {
  const ctx = await browser.newContext({ viewport });
  const page = await ctx.newPage();
  const errors = [];
  page.on('pageerror', (e) => errors.push(e.message));
  await page.route(/fonts\.(googleapis|gstatic)\.com/, (r) => r.abort());
  await page.goto(base + '/floorplan/edit', { waitUntil: 'load' });
  await page.waitForFunction(() => window.__fpe && document.querySelectorAll('#fpe-list li').length > 0);
  return { ctx, page, errors };
}

const dump = async () => (await fetch(ED + '/__dump')).json();
const status = (page) => page.textContent('#fpe-status');
const model = (page) => page.evaluate(() => JSON.parse(JSON.stringify(window.__fpe.model)));
/** Screen point of a normalized plan point, through the viewer's current camera (frame may be CSS-scaled). */
const planPoint = (page, x, y) => page.evaluate(([x, y]) => {
  const v = window.__fpe.viewer, g = v.getGeometry(), el = document.querySelector('#fpe-frame .fp-stage'), st = el.getBoundingClientRect(), k = st.width / el.clientWidth;
  return { x: st.left + (g.transform.tx + g.transform.scale * x * g.layout.rect.width) * k, y: st.top + (g.transform.ty + g.transform.scale * y * g.layout.rect.height) * k };
}, [x, y]);
const settled = (page) => page.waitForFunction(() => window.__fpe.viewer.getState().phase === 'OVERVIEW' && !window.__fpe.viewer.anim);
const nodeCenter = (page, slug) => page.$eval(`#fpe-frame [data-slug="${slug}"]`, (e) => { const r = e.getBoundingClientRect(); return { x: r.left + r.width / 2, y: r.top + r.height / 2 }; });

await fetch(ED + '/__reset');
await fetch(ED + '/__dump'); // creates + seeds

console.log('load');
{
  const { ctx, page, errors } = await open();
  ok((await page.$$('#fpe-list li')).length === 11, 'the 20th floor\'s 11 hotspots listed');
  ok((await page.$$('#fpe-floor-tabs button')).length === 2, 'a tab per floor');
  ok((await page.inputValue('[data-field="titleEn"]')) === 'Locker Room', 'first hotspot selected, English field filled');
  ok((await page.inputValue('[data-field="titleMn"]')) === 'Хувцас солих өрөө', 'Mongolian field filled');
  ok((await page.inputValue('[data-field="descriptionMn"]')).includes('хадгалах'), 'description field filled');
  ok((await page.inputValue('[data-field="descriptionEn"]')) === 'Changing room with personal storage', 'English description field filled');
  ok((await page.inputValue('[data-field="desktop.zoom"]')) === '2.8' && (await page.inputValue('[data-field="mobile.zoom"]')) === '4.5', 'camera fields filled');
  ok((await page.inputValue('#fpe-floor-title')) === '20-Р ДАВХАР', 'floor title field filled');
  ok(await page.isDisabled('#fpe-save'), 'Save disabled while clean');
  ok((await page.$$('#fpe-frame .fp-spot')).length === 11, 'all dots drawn on the plan');
  ok((await page.$$('#fpe-frame .fp-link')).length === 1, 'staircase marker drawn');
  ok(errors.length === 0, 'no page errors ' + errors.join('|'));
  await ctx.close();
}

console.log('drag, undo, keyboard nudge');
{
  const { ctx, page } = await open();
  const before = (await model(page)).floors[0].hotspots[0];
  const c = await nodeCenter(page, 'locker-room');
  await page.mouse.move(c.x, c.y);
  await page.mouse.down();
  await page.mouse.move(c.x + 40, c.y + 20, { steps: 8 });
  await page.mouse.up();
  const after = (await model(page)).floors[0].hotspots[0];
  const c2 = await nodeCenter(page, 'locker-room');
  ok(Math.abs(c2.x - (c.x + 40)) < 1.5 && Math.abs(c2.y - (c.y + 20)) < 1.5, `dot follows the pointer (${(c2.x - c.x).toFixed(1)}, ${(c2.y - c.y).toFixed(1)} px)`);
  ok(after.x > before.x && after.y > before.y, 'model x/y changed');
  ok(Math.abs(parseFloat(await page.inputValue('[data-field="x"]')) - after.x) < 1e-9, 'X field shows the new value');
  ok((await status(page)).includes('Хадгалаагүй'), 'unsaved-changes notice shown');
  ok(!(await page.isDisabled('#fpe-save')), 'Save enabled');
  await page.click('#fpe-undo');
  const undone = (await model(page)).floors[0].hotspots[0];
  ok(undone.x === before.x && undone.y === before.y, 'undo restores the original position');
  ok((await status(page)) === '', 'clean again -> no unsaved notice');

  await page.focus('#fpe-frame [data-slug="locker-room"]');
  await page.keyboard.press('ArrowRight');
  await page.keyboard.press('Shift+ArrowDown');
  const nud = (await model(page)).floors[0].hotspots[0];
  ok(Math.abs(nud.x - (before.x + 0.0005)) < 1e-9 && Math.abs(nud.y - (before.y + 0.005)) < 1e-9, 'arrow = 0.0005, Shift+arrow = 0.005');
  await ctx.close();
}

console.log('drag while previewing a zoomed camera');
{
  const { ctx, page } = await open();
  await page.click('[data-mode="desktop"]');
  await page.waitForFunction(() => window.__fpe.viewer.getState().phase === 'FOCUSED');
  const before = (await model(page)).floors[0].hotspots[0];
  const c = await nodeCenter(page, 'locker-room');
  await page.mouse.move(c.x, c.y);
  await page.mouse.down();
  await page.mouse.move(c.x + 30, c.y - 12, { steps: 6 });
  await page.mouse.up();
  const m = (await model(page)).floors[0].hotspots[0];
  // pointer delta -> normalized delta: undo the frame's CSS scale (k), the camera zoom (z) and the image width (bw)
  const g = await page.evaluate(() => { const v = window.__fpe.viewer, st = document.querySelector('#fpe-frame .fp-stage'); return { k: st.getBoundingClientRect().width / st.clientWidth, z: v.getGeometry().transform.scale, bw: v.getGeometry().layout.rect.width, bh: v.getGeometry().layout.rect.height }; });
  const ex = 30 / g.k / (g.z * g.bw);
  const ey = -12 / g.k / (g.z * g.bh);
  ok(g.k < 0.99, `the desktop frame is CSS-scaled (k=${g.k.toFixed(3)})`);
  ok(Math.abs((m.x - before.x) - ex) < 2e-5 && Math.abs((m.y - before.y) - ey) < 2e-5, `normalized delta exact under zoom ${g.z} and scale (${(m.x - before.x).toFixed(5)} vs ${ex.toFixed(5)})`);
  // the preview re-centres on the moved dot, exactly like visitors would see it
  const c2 = await nodeCenter(page, 'locker-room');
  const anchor = await page.evaluate(() => { const v = window.__fpe.viewer, st = document.querySelector('#fpe-frame .fp-stage').getBoundingClientRect(); return { x: st.left + st.width * v.preset.ax, y: st.top + st.height * v.preset.ay }; });
  ok(Math.abs(c2.x - anchor.x) < 1.5 && Math.abs(c2.y - anchor.y) < 1.5, 'after the drop the camera re-centres on the dot');
  await ctx.close();
}

console.log('text, camera, previews');
{
  const { ctx, page } = await open();
  await page.fill('[data-field="titleEn"]', 'Locker Room 2');
  await page.fill('[data-field="titleMn"]', 'Хувцас солих өрөө 2');
  await page.fill('[data-field="descriptionMn"]', 'Шинэ тайлбар');
  ok((await page.textContent('#fpe-list li.is-active .fpe-name')).includes('Locker Room 2'), 'list label follows the text');
  ok((await page.$eval('#fpe-frame [data-slug="locker-room"]', (e) => e.getAttribute('aria-label'))) === 'Locker Room 2 — Хувцас солих өрөө 2', 'dot label follows the text');

  await page.fill('[data-field="desktop.zoom"]', '100');
  ok((await model(page)).floors[0].hotspots[0].desktop.zoom === 16, 'zoom=100 is clamped to 16 in the model');
  ok((await page.getAttribute('[data-mode="desktop"]', 'class')).includes('btn-primary'), 'editing a camera group switches to its preview');
  await page.waitForFunction(() => window.__fpe.viewer.getState().phase === 'FOCUSED');
  ok((await page.textContent('#fpe-frame [data-fp-en]')) === 'Locker Room 2', 'preview card shows the edited text');
  const size = await page.$eval('#fpe-frame', (e) => ({ w: e.style.width, h: e.style.height }));
  ok(size.w === '1440px' && size.h === '780px', 'desktop preview frame is a real 1440x780');

  await page.fill('[data-field="desktop.zoom"]', '4.5');
  await page.fill('[data-field="desktop.offsetX"]', '0.2');
  const camX = await page.evaluate(() => { const v = window.__fpe.viewer; const r = v.getNode('locker-room').getBoundingClientRect(), s = document.querySelector('#fpe-frame .fp-stage').getBoundingClientRect(); return (r.left + r.width / 2 - s.left) / s.width; });
  ok(Math.abs(camX - (0.53 + 0.2)) < 0.01, `offsetX moves the dot on screen (dot at ${camX.toFixed(3)} of the width)`);

  await page.click('#fpe-preview-mobile');
  await page.waitForFunction(() => document.querySelector('#fpe-frame .fp-stage').getAttribute('data-fp-layout') === 'portrait');
  ok((await page.$eval('#fpe-frame', (e) => e.style.width)) === '390px', 'mobile preview frame is 390 wide with the portrait layout');
  await page.fill('[data-field="mobile.zoom"]', '9');
  ok((await model(page)).floors[0].hotspots[0].mobile.zoom === 9, 'mobile zoom editable');

  await page.uncheck('[data-field="desktop.focusDot"]');
  const fx = await page.inputValue('[data-field="desktop.focusX"]');
  ok(fx !== '' && !(await page.isDisabled('[data-field="desktop.focusX"]')), 'focus override can be switched on');
  await page.check('[data-field="desktop.focusDot"]');
  ok((await model(page)).floors[0].hotspots[0].desktop.focusX === null, 'focus follows the dot again -> null');

  await page.click('#fpe-preview-overview');
  await page.waitForFunction(() => window.__fpe.viewer.getState().phase === 'OVERVIEW');
  ok(true, 'return to the overview');
  await ctx.close();
}

console.log('add, duplicate, reorder, disable, delete');
{
  const { ctx, page } = await open();
  await page.click('#fpe-add');
  await settled(page);
  const pt = await planPoint(page, 0.5, 0.9);
  await page.mouse.click(pt.x, pt.y);
  let m = await model(page);
  ok(m.floors[0].hotspots.length === 12 && m.floors[0].hotspots[11]._new === true, 'click on the plan adds a hotspot');
  ok(Math.abs(m.floors[0].hotspots[11].x - 0.5) < 0.002 && Math.abs(m.floors[0].hotspots[11].y - 0.9) < 0.002, `new point at the clicked place (${m.floors[0].hotspots[11].x}, ${m.floors[0].hotspots[11].y})`);
  ok(!(await page.$eval('#fpe-frame .fp-floor', (e) => e.classList.contains('is-add-mode'))), 'add mode switches off after one point');
  ok(!(await page.isDisabled('[data-field="slug"]')), 'slug of a new point is editable');
  await page.fill('[data-field="slug"]', 'Test Area!!');
  ok((await page.inputValue('[data-field="slug"]')) === 'testarea', 'slug is sanitized as you type');
  await page.fill('[data-field="titleMn"]', 'Туршилтын талбай');

  await page.click('#fpe-dup');
  m = await model(page);
  ok(m.floors[0].hotspots.length === 13 && m.floors[0].hotspots[12].slug !== 'testarea', 'duplicate creates a new unique slug');

  await page.click('#fpe-up');
  m = await model(page);
  ok(m.floors[0].hotspots[11].titleEn.includes('(copy)'), 'move up reorders');

  await page.click('#fpe-list li:nth-child(3)');
  await page.uncheck('[data-field="enabled"]');
  ok((await page.getAttribute('#fpe-list li:nth-child(3)', 'class')).includes('is-off'), 'disabled hotspot is struck through in the list');
  ok((await page.$eval('#fpe-frame [data-slug="showroom"]', (e) => e.classList.contains('is-disabled'))), 'and greyed on the plan');
  await page.click('#fpe-preview-desktop');
  ok((await page.textContent('#fpe-hint')).includes('идэвхтэй'), 'previewing a disabled hotspot explains why nothing happens');
  await page.click('[data-mode="overview"]');

  page.once('dialog', (d) => d.accept());
  await page.click('#fpe-list li:nth-child(13)');
  await page.click('#fpe-del');
  m = await model(page);
  ok(m.floors[0].hotspots.length === 12, 'delete removes the hotspot (after confirmation)');

  await page.click('#fpe-reset', { trial: false }).catch(() => {});
  await ctx.close();
}

console.log('validation before save');
{
  const { ctx, page } = await open();
  await page.fill('[data-field="titleEn"]', '');
  await page.fill('[data-field="titleMn"]', '');
  await page.click('#fpe-save');
  ok((await page.textContent('#fpe-errors')).includes('нэр'), 'a point with no name is refused client-side');
  await page.fill('#fpe-floor-title', '');
  await page.fill('[data-field="titleEn"]', 'Locker Room');
  await page.click('#fpe-save');
  ok((await page.textContent('#fpe-errors')).includes('гарчиг'), 'empty floor title refused');
  await ctx.close();
}

console.log('cancel / reset');
{
  const { ctx, page } = await open();
  await page.fill('[data-field="titleEn"]', 'Changed');
  page.once('dialog', (d) => d.accept());
  await page.click('#fpe-reset');
  ok((await page.inputValue('[data-field="titleEn"]')) === 'Locker Room', 'cancel restores the saved values');
  ok(await page.isDisabled('#fpe-save'), 'and Save is disabled again');
  await ctx.close();
}

console.log('save + persistence + public data');
{
  const { ctx, page } = await open();
  await page.fill('#fpe-floor-title', '20-Р ДАВХАР (A)');
  await page.fill('[data-field="titleMn"]', 'Хувцас солих өрөө (шинэ)');
  await page.fill('[data-field="descriptionMn"]', 'Шинэ тайлбар');
  await page.fill('[data-field="descriptionEn"]', 'New description');
  await page.fill('[data-field="desktop.zoom"]', '3.2');
  await page.fill('[data-field="mobile.offsetY"]', '-0.05');
  await page.click('#fpe-list li:nth-child(2)');
  await page.click('#fpe-down'); // main-entrance below showroom
  await page.click('#fpe-add');
  await settled(page);
  const pt = await planPoint(page, 0.4, 0.88);
  await page.mouse.click(pt.x, pt.y);
  await page.fill('[data-field="slug"]', 'archive');
  await page.fill('[data-field="titleEn"]', 'Archive');
  await page.fill('[data-field="titleMn"]', 'Архив');
  await page.click('#fpe-save');
  await page.waitForFunction(() => document.querySelector('#fpe-status').textContent === 'Хадгаллаа');
  ok(true, 'save reports success');
  ok(await page.isDisabled('#fpe-save'), 'Save disabled after saving');

  const d = await dump();
  ok(d.revisions['office-20f'] === 2 && d.revisions['office-21f'] === 2, 'revisions bumped in the DB');
  ok(d.floors[0].plan.floorTitle === '20-Р ДАВХАР (A)', 'floor title persisted');
  ok(d.floors[0].hotspots.length === 12 && d.floors[0].hotspots[11].slug === 'archive' && d.floors[0].hotspots[11].titleMn === 'Архив', 'new hotspot persisted (last)');
  ok(d.floors[0].hotspots[0].titleMn === 'Хувцас солих өрөө (шинэ)' && d.floors[0].hotspots[0].descriptionMn === 'Шинэ тайлбар', 'edited Mongolian text + description persisted');
  ok(d.floors[0].hotspots[0].descriptionEn === 'New description', 'English description persisted');
  ok(d.floors[0].hotspots[0].desktop.zoom === 3.2 && d.floors[0].hotspots[0].mobile.offsetY === -0.05, 'camera settings persisted');
  ok(d.floors[0].hotspots[1].slug === 'showroom' && d.floors[0].hotspots[2].slug === 'main-entrance', 'order persisted');

  await page.reload({ waitUntil: 'load' });
  await page.waitForFunction(() => window.__fpe && document.querySelectorAll('#fpe-list li').length > 0);
  ok((await page.$$('#fpe-list li')).length === 12 && (await page.inputValue('#fpe-floor-title')) === '20-Р ДАВХАР (A)', 'reloading shows the saved state');
  await ctx.close();
}

console.log('floors + staircase in the editor');
{
  const { ctx, page } = await open();
  await page.click('[data-floor="office-21f"]');
  await page.waitForFunction(() => window.__fpe.floor === 1);
  ok((await page.$$('#fpe-list li')).length === 5, '21st floor tab lists its 5 hotspots');
  ok((await page.inputValue('#fpe-floor-title')) === '21-Р ДАВХАР' && (await page.textContent('#fpe-title-preview')) === '21-Р ДАВХАР', 'floor fields + preview title switch with the tab');
  ok((await page.$$('#fpe-frame .fp-spot')).length === 5, 'only the 21st floor dots are drawn');

  // drag the staircase marker
  const before = (await model(page)).floors[1].link;
  const lc = await page.$eval('#fpe-frame .fp-link', (e) => { const r = e.getBoundingClientRect(); return { x: r.left + r.width / 2, y: r.top + r.height / 2 }; });
  await page.mouse.move(lc.x, lc.y);
  await page.mouse.down();
  await page.mouse.move(lc.x + 30, lc.y - 15, { steps: 6 });
  await page.mouse.up();
  const after = (await model(page)).floors[1].link;
  ok(after.x > before.x && after.y < before.y, `staircase marker dragged (${before.x}, ${before.y}) -> (${after.x}, ${after.y})`);
  ok((await page.textContent('#fpe-stair-pos')).includes(after.x.toFixed(4)), 'staircase position shown');
  await page.click('#fpe-undo');
  const undone = (await model(page)).floors[1].link;
  ok(undone.x === before.x && undone.y === before.y, 'undo restores the staircase');
  await page.mouse.move(lc.x, lc.y);
  await page.mouse.down();
  await page.mouse.move(lc.x + 20, lc.y, { steps: 5 });
  await page.mouse.up();
  const moved = (await model(page)).floors[1].link;

  // a slug that already exists on the OTHER floor is refused
  await page.click('#fpe-add');
  await settled(page);
  const pt = await planPoint(page, 0.3, 0.9);
  await page.mouse.click(pt.x, pt.y);
  await page.fill('[data-field="slug"]', 'locker-room');
  ok((await model(page)).floors[1].hotspots[5].slug !== 'locker-room', 'a slug used on the 20th floor cannot be reused on the 21st');
  await page.fill('[data-field="slug"]', 'print-room');
  await page.fill('[data-field="titleEn"]', 'Print Room');

  await page.fill('#fpe-floor-title', '21-Р ДАВХАР (B)');
  ok((await page.textContent('[data-floor="office-21f"]')) === '21-Р ДАВХАР (B)', 'tab label follows the floor title');

  // edits survive switching tabs
  await page.click('[data-floor="office-20f"]');
  await page.waitForFunction(() => window.__fpe.floor === 0);
  await page.click('[data-floor="office-21f"]');
  await page.waitForFunction(() => window.__fpe.floor === 1);
  ok((await page.inputValue('#fpe-floor-title')) === '21-Р ДАВХАР (B)' && (await page.$$('#fpe-list li')).length === 6, 'unsaved edits on a floor survive switching tabs');

  await page.click('#fpe-save');
  await page.waitForFunction(() => document.querySelector('#fpe-status').textContent === 'Хадгаллаа');
  const d = await dump();
  const f21 = d.floors[1];
  ok(f21.plan.floorTitle === '21-Р ДАВХАР (B)' && f21.hotspots.length === 6 && f21.hotspots[5].slug === 'print-room', '21st floor saved');
  ok(Math.abs(f21.plan.link.x - moved.x) < 1e-9 && Math.abs(f21.plan.link.y - moved.y) < 1e-9, 'staircase position saved');
  ok(d.floors[0].hotspots.length === 12, 'the 20th floor was saved unchanged in the same request');
  await ctx.close();
}

console.log('conflict: two editors');
{
  const A = await open();
  const B = await open();
  await A.page.fill('[data-field="titleEn"]', 'From tab A');
  await A.page.click('#fpe-save');
  await A.page.waitForFunction(() => document.querySelector('#fpe-status').textContent === 'Хадгаллаа');
  await B.page.fill('[data-field="titleEn"]', 'From tab B');
  await B.page.click('#fpe-save');
  await B.page.waitForFunction(() => document.querySelector('#fpe-errors').textContent.length > 0);
  ok((await B.page.textContent('#fpe-errors')).includes('Өөр хэн нэгэн'), 'the stale tab is refused with an explanation');
  ok((await dump()).floors[0].hotspots[0].titleEn === 'From tab A', 'the first save was not overwritten');
  ok(!(await B.page.isDisabled('#fpe-save')) && (await B.page.inputValue('[data-field="titleEn"]')) === 'From tab B', 'the stale tab keeps its edits (nothing lost)');
  await A.ctx.close();
  await B.ctx.close();
}

console.log('server-side security');
{
  const { ctx, page } = await open();
  const csrf = await page.evaluate(() => JSON.parse(document.getElementById('fpe-data').textContent).csrf);
  const revs = JSON.stringify((await dump()).revisions);
  const payload = JSON.stringify({ floors: [{ key: 'office-20f', floorTitle: 'Hacked', enabled: true, hotspots: [{ slug: 'x', x: 0.1, y: 0.1, titleEn: 'X' }] }] });
  const post = (fields) => page.request.post(ED + '/userPost/floorplan', { form: fields });

  let r = await post({ frmPost: 'floorPlanSave', ajaxOrder: '1', revisions: revs, payload });
  ok(r.status() === 403 && (await r.json()).code === 'csrf', 'save without CSRF token -> 403');
  r = await post({ frmPost: 'floorPlanSave', ajaxOrder: '1', csrf: 'wrong', revisions: revs, payload });
  ok(r.status() === 403, 'save with a wrong CSRF token -> 403');
  r = await post({ frmPost: 'floorPlanSave', ajaxOrder: '1', csrf, revisions: revs, payload: 'not json' });
  ok(r.status() === 400, 'garbage payload -> 400');
  r = await post({ frmPost: 'floorPlanSave', ajaxOrder: '1', csrf, revisions: revs, payload: JSON.stringify({ floors: [{ key: 'office-20f', floorTitle: 'x', hotspots: [{ slug: 'a', x: 'abc', y: 1, titleEn: 'A' }] }] }) });
  ok(r.status() === 422, 'invalid hotspot -> 422');
  r = await post({ frmPost: 'floorPlanSave', ajaxOrder: '1', csrf, revisions: revs, payload: JSON.stringify({ floors: [{ key: 'office-99f', floorTitle: 'x', hotspots: [] }] }) });
  ok(r.status() === 422, 'a floor that does not exist cannot be created by a request');
  r = await post({ frmPost: 'floorPlanSave', ajaxOrder: '1', csrf, revisions: revs, payload: JSON.stringify({ floors: [{ key: 'office-20f', floorTitle: 'x', hotspots: [{ slug: 'a', x: 'abc', y: 1, titleEn: 'A' }] }] }) });
  ok(r.status() === 422 && (await r.json()).errors.length > 0, 'invalid hotspot -> 422 with messages');
  r = await post({ frmPost: 'other', csrf });
  ok(r.status() === 400, 'unknown action -> 400');
  r = await page.request.get(ED + '/userPost/floorplan');
  ok(r.status() === 400 || r.status() === 405, 'GET is not a write');
  ok((await dump()).floors[0].plan.floorTitle !== 'Hacked', 'none of the rejected requests changed the data');

  // out-of-range values sent by a hand-crafted request are clamped, not stored
  r = await post({ frmPost: 'floorPlanSave', ajaxOrder: '1', csrf, revisions: revs, payload: JSON.stringify({ floors: [{ key: 'office-20f', floorTitle: 'Clamp', enabled: true, link: { x: 7, y: -7 }, hotspots: [{ slug: 'clamped', x: 9, y: -9, titleEn: 'C', desktop: { zoom: 1e9, offsetX: 99 } }] }] }) });
  const j = await r.json();
  const dd = await dump();
  const h = dd.floors[0].hotspots[0];
  ok(dd.floors[0].plan.link.x === 1 && dd.floors[0].plan.link.y === 0, 'server clamps the staircase position');
  ok(j.ok === 1 && h.x === 1 && h.y === 0 && h.desktop.zoom === 16 && h.desktop.offsetX === 0.4, 'server clamps coordinates, zoom and offsets');

  r = await page.request.get(ED + '/?incPageType=floorplan&subPage=asset&asset=..%2f..%2fclass%2ffloorplan.class.php');
  ok(r.status() === 404, 'asset route cannot be used for path traversal');
  r = await page.request.get(ED + '/?incPageType=floorplan&subPage=asset&asset=post.sys.php');
  ok(r.status() === 404, 'asset route only serves the whitelisted files');
  await ctx.close();

  // an admin without the permission
  const np = await browser.newContext();
  const npPage = await np.newPage();
  await npPage.goto(NOPERM + '/floorplan/edit');
  ok((await npPage.textContent('body')).includes('Хандах эрхгүй байна'), 'no permission -> the editor is not served');
  ok((await np.request.get(NOPERM + '/?incPageType=floorplan&subPage=asset&asset=core.js')).status() === 403, 'no permission -> assets 403');
  const sess = await np.request.post(NOPERM + '/userPost/floorplan', { form: { frmPost: 'floorPlanSave', ajaxOrder: '1', csrf: 'x', revisions: '{}', payload } });
  ok(sess.status() === 403, 'no permission -> save refused (403) even with a payload');
  await np.close();
}

await browser.close();
console.log(`\n${total - failed}/${total} passed`);
process.exit(failed ? 1 : 0);
