/*
	mglenc media worker.

	Serves the three image URL shapes the site already uses:

		/pics/<folder>/<file>   ->  R2 key  <folder>/<file>
		/newsimg/<path>         ->  R2 key  image/<path>
		/newstimg/<path>        ->  R2 key  _thumbs/Images/<path>

	R2 is checked first; anything missing is fetched from the origin server
	and cached at the edge. That fallback is the whole point - new uploads
	land in R2 while every image already on the hosting keeps working, with
	no migration and no change to the stored filenames.

	Bindings (wrangler.toml or the dashboard):
		MEDIA       R2 bucket binding
		ORIGIN      var, e.g. "https://mglenc.com"
		TRANSFORMS  var, "on" to enable resizing (needs a Cloudflare zone,
		            not workers.dev - see below)
*/

const ROUTES = [
	[/^\/pics\/([A-Za-z0-9_.-]+)\/(.+)$/, (m) => `${m[1]}/${m[2]}`],
	[/^\/newsimg\/(.+)$/, (m) => `image/${m[1]}`],
	[/^\/newstimg\/(.+)$/, (m) => `_thumbs/Images/${m[1]}`],
];

const CACHE_R2 = "public, max-age=31536000, immutable";
const CACHE_ORIGIN = "public, max-age=86400";

export default {
	async fetch(request, env, ctx) {
		if (request.method !== "GET" && request.method !== "HEAD") {
			return new Response("Method not allowed", { status: 405 });
		}

		const url = new URL(request.url);
		const key = matchKey(url.pathname);

		if (!key) return new Response("Not found", { status: 404 });

		// Resize pass: hand the raw object back to Cloudflare's image pipeline.
		// On workers.dev the cf.image options are ignored and the original is
		// returned unchanged, so this stays safe to leave switched on.
		const resize = imageOptions(url);
		if (env.TRANSFORMS === "on" && resize && !url.searchParams.has("raw")) {
			const rawUrl = new URL(url);
			rawUrl.search = "";
			rawUrl.searchParams.set("raw", "1");
			return fetch(rawUrl.toString(), { cf: { image: resize } });
		}

		const cache = caches.default;
		const cacheKey = new Request(url.toString(), { method: "GET" });

		const cached = await cache.match(cacheKey);
		if (cached) return cached;

		let response = await fromR2(env, key, request);

		if (!response) response = await fromOrigin(env, url);

		if (response.ok || response.status === 304) {
			ctx.waitUntil(cache.put(cacheKey, response.clone()));
		}

		return response;
	},
};

function matchKey(pathname) {
	for (const [pattern, build] of ROUTES) {
		const match = pathname.match(pattern);
		if (match) return decodeURIComponent(build(match));
	}
	return null;
}

function imageOptions(url) {
	const width = parseInt(url.searchParams.get("w") || "", 10);
	const height = parseInt(url.searchParams.get("h") || "", 10);
	const quality = parseInt(url.searchParams.get("q") || "", 10);

	if (!width && !height) return null;

	const options = { fit: url.searchParams.get("fit") || "cover", format: "auto" };

	if (width) options.width = Math.min(width, 4000);
	if (height) options.height = Math.min(height, 4000);
	if (quality) options.quality = Math.min(Math.max(quality, 1), 100);

	return options;
}

async function fromR2(env, key, request) {
	if (!env.MEDIA) return null;

	const object = await env.MEDIA.get(key, {
		onlyIf: request.headers,
		range: request.headers,
	});

	if (!object) return null;

	const headers = new Headers();
	object.writeHttpMetadata(headers);
	headers.set("etag", object.httpEtag);
	headers.set("cache-control", CACHE_R2);
	headers.set("x-media-source", "r2");

	if (!object.body) return new Response(null, { status: 304, headers });

	const status = object.range ? 206 : 200;
	return new Response(object.body, { status, headers });
}

async function fromOrigin(env, url) {
	const origin = (env.ORIGIN || "").replace(/\/$/, "");

	if (!origin) return new Response("Not found", { status: 404 });

	const upstream = await fetch(origin + url.pathname, {
		cf: { cacheEverything: true, cacheTtl: 86400 },
	});

	const headers = new Headers(upstream.headers);
	headers.set("cache-control", upstream.ok ? CACHE_ORIGIN : "no-store");
	headers.set("x-media-source", "origin");

	return new Response(upstream.body, { status: upstream.status, headers });
}
