MGL E&C — Project detail: hero slider (v1)
==========================================

Юу өөрчлөгдсөн бэ:
  - Төслийн дэлгэрэнгүй хуудасны (/project/ID) доод талын 2 зурагтай grid
    (.pdetail-photos) АРИЛСАН.
  - Hero хэсэг нь одоо slider: тухайн төслийн БҮХ slide зургууд (CP Admin-ы
    ceoSlide дараалал) хуучин шигээ дарааллаараа гүйнэ.
      • 6 секунд тутам автоматаар дараагийн зураг руу гулсана (loop)
      • Хулганаар чирэх / утсан дээр swipe хийхэд баруун-зүүн солигдоно
      • Hover хийхэд автомат гүйлт түр зогсоно
      • Зургийн доод хэсэгт цэгэн (dots) заагч
  - Slide зураг 1 л байвал (эсвэл байхгүй бол нүүр зураг) хуучин шигээ
    энгийн нэг зураг харагдана — slider үүсэхгүй.
  - Owl Carousel-ийг home.php аль хэдийн CDN-ээс ачаалдаг тул шинэ
    library нэмэгдээгүй.

public_html дотор ижил замаар нь дарж хуулна (overwrite):

  assets/css/project-detail-hero.css   — Hero + slider + dots стиль
                                         (live дээрх "natural size" хувилбар
                                         дээр суурилсан, өргөн/өндөр нь
                                         өөрчлөгдөөгүй)
  pages/projects/sys.php               — $pdetailSlides (бүх slide зураг)
                                         бэлддэг болсон; $pdetailGallery устсан
  widgets/projectmore/sys.php          — more.js.php-г footer дээр оруулна
  widgets/projectmore/temp.php         — Hero slider markup; доод grid устсан
  widgets/projectmore/more.js.php      — Owl slider init

ЗААВАЛ 5 файлаа хамтад нь хуулна:
  temp.php нь sys.php-ийн $pdetailSlides-ийг хэрэглэдэг тул
  pages/projects/sys.php + widgets/projectmore/temp.php хоёрыг салгаж
  хуулбал хуудас алдаа өгнө.

Хуулсны дараа: Cloudflare cache purge (эсвэл Ctrl+Shift+R).

ШАЛГАХ:
  1. https://mglenc.com/assets/css/project-detail-hero.css нээгээд
     2-р мөрөнд "/* build: hero-slider-v1 */" байвал CSS зөв хуулагдсан.
  2. https://mglenc.com/project/12 (Encanto, 3 зурагтай) нээхэд hero зураг
     6 секундын дараа дараагийнх руу гулсаж, доор нь 3 цэг харагдана.
     Хулганаар зүүн тийш чирэхэд дараагийн зураг гарна.
  3. Хуудасны доод талд, тайлбар текстийн дараа зурагтай grid байхгүй байх.
