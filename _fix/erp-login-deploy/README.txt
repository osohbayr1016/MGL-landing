MGL E&C — ERP нэвтрэлт (толгойн "Нэвтрэх" товч) deploy
=====================================================

Юу хийдэг вэ
------------
Сайтын толгойд "Нэвтрэх" товч гарна. Дарахад хуудас дээрээ модал цонх
нээгдэж нэвтрэх нэр/нууц үг асууна. Илгээхэд манай PHP сервер тэр мэдээллийг
ERP-ийн Cloudflare Worker руу server-to-server дамжуулж шалгуулна. Worker
зөвшөөрвөл буцаасан token-той нь https://mgl-design-system.pages.dev/app
рүү шилжинэ. Буруу бол цонхон дотор алдаа гарч, хуудас солигдохгүй.

Нууц үг энэ сервер дээр ХАДГАЛАГДАХГҮЙ, ШАЛГАГДАХГҮЙ. mglenc.com-ын
MySQL (db_user) огт хөндөгдөөгүй, /clientarea хэвээрээ ажиллана.


public_html дотор ижил замаар нь дарж хуулна (overwrite)
--------------------------------------------------------

  erp.login.php                — POST endpoint. CSRF шалгах, оролдлого
                                 хязгаарлах, Worker руу дамжуулах, JSON
                                 хариу буцаах.
  class/erp.auth.class.php     — ErpAuth класс: Worker дуудлага, хариу задлах,
                                 redirect URL бүрдүүлэх, brute-force тоолуур.
  skin/new/erp-login.php       — Модал цонхны markup + CSRF token үүсгэлт.
  skin/new/header.php          — Толгойд <li class="erp-login-li"> товч нэмэв.
  skin/new/home.php            — erp-login.css холбов, модал + erp-login.js-ийг
                                 </body>-ийн өмнө нэмэв. ЗААВАЛ.
  assets/css/erp-login.css     — Товч болон цонхны загвар (сайтын монохром хэв).
  assets/js/erp-login.js       — Нээх/хаах, fetch, алдаа харуулах, redirect.
  preview-login.php            — Заавал биш. Локал дизайн шалгах хуудас:
                                 php -S localhost:8000 → /preview-login.php

  ERP-TALD-NEMEH.js            — Сервер рүү хуулах биш. ERP-ийн /app дээр
                                 нэмэх ~10 мөр код (token-ыг хүлээж авах).


const.php — ГАР ХҮРЭХ ШААРДЛАГАГҮЙ
---------------------------------
Ажиллах бүх утга class/erp.auth.class.php дотор өгөгдмөлөөр байна:

	appUrl      https://mgl-design-system.pages.dev/app
	loginUrl    https://mgl-progress-api.mglenc-design.workers.dev/api/auth/login
	userKey     username        (Worker нь email биш username авдаг)
	passKey     password
	tokenPath   token           (хариу нь {user, token})
	tokenParam  access_token    (ERP fragment-ээс үүнийг уншина)
	tokenMode   fragment

Эдгээр нь нууц утга биш тул git-д ордоггүй const.php-д байх шаардлагагүй.
Файлуудыг хуулаад л ажиллана.

Өгөгдмөлөөс өөрчлөх бол const.php дээр ижил нэртэй хувьсагч зарлана:

	$gloErpTokenMode	= "none";	/* ERP тал token уншиж эхлэх хүртэл */
	$gloErpApiKey		= "...";	/* Worker талд X-Api-Key шалгуулах бол */
	$gloErpCaBundle		= "...";	/* CA алдаа гарвал cacert.pem-ийн зам */

Хоосон мөр ($gloErpLoginUrl = "" гэх мэт) бичвэл ҮЛ ТООМСОРЛОЖ өгөгдмөлөө
хэрэглэнэ — хагас бөглөсөн блок ажиллаж байсныг эвдэхгүй.

Worker талын баталгаажуулсан зан төлөв
-------------------------------------
Endpoint: POST https://mgl-progress-api.mglenc-design.workers.dev/api/auth/login

  Хүсэлт:  {"username": "...", "password": "..."}
           ANHAARAH: талбар нь "email" биш "username". "email" илгээхэд
           400 {"error":"Username and password are required"} буцаадгийг
           шалгаж тогтоов.

  Буруу:   401 {"error":"Invalid credentials"}
  Зөв:     2xx + {"user": {...}, "token": "..."}
           (backend/src/routes/auth.ts:70 — c.json({ user: profile, token }))

  Кодын өгөгдмөл нь үүнд аль хэдийн таарсан: tokenPath = "token"
  (Worker юу буцаадаг), tokenParam = "access_token" (ERP рүү явуулах
  хаягийн параметр). Энэ хоёр өөр зүйл — хольж const.php дээр дарж
  бичвэл error_log-д "worker 200 but no token at path ..." гарна.

