<?php

define("VERSION", "2.6.9");
define('SQLCOMMENT', '--');

// ***** uncomment for PostgreSQL
// define("DBENGINE", "pgsql");
// define("PGSQLUSER", "hassan");
// define("PGSQLPASS", "azimi");
// $db_list = array (
//     'test',
//     'world',
//     'album'
// );
// *********************************

// ***** uncomment for SQLite 3
define("DBENGINE", "sqlite3");
define("DBDIR", "/Users/hasan_azimi0/Sites/phpLiteAdmin/db");
$db_list = array (
    ':memory:',
    'world.db',
    'album.db',
    'test.db'
);
// *********************************

// ***** uncomment for MySQL
// define("DBENGINE", "mysql");
// define("MYSQLUSER", "hassan");
// define("MYSQLPASS", "azimi");
// $db_list = array (
//     '--NONE--',
//     'scratch',
//     'album',
//     'world'
// );
// *********************************

_init();
main();
page();

function main()
{
    if(isset($_REQUEST['a'])) jump($_REQUEST["a"]);
}

function _init( )
{
    global $SID;
    global $db_list;
    $default_db = $db_list[0];

    // initialize display vars
    foreach ( array( 'MESSAGES', 'ERRORS', 'CONTENT', 'SQLfield' ) as $v )
        $SID[$v] = '';

    // connect to the database (persistent)
    $database = (isset($_REQUEST['select_database'])) ? $_REQUEST['select_database'] : $default_db;
    if($database == '--NONE--') $database = $default_db;
    $SID['utf8'] = FALSE;
    try {
        switch(DBENGINE) {
            case 'sqlite3':
                // don't add the DBDIR to :memory: you'll create a file
                if($database == ':memory:') $dbh = new PDO('sqlite::memory:', 'unused', 'unused');
                else $dbh = new PDO('sqlite:' . implode('/', array(DBDIR, $database)), 'unused', 'unused');
                $dbh->sqliteCreateFunction('SEC_TO_TIME', 'sec_to_time', 1);        // custom functions ...
                $dbh->sqliteCreateFunction('TIME_TO_SEC', 'time_to_sec', 1);
                $dbh->sqliteCreateAggregate('SUM_SEC_TO_TIME',
                    'sum_sec_to_time_step', 'sum_sec_to_time_finalize', 1);
                $dbh->sqliteCreateFunction('REPLACE_REGEX', 'replace_regex', 3);
                $dbh->sqliteCreateAggregate('AVG_LENGTH',
                    'avg_length_step', 'avg_length_finalize', 1);
                $SID['DBVERSION'] = SQLite3::version();
                $SID['DBVERSION'] = 'SQLite version ' . $SID['DBVERSION']['versionString'];
                $SID['utf8'] = TRUE;
                break;
            case 'pgsql':
                if($database == '--NONE--') $database = 'test';
                $dbh = new PDO('pgsql:host=localhost;port=5432;dbname=' . $database, PGSQLUSER, PGSQLPASS,
                    array( PDO::ATTR_PERSISTENT => true ));
                $dbh->exec("set client_encoding to 'latin1'");
                $sth = $dbh->query('SELECT VERSION()');
                $SID['DBVERSION'] = explode(' ', $sth->fetchColumn());
                $SID['DBVERSION'] = 'PostgreSQL server version ' . $SID['DBVERSION'][1];
                break;
            case 'mysql':
                if($database == '--NONE--') $database = '';
                $dbh = new PDO('mysql:host=localhost;dbname=' . $database, MYSQLUSER, MYSQLPASS,
                    array( PDO::ATTR_PERSISTENT => true ));
                $dbh->exec('set character_set_client = utf8');
                $dbh->exec('set character_set_connection = utf8');
                $dbh->exec('set character_set_database = utf8');
                $dbh->exec('set character_set_results = utf8');
                $dbh->exec('set character_set_server = utf8');
                $sth = $dbh->query("SHOW VARIABLES WHERE Variable_name = 'version'");
                $SID['DBVERSION'] = 'MySQL server version ' . $sth->fetchColumn(1);
                $SID['utf8'] = TRUE;
                break;
            default:
                error('unsupported DBENGINE: ' . DBENGINE);
        }
    } catch (PDOException $e) {
        error("Error while constructing PDO object: " . $e->getMessage());
    }

    if($dbh) {
        // set exception mode for errors (why is this not the default?)
        // this is far more portable for different DB engines than trying to
        // parse error codes
        $dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $SID['dbh'] = $dbh;
    } else {
        exit();
    }

    // try to set the timezone to UTC for mysql
    // ignore error -- TZ support not installed in default win xampp
    if( $dbh && DBENGINE == 'mysql' ) {
        try {
            $dbh->exec('set time_zone = UTC');
        } catch (PDOException $e) {
            // ignore
        }
    }

    $SID['TITLE'] = "SQL Demo";
    $SID['SELF'] = $_SERVER["SCRIPT_NAME"];
    $SID['DATABASE_SELECT_LIST'] = database_select_list($database);

    // fixup missing common characters from the PHP entity translation table
    // (this is only used for latin1 conversions)
    $SID['xlat'] = get_html_translation_table(HTML_ENTITIES, ENT_NOQUOTES);
    $SID['xlat'][chr(130)] = '&sbquo;';     // Single Low-9 Quotation Mark
    $SID['xlat'][chr(131)] = '&fnof;';      // Latin Small Letter F With Hook
    $SID['xlat'][chr(132)] = '&bdquo;';     // Double Low-9 Quotation Mark
    $SID['xlat'][chr(133)] = '&hellip;';    // Horizontal Ellipsis
    $SID['xlat'][chr(136)] = '&circ;';      // Modifier Letter Circumflex Accent
    $SID['xlat'][chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
    $SID['xlat'][chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
    $SID['xlat'][chr(140)] = '&OElig;';     // Latin Capital Ligature OE
    $SID['xlat'][chr(145)] = '&lsquo;';     // Left Single Quotation Mark
    $SID['xlat'][chr(146)] = '&rsquo;';     // Right Single Quotation Mark
    $SID['xlat'][chr(147)] = '&ldquo;';     // Left Double Quotation Mark
    $SID['xlat'][chr(148)] = '&rdquo;';     // Right Double Quotation Mark
    $SID['xlat'][chr(149)] = '&bull;';      // Bullet
    $SID['xlat'][chr(150)] = '&ndash;';     // En Dash
    $SID['xlat'][chr(151)] = '&mdash;';     // Em Dash
    $SID['xlat'][chr(152)] = '&tilde;';     // Small Tilde
    $SID['xlat'][chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
    $SID['xlat'][chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
    $SID['xlat'][chr(156)] = '&oelig;';     // Latin Small Ligature OE
    $SID['xlat'][chr(159)] = '&Yuml;';      // Latin Capital Letter Y With Diaeresis

    // loose "index.php" if nec (regexes are fugly in php. Feh.)
    $SID["SELF"] = preg_replace('/([\\/\\\])index\\.php$/i', '$1', $SID["SELF"]);
}

function page( )
{
    global $SID;
    set_vars();

    require_once "assets/header.php";
    require_once "assets/main.php";
    require_once "assets/footer.php";
}

function jump( $action )
{
    switch($action) {
        case "go":
            do_sql($_REQUEST['SQLfield']);  // don't need stripslashes here?
            break;
    }
    return;
}

//

function do_sql( $query )
{
    global $SID;
    $dbh = $SID['dbh'];

    // do some cleanup and input checking
    $query = trim($query);     // trim leading and trailing spaces
    $query_list = split_queries($query);    // 2.1 - instead of explode
    $qcount = count($query_list);

    $SID['query_start_time'] = microtime(TRUE);

    $stmt_count = 0;
    $select_row_count = 0;
    $affected_row_count = 0;
    $select_qcount = 0;
    $non_select_qcount = 0;

    foreach ( $query_list as $k => $query ) {
        $query = strip_sql_comments($query);
        $qlen = strlen($query);

        if($qlen < 1) continue;     // skip empties
        else $stmt_count++;

        // debug -- display the query
        // message('%d: [%s]', $stmt_count, $query);

        if(is_select($query)) {
            // select statement
            try {
                $sth = $dbh->prepare($query);
                if($sth) {
                    $sth->execute();
                    $rc = select_results($sth, $qcount, $k + 1);
                    if($rc) {
                        $select_row_count += $rc;
                    }
                    if(DBENGINE == 'pgsql') {
                        // count affected rows for PostgreSQL
                        // note: must subtract $rc (returned rows) because
                        // PDO/pgsql incorrectly counts rows returned by SELECT statements as affected rows
                        $affected_row_count += ($sth->rowCount() - $rc);
                    }
                }
            } catch (PDOException $e) {
                // 2014-03-27 bw -- MySQL is putting out some bogus "general error" errors
                if($e->getCode() != 'HY000') {
                    error_message('query #%d: %s', $k + 1, $e->getMessage());
                }
            }
            $select_qcount++;
        } else {
            // non-select statement
            try {
                $sth = $dbh->prepare($query);
                if($sth) {
                    $sth->execute();
                    $affected_row_count += $sth->rowCount();
                }
            } catch (PDOException $e) {
                error_message('query #%d: %s', $k + 1, $e->getMessage());
            }
            $non_select_qcount++;
        }
    }

    // report statistics on results
    $elapsed_time = microtime(TRUE) - $SID['query_start_time'];
    $messages = array();
    if($stmt_count > 1) {
        array_push($messages, sprintf('%s queries performed', number_format($stmt_count)));
    }
    if($affected_row_count) array_push($messages, sprintf('%s rows affected', number_format($affected_row_count)));
    if($select_row_count) array_push($messages, sprintf('%s rows returned', number_format($select_row_count)));
    array_push($messages, sprintf('elapsed time: %s milliseconds', number_format($elapsed_time * 1000, 2)));
    message(join('; ', $messages) . '.');
}

function select_results( &$sth, $qcount, $qnum = NULL )
{
    global $SID;
    // $a is an accumulator for the output string
    $a = "\n";
    $a .= "<table class=\"results\">\n";
    $sth->setFetchMode(PDO::FETCH_ASSOC);

    // get the first row separately so we know if there are any results to display
    $row = $sth->fetch();
    if( ! $row ) {
        return 0;
    }

    $col_names = array_keys($row);

    // table heading
    $a .= "<tr>\n";
    foreach( $col_names as $name ) {
        $a .= "<td class=\"column_head\">$name</td>\n";
    }
    $a .= "</tr>\n";

    // the first row
    $a .= result_row($row);
    $row_count = 1;

    // the rest of the rows
    foreach( $sth as $row ) {
        $a .= result_row($row);
        $row_count ++;
    }

    $a .= "</table>\n";

    if($qcount > 1) {
        content(sprintf('<p class="message">Query %d:</p> %s', $qnum, $a));
    } else {
        content($a);
    }
    return $row_count;
}

function result_row( &$row )
{
    global $SID;
    $a = "<tr>\n";
    foreach( $row as $v ) {
        // show NULL values in red
        if( !isset($v) ) $v = "<span class=\"red\">NULL</span>\n";
        else $v = make_entities($v);

        $a .= "<td class=\"cell_value\">" . $v . "</td>\n";
    }
    $a .= "</tr>\n";
    return $a;
}

function database_select_list( $database )
{
    global $SID;
    global $db_list;
    $a = '';

    if(isset($SID['dbh'])) $dbh = $SID['dbh'];
    else return;

    switch(DBENGINE) {
        case 'mysql':
            try {
                $sth = $dbh->query("SHOW DATABASES");
            } catch (PDOException $e) {
                error_message($e->getMessage());
                return;
            }

            $a = "<option value=\"--NONE--\">-- Select Database --</option>\n";

            while( $row = $sth->fetch() ) {
                $v = $row['Database'];
                foreach( $db_list as $s ) {
                    if($v == $s) {
                        $selected = ($v == $database) ? ' selected' : '';
                        $a .= "<option$selected>$v</option>\n";
                    }
                }
            }
            break;
        case 'sqlite3':
            // use all the databases in DBDIR
            $d = dir(DBDIR);
            $a = "<option>:memory:</option>\n";    // start list with in-memory db
            while(($fn = $d->read()) !== FALSE) {
                if(substr($fn, 0, 1) == '.') { continue; }
                $selected = ($fn == $database) ? ' selected' : '';
                $a .= "<option$selected>$fn</option>\n";
            }
            /*  // previous behavior was:
                foreach($db_list as $s) {
                    $selected = ($s == $database) ? ' selected' : '';
                    $a .= "<option$selected>$s</option>\n";
                }
            */
            break;
        case 'pgsql':
            try {
                $sth = $dbh->query("
                    SELECT datname
                        FROM pg_database
                        WHERE datname NOT IN ('template1', 'template0', 'postgres')
                ");
            } catch (PDOException $e) {
                error_message($e->getMessage());
                return;
            }

            $a = "<option value=\"--NONE--\">-- Select Database --</option>\n";

            while( $row = $sth->fetch() ) {
                $v = $row['datname'];
                foreach( $db_list as $s ) {
                    if($v == $s) {
                        $selected = ($v == $database) ? ' selected' : '';
                        $a .= "<option$selected>$v</option>\n";
                    }
                }
            }
            break;
        }
    return $a;
}

// custom functions for SQLite 3

// SEC_TO_TIME( seconds INTEGER )
function sec_to_time( $sec )
{
    if(is_null($sec)) return NULL;
    $sec = intval($sec);    // make sure it's an integer
    $s = $sec % 60;
    $m = $sec / 60;
    return sprintf('%d:%02d', $m, $s);
}

// TIME_TO_SEC( time TEXT )  -- 'mm:ss'
function time_to_sec( $time )
{
    if(is_null($time)) return NULL;
    $t = explode(':', $time, 2);
    $m = intval($t[0]);
    $s = intval($t[1]);
    return ( $m * 60 ) + $s;
}

// SUM_SEC_TO_TIME
function sum_sec_to_time_step ($context, $rownumber, $value)
{
    if(is_null($value)) return $context;
    if(is_null($context)) $context = 0;
    $context += intval($value);     // accumulate the sum of the values
    return $context;
}

function sum_sec_to_time_finalize ( $context, $rownumber )
{
    $sec = $context;
    $s = $sec % 60;
    $m = $sec / 60;
    $h = 0;
    if($m > 60) {
        $h = $m / 60;
        $m = $m % 60;
    }
    return sprintf('%d:%02d:%02d', $h, $m, $s);
}

// REPLACE_REGEX( string TEXT, pattern TEXT, replace TEXT )
function replace_regex( $string, $pattern, $replace )
{
    if($pattern[0] != '/') $pattern = '/' . $pattern . '/';
    return @preg_replace($pattern, $replace, $string);
}

// AVG_LENGTH
function avg_length_step ($context, $rownumber, $value)
{
    if(is_null($value)) return $context;
    if(is_null($context)) {
        $context = array();
        $context['count'] = 1;
        $context['sum'] = strlen($value);
    } else {
        $context['sum'] += strlen($value);
        $context['count'] ++;
    }
    return $context;
}

// AVG_LENGTH
function avg_length_finalize ( $context, $rownumber )
{
    return $context['sum'] / $context['count'];
}

// utility functions

function is_select($query)
{
    switch(DBENGINE) {
        case 'mysql':
            $select_list = array( 'SELECT', 'DESCRIBE', 'SHOW', 'CALL' );
            break;
        case 'sqlite3':
            $select_list = array( 'SELECT', 'EXPLAIN', 'PRAGMA' );
            break;
        case 'pgsql':
            $select_list = array( 'SELECT', 'SHOW', 'TABLE', 'INSERT', 'EXPLAIN' );
            break;
        default:
            $select_list = array( 'SELECT' );
            break;
    }
    foreach ($select_list as $s) {
        if(strncmp(strtoupper($query), $s, strlen($s)) == 0) return TRUE;
    }
}

// split queries into array
// 2014-02-15 bw -- added support for MySQL-like DELIMITER
function split_queries($q_string)
{
    global $SID;
    $SID['del_kw'] = 'delimiter';
    $delimiter = ';';
    $q_array = array();

    if ( ( $i = stripos( $q_string, $SID['del_kw'] ) ) !== FALSE ) {
        $first_string = substr($q_string, 0, $i);
        $out_array = array(array('delimiter' => ';', 'string' => substr($q_string, 0, $i)));
        $out_array = array_merge($out_array, process_delimiters( $q_string ));
        foreach ( $out_array as $o ) {
            $q_array = array_merge( $q_array, split_parts($o['delimiter'], $o['string']));
        }
    } else {
        $q_array = array_merge( $q_array, split_parts($delimiter, $q_string));
    }

    return $q_array;
}

// handles CREATE TRIGGER correctly
function split_parts($delimiter, $q_string)
{
    global $SID;
    $q_array = array();
    $q_parts = explode($delimiter, $q_string);

    while($q_parts) {
        $qp = array_shift($q_parts);
        if( ($i = stripos($qp, 'TRIGGER') !== FALSE ) > 0
            and ($j = stripos($qp, 'CREATE') !== FALSE ) > 0
            and $j < $i
            and ( stripos($qp, 'BEGIN') !== FALSE )
        ) {
            // we have a CREATE TRIGGER statement
            // keep its parts together until we see the END
            while($q_parts) {
                if(stripos($qp, 'END')) break;
                else $qp .= ";\n" . array_shift($q_parts);
            }
        }
        $qp = trim($qp);
        if(strlen($qp) > 0) $q_array[] = $qp;
    }

    return $q_array;
}

// 2014-02-15 bw -- support MySQL-like DELIMITER
function process_delimiters ( $input_string ) {
    global $SID;
    $out = array();
    $chunk = array();
    $cur_delim = ';';

    $index = 0;
    while ( $index < strlen($input_string) ) {
        $rc = preg_match_all( "/.*" . $SID["del_kw"] . "\s*(\S+)(.*)/i", substr($input_string, $index), $chunk, PREG_OFFSET_CAPTURE );
        if ( $rc == 0 ) break;

        $delim = $chunk[1][0][0];
        $index += $chunk[2][0][1];
        $str = substr($input_string, $index);
        if ( $rc > 1 ) $str = substr( $str, 0, stripos( $str, $SID["del_kw"] ) );

        $chunk_array = array( 'delimiter' => $delim, 'string' => $str );
        array_push( $out, $chunk_array );
    }

    return $out;
}

// strip comments from query
function strip_sql_comments( $q )
{
    $lines = explode("\n", $q);     # break it into lines
    foreach($lines as $i => $l) {
        if(($index = strpos($l, SQLCOMMENT)) !== FALSE) {   # has comment?
            if($index == 0) unset($lines[$i]);
            else $lines[$i] = substr($l, 0, $index);
        }
    }
    return implode("\n", $lines);
}

function make_entities( $s )
{
    global $SID;
    if($SID['utf8']) {
        $s = htmlentities($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    } else {
        $s = strtr( $s, $SID['xlat'] );
    }
    return $s;
}

function set_vars( )
{
    global $SID;
    if(isset($SID["_MSG_ARRAY"])) foreach ( $SID["_MSG_ARRAY"] as $m ) $SID["MESSAGES"] .= $m;
    if(isset($SID["_ERR_ARRAY"])) foreach ( $SID["_ERR_ARRAY"] as $m ) $SID["ERRORS"] .= $m;
    if(isset($SID["_CON_ARRAY"])) foreach ( $SID["_CON_ARRAY"] as $m ) $SID["CONTENT"] .= $m;
    if(isset($_REQUEST['SQLfield'])) $SID['SQLfield'] = htmlspecialchars($_REQUEST['SQLfield']);  // stripslashes?
}

function content( $s )
{
    global $SID;
    $SID["_CON_ARRAY"][] = "\n<div class=\"content\">$s</div>\n";
}

function message()
{
    global $SID;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $SID["_MSG_ARRAY"][] = "<p class=\"message\">$s</p>\n";
}

function error_message()
{
    global $SID;
    $args = func_get_args();
    if(count($args) < 1) return;
    $s = vsprintf(array_shift($args), $args);
    $SID["_ERR_ARRAY"][] = "<p class=\"error_message\">$s</p>\n";
}

function error( $s )
{
    error_message($s);
    page();
}
