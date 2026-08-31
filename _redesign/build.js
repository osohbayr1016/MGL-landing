/* ============================================================================
   Generates the static preview pages.
   Run:  node _redesign/build.js
   Pulls real photography from cpadmin/postpic/ceo so the preview shows actual
   MGL E&C projects rather than grey boxes. Text is placeholder — in production
   every string here comes from the existing MySQL tables.
   ========================================================================== */
const fs   = require('fs');
const path = require('path');

const OUT      = __dirname;
const SITEROOT = path.join(__dirname, '..');

/* ------------------------------------------------------ real project images */
function loadImages() {
  const dir = path.join(SITEROOT, 'cpadmin/postpic/ceo');
  let files = [];
  try {
    files = fs.readdirSync(dir)
      .filter(f => /\.(jpe?g|png)$/i.test(f))
      .filter(f => {
        try { return fs.statSync(path.join(dir, f)).size > 40000; } // skip thumbs/broken
        catch { return false; }
      });
  } catch { /* folder absent — fall through to placeholders */ }

  // Deterministic spread across the set so the grid isn't 40 near-identical shots.
  files.sort((a, b) => (parseInt(a) || 0) - (parseInt(b) || 0));
  return files;
}

const IMAGES = loadImages();
const img = i => IMAGES.length ? '/img/ceo/' + IMAGES[(i * 7 + 3) % IMAGES.length] : '';

/* ------------------------------------------------------------- placeholders */
const SECTORS = ['Energy', 'Infrastructure', 'Mining', 'Industrial', 'Civic'];

const PROJECTS = [
  ['Central Thermal Power Plant IV',   'Energy',         'Ulaanbaatar',  '2024'],
  ['Tuul River Crossing',              'Infrastructure', 'Ulaanbaatar',  '2024'],
  ['Oyu Tolgoi Processing Facility',   'Mining',         'Ömnögovi',     '2023'],
  ['National Water Treatment Works',   'Infrastructure', 'Darkhan',      '2023'],
  ['Erdenet Concentrator Expansion',   'Mining',         'Orkhon',       '2023'],
  ['Steppe Logistics Terminal',        'Industrial',     'Zamyn-Üüd',    '2022'],
  ['Government Archive Building',      'Civic',          'Ulaanbaatar',  '2022'],
  ['Baganuur Substation Upgrade',      'Energy',         'Baganuur',     '2022'],
  ['Selenge Grain Silo Complex',       'Industrial',     'Selenge',      '2021'],
  ['Choibalsan Ring Road',             'Infrastructure', 'Dornod',       '2021'],
  ['Khövsgöl Community Hospital',      'Civic',          'Mörön',        '2021'],
  ['Solar Array — Gobi Phase II',      'Energy',         'Dundgovi',     '2020'],
  ['Ulaanbaatar Transit Interchange',  'Infrastructure', 'Ulaanbaatar',  '2020'],
  ['Tavan Tolgoi Conveyor System',     'Mining',         'Ömnögovi',     '2020'],
  ['Municipal Sports Arena',           'Civic',          'Ulaanbaatar',  '2019'],
  ['Cement Works Modernisation',       'Industrial',     'Khötöl',       '2019'],
  ['Northern Grid Interconnector',     'Energy',         'Bulgan',       '2019'],
  ['Airport Cargo Handling Facility',  'Infrastructure', 'Ulaanbaatar',  '2018'],
];

const NEWS = [
  ['MGL E&C awarded Central Thermal Power Plant IV contract', 'Announcement', '12 June 2025',
   'The firm will lead structural and mechanical delivery on the largest energy project commissioned in the region this decade.'],
  ['Completing the Tuul River Crossing ahead of schedule', 'Project', '28 April 2025',
   'A 340-metre span delivered eleven weeks early through off-site fabrication and a revised launch sequence.'],
  ['On designing for permafrost: a technical note', 'Insight', '3 March 2025',
   'Our geotechnical team on the foundation strategies that hold up under Mongolian ground conditions.'],
  ['MGL E&C joins the National Infrastructure Council', 'Announcement', '19 January 2025',
   'Representation on the body shaping the next decade of public works procurement standards.'],
  ['Water treatment works reaches full operational capacity', 'Project', '7 December 2024',
   'The Darkhan facility now serves 180,000 residents, doubling the previous treatment throughput.'],
  ['Graduate engineering intake opens for 2025', 'Careers', '14 November 2024',
   'Twelve positions across structural, mechanical and geotechnical disciplines.'],
];

