MGL E&C — Мэдээний social share preview (Open Graph / Twitter card)
====================================================================

Юуг шийдэж байгаа вэ:
  Мэдээний холбоосыг (https://mglenc.com/news/28 эсвэл /n/28) Facebook,
  Messenger, LinkedIn, X, Telegram, Discord дээр хуваалцахад одоо бүх
  мэдээ "MGL E&C LLC" гэсэн ижил гарчиг, нүүр хуудасны og:url, http://
  зурагтай гардаг байсан. Одоо мэдээ бүр ӨӨРИЙН гарчиг, товч тайлбар,
  нүүр зураг, canonical хаяг, огноо, ангилалтай share card гаргана.
  Бүх tag сервер талд (PHP) шууд HTML-д бичигдэх тул crawler JS
  ажиллуулахгүйгээр уншина.

  Нүүр зураггүй мэдээнд assets/images/og-default.png (1200x630, MGLENC
  лого) автоматаар орно. Устгагдсан / байхгүй мэдээ 404 + noindex.


1. ФАЙЛУУДЫГ БАЙРЛУУЛАХ
-----------------------
Энэ хавтасны доторх бүтцийг public_html дотор ЯГ ХЭВЭЭР нь хуулна:

  class/seo.class.php            (ШИНЭ — бүх мета энд бүрддэг)
  assets/images/og-default.png   (ШИНЭ — өгөгдмөл share зураг 1200x630)
  config.php                     (СОЛИНО — seo.class.php-г include хийнэ)
  site.info.php                  (СОЛИНО — сайтын өгөгдмөл мета $gloMeta,
                                  мэдээний хэлээр сайтын хэл тохируулна)
  skin/new/home.php              (СОЛИНО — <head> дотор SeoMeta::render())
  widgets/newsmore/sys.php       (СОЛИНО — мэдээний мета, 404)
  widgets/newsmore/temp.php      (СОЛИНО — байхгүй мэдээнд 404 хэсэг)
  functions.php                  (СОЛИНО — absUrl() https канон хаяг өгнө)

config.php, site.info.php, skin/new/home.php, widgets/newsmore/sys.php,
class/seo.class.php ТАВУУЛААГ ЗААВАЛ ХАМТ хуулна — аль нэг нь дутвал
"Class SeoMeta not found" алдаа гарна.

АНХААР: skin/new/home.php болон functions.php нь энэ репод хийгдсэн БУСАД
ажлыг (apply.css блок, menuNameFunc ОФФИС→ПРОФАЙЛ) бас агуулна. Тэдгээрийг
серверт гаргаагүй бол бүтнээр нь солихын оронд зөвхөн доорхийг нэмнэ:

  skin/new/home.php   —  <title> ... <meta itemprop="image"> хүртэлх хуучин
                         бүх мөрийг устгаад, оронд нь энэ багц дахь
                         home.php-ийн "<?php /* Title / description ..."
                         блокийг (echo SeoMeta::render($gloMeta); хүртэл)
                         тавина. Мөн <html lang="en"> мөрийг энэ багцынхаар
                         солино (заавал биш).

  functions.php       —  absUrl() функц дотор return-ийн ӨМНӨ:
                             if(class_exists("SeoMeta"))
                                 return SeoMeta::absUrl($url);
                         (заавал биш — page/N хуудсуудын зураг https болно)


2. const.php — НЭГ МӨР НЭМНЭ (гараар)
-------------------------------------
const.php нь серверийн нууц (DB) утгатай тул энэ багцад ОРООГҮЙ.
public_html/const.php дотор $gloConstSiteURL мөрийн ДАРАА нэмнэ:

    $gloConstSiteBaseUrl = 'https://mglenc.com';

Энэ нь canonical, og:url, og:image-ийн үндсэн хаяг. Бичээгүй бол
хүсэлтийн host-оор https:// хаяг үүсгэнэ (www.mglenc.com-оор орвол
www-тэй canonical гарна) — тиймээс бичих нь зүйтэй.


