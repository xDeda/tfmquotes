<?php

include_once 'database.php';
include_once 'db_maintenance.php';  // perform database installation/upgrades

$conn = Database::getInstance();

$id = $_GET["id"];
$submit = $_GET["submit"];
$page = $_GET["p"];

function Colorify($q) {
	$q = explode("<br />", $q);
	foreach ($q as $qq) {
		if (strpos($qq, "• [") === 0 && strpos($qq, "• [Moderation]") !== 0 && strpos($qq, "• [0") !== 0 && strpos($qq, "• [1") !== 0 && strpos($qq, "• [2") !== 0) {
			$qq = str_replace("• [", "<span class=\"tn\">• [", $qq);
			$qq = str_replace("]", "]</span><span class=\"tc\">", $qq) . "</span><br />";
			echo $qq;
		} elseif (strpos($qq, "• [0") === 0 || strpos($qq, "• [1") === 0 || strpos($qq, "• [2") === 0) {
			$qq = explode("]", $qq);
			echo "<span class=\"tn\">" . $qq[0] . "]" . $qq[1] . "]</span><span class=\"tc\">" . $qq[2];
			if (isset($qq[3])) { echo $qq[3]; }
			echo "</span><br />";
		} elseif (strpos($qq, "• [Moderation]") === 0) {
			echo "<span class=\"mod\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "• [SERVER]") === 0) {
			echo "<span class=\"mod\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "just connected.") || strpos($qq, "has disconnected.") || strpos($qq, "No cheese for you! ^_^") || strpos($qq, "You just entered room") !== false) {
			echo "<span class=\"connect\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "is now your shaman, follow her!") !== false) {
			echo "<span class=\"bluesham\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "name is already taken") !== false) {
			echo "<span class=\"errors\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "You have joined the channel") !== false) {
			echo "<span class=\"chat1\" style=\"color: #F0C5FE;\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "had just unlocked the") !== false) {
			echo "<span class=\"special\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "< [") !== false) {
			echo "<span class=\"wout\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "> [") !== false) {
			echo "<span class=\"wintext\">";
			$qq = str_replace("[", "[<span class=\"winname\">", $qq);
			echo str_replace("]", "</span>]", $qq);
			echo "</span><br />";
		} elseif (strpos($qq, "[Shaman ") !== false) {
			$qq = str_replace("[Shaman ", "<span class=\"names\">[Shaman ", $qq);
			echo str_replace("]", "]</span><span class=\"bluesham\"> ", $qq) . "</span><br />";
		} elseif (strpos($qq, "[") === 0) {
			$qq = str_replace("[", "<span class=\"names\">[", $qq);
			echo str_replace("]", "]</span>", $qq) . "<br />";
		} else {
			echo $qq . "<br />";
		}
	}
}

function mynl2br($text) {
	return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
}
?>
<!DOCTYPE html>
<head>
<title>TFM Quotes bin</title>
<script src="jquery-3.4.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/png" href="favicon.png">
</head>
<body><div class="form-style-1">
<div style="display:inline-block;"><h2><a href="./?submit"><span class="name">[Tactcat]</span></a> Hello, welcome to my <a href="." style="color: #ED67EA;border-bottom: 1px solid currentColor;">quotes</a> bin!</h2></div><input type="text" class="search" placeholder="search..." autocomplete="off" autofocus><font size="2"><a href="./?submit"> » submit</a></font><hr>

<?php


if (isset($_POST["quote"])) {
	$quote = mynl2br($_POST["quote"]);
	$quote = addslashes($quote);
	$quote = strip_tags($quote, '<br>');

	$sql = "INSERT INTO quotes (id, quote, postdate) VALUES (NULL, '$quote', CURRENT_TIMESTAMP)";
	if ($conn->query($sql)){
		$last_id = $conn->insert_id;
	    echo "Records added successfully. " . $last_id;
	} else{
	    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}

	header("Location: ./?id=$last_id");

} elseif (isset($id)) {
	$sql = "SELECT id, quote, postdate FROM quotes WHERE id='$id'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id = $row["id"];
			echo "<a href=\"./?id=$id\" class=\"chat1\" style=\"color: #F0C5FE;\"> Quote ID: $id</a><div class=\"quote\">";
			Colorify($row["quote"]);
			echo "</div><small>date: " . $row["postdate"]. "</small><br />";
		}
	} else {
		echo "0 results";
	}

?>
    </li>
</ul>
<?php
} elseif (isset($submit)) {
?>
        Your Quote <span class="required">*</span>
     	<form method="post" method="index.php">
        <textarea name="quote" id="field5" class="field-long field-textarea"></textarea>
    </li>
    <li>
        <input type="submit" value="Submit" />
    </li>
    </form>
</ul>


<?php } elseif (isset($page)) { 

	$page2 = $page;
	$page3 = $page2-1;
	$page4 = $page2+1;
	$page *= 10;

	$result = $conn->query("SELECT COUNT(*) FROM `quotes`");
	$row = $result->fetch_row();
	$maxpage = substr($row[0], 0, -1);
	echo "<div style=\"align:left;float:right;\"><a href=\"/.?p=0\">« <a href=\"./?p=$page3\">‹</a> $page2 <a href=\"./?p=$page4\">›</a> <a href=\"./?p=$maxpage\">»</a></div>";

	$sql = "SELECT id, quote, postdate FROM quotes ORDER BY id desc LIMIT $page,10";
	$result = $conn->query($sql);	
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	$item = array_rand($items,1);
			$item = $items["$item"];
	    	$id = $row["id"];
			echo "<a href=\"./?id=$id\" class=\"chat2\" style=\"color: #F0C5FE;\">Quote ID: $id</a><div class=\"quote\">";
			Colorify($row["quote"]);
			echo "</div><small>date: " . $row["postdate"]. " </small><hr />";
		}
	} else {
	    echo "0 results";
	}

?>
<?php } else {
	
	$sql = "SELECT id, quote, postdate FROM quotes ORDER BY id desc";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	$item = array_rand($items,1);
			$item = $items["$item"];
	    	$id = $row["id"];
			echo "<div><a href=\"./?id=$id\" class=\"chat2\" style=\"color: #F0C5FE;\">Quote ID: $id</a><div class=\"quote\">";
			Colorify($row["quote"]);
			echo "</div><small>date: " . $row["postdate"]. " </small><hr /></div>";
		}
	} else {
	    echo "0 results";
	}

} ?>
<script>
function delay(fn, ms) {
	let timer = 0
	return function(...args) {
		clearTimeout(timer)
		timer = setTimeout(fn.bind(this, ...args), ms || 0)
	}
}
jQuery.expr[':'].icontains = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};
$(".search").keyup(delay(function(){$(".quote:not(:icontains('"+$(this).val()+"'))").parent().css("display","none");}, 500));
$(".search").keyup(delay(function(){$(".quote:icontains('"+$(this).val()+"')").parent().css("display","initial");console.log("showing "+$(this).val())}, 500));
</script>
</div>
</body>