<?php

define('TITLE', 'Testimonials Database');
define('VERSION', 'db.php version 1.2.1');
define('ASSETDIR', 'assets');
define('PAGELIMIT', 5);

require_once 'lib/bwSQLite3.php';
define('DBNAME', 'data/testimonials.db');
define('TABLENAME', 'testimonial');

_init();
main();

function main()
{
    if(isset($_REQUEST['a'])) jump($_REQUEST["a"]);
    main_page();
}

function _init( )
{
    global $G;
    $G['mainPageTitle'] = TITLE;
    $G['ME'] = basename($_SERVER['SCRIPT_FILENAME']);
    $G['VERSION'] = VERSION;

    // define SELF
    $G['SELF'] = linkback();
    
    // initialize display vars
    foreach ( array( 'pageTitle', 'testimonial', 'byline', 'HIDDENS', 'MESSAGES', 'ERRORS', 'CONTENT' ) as $v )
        $G[$v] = "";

    try {
        $G['db'] = new bwSQLite3(DBNAME, TABLENAME);
    } catch(PDOException $e) {
        error($e->getMessage());
    }
}

function jump( $action )
{
    switch($action) {
        case 'add':
            add();
            break;
        case 'edit_del':
            if(array_key_exists('edit', $_REQUEST)) edit();
            elseif(array_key_exists('delete', $_REQUEST)) delete_confirm();
            else error('invalid edit_del');
            break;
        case 'update':
            if(array_key_exists('cancel', $_REQUEST)) {
                message('Edit canceled');
                main_page();
            } else update();
            break;
        case 'delete_do':
            if(array_key_exists('cancel', $_REQUEST)) {
                message('Delete canceled');
                main_page();
            } else delete_do();
            break;
        default:
            error_message('unhandled jump: ' . $action);
            main_page();
    }
    // fall-through
    message('jump > fall-through (%s)', $action);
    main_page();
}

function main_page()
{
    listrecs();
    hidden('a', 'add');
    page('main', 'Enter a new testimonial');
}

// actions

function add()
{
    global $G;
    $db = $G['db'];

    $rec = array(
        'testimonial' => htmlentities($_REQUEST['testimonial']),
        'byline' => htmlentities($_REQUEST['byline'])
    );

    try {
        $db->insert($rec);
    } catch ( PDOException $e ) {
        error($e->getMessage());
        main_page();
    }
    message('Record (%s) added', $rec['byline']);
    main_page();
}

function delete_confirm()
{
    global $G;
    $db = $G['db'];

    $id = $_REQUEST['id'];

    try {
        $rec = $db->get_rec($id);
    } catch ( PDOException $e ) {
        error($e->getMessage());
        main_page();
    }

    set_form_vars($rec);
    hidden('a', 'delete_do');
    hidden('id', $id);
    hidden('byline', $rec['byline']);
    page('delconfirm', 'Delete this testimonial?');
}

function delete_do()
{
    global $G;
    $db = $G['db'];

    $id = $_REQUEST['id'];
    $byline = $_REQUEST['byline'];

    try {
        $rec = $db->delete($id);
    } catch ( PDOException $e ) {
        error($e->getMessage());
        main_page();
    }

    message('Record (%s) deleted', $byline);
    main_page();
}

function update()
{
    global $G;
    $db = $G['db'];

    $id = $_REQUEST['id'];
    $rec = array(
        'testimonial' => htmlentities($_REQUEST['testimonial']),
        'byline' => htmlentities($_REQUEST['byline'])
    );

    try {
        $db->update($id, $rec);
    } catch ( PDOException $e ) {
        error($e->getMessage());
        main_page();
    }

    message('Record (%s) updated', $rec['byline']);
    main_page();
}

function edit()
{
    global $G;
    $db = $G['db'];
    $id = $_REQUEST['id'];

    try {
        $rec = $db->get_rec($id);
    } catch ( PDOException $e ) {
        error($e->getMessage());
        main_page();
    }

    set_form_vars($rec);
    hidden('a', 'update');
    hidden('id', $id);
    page('edit', 'Edit this testimonial');
}

// other database functions

