-- LENGTH
SELECT Name, LENGTH(Name), Code, Code2 FROM Country;

SELECT Name, Code, Code2 FROM Country WHERE LENGTH(Name) >= 10;

SELECT Name, LENGTH(Name) AS Len, Code, Code2 FROM Country
  WHERE LENGTH(Name) BETWEEN 10 AND 12
  ORDER BY Len DESC;

-- UPPER
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'Aaa', 'Bbb' );
INSERT INTO t VALUES ( 'aaa', 'bab' );
INSERT INTO t VALUES ( 'ABA', 'BbB' );
SELECT * FROM t ORDER BY a;
SELECT * FROM t ORDER BY UPPER(a);
SELECT * FROM t ORDER BY a COLLATE NOCASE;

-- SUBSTR
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'NY0123', 'US4567' );
INSERT INTO t VALUES ( 'AZ0123', 'UK4567' );
INSERT INTO t VALUES ( 'CA0123', 'FR4567' );
SELECT SUBSTR(a, 1, 2) AS State, SUBSTR(a, 3) AS StValue,
    SUBSTR(b, 1, 2) AS Country, SUBSTR(b, 3) AS CoValue FROM t;

-- REPLACE
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'Thing One', 'Thing Two' );
INSERT INTO t VALUES ( 'Thing Three', 'Thing Four' );
INSERT INTO t VALUES ( 'Thing Five', 'Thing Six' );
SELECT * FROM t;
SELECT REPLACE(a, 'Thing', 'Other') FROM t;

-- TRIM
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( '  Thing One  ', '  Thing Two  ' );
INSERT INTO t VALUES ( '  Thing Three  ', '  Thing Four  ' );
INSERT INTO t VALUES ( '  Thing Five  ', '  Thing Six  ' );
SELECT '[' || a || ']', '[' || b || ']' FROM t;
SELECT '[' || TRIM(a) || ']', '[' || b || ']' FROM t;

-- ABS
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 1, 2 );
INSERT INTO t VALUES ( 3, 4 );
INSERT INTO t VALUES ( -1, -2 );
INSERT INTO t VALUES ( -3, -4 );
SELECT a, b FROM t;
SELECT ABS(a), b FROM t;

-- ROUND
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 123.456, 456.789 );
INSERT INTO t VALUES ( -123.456, -456.789 );
SELECT ROUND(a), b FROM t;
SELECT ROUND(a, 2), b FROM t;

-- TYPEOF
CREATE TABLE t ( a INT, b REAL, c TEXT );
INSERT INTO t VALUES ( 1, 2, 3 );
INSERT INTO t VALUES ( 'a', 'b', 'c' );
INSERT INTO t VALUES ( 'one', 'two', 'three' );
INSERT INTO t VALUES ( '1', '2', '3' );
SELECT * FROM t;
SELECT TYPEOF(a), TYPEOF(b), TYPEOF(c) FROM t;

-- LAST_INSERT_ROWID
CREATE TABLE t ( id INTEGER PRIMARY KEY AUTOINCREMENT, a, b, c );
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
SELECT LAST_INSERT_ROWID();

-- SQLITE_VERSION

SELECT SQLITE_VERSION()