3. ӨГӨГДЛИЙН САН
----------------
Юу ч хийх шаардлагагүй. Одоо байгаа талбаруудыг ашиглана:

  newsTitle    -> <title>, og:title, twitter:title
  newsDesc     -> description, og:description (хоосон бол newsBody-с
                  200 тэмдэгт, HTML-гүй)
  зураг        -> /newsimg/news/{ID}.jpg (cpadmin дээр оруулсан "Зураг");
                  файл байхгүй бол og-default.png
  createDate   -> article:published_time
  updateDate   -> article:modified_time
  newsCat      -> article:section
  newsSubCatn  -> article:tag
  lang         -> og:locale (MN -> mn_MN, EN -> en_US), <html lang>

CP Admin-д шинэ талбар нэмээгүй. "Мэдээний товч хэсэг"-ийг бөглөвөл
тэр нь share card-ын тайлбар болно.


4. ШАЛГАХ (хуулсны дараа)
-------------------------
Terminal (Git Bash) дээр:

  curl -sA "facebookexternalhit/1.1" https://mglenc.com/news/28 | grep -i "og:\|twitter:\|canonical\|<title>"

Гарах ёстой:
  <title>         — тухайн мэдээний гарчиг (MGL E&C LLC БИШ)
  og:type         — article
  og:url          — https://mglenc.com/news/28   (canonical-тай ижил)
  og:image        — https://mglenc.com/newsimg/news/28.jpg  (https!)
  twitter:card    — summary_large_image

Өөр хоёр мэдээ (/news/25, /news/22) дээр давтаж, гарчиг өөр өөр гарч
буйг харна. Байхгүй дугаар (/news/99999) 404 буцаах ёстой:

  curl -s -o /dev/null -w "%{http_code}\n" https://mglenc.com/news/99999

Зураг нэвтрэлтгүй нээгдэж буйг:

  curl -sI https://mglenc.com/assets/images/og-default.png | head -3
  curl -sI https://mglenc.com/newsimg/news/28.jpg | head -3


5. SOCIAL CACHE ЦЭВЭРЛЭХ
------------------------
Social сүлжээнүүд preview-г кэшэлдэг. Хуулсны дараа өмнө нь хуваалцсан
мэдээний холбоос хуучнаараа харагдаж болно — доорх хэрэгслээр шинэчилнэ:

  Facebook / Messenger:  https://developers.facebook.com/tools/debug/
                         (URL оруулаад "Scrape Again")
  LinkedIn:              https://www.linkedin.com/post-inspector/
  X / Twitter:           card-аа автоматаар шинэчилдэг (~7 хоног);
                         шинэ post дээр шууд шинээр татна
  Telegram:              @WebpageBot-д холбоосыг илгээнэ -> кэш шинэчилнэ
  Discord:               кэш ~30 мин; шинэ мессежид шинээр татна

Мэдээний гарчиг/зургаа CP Admin дээр өөрчилсний дараа ч дээрхийг
хийвэл шинэ утгаар харагдана. HTML нь үргэлж DB-ийн хамгийн сүүлийн
утгыг гаргана (кэшгүй). Cloudflare ашиглаж байвал HTML cache purge.


6. ЮУ ӨӨРЧЛӨГДӨӨГҮЙ
-------------------
  - Мэдээний жагсаалт, дэлгэрэнгүйн харагдах байдал, /n/{id} хаяг —
    хэвээр. (/n/{id} ажиллана, гэхдээ canonical/og:url нь /news/{id}.)
  - Нүүр болон бусад хуудас сайтын өгөгдмөл мета (site_name, siteDes,
    og-default.png)-г хэрэглэнэ — мэдээний мета тэнд орохгүй.
  - registration, clientarea хуудсууд өөрийн <head>-тэй тул хамаагүй.
