MGL E&C — САЙТЫН НЭГДСЭН ШИНЭЧЛЭЛТ (бүх өөрчлөлт нэг багцад)
============================================================

Энэ багц нь ажлын хавтас дээр хийгдсэн БҮХ шинэчлэлтийг агуулна.
Файлуудыг public_html дотор ЯГ ХЭВЭЭР нь (хавтасны бүтэц хадгалж) хуулна.

Нийт 52 файл. Дотор нь дараах 5 ажил багтсан:


1. ДАДЛАГА / АЖЛЫН БАЙРНЫ ӨРГӨДӨЛ  (шинэ модуль)
------------------------------------------------
Асуудал: /page/7 ("Apply for Internship") хоосон, /page/6 (APPLY NOW) дээр
зөвхөн mailto линк байсан тул ирсэн хүсэлт CP Admin дээр огт харагддаггүй байв.

Одоо: сайтад форм нэмэгдэж, хүсэлт бүр DB-д хадгалагдаж, CP Admin -> "Дадлага"
цэсээс харагдана.

  Хаяг:      /internship  (дадлага),  /career  (ажлын байр)
             /page/7, /page/6 дээр ч форм автоматаар гарна.
  CP Admin:  Дадлага -> Ирсэн хүсэлт / Формын асуулт / Тохиргоо

  Файлууд:   class/apply.class.php
             pages/apply/            (4 файл)
             assets/css/apply.css
             cpadmin/pages/internship/   (12 файл)
             pages/page/sys.php, page.php, onepage.php   (СОЛИНО)

  DB:        db_apply_setting / db_apply_field / db_apply_entry —
             хуудсыг анх нээхэд АВТОМАТААР үүснэ, SQL ажиллуулах шаардлагагүй.
             Хавсралт cpadmin/postpic/apply/ дотор хадгалагдана (уг хавтас ба
             гаднаас татахыг хориглосон .htaccess нь автоматаар үүснэ).

  Дэлгэрэнгүй заавар: _fix/internship-deploy/README.txt


2. ХАРИЛЦАГЧ БАЙГУУЛЛАГА  (шинэ хэсэг)
--------------------------------------
  CP Admin:  Мэдээлэл оруулах -> Харилцагч байгууллага (лого + вэб хаяг)
  Сайт:      widgets/pagesch/wid4.php дээрх лого хэсэг, шинэ partners.css
  Файлууд:   cpadmin/pages/insert/clients.*.php  (6 файл)
             cpadmin/pages/insert/sys.php, post.sys.php   (СОЛИНО)
             assets/css/partners.css, widgets/pagesch/wid4.php, sys.php
             functions.php, cpadmin/functions.php  (partnerLinkFnc нэмэгдсэн —
             админ "mglenc.com" гэж бичсэн ч гадаад хаяг руу зөв очно)


3. ERP НЭВТРЭЛТ
---------------
  Толгой цэсэн дэх "Нэвтрэх" товч, нэвтрэх цонх ба Worker руу дамжуулах логик.
  Файлууд:   erp.login.php, class/erp.auth.class.php,
             skin/new/erp-login.php, assets/css/erp-login.css,
             assets/js/erp-login.js, skin/new/header.php, home.php
  (Хэрэв энэ хэсэг серверт аль хэдийн байгаа бол зүгээр л дарж бичигдэнэ.)


4. ЦЭСНИЙ НЭР
-------------
  "ОФФИС" -> "ПРОФАЙЛ" (EN: OFFICE -> PROFILE) болж харагдана.
  functions.php доторх menuNameFunc(), skin/new/header.php.
  DB дэх цэсний нэрийг өөрчлөөгүй — зөвхөн харагдах нэр солигдоно.


5. НҮҮР ХУУДАСНЫ ТӨСЛҮҮД
------------------------
  Гарчгийг зүүн тийш шилжүүлж, хэмжээг нь багасгав.
  assets/css/home-projects.css


ЗАМЫН ТОХИРГОО (.htaccess)
--------------------------
Багц дахь .htaccess файлууд нь шинэ замуудыг агуулна. Хуучныг нь нөөцөлж
байгаад солино. Өөрөө засах бол зөвхөн эдгээрийг нэмнэ:

  public_html/.htaccess   —  "^n/([0-9]+)" мөрийн ӨМНӨ:
      RewriteRule ^internship(/?)$ index.php?incPageType=apply&applyType=intern [L,QSA]
      RewriteRule ^career(/?)$ index.php?incPageType=apply&applyType=job [L,QSA]

  cpadmin/.htaccess       —  "^registration/..." мөрүүдийн ДАРАА:
      RewriteRule ^internship/([^/]+)/([0-9]+)(/?)+$ index.php?incPageType=internship&subPage=$1&objID=$2 [L,QSA]
      RewriteRule ^internship/([^/]+)(/?) index.php?incPageType=internship&subPage=$1 [L,QSA]

cpadmin/router.php нь зөвхөн локал хөгжүүлэлтэд хэрэгтэй (сервер дээр
.htaccess ажилладаг тул хуулсан ч хамаагүй).


ХУУЛСНЫ ДАРАА ШАЛГАХ
--------------------
  1. https://mglenc.com/internship — форм гарч байна уу?
  2. Туршилтын хүсэлт илгээх (PDF хавсаргаад).
  3. CP Admin -> Дадлага -> Ирсэн хүсэлт дээр харагдаж байна уу?
     Дэлгэрэнгүй нээж, хавсралтаа татаж, төлөв солиод, Excel татаж үзнэ.
     Туршилтын бичлэгээ дараа нь устгана.
  4. CP Admin -> Мэдээлэл оруулах -> Харилцагч байгууллага нээгдэж байна уу?
  5. Нүүр хуудас, /about, /page/6, /page/7 хуудсууд хэвийн ачаалж байна уу?
  6. Толгой цэсэн дэх "Нэвтрэх" товч ажиллаж байна уу?

ЮУГ ХӨНДӨӨГҮЙ ВЭ
----------------
  - const.php, config.php (DB нууц үг) — багцад ОРООГҮЙ, серверийнх хэвээр.
  - Арга хэмжээний бүртгэлийн модуль (/registration) огт өөрчлөгдөөгүй.
  - Байгаа зураг, өгөгдлийн сангийн агуулга.
