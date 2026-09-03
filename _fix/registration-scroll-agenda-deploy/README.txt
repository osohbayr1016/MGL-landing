MGL — АРГА ХЭМЖЭЭНИЙ БҮРТГЭЛ  |  SECTION SCROLL + AGENDA
=========================================================

Энэ багц нь /registration хуудсанд хийгдсэн шинэчлэлтийг агуулна (scroll animation засвар орсон):

  1) Section scroll — дэвсгөр fixed, текст slide animation
  2) Agenda блок — Огноо / Цаг / Байршил + rich text хөтөлбөр

  SQL ажиллуулах        ШААРДЛАГАГҮЙ
  .htaccess засах       ШААРДЛАГАГҮЙ
  Зөвхөн extract хийнэ


1. ХУУЛАХ
---------
cPanel -> File Manager -> public_html -> Upload -> энэ zip
-> Extract -> зам нь /public_html мөн эсэхийг шалгана
-> дуусмагц zip-ээ устгана

Хуулагдах файлууд (16):

  class/registration.class.php              (agenda block type нэмэгдсэн)

  pages/registration/inc/scroll-section.php   <- ШИНЭ
  pages/registration/blocks/hero.php
  pages/registration/blocks/info.php
  pages/registration/blocks/text.php
  pages/registration/blocks/image.php
  pages/registration/blocks/gallery.php
  pages/registration/blocks/countdown.php
  pages/registration/blocks/html.php
  pages/registration/blocks/form.php
  pages/registration/blocks/agenda.php        <- ШИНЭ

  skin/new/registration.php                   (шинэ CSS/JS ачаалалт)

  assets/css/registration-scroll.css          <- ШИНЭ
  assets/css/registration-agenda.css          <- ШИНЭ
  assets/css/registration-edit.css            (edit горимд animation идэвхгүй)
  assets/js/registration-scroll.js            <- ШИНЭ


2. AGENDA БЛОК НЭМЭХ (одоогийн сайт дээр)
-----------------------------------------
CP Admin -> Арга хэмжээний бүртгэл -> Хуудасны дизайн
-> "Хөтөлбөр (agenda)" блок нэмнэ
-> Дэд мөр бүрт: Огноо, Цаг, Байршил + Агуулга (editor)

Шинэ install-д defaultBlocks-д жишээ хөтөлбөр автоматаар орно.
Одоо ажиллаж байгаа сайт дээр гараар нэмнэ.


3\. SCROLL ЗАСВАР
-----------------
  - Текст эхэнд харагдаад алга болох асуудлыг зассан
  - Scroll хийхэд харагдаж байгаа section-ийн текст үлдэнэ
  - Дээш/доош scroll-д текст slide up/down хийнэ

4. ШАЛГАХ
---------
  - https://YOUR-DOMAIN/registration нээнэ
  - Scroll хийхэд section бүрт текст доороос slide up хийнэ
  - Дэвсгэр зураг/өнгө section дотор fixed харагдана
  - Form section scroll, submit ажиллана
  - Live edit (?edit=1) — animation идэвхгүй, текст засварлагдана
  - Agenda: 3 тэнцүү багана (mobile дээр 1 багана)


5. АНХААР
---------
  - Бүртгэлийн модуль суулгаагүй бол эхлээд registration-full.zip
    багцыг суулгана
  - spacer блок scroll animation-д ороогүй (хоосон snap point үүсгэхгүй)
  - prefers-reduced-motion идэвхтэй хэрэглэгчид animation идэвхгүй

