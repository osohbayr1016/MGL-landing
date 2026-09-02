<?php
/**
 * Чөлөөт HTML блок — дизайнер өөрийн код бичнэ (газрын зураг, embed г.м).
 * Зөвхөн нэвтэрсэн админ бичдэг тул шууд гаргана.
 */

if (RegistrationCore::val($regData, "body") == "") {
	return;
}
?>
<section class="reg-section reg-html" style="<?php echo RegistrationCore::sectionStyle($regData, 0, 0); ?>">
	<?php echo $regData["body"]; ?>
</section>
