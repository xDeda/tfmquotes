<?php

include_once 'database.php';

$curr_version = 2;
$conn = Database::getInstance();

function db_deleteAll() {
    global $conn;
    
    $query = 'DROP TABLE IF EXISTS quotes';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    $query = 'DROP TABLE IF EXISTS version';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
}

function db_install() {
    global $conn, $curr_version;
    $query = 'CREATE TABLE quotes (
            id MEDIUMINT AUTO_INCREMENT,
            quote TEXT(32768),
            postdate DATETIME NOT NULL,
            PRIMARY KEY (id)
            )';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    $query = 'CREATE TABLE version (id INT PRIMARY KEY)';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    $query = "INSERT INTO version VALUES ($curr_version)";
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
}

function db_upgrade($version) {
    global $conn, $curr_version;
    if ($version < 2) {
        $query = "SELECT id, quote FROM quotes";
        $result = $conn->query($query);
        while ($row = $result->fetch_assoc()) {
            $id = $row["id"];
            
            // Convert all <br /> tags back to \n
            // Undo addslashes and use escape_string instead
            $new = str_ireplace(array("<br />","<br>","<br/>"), "\n", stripslashes($row["quote"]));
            $new = $conn->escape_string($new);

            $query = "UPDATE quotes SET quote = '$new' WHERE id = $id";
            if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    	}
    }
    $query = "UPDATE version SET id = $curr_version";
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
}

$quotes_result = $conn->query('SELECT 1 from quotes LIMIT 1');
$version_result = $conn->query('SELECT id from version');

if ($quotes_result == false) {
    db_install();
    error_log("install db");
} elseif ($quotes_result !== false && $version_result == false) {
    // migrate to versioned database
    $query = 'CREATE TABLE version (id INT PRIMARY KEY)';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    $query = 'INSERT INTO version VALUES (1)';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    if ($curr_version > 1) {
        db_upgrade(1);
    }
} elseif ($version = $version_result->fetch_row()[0] < $curr_version) {
    db_upgrade($version);
    error_log("upgrade db ".$version." -> ".$curr_version);
}
