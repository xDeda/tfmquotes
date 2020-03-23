<?php

include_once 'database.php';

$conn = Database::getInstance();

$action = $_REQUEST["action"];
$id = $_REQUEST["id"];
$limit = $_REQUEST["limit"];
$skip = $_REQUEST["skip"];

function Colorify($q) {
    $buf = "";
	foreach (preg_split("/((\r?\n)|(\r\n?))/", $q) as $qq) {
		if (strpos($qq, "• [") === 0 && strpos($qq, "• [Moderation]") !== 0 && strpos($qq, "• [0") !== 0 && strpos($qq, "• [1") !== 0 && strpos($qq, "• [2") !== 0) {
			$qq = str_replace("• [", "<span class=\"tn\">• [", $qq);
			$qq = str_replace("]", "]</span><span class=\"tc\">", $qq) . "</span><br />";
			$buf .= $qq;
		} elseif (strpos($qq, "• [0") === 0 || strpos($qq, "• [1") === 0 || strpos($qq, "• [2") === 0) {
			$qq = explode("]", $qq);
			$buf .= "<span class=\"tn\">" . $qq[0] . "]" . $qq[1] . "]</span><span class=\"tc\">" . $qq[2];
			if (isset($qq[3])) { $buf .= $qq[3]; }
			$buf .= "</span><br />";
		} elseif (strpos($qq, "• [Moderation]") === 0) {
			$buf .= "<span class=\"mod\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "• [SERVER]") === 0) {
			$buf .= "<span class=\"mod\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "just connected.") || strpos($qq, "has disconnected.") || strpos($qq, "No cheese for you! ^_^") || strpos($qq, "You just entered room") !== false) {
			$buf .= "<span class=\"connect\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "is now your shaman, follow her!") !== false) {
			$buf .= "<span class=\"bluesham\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "name is already taken") !== false) {
			$buf .= "<span class=\"errors\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "You have joined the channel") !== false) {
			$buf .= "<span class=\"chat1\" style=\"color: #F0C5FE;\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "had just unlocked the") !== false) {
			$buf .= "<span class=\"special\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "< [") !== false) {
			$buf .= "<span class=\"wout\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "> [") !== false) {
			$buf .= "<span class=\"wintext\">";
			$qq = str_replace("[", "[<span class=\"winname\">", $qq);
			$buf .= str_replace("]", "</span>]", $qq);
			$buf .= "</span><br />";
		} elseif (strpos($qq, "[Shaman ") !== false) {
			$qq = str_replace("[Shaman ", "<span class=\"names\">[Shaman ", $qq);
			$buf .= str_replace("]", "]</span><span class=\"bluesham\"> ", $qq) . "</span><br />";
		} elseif (strpos($qq, "[") === 0) {
			$qq = str_replace("[", "<span class=\"names\">[", $qq);
			$buf .= str_replace("]", "]</span>", $qq) . "<br />";
		} else {
			$buf .= $qq . "<br />";
		}
	}
	return $buf;
}

if ($action == "get") {
	$sql = "SELECT id, quote, postdate FROM quotes";
	if (isset($id)) {
	    $sql .= " WHERE id='$id'";
	} elseif (isset($skip) && isset($limit)) {
	    $sql .= " ORDER BY id desc LIMIT $limit OFFSET $skip";
	} else {
	    $sql .= " ORDER BY id desc";
	}
	$result = $conn->query($sql);
    
    $array = array();
	while ($row = $result->fetch_assoc()) {
	    $row['quote'] = Colorify($row['quote']);  // TODO: temporarily while we migrate colorify to js
		$array[] = $row;
	}
	echo json_encode($array);
}