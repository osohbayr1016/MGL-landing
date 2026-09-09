<?php
/** Дадлагын модулийн тохиргоо. */

$incPageUrl = $clkMenuModDir . "settings.php";

$applySet     = ApplyCore::settings($db);
$applyMaxText = ApplyCore::sizeText(ApplyCore::uploadMaxBytes());
