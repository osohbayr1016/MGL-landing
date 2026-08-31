// MGL E&C redesign preview server - zero dependencies, Node built-ins only.
// Run:  node _redesign/serve.js      then open http://localhost:3000
const http = require('http');
const fs   = require('fs');
const path = require('path');

const ROOT     = __dirname;
const SITEROOT = path.join(__dirname, '..');
const PORT     = process.env.PORT || 3000;

const MIME = {
  '.html': 'text/html; charset=utf-8',
  '.css' : 'text/css; charset=utf-8',
  '.js'  : 'text/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.svg' : 'image/svg+xml',
  '.jpg' : 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.png' : 'image/png',
  '.gif' : 'image/gif',
  '.mp4' : 'video/mp4',
  '.woff': 'font/woff',
  '.woff2':'font/woff2',
  '.ico' : 'image/x-icon',
};

// Map a request URL to a file on disk. Returns null if it escapes the allowed roots.
function resolve(urlPath) {
  let p = decodeURIComponent(urlPath.split('?')[0]);
  if (p === '/' || p === '') p = '/index.html';

  // Real project photography from the production uploads folder.
  if (p.startsWith('/img/ceo/'))    return safe(SITEROOT, 'cpadmin/postpic/ceo/'    + p.slice(9));
  if (p.startsWith('/img/image/'))  return safe(SITEROOT, 'cpadmin/postpic/image/'  + p.slice(11));
  // Existing brand SVGs / logos.
  if (p.startsWith('/assets/'))     return safe(SITEROOT, p.slice(1));

  if (!path.extname(p)) p += '.html';   // /projects -> /projects.html
  return safe(ROOT, p.slice(1));
}

function safe(base, rel) {
  const full = path.resolve(base, rel);
  return full.startsWith(path.resolve(base)) ? full : null;
}

http.createServer((req, res) => {
  const file = resolve(req.url);
  if (!file) { res.writeHead(403).end('Forbidden'); return; }

  fs.stat(file, (err, stat) => {
    if (err || !stat.isFile()) {
      res.writeHead(404, { 'Content-Type': 'text/html; charset=utf-8' });
      res.end('<h1 style="font:600 24px system-ui;padding:40px">404 &mdash; ' + req.url + '</h1>');
      return;
    }
    res.writeHead(200, {
      'Content-Type'  : MIME[path.extname(file).toLowerCase()] || 'application/octet-stream',
      'Content-Length': stat.size,
      'Cache-Control' : 'no-cache',
    });
    fs.createReadStream(file).pipe(res);
  });
}).listen(PORT, () => {
  console.log('');
  console.log('  MGL E&C redesign preview');
  console.log('  ------------------------------------');
  console.log('  http://localhost:' + PORT + '/');
  console.log('');
  console.log('  Home            http://localhost:' + PORT + '/');
  console.log('  Projects        http://localhost:' + PORT + '/projects');
  console.log('  Project detail  http://localhost:' + PORT + '/project');
  console.log('  About           http://localhost:' + PORT + '/about');
  console.log('  News            http://localhost:' + PORT + '/news');
  console.log('  Article         http://localhost:' + PORT + '/article');
  console.log('');
  console.log('  Press Ctrl+C to stop.');
  console.log('');
});
