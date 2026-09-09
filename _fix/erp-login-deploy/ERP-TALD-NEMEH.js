/* ------------------------------------------------------------------
   ERP тал (mgl-design-system) дээр нэмэх хэсэг.
   Энэ файлыг хуулах биш — доорх кодыг /app-ийн эхлэл дээр тавина.

   mglenc.com дээрээс нэвтэрсэн хэрэглэгч ийм хаягаар ирнэ:

       https://mgl-design-system.pages.dev/app#access_token=<token>

   Fragment (#) хэсэг нь сервер рүү илгээгддэггүй, Referer толгойд ч
   үлддэггүй тул token нь Cloudflare болон бусад log-д бичигдэхгүй.

   Доорх код түүнийг уншаад session-д хийж, хаягийн мөрөөс арилгана.
   ЗААВАЛ app-ийн бусад код token уншихаас ӨМНӨ ажиллах ёстой —
   өөрөөр хэлбэл entry файлын (main.tsx / index.html) хамгийн эхэнд.
   ------------------------------------------------------------------ */

(function () {
	var hash = window.location.hash;

	if (!hash || hash.indexOf("access_token=") === -1) return;

	var token = new URLSearchParams(hash.slice(1)).get("access_token");

	if (!token) return;

	/* ---------------------------------------------------------------
	   "access_token" гэдгийг ERP өөрөө ашигладаг түлхүүрийн нэрээр
	   солино (ж: "token", "mgl_token", "auth.access_token" гэх мэт).
	   --------------------------------------------------------------- */
	sessionStorage.setItem("access_token", token);

	/* Token-ыг хаягийн мөрөөс арилгана — хуудас дахин ачаалахад,
	   хэн нэгэн линкийг хуулж илгээхэд token гарахгүй. */
	history.replaceState(null, "", window.location.pathname + window.location.search);
})();

/* ------------------------------------------------------------------
   Тэмдэглэл

   1. sessionStorage нь табыг хаахад цэвэрлэгддэг. ERP нь одоо
      localStorage ашигладаг бол түүнийг тавина.

   2. Token байхгүй үед /app нь урьдын адил өөрийн login хуудсаа
      харуулна. Өөрөөр хэлбэл шууд /app руу орсон хүн ERP-ийн
      өөрийн нэвтрэлтээр л ордог — mglenc.com-ын товч зөвхөн
      тав тухын зориулалттай, хамгаалалтыг орлохгүй.

   3. Token-ы нэрийг өөрчлөх бол mglenc.com талд const.php доторх
      $gloErpTokenParam-ыг мөн адил өөрчилнө.

   4. Fragment-ийн оронд query string (?access_token=...) илүү
      тохиромжтой бол const.php дээр $gloErpTokenMode = "query"
      болгоод, энд hash-ийн оронд location.search-ыг уншина.
      Гэхдээ энэ үед token нь Cloudflare-ийн log-д бичигдэнэ.
   ------------------------------------------------------------------ */
