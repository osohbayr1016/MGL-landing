<?php
/*
	Нүүр болон бусад хуудсанд байрлах ERP нэвтрэлтийн цонх.
	home.php-ийн төгсгөлд нэг л удаа include хийгдэнэ.
*/

if(!isset($_SESSION["erpLoginCsrf"]))
	$_SESSION["erpLoginCsrf"] = bin2hex(random_bytes(32));

$erpCsrf = $_SESSION["erpLoginCsrf"];
?>
<div class="erp-login" id="erpLogin" hidden>
	<div class="erp-login__backdrop" data-erp-close></div>

	<div class="erp-login__panel" role="dialog" aria-modal="true" aria-labelledby="erpLoginTitle">
		<button type="button" class="erp-login__x" data-erp-close aria-label="Хаах">
			<span></span><span></span>
		</button>

		<div class="erp-login__head">
			<span class="erp-login__eyebrow">MGL E&amp;C design management system</span>
			<h2 class="erp-login__title" id="erpLoginTitle">Нэвтрэх</h2>
		</div>

		<form class="erp-login__form" id="erpLoginForm" method="post" action="/erp.login.php" novalidate>
			<input type="hidden" name="csrf" value="<?php echo htmlspecialchars($erpCsrf, ENT_QUOTES, "UTF-8"); ?>">

			<label class="erp-login__field">
				<span class="erp-login__label">Нэвтрэх нэр</span>
				<input type="text" name="login" autocomplete="username" autocapitalize="none"
					spellcheck="false" required placeholder="Нэвтрэх нэр">
			</label>

			<label class="erp-login__field">
				<span class="erp-login__label">Нууц үг</span>
				<span class="erp-login__pass">
					<input type="password" name="password" autocomplete="current-password" required placeholder="Нууц үг">
					<button type="button" class="erp-login__peek" data-erp-peek
						aria-label="Нууц үг харуулах" aria-pressed="false">
						<!-- нууц үг далд үед: нээлттэй нүд = "харах" -->
						<svg class="erp-login__eye erp-login__eye--show" viewBox="0 0 20 20" aria-hidden="true"
							fill="none" stroke="currentColor" stroke-width="1.4"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M1.2 10S4.7 4.2 10 4.2 18.8 10 18.8 10 15.3 15.8 10 15.8 1.2 10 1.2 10Z"/>
							<circle cx="10" cy="10" r="2.6"/>
						</svg>
						<!-- нууц үг ил үед: зурсан нүд = "нуух" -->
						<svg class="erp-login__eye erp-login__eye--hide" viewBox="0 0 20 20" aria-hidden="true"
							fill="none" stroke="currentColor" stroke-width="1.4"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M8.3 4.4A9.7 9.7 0 0 1 10 4.2c5.3 0 8.8 5.8 8.8 5.8a17 17 0 0 1-3.2 3.7"/>
							<path d="M5.9 6A16.6 16.6 0 0 0 1.2 10S4.7 15.8 10 15.8a9.5 9.5 0 0 0 3.4-.6"/>
							<path d="m3.2 3.2 13.6 13.6"/>
						</svg>
					</button>
				</span>
			</label>

			<p class="erp-login__error" id="erpLoginError" role="alert" hidden></p>

			<button type="submit" class="erp-login__submit">
				<span class="erp-login__submit-text">Нэвтрэх</span>
				<span class="erp-login__spinner" aria-hidden="true"></span>
			</button>
		</form>
	</div>
</div>
