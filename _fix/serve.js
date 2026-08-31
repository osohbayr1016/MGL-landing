/* ============================================================================
   Production-faithful preview with a before/after toggle.

   Serves the REAL mglenc.com HTML (saved in _fix/live/) using your local
   production CSS/JS, and injects patch.css on top. Photography still loads
   from cp.mglenc.com, so what you see is the real site — same markup, same
   stylesheet, same scripts — with only the patch layered on.

     node _fix/serve.js      ->  http://localhost:4000

   Add ?fix=0 to any URL to switch the patch OFF and see the current live
   behaviour for comparison.
   ========================================================================== */
const http = require('http');
const fs   = require('fs');
const path = require('path');

const HERE     = __dirname;
const SITEROOT = path.join(__dirname, '..');
const PORT     = process.env.PORT || 4000;

const MIME = {
  '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8', '.svg': 'image/svg+xml',
  '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png',
  '.gif': 'image/gif', '.mp4': 'video/mp4', '.woff': 'font/woff',
  '.woff2': 'font/woff2', '.ttf': 'font/ttf', '.eot': 'application/vnd.ms-fontobject',
  '.ico': 'image/x-icon', '.json': 'application/json; charset=utf-8',
};

const PAGES = {
  '/':          'home.html',
  '/home':      'home.html',
  '/projects':  'projects.html',
  '/about':     'about.html',
};

/* A small bar so it is obvious which mode you are looking at.
   Two independent switches: the main patch, and the mobile hero fix. */
function toolbar(on, hero, page) {
  const bg = !on ? '#8a1c1c' : (hero ? '#003d59' : '#6b4c00');
  return `
<style>
  #fixbar{position:fixed;left:0;right:0;bottom:0;z-index:99999;display:flex;
    align-items:center;flex-wrap:wrap;gap:10px 14px;padding:9px 16px;
    font:600 11px/1.4 Montserrat,system-ui,sans-serif;letter-spacing:.08em;
    text-transform:uppercase;color:#fff;background:${bg}}
  #fixbar a{color:#fff;text-decoration:underline;text-underline-offset:3px}
  #fixbar .sp{flex:1}
  #fixbar .tag{padding:2px 7px;border:1px solid rgba(255,255,255,.5);border-radius:99px;
    text-decoration:none;letter-spacing:.06em}
  @media(max-width:700px){#fixbar{font-size:9px;gap:7px 10px;padding:8px 10px}}
</style>
<div id="fixbar">
  <span>${on ? 'PATCH ON' : 'PATCH OFF — live site'}</span>
  ${on ? `<span>|</span><span>Hero&nbsp;fix: ${hero ? 'ON' : 'OFF'}</span>` : ''}
  <span class="sp"></span>
  ${on ? `<a class="tag" href="${page}?hero=${hero ? '0' : '1'}">${hero ? 'Hero fix OFF' : 'Hero fix ON'}</a>` : ''}
  <a class="tag" href="${page}?fix=${on ? '0' : '1'}">${on ? 'All patches OFF' : 'All patches ON'}</a>
  <a href="/">Home</a><a href="/projects">Projects</a><a href="/about">About</a>
</div>`;
}

function servePage(file, req, res, urlPath) {
  let html;
  try { html = fs.readFileSync(path.join(HERE, 'live', file), 'utf8'); }
  catch {
    res.writeHead(500, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end('<h1 style="font:600 20px system-ui;padding:40px">Missing _fix/live/' + file +
            '<br><small style="font-weight:400">Run the fetch step again.</small></h1>');
    return;
  }

  const on   = !/[?&]fix=0/.test(req.url);
  const hero = on && !/[?&]hero=0/.test(req.url);

  // Absolute base so relative asset URLs in the saved HTML resolve locally.
  html = html.replace(/<head([^>]*)>/i, '<head$1>\n<base href="/">');

  if (on) {
    const t = Date.now();
    let links = '<link rel="stylesheet" href="/patch.css?t=' + t + '">\n';
    // Loaded second, and only when enabled — exactly how it will work in
    // production, where reverting means deleting this one <link>.
    if (hero) links += '<link rel="stylesheet" href="/patch-hero-mobile.css?t=' + t + '">\n';
    html = html.replace(/<\/head>/i, links + '</head>');
  }
  html = html.replace(/<\/body>/i, toolbar(on, hero, urlPath) + '\n</body>');

  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8', 'Cache-Control': 'no-store' });
  res.end(html);
}

function safe(base, rel) {
  const full = path.resolve(base, rel);
  return full.startsWith(path.resolve(base)) ? full : null;
}

http.createServer((req, res) => {
  const urlPath = decodeURIComponent(req.url.split('?')[0]);

  if (PAGES[urlPath]) return servePage(PAGES[urlPath], req, res, urlPath);

  if (urlPath === '/patch.css' || urlPath === '/patch-hero-mobile.css') {
    const css = fs.readFileSync(path.join(HERE, urlPath.slice(1)));
    res.writeHead(200, { 'Content-Type': 'text/css; charset=utf-8', 'Cache-Control': 'no-store' });
    return res.end(css);
  }

  // Local production assets, and uploads via the same paths .htaccess rewrites.
  let file = null;
  if (urlPath.startsWith('/assets/'))        file = safe(SITEROOT, urlPath.slice(1));
  else if (urlPath.startsWith('/newsimg/'))  file = safe(SITEROOT, 'cpadmin/postpic/image/' + urlPath.slice(9));
  else if (urlPath.startsWith('/newstimg/')) file = safe(SITEROOT, 'cpadmin/postpic/_thumbs/Images/' + urlPath.slice(10));
  else if (urlPath.startsWith('/pics/'))     file = safe(SITEROOT, 'cpadmin/postpic/' + urlPath.slice(6));
  else                                       file = safe(SITEROOT, urlPath.slice(1));

  if (!file) { res.writeHead(403).end('Forbidden'); return; }

  fs.stat(file, (err, stat) => {
    if (err || !stat.isFile()) { res.writeHead(404).end('Not found: ' + urlPath); return; }
    res.writeHead(200, {
      'Content-Type': MIME[path.extname(file).toLowerCase()] || 'application/octet-stream',
      'Content-Length': stat.size,
    });
    fs.createReadStream(file).pipe(res);
  });
}).listen(PORT, () => {
  console.log('\n  MGL E&C — layout fix preview');
  console.log('  ---------------------------------------------');
  console.log('  Home      http://localhost:' + PORT + '/');
  console.log('  Projects  http://localhost:' + PORT + '/projects');
  console.log('');
  console.log('  Add ?fix=0 to compare against the current live site.');
  console.log('  Ctrl+C to stop.\n');
});
