MGL E&C — home projects padding (v4)  — ЭНЭ ХУВИЛБАРЫГ АШИГЛАНА
==============================================================

Юу байсан бэ:
  Live сайт нь marquee (гүйдэг) биш, СТАТИК GRID ашигладаг байсан
  (.home-projects-grid-full). Өмнөх v1-v3 zip доторхи home-projects.css нь
  marquee-гийн хувилбар байсан тул grid-ийн бүх дүрэм (padding ороод)
  алга болж, тиймээс хажуугийн зай гарахгүй байсан юм.

public_html дотор ижил замаар нь дарж хуулна (overwrite):

  assets/css/home-projects.css           — ГОЛ ФАЙЛ. Grid-ийн бүх дүрэм
                                           буцаж сэргэв + хажуугийн зай 48px,
                                           зураг хоорондын зай 24px (effekt.dk-тай
                                           ижил). ТӨСЛҮҮД гарчиг бас ижил 48px
                                           шугамаас эхэлнэ.
  assets/css/home-projects-marquee.css   — Live дээр одоогоор ашиглагдахгүй,
                                           гэхдээ хуулсан ч асуудалгүй.
  functions.php                          — menuNameFunc(): ОФФИС → ПРОФАЙЛ.
  skin/new/header.php                    — цэсэнд menuNameFunc() хэрэглэнэ.

functions.php ба skin/new/header.php хоёрыг ЗААВАЛ хамтад нь хуулна.
Хуулсны дараа: Cloudflare cache purge (эсвэл Ctrl+Shift+R).

ШАЛГАХ: https://mglenc.com/assets/css/home-projects.css нээгээд
  2-р мөрөнд "/* build: gutter-v4 ... */" байвал зөв хуулагдсан.

1920px дэлгэц дээр: зүүн/баруун зай 48px, зураг хооронд 24px.
