<?php
/** Хуудасны дизайн — блокууд + өнгө/загварын тохиргоо. */

$widJsArr["regDesign"] = $clkMenuModDir . "design.js.php";
$incPageUrl            = $clkMenuModDir . "design.php";

$regSet        = RegistrationCore::settings($db);
$regBlockTypes = RegistrationCore::blockTypes();

/* Идэвхгүй блокуудыг ч харуулна (унтраасныг буцааж асаах боломжтой) */
$regBlockList = RegistrationCore::blocks($db, false);

$regPageLink = RegistrationCore::pageUrl($regSet);
