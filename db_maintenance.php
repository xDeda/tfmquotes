<?php

include_once 'database.php';

$curr_version = 1;
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
            quote VARCHAR(32768),
            postdate DATETIME NOT NULL,
            PRIMARY KEY (id)
            )';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    $query = 'CREATE TABLE version (id INT)';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    $query = "INSERT INTO version VALUES ($curr_version)";
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
}

function db_upgrade($version) {
    global $curr_version;
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
    $query = 'CREATE TABLE version (id INT)';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    $query = 'INSERT INTO version VALUES (1)';
    if ($conn->query($query) == false) { error_log("err: " . $conn->error);return; }
    if ($curr_version > 1) {
        db_upgrade();
    }
} elseif ($version = $version_result->fetch_row()[0] < $curr_version) {
    db_upgrade($version);
    error_log("upgrade db ".$version." -> ".$curr_version);
}
