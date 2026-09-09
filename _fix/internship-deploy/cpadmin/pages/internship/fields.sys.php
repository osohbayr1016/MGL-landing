<?php
/** Формын асуулт / талбарууд. */

$widJsArr["applyFields"] = $clkMenuModDir . "fields.js.php";
$incPageUrl              = $clkMenuModDir . "fields.php";

$applyFieldRows  = ApplyCore::fields($db, "", false);
$applyFieldTypes = ApplyCore::fieldTypes();
$applyFieldFor   = ApplyCore::fieldForTypes();
