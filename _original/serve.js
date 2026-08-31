/* Original MGL E&C site — no patches, no redesign.
   Serves saved production HTML from _fix/live/ with local assets.
   Run:  node _original/serve.js  ->  http://localhost:4000 */
const http = require('http');
const fs   = require('fs');
const path = require('path');

const HERE     = __dirname;
const SITEROOT = path.join(__dirname, '..');
const LIVE     = path.join(SITEROOT, '_fix', 'live');
const PORT     = process.env.PORT || 4000;

const MIME = {
  '.html': 'text/html; charset=utf-8', '.css': 'text/css; charset=utf-8',
  '.js': 'text/javascript; charset=utf-8', '.svg': 'image/svg+xml',
  '.jpg': 'image/jpeg', '.jpeg': 'image/jpeg', '.png': 'image/png',
  '.gif': 'image/gif', '.mp4': 'video/mp4', '.woff': 'font/woff',
  '.woff2': 'font/woff2', '.ttf': 'font/ttf', '.ico': 'image/x-icon',
  '.json': 'application/json; charset=utf-8',
};

const PAGES = {
  '/': 'home.html', '/home': 'home.html',
  '/projects': 'projects.html', '/about': 'about.html',
  '/clientarea': 'cp-login.html', '/cp-login': 'cp-login.html',
};

function safe(base, rel) {
  const full = path.resolve(base, rel);
  return full.startsWith(path.resolve(base)) ? full : null;
}

function servePage(file, res) {
  let html;
  try { html = fs.readFileSync(path.join(LIVE, file), 'utf8'); }
  catch {
    res.writeHead(500, { 'Content-Type': 'text/html; charset=utf-8' });
    res.end('<h1 style="font:600 20px system-ui;padding:40px">Missing ' + file + '</h1>');
    return;
  }
  html = html.replace(/<head([^>]*)>/i, '<head$1>\n<base href="/">');
  html = html.replace(/maximum-scale=1\.0, user-scalable=no, /g, '');
  const t = Date.now();
  let extra = '';
  if (file === 'home.html') {
    extra += '<link rel="stylesheet" href="/assets/css/home-projects.css?t=' + t + '">\n';
    extra += '<link rel="stylesheet" href="/assets/css/home-news.css?t=' + t + '">\n';
  }
  extra += '<link rel="stylesheet" href="/assets/css/mobile.css?t=' + t + '">\n';
  extra += '<script src="/assets/js/mobile.js?t=' + t + '" defer></script>\n';
  html = html.replace(/<\/head>/i, extra + '</head>');
  res.writeHead(200, { 'Content-Type': 'text/html; charset=utf-8', 'Cache-Control': 'no-store' });
  res.end(html);
}

http.createServer((req, res) => {
  const urlPath = decodeURIComponent(req.url.split('?')[0]);
  if (PAGES[urlPath]) return servePage(PAGES[urlPath], res);

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
  console.log('\n  MGL E&C — original site + mobile UI overlay');
  console.log('  ------------------------------------------------');
  console.log('  Home       http://localhost:' + PORT + '/');
  console.log('  Projects   http://localhost:' + PORT + '/projects');
  console.log('  About      http://localhost:' + PORT + '/about');
  console.log('  Client     http://localhost:' + PORT + '/clientarea');
  console.log('\n  Ctrl+C to stop.\n');
});