/* -------------------------------------------------------------- components */
const esc = s => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');

const NAV = [
  ['Projects', '/projects'],
  ['About',    '/about'],
  ['News',     '/news'],
  ['Contact',  '/contact'],
];

function head(title, opts = {}) {
  return `<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>${esc(title)} — MGL E&amp;C</title>
<!-- NOTE: user-scalable=no / maximum-scale=1 REMOVED. The old site blocked
     pinch-zoom, which is an accessibility failure and masked layout bugs. -->
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="description" content="MGL Engineering &amp; Construction — delivering energy, infrastructure and industrial projects across Mongolia.">
<meta name="theme-color" content="#003d59">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/mgl.css">
<link rel="stylesheet" href="/css/mgl.ui.css">
</head>
<body>
<a class="skip-link" href="#main">Skip to content</a>
${header(opts)}
${drawer()}
<main id="main" class="site-main${opts.hero ? ' site-main--hero' : ''}">`;
}

function header(opts = {}) {
  return `<header class="site-header${opts.hero ? ' is-over-hero' : ''}">
  <div class="navbar">
    <a class="navbar__logo" href="/" aria-label="MGL E&amp;C home">
      <img src="/assets/images/logoNew.svg" alt="MGL E&amp;C" width="120" height="32">
    </a>
    <nav class="navbar__links" aria-label="Primary">
      ${NAV.map(([l, h]) => `<a class="navbar__link"${opts.current === h ? ' aria-current="page"' : ''} href="${h}">${l}</a>`).join('\n      ')}
    </nav>
    <div class="navbar__actions">
      <div class="navbar__lang">
        <a href="#" aria-current="true">EN</a><span>/</span><a href="#">MN</a>
      </div>
      <button class="navbar__burger" type="button" aria-expanded="false" aria-controls="mobile-nav" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>`;
}

function drawer() {
  return `<nav class="mobile-nav" id="mobile-nav" aria-label="Mobile" aria-hidden="true">
  <ul class="mobile-nav__list">
    ${NAV.map(([l, h], i) => `<li class="mobile-nav__item">
      <a class="mobile-nav__link" href="${h}">${l}<span class="mobile-nav__num">0${i + 1}</span></a>
    </li>`).join('\n    ')}
  </ul>
  <div class="mobile-nav__foot">
    <div class="mobile-nav__social">
      <a href="https://www.facebook.com/mglengineer" target="_blank" rel="noopener">Facebook</a>
      <a href="#" target="_blank" rel="noopener">LinkedIn</a>
    </div>
    <p class="mobile-nav__meta">
      Ulaanbaatar, Mongolia<br>
      <a href="mailto:info@mglenc.com">info@mglenc.com</a>
    </p>
  </div>
</nav>`;
}

function footer() {
  return `</main>
<footer class="site-footer">
  <div class="wrap">
    <div class="footer-top">
      <div class="footer-brand">
        <img class="footer-brand__logo" src="/assets/images/logoNew.svg" alt="MGL E&amp;C" width="110" height="30">
        <p class="footer-brand__text">
          MGL Engineering &amp; Construction delivers energy, infrastructure and
          industrial projects across Mongolia — from feasibility through to
          commissioning.
        </p>
      </div>
      <div class="footer-col">
        <h3 class="footer-col__title">Company</h3>
        <div class="footer-col__list">
          <a href="/about">About</a><a href="/projects">Projects</a>
          <a href="/news">News</a><a href="/contact">Contact</a>
        </div>
      </div>
      <div class="footer-col">
        <h3 class="footer-col__title">Sectors</h3>
        <div class="footer-col__list">
          ${SECTORS.map(s => `<a href="/projects">${s}</a>`).join('')}
        </div>
      </div>
      <div class="footer-col">
        <h3 class="footer-col__title">Contact</h3>
        <div class="footer-col__list">
          <a href="mailto:info@mglenc.com">info@mglenc.com</a>
          <a href="/clientarea">Client area</a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© ${new Date().getFullYear()} MGL E&amp;C LLC. All rights reserved.</p>
      <div class="footer-social">
        <a href="https://www.facebook.com/mglengineer" target="_blank" rel="noopener">Facebook</a>
        <a href="#" target="_blank" rel="noopener">LinkedIn</a>
      </div>
    </div>
  </div>
</footer>
<script src="/js/mgl.js"></script>
</body>
</html>`;
}