function listrecs()
{
    global $G;
    $db = $G['db'];
    $sql_limit = PAGELIMIT;
    $t = TABLENAME;

    try {
        $count = $db->count_recs();
    } catch ( PDOException $e ) {
        error($e->getMessage());
        main_page();
    }

    message('There are %d testimonials in the database. Add some more!', $count);

    // how many pages do we have?
    $numpages = floor($count / $sql_limit);
    if($count % $sql_limit) $numpages++;

    // what page is this?
    $curpage = 0;
    if(array_key_exists('jumppage', $_REQUEST)) $curpage = $_REQUEST['jumppage'];
    elseif(array_key_exists('nextpage', $_REQUEST)) $curpage = $_REQUEST['pageno'] + 1;
    elseif(array_key_exists('prevpage', $_REQUEST)) $curpage = $_REQUEST['pageno'] - 1;

    $pagebar = list_pagebar($curpage, $numpages);

    $a = '';
    $q = "SELECT * FROM $t ORDER BY byline LIMIT ? OFFSET ?";

    try {
        foreach ($db->sql_query($q, $sql_limit, $curpage * $sql_limit) as $row) {
            set_form_vars($row);
            $a .= getpage('recline');
        }
    } catch ( PDOException $e ) {
        error($e->getMessage());
        main_page();
    }
    set_form_vars();  # clear vars for "new testimonial" box
    $G['CONTENT'] = $pagebar . $a . $pagebar;
}

function list_pagebar( $pageno, $numpages )
{
    global $G;
    $prevlink = '<span class="n">&lt;&lt;</span>';
    $nextlink = '<span class="n">&gt;&gt;</span>';
    $linkback = $G['SELF'];

    if($pageno > 0)
        $prevlink = "<a href=\"$linkback?pageno=$pageno&prevpage=1\">&lt;&lt;</a>";
    if($pageno < ( $numpages - 1 ))
        $nextlink = "<a href=\"$linkback?pageno=$pageno&nextpage=1\">&gt;&gt;</a>";

    $pagebar = '';
    for( $n = 0; $n < $numpages; $n++ ) {
        $np = $n + 1;
        if($n == $pageno) $pagebar .= "<span class=\"n\">$np</span>";
        else $pagebar .= "<a href=\"$linkback?jumppage=$n\">$np</a>";
    }

    $G['prevlink'] = $prevlink;
    $G['nextlink'] = $nextlink;
    $G['pagebar'] = $pagebar;

    return getpage('nextprev');
}

// Utility functions

function getpage( $p )
{
    global $G;
    ob_start();
    require(ASSETDIR . "/$p.php");
    $rv = ob_get_contents();
    ob_end_clean();
    return $rv;
}

function set_form_vars( $rec = NULL )
{
    global $G;
    if($rec) {
        $t = $rec['testimonial'];
        $b = $rec['byline'];
        $id = $rec['id'];
    } else {
        $t = '';
        $b = '';
        $id = '';
    }

    $G['testimonial'] = $t;
    $G['byline'] = $b;
    $G['id'] = $id;
}

function page( $pagename = 'main', $pagetitle = 'Enter a new testimonial' )
{
    global $G;
    set_vars();

    $G['pageTitle'] = $pagetitle;

    foreach ( array('header', $pagename, 'footer') as $p ) {
        require_once(ASSETDIR . "/$p.php");
    }
    exit();
}

function set_vars( )
{
    global $G;
    if(isset($G["_MSG_ARRAY"])) foreach ( $G["_MSG_ARRAY"] as $m ) $G["MESSAGES"] .= $m;
    if(isset($G["_ERR_ARRAY"])) foreach ( $G["_ERR_ARRAY"] as $m ) $G["ERRORS"] .= $m;
    if(isset($G["_HID_ARRAY"])) foreach ( $G["_HID_ARRAY"] as $m ) $G["HIDDENS"] .= $m;
}

function linkback()
{
    $l = NULL;
    foreach(array('REQUEST_URI', 'SCRIPT_NAME') as $e) {
        if(array_key_exists($e, $_SERVER)) {
            $l = $_SERVER[$e];
            break;
        }
    }
    if(is_null($l)) return NULL;

    // strip the query
    if(( $i = strpos($l, '?') )) {
        $l = substr($l, 0, $i);
    }

    // handle default document
    if(substr($l, -1) == '/') return $l;
    else return basename($l);
}

function hidden( $n, $v )
{
    global $G;
    $G["_HID_ARRAY"][] = "<input type=\"hidden\" name=\"$n\" value=\"$v\">\n";
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
    $G["_ERR_ARRAY"][] = "<p class=\"error\">$s</p>\n";
}

function error( $s )
{
    error_message($s);
    page();
}

?>
