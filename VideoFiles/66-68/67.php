<?php

define('DATABASE', '/Users/hasan_azimi0/Sites/phpLiteAdmin/db/test.sqlite3');

define('TITLE', 'PHP testing sandbox');
define('VERSION', '1.0.4');
define('HEADER', '../../assets/header.php');
define('BODY', '../../assets/body.php');
define('FOOTER', '../../assets/footer.php');

_init();
main();
page();

function main()
{
    global $G;
    message("PHP testing sandbox (%s) version %s", $G['ME'], VERSION);
    try {
        $db = new PDO('sqlite:' . DATABASE);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->exec('DROP TABLE IF EXISTS t');
        $db->exec('CREATE TABLE t (a, b, c)');
        message('Table t sucessfully created');
        $sth = $db->prepare('INSERT INTO t VALUES (?, ?, ?)');
        $sth->execute(array('a', 'b', 'c'));
        $sth->execute(array(1, 2, 3));
        $sth->execute(array('one', 'two', 'three'));
        $sth = $db->prepare('SELECT * FROM t');
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $sth->execute();
        foreach ( $sth as $row ) {
            message('%s, %s, %s', $row['a'], $row['b'], $row['c']);
        }
    } catch(PDOException $e) {
        error($e->getMessage());
    }
    
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
