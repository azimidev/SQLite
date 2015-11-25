-- date and time
CREATE TABLE t ( d1, d2 );
INSERT INTO t VALUES ( DATETIME('now'), DATETIME('now', '+7 days'));
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', '+7 days'));
SELECT d1, TYPEOF(d1), d2, TYPEOF(d2) FROM t;
SELECT DATETIME(d1), DATETIME(d2) FROM t;

-- unix epoch times are handled a little differently
CREATE TABLE t ( d1 int, d2 int );
INSERT INTO t VALUES ( STRFTIME('%s', 'now'), STRFTIME('%s', 'now', '+7 days'));
SELECT d1, TYPEOF(d1), d2, TYPEOF(d2) FROM t;
SELECT DATETIME(d1, 'unixepoch'), DATETIME(d2, 'unixepoch') FROM t;

-- DATE / TIME / DATETIME
CREATE TABLE t ( d1 TEXT, d2 TEXT );
INSERT INTO t VALUES ( DATETIME('now'), DATETIME('now', 'localtime'));
SELECT * FROM t;
http://www.sqlite.org/lang_datefunc.html

-- JULIANDAY
DROP TABLE IF EXISTS t;
CREATE TABLE t ( d1 REAL, d2 REAL );
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', 'localtime') );
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', 'localtime') );
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', 'localtime') );
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', 'localtime') );
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', 'localtime') );
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', 'localtime') );
INSERT INTO t VALUES ( JULIANDAY('now'), JULIANDAY('now', 'localtime') );
SELECT d1, STRFTIME('%Y-%m-%d %H:%M:%f', d1) AS d1_ISO, d2, STRFTIME('%Y-%m-%d %H:%M:%f', d2) AS d2_ISO FROM t;

-- STRFTIME
CREATE TABLE t ( d1 TEXT, d2 TEXT );
INSERT INTO t VALUES ( DATETIME('now'), DATETIME('now', 'localtime'));
SELECT STRFTIME('%Y-%m-%d %H:%M:%S', d1), STRFTIME('%Y-%m-%d %H:%M:%S', d2) FROM t;
SELECT STRFTIME('%s', d1), STRFTIME('%J', d2) FROM t;
http://www.sqlite.org/lang_datefunc.html
