<?php

$servername = "localhost";
$username = "USER";
$password = "PASS";
$dbname = "quotes";

$id = $_GET["id"];
$all = $_GET["all"];

function Colorify($q) {
	$q = explode("<br />", $q);
	foreach ($q as $qq) {
		if (strpos($qq, "just connected.") || strpos($qq, "has disconnected.") || strpos($qq, "No cheese for you! ^_^") !== false) {
			echo "<span class=\"connect\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "< [") !== false) {
			echo "<span class=\"wout\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "is now your shaman, follow her!") !== false) {
			echo "<span class=\"bluesham\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "name is already taken") !== false) {
			echo "<span class=\"errors\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "You have joined the channel") !== false) {
			echo "<span class=\"chat1\" style=\"color: #F0C5FE;\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "had just unlocked the") !== false) {
			echo "<span class=\"special\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "> [") !== false) {
			echo "<span class=\"wintext\">";
			$qq = str_replace("[", "[<span class=\"winname\">", $qq);
			echo str_replace("]", "</span>]", $qq);
			echo "</span><br />";
		} elseif (strpos($qq, "[Shaman ") !== false) {
			$qq = str_replace("[Shaman ", "<span class=\"names\">[Shaman ", $qq);
			echo str_replace("]", "]</span><span class=\"bluesham\"> ", $qq) . "</span><br />";
		} elseif (strpos($qq, "[") == 0) {
			$qq = str_replace("[", "<span class=\"names\">[", $qq);
			echo str_replace("]", "]</span>", $qq) . "<br />";
		} elseif (strpos($qq, "• [SERVER]") !== false) {
			echo "<span class=\"mod\">" . $qq . "</span><br />";
		} elseif (strpos($qq, "• [") == 0) {
			$qq = str_replace("• [", "<span class=\"tc\">•</span> <span class=\"tn\">[", $qq);
			echo str_replace("]", "]</span><span class=\"tc\">", $qq) . "</span><br />";
		}
		else {
			echo $qq . "<br />";
		}
	}
}

function mynl2br($text) {
	return strtr($text, array("\r\n" => '<br />', "\r" => '<br />', "\n" => '<br />')); 
}

if (isset($_POST["quote"])) {
	$link = mysqli_connect($servername, $username, $password, $dbname);

	if($link === false){
	    die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	$quote = mynl2br($_POST["quote"]);
	$quote = addslashes($quote);
	$quote = strip_tags($quote, '<br>');

	$sql = "INSERT INTO quotes (id, quote, postdate) VALUES (NULL, '$quote', CURRENT_TIMESTAMP)";
	if(mysqli_query($link, $sql)){
		$last_id = mysqli_insert_id($link);
	    echo "Records added successfully. " . $last_id;
	} else{
	    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
	}

	mysqli_close($link);

	header("Location: http://178.62.23.109/quotes/?id=$last_id");

} elseif (isset($id)) {

?>

<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/png" href="favicon.png">
<ul class="form-style-1">
	<li>
		<label><h2><a href="http://178.62.23.109/quotes/"><font id="name">[Tactcat]</font></a> Hello, welcome to my <a href="http://178.62.23.109/quotes/?all" class="mod" style="color: #ED67EA;" style="color: #ED67EA;">quotes</a> bin!<hr></h2>

<?php 
	
	$conn = new mysqli($servername, $username, $password, $dbname);
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "SELECT id, quote, postdate FROM quotes WHERE id='$id'";
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$id = $row["id"];
			echo "</label><a href=\"http://178.62.23.109/quotes/?id=$id\" class=\"chat1\" style=\"color: #F0C5FE;\"> Quote ID: $id</a><div id=\"quote\">";
			Colorify($row["quote"]);
			echo "</div><small>date: " . $row["postdate"]. "</small><br />";
		}
	} else {
		echo "0 results";
	}
	$conn->close();

?>
    </li>
</ul>
<?php
} elseif (isset($all)) {
?>

<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/png" href="favicon.png">
<ul class="form-style-1">
	<li>
		<label><h2><a href="http://178.62.23.109/quotes/"><font id="name">[Tactcat]</font></a> Hello, welcome to my <a href="http://178.62.23.109/quotes/?all" class="mod" style="color: #ED67EA;">quotes</a> bin!<hr></h2>

<?php 

$items = array("https://i.imgur.com/gFbvY24.png", "https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/High_Trestle_Trail_Bridge.jpg/1920px-High_Trestle_Trail_Bridge.jpg", "http://xdeda.github.io/?title=amy", "https://i.imgur.com/EwNBaAc.png", "https://i.imgur.com/yWO4pM3.png","https://en.wikipedia.org/wiki/List_of_fictional_felines","https://vocaroo.com/i/s1V5mMHFcsbH","https://vocaroo.com/i/s12cezlop9Ry","https://prnt.sc/k984ek","https://hastebin.com/ujicalilon.lua","https://mixmag.net/feature/is-despacio-a-new-take-on-clubbing-or-just-a-nostalgic-pipe-dream","https://cdn.discordapp.com/attachments/410507088058646528/427085528383619082/IMG-20180320-WA0079.jpg","http://shockslayer.com/feature-list/","https://cdn.shopify.com/s/files/1/1829/4817/products/qc-cod-mug.jpg?v=1491666560","http://api.urbandictionary.com/v0/define?term=Internet%20Friend","https://i.imgur.com/tLBnZ6n.jpg","https://i.imgur.com/qgZu7Sm.gifv","https://www.stereogum.com/1984421/the-low-key-legacy-of-duster-your-favorite-indie-bands-favorite-indie-band/franchises/sounding-board/","https://imgur.com/a/O0Ezm","https://78.media.tumblr.com/1e727241a78365d0a0cf565723bd7702/tumblr_oy2zbfRnTA1rhz81io1_500.jpg","https://en.wiktionary.org/wiki/Thesaurus:person");

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 
	
	$sql = "SELECT id, quote, postdate FROM quotes ORDER BY id desc";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	$item = array_rand($items,1);
			$item = $items["$item"];
	    	$id = $row["id"];
			echo "</label><a href=http://178.62.23.109/quotes/?id=$id class=\"chat2\" style=\"color: #F0C5FE;\">Quote ID: $id</a><div id=quote>";
			Colorify($row["quote"]);
			echo "</div><small>date: " . $row["postdate"]. " <a href='$item' target='_blank'>x</a></small><hr />";
		}
	} else {
	    echo "0 results";
	}
	$conn->close();

} else { ?>

<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/png" href="favicon.png">
<ul class="form-style-1">
	<form method="post" method="index.php">
	<li>
		<label><h2><a href="http://178.62.23.109/quotes/"><font id="name">[Tactcat]</font></a> Hello, welcome to my <a href="http://178.62.23.109/quotes/?all" class="mod" style="color: #ED67EA;">quotes</a> bin!<hr></h2>
        Your Quote <span class="required">*</span></label>
        <textarea name="quote" id="field5" class="field-long field-textarea"></textarea>
    </li>
    <li>
        <input type="submit" value="Submit" />
    </li>
    </form>
</ul>

<?php } ?>