/* card renderers ----------------------------------------------------------- */
function pcard(p, i, bare) {
  const [name, sector, loc, year] = p;
  const n = String(i + 1).padStart(2, '0');
  return `<a class="pcard" href="/project" data-sector="${sector}">
  <div class="pcard__media">
    <span class="pcard__index">${n}</span>
    <img src="${img(i)}" alt="${esc(name)}" loading="lazy" decoding="async">
    ${bare ? `<div class="pcard__overlay">
      <div class="pcard__overlay-name">${esc(name)}</div>
      <div class="pcard__overlay-meta">${sector} — ${loc}</div>
    </div>` : ''}
  </div>
  <div class="pcard__body">
    <h3 class="pcard__name">${esc(name)}</h3>
    <div class="pcard__meta"><span>${sector}</span><span>${loc}</span><span>${year}</span></div>
  </div>
</a>`;
}

/* ================================================================== PAGES == */

/* ---- Home ---------------------------------------------------------------- */
function pageHome() {
  const featured = PROJECTS.slice(0, 6);
  return head('Engineering & Construction', { hero: true, current: '/' }) + `
<section class="hero" data-hero>
  <div class="hero__media">
    ${[0, 5, 9].map((n, idx) => `<div class="hero__slide" style="position:absolute;inset:0;opacity:${idx ? 0 : 1};transition:opacity 1s var(--ease)" aria-hidden="${idx !== 0}">
      <img src="${img(n)}" alt="" ${idx ? 'loading="lazy"' : 'fetchpriority="high"'} decoding="async">
    </div>`).join('\n    ')}
  </div>
  <div class="hero__inner">
    <p class="hero__eyebrow">MGL Engineering &amp; Construction</p>
    <h1 class="hero__title">Building what Mongolia runs on.</h1>
    <p class="hero__lead">
      Energy, infrastructure and industrial projects delivered end to end —
      from feasibility and design through construction and commissioning.
    </p>
    <div class="hero__actions">
      <a class="btn btn--ghost-light btn--block" href="/projects">View our projects</a>
      <a class="btn btn--ghost-light btn--block" href="/about">About the firm</a>
    </div>
    <div class="hero__dots">
      ${[0, 1, 2].map(i => `<button class="hero__dot" type="button" aria-current="${i === 0}" aria-label="Slide ${i + 1}"></button>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head reveal">
      <div>
        <p class="eyebrow"><span class="eyebrow__num">01</span> Selected work</p>
        <h2 class="section-head__title" style="margin-top:var(--s-4)">Projects that had to hold.</h2>
      </div>
      <div class="section-head__aside">
        <a class="tlink" href="/projects">All projects <span class="tlink__arrow">&rarr;</span></a>
      </div>
    </div>

    <!-- THE FIX: one CSS Grid declaration, no MagicGrid, no fixed columns.
         Reflows cleanly at every width from 320px up. -->
    <div class="pgrid pgrid--wide reveal">
      ${featured.map((p, i) => pcard(p, i, false)).join('\n      ')}
    </div>
  </div>
</section>

<section class="section section--warm">
  <div class="wrap">
    <p class="eyebrow reveal"><span class="eyebrow__num">02</span> By the numbers</p>
    <div class="stats reveal" style="margin-top:var(--s-5)">
      ${[['180+', 'Projects delivered'], ['19', 'Years in operation'], ['240', 'Engineers &amp; staff'], ['6', 'Provinces served']]
        .map(([n, l]) => `<div class="stat"><div class="stat__num">${n}</div><div class="stat__label">${l}</div></div>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head reveal">
      <div>
        <p class="eyebrow"><span class="eyebrow__num">03</span> Capability</p>
        <h2 class="section-head__title" style="margin-top:var(--s-4)">What we do.</h2>
      </div>
    </div>
    <div class="caps reveal">
      ${[
        ['Structural engineering', 'Design and analysis for heavy industrial, civic and commercial structures under Mongolian seismic and thermal loading.'],
        ['Power &amp; energy systems', 'Generation, transmission and substation work — thermal, solar and grid interconnection.'],
        ['Infrastructure', 'Roads, bridges, water treatment and transport interchanges delivered to national standards.'],
        ['Geotechnical', 'Ground investigation and permafrost-aware foundation design across varied terrain.'],
        ['Construction management', 'Programme, cost and quality control from mobilisation to handover.'],
        ['Commissioning', 'Testing, validation and operational handover with full documentation.'],
      ].map(([t, d], i) => `<div class="cap">
        <div class="cap__num">${String(i + 1).padStart(2, '0')}</div>
        <div><h3 class="cap__title">${t}</h3><p class="cap__text">${d}</p></div>
      </div>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="wrap">
    <div class="section-head reveal">
      <div>
        <p class="eyebrow"><span class="eyebrow__num">04</span> Latest</p>
        <h2 class="section-head__title" style="margin-top:var(--s-4)">News &amp; insight.</h2>
      </div>
      <div class="section-head__aside">
        <a class="tlink" href="/news">All news <span class="tlink__arrow">&rarr;</span></a>
      </div>
    </div>
    <div class="nlist reveal">
      ${NEWS.slice(0, 3).map((n, i) => nrow(n, i)).join('\n      ')}
    </div>
  </div>
</section>
` + footer();
}

function nrow([title, cat, date, excerpt], i) {
  return `<a class="nrow" href="/article">
  <div class="nrow__media"><img src="${img(i + 21)}" alt="" loading="lazy" decoding="async"></div>
  <div class="nrow__body">
    <div class="nrow__meta"><span class="nrow__cat">${cat}</span><span>${date}</span></div>
    <h3 class="nrow__title">${esc(title)}</h3>
    <p class="nrow__excerpt">${esc(excerpt)}</p>
  </div>
  <div class="nrow__cta" aria-hidden="true">&rarr;</div>
</a>`;
}

/* ---- Projects index ------------------------------------------------------ */
function pageProjects() {
  return head('Projects', { current: '/projects' }) + `
<section class="page-head">
  <div class="wrap">
    <p class="eyebrow"><span class="eyebrow__num">01</span> Portfolio</p>
    <h1 class="page-head__title">Projects</h1>
    <p class="page-head__lead">
      ${PROJECTS.length} completed and active commissions across energy,
      infrastructure, mining, industrial and civic sectors.
    </p>
  </div>
</section>

<div class="filterbar">
  <div class="filterbar__inner">
    <div class="filterbar__scroll">
      <button class="fpill" type="button" data-filter="all" aria-pressed="true">
        All <span class="fpill__count">${PROJECTS.length}</span>
      </button>
      ${SECTORS.map(s => `<button class="fpill" type="button" data-filter="${s}" aria-pressed="false">
        ${s} <span class="fpill__count">${PROJECTS.filter(p => p[1] === s).length}</span>
      </button>`).join('\n      ')}
    </div>
    <div class="filterbar__count">${PROJECTS.length} projects</div>
  </div>
</div>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <!-- Image-forward, as requested — but with an index number, a consistent
         4:3 crop, an editorial header above and a caption on hover/touch.
         That ordering + hierarchy is what separates a company portfolio from
         a social-media wall. -->
    <div class="pgrid pgrid--bare pgrid--compact">
      ${PROJECTS.map((p, i) => pcard(p, i, true)).join('\n      ')}
    </div>
  </div>
</section>
` + footer();
}

/* ---- Project detail ------------------------------------------------------ */
function pageProject() {
  return head('Central Thermal Power Plant IV', { current: '/projects' }) + `
<div class="pdetail-hero"><img src="${img(0)}" alt="Central Thermal Power Plant IV" fetchpriority="high"></div>

<section class="pdetail-head">
  <div class="wrap">
    <p class="eyebrow"><span class="eyebrow__num">Energy</span> Ulaanbaatar — 2024</p>
    <h1 class="pdetail-head__title">Central Thermal Power Plant IV</h1>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="specs">
      ${[['Client', 'Ministry of Energy'], ['Sector', 'Energy'], ['Location', 'Ulaanbaatar'],
         ['Completed', '2024'], ['Capacity', '450 MW'], ['Floor area', '82,000 m²'],
         ['Duration', '38 months'], ['Role', 'Design &amp; build']]
        .map(([l, v]) => `<div class="spec"><div class="spec__label">${l}</div><div class="spec__value">${v}</div></div>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section" style="padding-top:var(--s-7)">
  <div class="wrap">
    <div class="prose reveal">
      <h2>Overview</h2>
      <p>
        Central Thermal Power Plant IV is the largest generation project commissioned
        in the region this decade. MGL E&amp;C led structural and mechanical delivery
        across a 38-month programme, working to a fixed commissioning date tied to
        the national winter heating schedule.
      </p>
      <p>
        The site sits on seasonally unstable ground, so the foundation strategy was
        resolved first: a piled raft tied into bedrock, designed to tolerate the
        freeze-thaw cycle without differential settlement under turbine loading.
      </p>
      <h3>Scope</h3>
      <ul>
        <li>Structural design and analysis of the turbine hall, boiler house and stack</li>
        <li>Geotechnical investigation and permafrost-aware foundation design</li>
        <li>Mechanical installation and pipework across the generation block</li>
        <li>Construction management, programme and cost control</li>
        <li>Testing, commissioning and operational handover</li>
      </ul>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <div class="gallery reveal">
      ${[1, 2, 3, 4, 5].map((n, i) => `<div class="gallery__item${i === 0 ? ' gallery__item--wide' : ''}">
        <img src="${img(n + 11)}" alt="" loading="lazy" decoding="async">
      </div>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section section--warm">
  <div class="wrap">
    <div class="section-head reveal">
      <div>
        <p class="eyebrow"><span class="eyebrow__num">Next</span> More work</p>
        <h2 class="section-head__title" style="margin-top:var(--s-4)">Related projects.</h2>
      </div>
      <div class="section-head__aside"><a class="tlink" href="/projects">All projects <span class="tlink__arrow">&rarr;</span></a></div>
    </div>
    <div class="pgrid pgrid--wide reveal">
      ${PROJECTS.slice(7, 10).map((p, i) => pcard(p, i + 7, false)).join('\n      ')}
    </div>
  </div>
</section>
` + footer();
}

/* ---- About (sections ABOVE the team block only) -------------------------- */
function pageAbout() {
  return head('About', { current: '/about' }) + `
<section class="page-head">
  <div class="wrap">
    <p class="eyebrow"><span class="eyebrow__num">01</span> About</p>
    <h1 class="page-head__title">Engineering, end to end.</h1>
  </div>
</section>

<section class="section" style="padding-top:var(--s-5)">
  <div class="wrap">
    <div class="about-intro reveal">
      <h2 class="about-intro__title">Nineteen years of building in hard conditions.</h2>
      <div class="about-intro__body">
        <p>
          MGL E&amp;C was founded in Ulaanbaatar in 2006 to deliver the kind of
          engineering Mongolia's growth demanded — power generation, transport
          infrastructure and heavy industrial facilities, built to hold under
          extreme thermal range and difficult ground.
        </p>
        <p>
          We work end to end. Feasibility, design, geotechnical investigation,
          construction management and commissioning sit under one roof, which
          means the people who set the design intent are the people who see it
          through to handover.
        </p>
      </div>
    </div>
  </div>
</section>

<section class="section section--tight">
  <div class="wrap">
    <div class="stats reveal">
      ${[['2006', 'Founded'], ['180+', 'Projects delivered'], ['240', 'Engineers &amp; staff'], ['6', 'Provinces served']]
        .map(([n, l]) => `<div class="stat"><div class="stat__num">${n}</div><div class="stat__label">${l}</div></div>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="band reveal">
      <div class="band__media"><img src="${img(4)}" alt="MGL E&amp;C on site" loading="lazy" decoding="async"></div>
      <div class="band__body">
        <p class="eyebrow"><span class="eyebrow__num">02</span> How we work</p>
        <h2>One team from feasibility to commissioning.</h2>
        <p style="color:var(--ink-70)">
          Fragmented delivery is where projects lose time and money. We keep
          design, geotechnical and construction management in the same team so
          decisions get made once, by people who carry them through.
        </p>
        <a class="tlink" href="/projects">See how that works <span class="tlink__arrow">&rarr;</span></a>
      </div>
    </div>
  </div>
</section>

<section class="section section--warm">
  <div class="wrap">
    <div class="section-head reveal">
      <div>
        <p class="eyebrow"><span class="eyebrow__num">03</span> Principles</p>
        <h2 class="section-head__title" style="margin-top:var(--s-4)">What we hold to.</h2>
      </div>
    </div>
    <div class="values reveal">
      ${[
        ['Design for the ground you have', 'Every site is investigated before anything is drawn. Permafrost, seismic loading and thermal range are inputs, not afterthoughts.'],
        ['One team, one accountability', 'The engineers who set the design intent stay on through construction and commissioning.'],
        ['Programme certainty', 'Fixed commissioning dates are treated as constraints, not targets. Sequencing is planned to absorb weather and supply risk.'],
        ['Documented handover', 'Clients receive complete as-built documentation, test records and operating procedures at handover.'],
        ['Local capability', 'We train and retain Mongolian engineers. Capability built here stays here.'],
        ['Safety without exception', 'No programme pressure justifies a shortcut. Zero-harm is the operating baseline.'],
      ].map(([t, d], i) => `<div class="value">
        <div class="value__num">${String(i + 1).padStart(2, '0')}</div>
        <h3 class="value__title">${t}</h3>
        <p class="value__text">${d}</p>
      </div>`).join('\n      ')}
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="section-head reveal">
      <div>
        <p class="eyebrow"><span class="eyebrow__num">04</span> History</p>
        <h2 class="section-head__title" style="margin-top:var(--s-4)">Where we've been.</h2>
      </div>
    </div>
    <div class="timeline reveal">
      ${[
        ['2006', 'Founded in Ulaanbaatar as a structural engineering consultancy.'],
        ['2011', 'First major energy commission — Baganuur substation works.'],
        ['2015', 'Geotechnical division established; in-house ground investigation capability.'],
        ['2019', 'Expanded into transport infrastructure with the Choibalsan Ring Road.'],
        ['2023', 'Delivered the National Water Treatment Works at Darkhan.'],
        ['2024', 'Central Thermal Power Plant IV commissioned — 450 MW.'],
      ].map(([y, t]) => `<div class="tl-item">
        <div class="tl-item__year">${y}</div>
        <div class="tl-item__text">${t}</div>
      </div>`).join('\n      ')}
    </div>
  </div>
</section>

<!-- ======================================================================
     TEAM SECTION AND EVERYTHING BELOW IT ARE DELIBERATELY NOT INCLUDED.
     Per the brief, the existing "Хамт олон" block and the sections beneath
     it stay exactly as they are in production. Only the sections ABOVE the
     team block have been redesigned. When porting, this new markup replaces
     the About page content down to — but not including — the team widget.
     ====================================================================== -->
<section class="section section--warm" style="text-align:center">
  <div class="wrap">
    <p class="eyebrow" style="justify-content:center"><span class="eyebrow__num">05</span></p>
    <p style="margin-top:var(--s-4);color:var(--ink-45);font-size:var(--fs-small);max-width:52ch;margin-inline:auto">
      Existing team section (“Хамт олон”) and all sections below it continue
      here unchanged — not part of this redesign.
    </p>
  </div>
</section>
` + footer();
}

/* ---- News list ----------------------------------------------------------- */
function pageNews() {
  return head('News', { current: '/news' }) + `
<section class="page-head">
  <div class="wrap">
    <p class="eyebrow"><span class="eyebrow__num">01</span> Newsroom</p>
    <h1 class="page-head__title">News &amp; insight</h1>
    <p class="page-head__lead">Announcements, project milestones and technical notes from the team.</p>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="wrap">
    <!-- Replaces MagicGrid masonry. An editorial row list reads as a company
         newsroom and — critically — reflows to a single readable column on a
         phone without absolute positioning. -->
    <div class="nlist">
      ${NEWS.map((n, i) => nrow(n, i)).join('\n      ')}
    </div>
    <div style="margin-top:var(--s-6);display:flex;justify-content:center">
      <button class="btn btn--block" type="button">Load more</button>
    </div>
  </div>
</section>
` + footer();
}

/* ---- Article ------------------------------------------------------------- */
function pageArticle() {
  const [title, cat, date] = NEWS[0];
  return head(title, { current: '/news' }) + `
<article>
  <section class="article-head">
    <div class="wrap wrap--text">
      <p class="eyebrow"><span class="eyebrow__num">${cat}</span></p>
      <h1 class="article-head__title">${esc(title)}</h1>
      <div class="article-head__meta">
        <span>${date}</span><span>4 min read</span><span>Share</span>
      </div>
    </div>
  </section>

  <div class="article-hero"><img src="${img(1)}" alt="" fetchpriority="high"></div>

  <section class="section">
    <div class="wrap wrap--text">
      <div class="prose">
        <p style="font-size:var(--fs-lead);color:var(--ink)">
          The firm will lead structural and mechanical delivery on the largest
          energy project commissioned in the region this decade.
        </p>
        <p>
          The Ministry of Energy has awarded MGL E&amp;C the structural and
          mechanical delivery package for Central Thermal Power Plant IV, a
          450 MW facility serving Ulaanbaatar and the surrounding aimags.
        </p>
        <h2>Scope of the award</h2>
        <p>
          The package covers the turbine hall, boiler house and stack, together
          with the full mechanical installation across the generation block. Work
          begins with ground investigation this quarter.
        </p>
        <p>
          <strong>Commissioning is fixed to the national winter heating schedule</strong>,
          which sets a hard programme constraint rather than a target date. Sequencing
          has been planned to absorb weather and supply risk without moving handover.
        </p>
        <h3>What happens next</h3>
        <ul>
          <li>Ground investigation and geotechnical modelling — Q3</li>
          <li>Foundation design sign-off — Q4</li>
          <li>Main structural works commence — Q1 following</li>
        </ul>
      </div>
    </div>
  </section>

  <section class="section section--warm">
    <div class="wrap">
      <div class="section-head reveal">
        <div>
          <p class="eyebrow"><span class="eyebrow__num">Next</span> Keep reading</p>
          <h2 class="section-head__title" style="margin-top:var(--s-4)">More news.</h2>
        </div>
        <div class="section-head__aside"><a class="tlink" href="/news">All news <span class="tlink__arrow">&rarr;</span></a></div>
      </div>
      <div class="nlist reveal">
        ${NEWS.slice(1, 4).map((n, i) => nrow(n, i + 1)).join('\n        ')}
      </div>
    </div>
  </section>
</article>
` + footer();
}

/* ------------------------------------------------------------------- write */
const pages = {
  'index.html'   : pageHome(),
  'projects.html': pageProjects(),
  'project.html' : pageProject(),
  'about.html'   : pageAbout(),
  'news.html'    : pageNews(),
  'article.html' : pageArticle(),
};

Object.entries(pages).forEach(([file, html]) => {
  fs.writeFileSync(path.join(OUT, file), html, 'utf8');
  console.log('  wrote ' + file.padEnd(16) + (html.length / 1024).toFixed(1) + ' KB');
});

console.log('\n  ' + IMAGES.length + ' real project images found in cpadmin/postpic/ceo');
console.log('  Done. Run:  node _redesign/serve.js\n');
