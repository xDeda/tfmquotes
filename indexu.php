<?php

$servername = "localhost";
$username = "USER";
$password = "PASS";
$dbname = "quotes";

$id = $_GET["id"];
$all = $_GET["all"];
$msg = $_GET["msg"];
$pw = "PASS";
$pwtry = $_POST["pw"];

session_start();

if($pwtry == $pw || $_SESSION["pw"] == $pw) {

$_SESSION["pw"] = $pw;

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
			echo "<span class=\"chat1\">" . $qq . "</span><br />";
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

	header("Location: http://178.62.23.109/quotes/indexu.php?id=$last_id");

} elseif (isset($id)) {

?>

<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/png" href="favicon.png">
<ul class="form-style-1">
	<li>
		<label><h2><a href="http://178.62.23.109/quotes/indexu.php"><font id="name">[Tactcat]</font></a> Hello, welcome to my <a href="http://178.62.23.109/quotes/indexu.php?all" class="mod">quotes</a> bin!<hr></h2>

<?php if (isset($_GET["msg"])) { echo "<small>Post ID $msg was deleted.</small><hr>"; } ?>

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
			echo "</label><a href=\"http://178.62.23.109/quotes/indexu.php?id=$id\" class=\"chat1\"> Quote ID: $id</a><div id=\"quote\">";
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
		<label><h2><a href="http://178.62.23.109/quotes/indexu.php"><font id="name">[Tactcat]</font></a> Hello, welcome to my <a href="http://178.62.23.109/quotes/indexu.php?all" class="mod">quotes</a> bin!<hr></h2>

<?php if (isset($_GET["msg"])) { echo "<small>Post ID $msg was deleted.</small><hr>"; } ?>

<?php 

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 
	
	$sql = "SELECT id, quote, postdate FROM quotes ORDER BY id desc";
	$result = $conn->query($sql);
	
	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    	$id = $row["id"];
			echo "</label><a href=http://178.62.23.109/quotes/indexu.php?id=$id class=chat2>Quote ID: $id</a><div id=quote>";
			Colorify($row["quote"]);
			echo "</div><small>date: " . $row["postdate"]. " <a href='http://niclasjensen.dk/quotes/x.php?id=$id' style='color:white'>X</a></small><hr />";
		}
	} else {
	    echo "0 results";
	}
	$conn->close();

} else { ?>

<link rel="stylesheet" type="text/css" href="style.css">
<link rel="icon" type="image/png" href="favicon.png">
<ul class="form-style-1">
	<form method="post" method="indexu.php">
	<li>
		<label><h2><a href="http://178.62.23.109/quotes/indexu.php"><font id="name">[Tactcat]</font></a> Hello, welcome to my <a href="http://178.62.23.109/quotes/indexu.php?all" class="mod">quotes</a> bin!<hr></h2>

<?php if (isset($_GET["msg"])) { echo "<small>Post ID $msg was deleted.</small><hr>"; } ?>

        Your Quote <span class="required">*</span></label>
        <textarea name="quote" id="field5" class="field-long field-textarea"></textarea>
    </li>
    <li>
        <input type="submit" value="Submit" />
    </li>
    </form>
</ul>

<?php } } 

if(!isset($_SESSION["pw"])) {

?>
<style>
::placeholder { /* Chrome, Firefox, Opera, Safari 10.1+ */
    color: white;
    opacity: 1; /* Firefox */
    padding: 7px;
}

:-ms-input-placeholder { /* Internet Explorer 10-11 */
    color: white;
}

::-ms-input-placeholder { /* Microsoft Edge */
    color: white;
}
</style>
<div style="position: absolute;width: 300px;height: 200px;z-index: 15;top: 50%;left: 50%;margin: -100px 0 0 -150px;background: #99C7A6;border: 1px solid black;"><center>
<form method="post" method="indexu.php" />
<input type="password" name="pw"/ placeholder="password" style="background: none;border: 1px solid black;color: white;margin: 50px;" />
<input type="submit" value="submit" style="background: none;border: 1px solid black;color: black;margin: 5px;" />
</form>
</center></div>

<?php } ?>