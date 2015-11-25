<?php

define('TITLE', 'PHP testing sandbox');
define('VERSION', '1.0.4');
define('HEADER', '../assets/header.php');
define('BODY', '../assets/body.php');
define('FOOTER', '../assets/footer.php');

require_once 'bwSQLite3.php';

// define("FILENAME", ":memory:");
define("FILENAME", "/Users/bweinman/sqlite3_data/bwTestDB.db");
define("TABLE", 't');


_init();
main();
page();

function main()
{
    global $G;
    $G['query_start_time'] = microtime(TRUE);
    $tn = TABLE;

    message('this is %s, filename is %s, table is %s', $G['ME'], FILENAME, TABLE);
    try {
        $db = new bwSQLite3(FILENAME, TABLE);
        message("bwSQLite3 version %s", $db->version());
        message("sqlite3 version %s", $db->version('sqlite3'));
        $db->sql_do("drop table if exists $tn");
        $db->begin_transaction();
        $db->sql_do("create table $tn ( id integer primary key, string text, number int )");
        $db->sql_do("insert into $tn (string, number) values (?, ?)", 'one', 1);
        $db->sql_do("insert into $tn (string, number) values (?, ?)", 'two', 2);
        $db->sql_do("insert into $tn (string, number) values (?, ?)", 'three', 3);
        $db->commit();
    } catch (PDOException $e) {
        error($e->getMessage());
    }

    $db->timer_start();
    message('table_exists %s: %s (%s ms elapsed)', TABLE, $db->table_exists(TABLE) ? 'YES' : 'NO', $db->timer());
    $db->timer_start();
    message('table_exists %s: %s (%s ms elapsed)', 'foo', $db->table_exists('foo') ? 'YES' : 'NO', $db->timer());

    message('all rows:');
    $db->timer_start();
    try {
        foreach ($db->sql_query("select * from $tn") as $row) {
            message('id: %d, string: %s, number: %d', $row['id'], $row['string'], $row['number']);
        }
        message('&nbsp;-- %s ms elapsed', $db->timer());
    } catch (PDOException $e) {
        error($e->getMessage());
    }

    try {
        $row = $db->sql_query_row("select * from $tn where number = ?", 2);
        message("query_row (select * from %s where number = 2): string: %s number: %s", TABLE, $row['string'], $row['number']);
        $value = $db->sql_query_value("select id from $tn where number = ?", 3);
        message("query_value (select id from %s where number = 3): id: %s", TABLE, $value);
    } catch (PDOException $e) {
        error($e->getMessage());
    }

    try {
        $row = $db->get_rec(2);
        message('get_rec 2 -- id: %d, string: %s, number: %d', $row['id'], $row['string'], $row['number']);
    } catch (PDOException $e) {
        error($e->getMessage());
    }

    $db->timer_start();
    message('get_recs() ...');
    try {
        foreach( $db->get_recs() as $row ) {
            message('id: %d, string: %s, number: %d', $row['id'], $row['string'], $row['number']);
        }
        message('&nbsp;-- %s ms elapsed', $db->timer());
    } catch (PDOException $e) {
        error($e->getMessage());
    }
    message('count_recs: ' . $db->count_recs());

    try {
        $new_id = $db->insert(array( 'number' => 4, 'string' => 'four'));
        message("inserted new id " . $new_id);
    } catch (PDOException $e) {
        error($e->getMessage());
    }
    message('after insert count_recs: ' . $db->count_recs());

    try {
        $db->get_recs();
    } catch (PDOException $e) {
        error($e->getMessage());
    }
    foreach($db->sth() as $row) {
            message('id: %d, string: %s, number: %d', $row['id'], $row['string'], $row['number']);
    }

    message('update rec 2: ');
    try {
        $db->update(2, array( 'number' => 7, 'string' => 'seven'));
    } catch (PDOException $e) {
        error($e->getMessage());
    }
    $row = $db->get_rec(2);
    message('get_rec 2 -- id: %d, string: %s, number: %d', $row['id'], $row['string'], $row['number']);

    message('delete rec 2: ');
    try {
        $row = $db->delete(2);
    } catch (PDOException $e) {
        error($e->getMessage());
    }
    message('after delete count_recs: ' . $db->count_recs());
    foreach($db->get_recs() as $row) {
            message('id: %d, string: %s, number: %d', $row['id'], $row['string'], $row['number']);
    }
    $elapsed_time = microtime(TRUE) - $G['query_start_time'];
    message('whole set elapsed time: %s ms', number_format($elapsed_time * 1000, 2));
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
