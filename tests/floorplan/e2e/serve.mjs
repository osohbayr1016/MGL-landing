// Tiny static server for the e2e harness: node tests/floorplan/e2e/serve.mjs [port]
import http from 'node:http';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const here = path.dirname(fileURLToPath(import.meta.url));
const ROOT = path.resolve(here, '../../..');
const port = Number(process.argv[2] || 8099);
const mime = { '.css': 'text/css', '.js': 'application/javascript', '.webp': 'image/webp', '.png': 'image/png', '.jpg': 'image/jpeg', '.svg': 'image/svg+xml', '.html': 'text/html; charset=utf-8', '.woff2': 'font/woff2' };

http.createServer((req, res) => {
  const p = new URL(req.url, 'http://x').pathname;
  if (p === '/about') { res.writeHead(200, { 'content-type': mime['.html'] }); return res.end(fs.readFileSync(path.join(here, 'page.html'))); }
  const f = path.resolve(ROOT, '.' + decodeURIComponent(p));
  if (f.startsWith(ROOT) && fs.existsSync(f) && fs.statSync(f).isFile()) {
    res.writeHead(200, { 'content-type': mime[path.extname(f)] || 'application/octet-stream', 'cache-control': 'no-store' });
    return fs.createReadStream(f).pipe(res);
  }
  res.writeHead(404); res.end('not found');
}).listen(port, '127.0.0.1', () => console.log('floorplan e2e harness on http://127.0.0.1:' + port + '/about'));
