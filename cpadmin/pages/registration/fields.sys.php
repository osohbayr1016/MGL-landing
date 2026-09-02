<?php
/** Формын талбарууд. */

$widJsArr["regFields"] = $clkMenuModDir . "fields.js.php";
$incPageUrl            = $clkMenuModDir . "fields.php";

$regFieldRows = RegistrationCore::fields($db, false);
$regFieldTypes = RegistrationCore::fieldTypes();
