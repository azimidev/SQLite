<?php

define('TITLE', 'PHP testing sandbox');
define('VERSION', '1.0.4');
define('HEADER', '../../assets/header.php');
define('BODY', '../../assets/body.php');
define('FOOTER', '../../assets/footer.php');

require_once('../../lib/bwSQLite3.php');

_init();
main();

function main()
{
    global $G;
    define('DATABASE', '/Users/hasan_azimi0/Sites/phpLiteAdmin/db/test.db');
    define('FILENAME', 'olives.jpg');
    $fh = fopen(FILENAME, 'rb');
    $blob = fread($fh, filesize(FILENAME));

    try {
        $db = new bwSQLite3(DATABASE);
        $db->sql_do('DROP TABLE IF EXISTS blobtest');
        $db->sql_do('CREATE TABLE blobtest (id INTEGER PRIMARY KEY, b BLOB)');
        $db->sql_do('INSERT INTO blobtest (b) VALUES (CAST(? AS BLOB))', $blob);
        $result = $db->sql_query_value('SELECT b FROM blobtest');
    } catch (PDOException $e) {
        error($e->getMessage());
    }

    header('Content-type: image/jpeg');
    echo($result);
}

function _init( )
{
    global $G;
    $G['TITLE'] = TITLE;
    $G['ME'] = basename($_SERVER['SCRIPT_FILENAME']);

    // initialize display vars
    foreach ( array( 'MESSAGES', 'ERRORS', 'CONTENT' ) as $v )
        $G[$v] = "";
}

function page( )
{
    global $G;
    set_vars();

    require_once(HEADER);
    require_once(BODY);
    require_once(FOOTER);
    exit();
}

// Utility functions

function set_vars( )
{
    global $G;
    if(isset($G["_MSG_ARRAY"])) foreach ( $G["_MSG_ARRAY"] as $m ) $G["MESSAGES"] .= $m;
    if(isset($G["_ERR_ARRAY"])) foreach ( $G["_ERR_ARRAY"] as $m ) $G["ERRORS"] .= $m;
    if(isset($G["_CON_ARRAY"])) foreach ( $G["_CON_ARRAY"] as $m ) $G["CONTENT"] .= $m;
}

function content( $s )
{
    global $G;
    $G["_CON_ARRAY"][] = "\n<div class=\"content\">$s</div>\n";
}

function message()
{
    global $G;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $G["_MSG_ARRAY"][] = "<p class=\"message\">$s</p>\n";
}

function error_message()
{
    global $G;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $G["_ERR_ARRAY"][] = "<p class=\"error_message\">$s</p>\n";
}

function error( $s )
{
    error_message($s);
    page();
}

?>
