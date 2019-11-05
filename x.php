<?php

$servername = "localhost";
$username = "USER";
$password = "PASS";
$dbname = "quotes";

$id = $_GET["id"];

$link = mysqli_connect($servername, $username, $password, $dbname);

$sql = "DELETE FROM quotes WHERE id='$id';";

if(mysqli_query($link, $sql)){
	$last_id = mysqli_insert_id($link);
    echo "Records added successfully. " . $last_id;
} else {
    echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
}

mysqli_close($link);

header("Location: http://178.62.23.109/quotes/indexu.php?all&msg=$id");

?>

fuck off urwa