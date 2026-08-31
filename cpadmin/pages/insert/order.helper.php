<?php

function reorderScopedItem($db, $table, $idCol, $orderCol, $itemId, $newOrder, $scopeWhere)
{
	$itemId = (int)$itemId;
	$newOrder = max(1, (int)$newOrder);
	if ($itemId < 1) {
		return;
	}

	foreach ($scopeWhere as $col => $val) {
		$db->where($col, $val);
	}
	$db->orderBy($orderCol, "ASC");
	$db->orderBy($idCol, "ASC");
	$rows = $db->get($table, null, array($idCol, $orderCol));

	if (is_array($rows) && $orderCol === "ceoOrder") {
		usort($rows, function ($a, $b) use ($idCol, $orderCol) {
			$orderA = (int)$a[$orderCol];
			$orderB = (int)$b[$orderCol];
			if ($orderA !== $orderB) {
				return $orderA - $orderB;
			}
			return (int)$a[$idCol] - (int)$b[$idCol];
		});
	}

	$ids = array();
	if (is_array($rows)) {
		foreach ($rows as $row) {
			if ((int)$row[$idCol] !== $itemId) {
				$ids[] = (int)$row[$idCol];
			}
		}
	}

	$insertAt = min($newOrder, count($ids) + 1) - 1;
	if ($insertAt < 0) {
		$insertAt = 0;
	}
	array_splice($ids, $insertAt, 0, array($itemId));

	$order = 1;
	foreach ($ids as $id) {
		$db->where($idCol, $id);
		$db->update($table, array($orderCol => (int)$order));
		$order++;
	}
}

function orderAjaxDone($payload = array())
{
	header("Content-Type: application/json; charset=utf-8");
	echo json_encode(array_merge(array("ok" => 1), $payload));
	exit;
}
