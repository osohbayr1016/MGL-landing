MGL E&C — home news carousel deploy
===================================

public_html дотор ижил замаар нь дарж хуулна (overwrite):

  assets/js/all.js                       — Мэдээ карусел: 992px+ дээр 4 карт,
                                            768-991 → 3, 560-767 → 2, доош нь 1.
                                            Auto-slide 5 сек, 1200мс зөөлөн шилжилт,
                                            resize хийсний дараа автоматаар үргэлжилнэ.
  assets/css/home-news.css               — картын зураг 16:10, desktop-ийн padding/
                                            гарчгийн хэмжээ 992px-ээс эхэлнэ.
  assets/css/mobile/content.css          — 560-768px дээр 2 карт зэрэгцэхэд картын
                                            хажуугийн padding сэргээв.
  skin/new/home.php                      — all.js?v=4 -> v=5 (кэш шинэчлэх). ЗААВАЛ.

Мөн working tree-д байсан бусад засварууд (мэдээний хэсэгт хамаагүй):
  assets/css/style.css
  assets/css/home-projects-marquee.css
  assets/css/mobile/home.css
  assets/css/mobile/projects.css

Хуулсны дараа: Cloudflare cache purge хийх (эсвэл Ctrl+F5).
