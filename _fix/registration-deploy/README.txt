АРГА ХЭМЖЭЭНИЙ БҮРТГЭЛИЙН МОДУЛЬ — суулгах заавар
==================================================

Хуудасны хаяг:  https://mglenc.com/registration
CP Admin цэс:   "Арга хэмжээний бүртгэл"


1. ФАЙЛУУДЫГ БАЙРЛУУЛАХ
-----------------------
Энэ хавтасны доторх бүтцийг public_html дотор ЯГ ХЭВЭЭР нь хуулна:

  class/registration.class.php        (шинэ)
  class/xlsx.writer.class.php         (шинэ)
  pages/registration/                 (шинэ хавтас — sys.php + blocks/)
  skin/new/registration.php           (шинэ)
  assets/css/registration.css         (шинэ)
  assets/js/registration.js           (шинэ)
  cpadmin/pages/registration/         (шинэ хавтас)
  cpadmin/user.info.php               (СОЛИНО — цэс, эрх нэмэгдсэн)
  cpadmin/router.php                  (СОЛИНО — зөвхөн локал хөгжүүлэлтэд)
  .htaccess                           (СОЛИНО — /registration зам нэмэгдсэн)
  cpadmin/.htaccess                   (СОЛИНО — админы зам нэмэгдсэн)

.htaccess файлуудыг солихоос өмнө хуучныг нь нөөцлөх нь зүйтэй.
Хэрэв та өөрөө .htaccess-ээ засах бол зөвхөн доорх мөрүүдийг нэмнэ:

  public_html/.htaccess   —  "RewriteRule ^home" мөрийн ДАРАА:
      RewriteRule ^registration(/?)$ index.php?incPageType=registration [L,QSA]

  cpadmin/.htaccess       —  "^insert/..." мөрүүдийн ДАРАА:
      RewriteRule ^registration/([^/]+)/([0-9]+)(/?)+$ index.php?incPageType=registration&subPage=$1&objID=$2 [L,QSA]
      RewriteRule ^registration/([^/]+)(/?) index.php?incPageType=registration&subPage=$1 [L,QSA]


2. ӨГӨГДЛИЙН САН
----------------
Хүснэгтүүд CP Admin дээр модулийг АНХ НЭЭХЭД автоматаар үүснэ.
Гараар үүсгэх бол phpMyAdmin дээр _sql/registration.sql-ийг ажиллуулна.

Үүсэх хүснэгтүүд:
  db_reg_setting   — тохиргоо, өнгө, мессежүүд
  db_reg_field     — формын талбарууд
  db_reg_block     — хуудасны дизайны блокууд
  db_reg_entry     — бүртгүүлсэн хүмүүс

const.php-г ЗАСАХ ШААРДЛАГАГҮЙ (хүснэгтийн нэрийг $tbl_pref-ээс автоматаар
гаргадаг). Хүсвэл const.php-д доорх мөрүүдийг нэмж болно:

  $db_reg_setting = $tbl_pref.'reg_setting';
  $db_reg_field   = $tbl_pref.'reg_field';
  $db_reg_block   = $tbl_pref.'reg_block';
  $db_reg_entry   = $tbl_pref.'reg_entry';


3. ЭХНИЙ ТОХИРУУЛГА
-------------------
  1) CP Admin -> Арга хэмжээний бүртгэл -> Тохиргоо
     - Арга хэмжээний нэр, болох огноо, байршил
     - Вэб сайтын үндсэн хаяг (QR код үүсгэхэд ашиглана)
     - Багтаамж, бүртгэл эхлэх/дуусах огноо
  2) Формын талбар — Нэр / Утас / И-мэйл бэлэн. Шинээр нэмж болно.
  3) Хуудасны дизайн — блокуудыг нэмж, зураг, текст, өнгийг тохируулна.
  4) https://mglenc.com/registration -г нээж шалгана.
  5) Тэр хаягаас QR код үүсгэнэ.


4. ЮУГ АНХААРАХ
---------------
  - Хуудас сайтын цэс, footer-т ХАРАГДАХГҮЙ. noindex, nofollow тавьсан тул
    Google-д индекслэгдэхгүй. Зөвхөн шууд линк / QR-аар нээгдэнэ.
  - Bold Montserrat шрифт ашигласан. Гарчгийн зузаан, хэмжээ, том үсэг
    бүгд CP Admin -> Хуудасны дизайн -> Загвар хэсгээс өөрчлөгдөнө.
  - "Нэмэлт CSS" талбар нь бүх стандарт загварыг дарж бичдэг тул
    дизайнер хүссэнээрээ өөрчилж чадна.
  - Excel татах: Бүртгэлийн жагсаалт -> "Excel татах (.xlsx)".
    Багана нь "Формын талбар" хэсгээс автоматаар үүсдэг — шинэ талбар
    нэмэхэд Excel-д ч шууд нэмэгдэнэ.
  - Робот хамгаалалт: нуугдмал талбар (honeypot), хэт хурдан илгээлт,
    нэг IP-ээс цагт 10 удаагийн хязгаар. Captcha хэрэглээгүй.
  - Админы эрх: ямар нэг эрхтэй нэвтэрсэн админ бүрт автоматаар нээгдэнэ.
    Хязгаарлах бол Хандах эрх -> Эрхийн бүлэг хэсгээс тохируулна.


5. АЖИЛЛАХГҮЙ БОЛ — ОНОШЛОГОО
------------------------------
Багцад regcheck.php орсон. Үүнийг public_html дотор (index.php-ийн хажууд)
байрлуулаад https://mglenc.com/regcheck.php хаягаар нээнэ. Тэр нь:

  - PHP хувилбар, файл бүр байрласан эсэх
  - .htaccess дүрэм нэмэгдсэн эсэх
  - cpadmin/user.info.php ШИНЭ хувилбар мөн эсэх
  - өгөгдлийн сангийн хүснэгтүүд, CREATE эрх
  - админы эрхийн бүлгүүд
  - /registration хуудсыг ЖИНХЭНЭЭР ачаалж, нуугдсан fatal алдааг харуулна

Гарсан текстийг бүхэлд нь илгээнэ үү.
ШАЛГАЖ ДУУССАНЫ ДАРАА regcheck.php-Г УСТГАНА (мэдээлэл задруулдаг).


6. GIT-ЭЭР DEPLOY ХИЙДЭГ БОЛ — АНХААР
--------------------------------------
Шинэ файлууд git-д хараахан commit хийгдээгүй (untracked) байгаа:

  class/registration.class.php
  class/xlsx.writer.class.php
  pages/registration/
  skin/new/registration.php
  assets/css/registration.css
  assets/js/registration.js
  cpadmin/pages/registration/

Хэрэв та git pull-аар deploy хийдэг бол ЭДГЭЭР ФАЙЛ ОГТ ОЧИХГҮЙ.
Эхлээд commit хийнэ:

  git add class/registration.class.php class/xlsx.writer.class.php \
          pages/registration skin/new/registration.php \
          assets/css/registration.css assets/js/registration.js \
          cpadmin/pages/registration _sql .htaccess cpadmin/.htaccess \
          cpadmin/user.info.php cpadmin/router.php
  git commit -m "Add event registration module"

Анхаар: const.php болон cpadmin/const.php нь .gitignore-д байдаг тул
git-ээр ЯВАХГҮЙ. Гэхдээ тэднийг засах шаардлагагүй (2-р хэсгийг үзнэ үү).