Нэмэлт толгойнууд: X-Client-IP, X-Forwarded-For — хэрэглэгчийн жинхэнэ IP.
$gloErpApiKey тохируулж, Worker талд X-Api-Key-г шалгуулбал энэ endpoint-ыг
зөвхөн mglenc.com дуудаж чадах болно (одоогоор хоосон).


ИРЭЭДҮЙД: оффисын IP шалгалт асаах үед
--------------------------------------
Backend-ийн getClientIp() нь cf-connecting-ip-г тэргүүнд уншдаг. mglenc.com-оор
дамжсан нэвтрэлтэд тэр утгыг Cloudflare нь hosting серверийн IP-гээр дардаг —
хэрэглэгчийн жинхэнэ IP биш. Одоо OFFICE_ACCESS_ENFORCED = "0" (wrangler.toml:79)
тул нөлөөгүй.

Уг шалгалтыг асаавал mglenc.com-оор нэвтэрсэн БҮХ хүн hosting-ийн IP-гээр
шүүгдэж, remote_access_allowed=1 биш ажилтан бүр 403 авна.

Шийдэл (Worker талд, асаахаас өмнө):
  1. const.php дээр $gloErpApiKey-д нууц түлхүүр тавина.
  2. Worker талд: X-Api-Key зөв ирсэн үед л X-Client-IP-г итгэж унших.
     Энэ PHP endpoint хэрэглэгчийн жинхэнэ IP-г тэр толгойгоор аль хэдийн
     илгээж байгаа (Cloudflare-ийн ард байвал cf-connecting-ip-гээс,
     эс бөгөөс REMOTE_ADDR-аас).

Hosting-ийн IP-г оффисын жагсаалтад нэмэх нь ШИЙДЭЛ БИШ — тэгвэл
дэлхийн хаанаас ч mglenc.com-оор дамжаад шалгалтыг тойрч орно.


ERP тал дээр нэмэх зүйл
-----------------------
Амжилттай нэвтрэхэд хэрэглэгч ийм хаягаар очно:

	https://mgl-design-system.pages.dev/app#access_token=<token>

/app нь одоогоор энэ token-ыг уншдаггүй тул ERP-ийн entry файлын хамгийн
эхэнд ERP-TALD-NEMEH.js доторх ~10 мөрийг тавина. Тэр нь token-ыг
sessionStorage-д хийж, хаягийн мөрөөс арилгана.

Хэрэв одоохондоо ERP талд гар хүрэх боломжгүй бол const.php дээр
$gloErpTokenMode = "none" болгоно — тэгвэл товч нь зүгээр /app руу
аваачиж, ERP өөрөө дахин нэвтрүүлнэ. Дараа нь ERP тал бэлэн болоход
"fragment" болгож буцаана.


Хамгаалалт
----------
  - Форм зөвхөн манай хуудаснаас илгээгдсэн эсэхийг session CSRF-ээр шалгана.
  - Нэг IP-аас 15 минутанд 12, нэг акаунт дээр 10 алдаатай оролдлогын дараа
    түр хаана (sys_get_temp_dir доторх тоолуур).
  - Cloudflare-ийн ард байвал CF-Connecting-IP-г ашиглана, гэхдээ зөвхөн
    REMOTE_ADDR нь Cloudflare-ийн IP мужид байвал л итгэнэ.
  - Redirect хаягийн host/схемийг $gloErpAppUrl-тай тулгаж шалгана.
  - PHP тал нууц үгийг лог-д бичихгүй, хадгалахгүй.
  - PHP шалгалт нь тав тухын зориулалттай. /app-ыг жинхэнэ хамгаалдаг нь
    ERP-ийн өөрийн auth — тэр хэвээрээ үлдэнэ.


Хуулсны дараа
-------------
  - Ctrl+F5 (Cloudflare ашиглаж байвал cache purge).
  - Шалгах: буруу нууц үг → цонхон дотор улаан алдаа, хуудас солигдохгүй.
             зөв нууц үг → /app руу шилжинэ.
  - Алдаа гарвал сервэрийн error_log дотроос "[erp-login]" гэж хайна.

Түгээмэл алдаа
--------------
  503 + "Нэвтрэх үйлчилгээ тохируулагдаагүй байна"
      → const.php дээр $gloErpLoginUrl = "" гэж ХООСНООР зарлачихсан.
        Тэр мөрийг устгавал өгөгдмөл нь хэрэглэгдэнэ.

  "unable to get local issuer certificate"
      → PHP-д CA багц олдоогүй. cPanel дээр ховор. Гарвал cacert.pem-ийн
        замыг $gloErpCaBundle-д бичнэ. TLS шалгалтыг УНТРААХГҮЙ.

  "worker 200 but no token at path 'token'; response keys: ..."
      → Амжилттай нэвтэрсэн ч token нь өөр талбарт байна.
        Мөрөнд бичигдсэн түлхүүрүүдээс сонгож $gloErpTokenPath-ыг засна.
