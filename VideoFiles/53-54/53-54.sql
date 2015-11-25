-- TRANSACTIONS
CREATE TABLE item ( id INTEGER PRIMARY KEY, name TEXT, description TEXT );
CREATE TABLE inventory ( id INTEGER PRIMARY KEY, item_id INTEGER, quantity INTEGER );
CREATE TABLE sale ( id INTEGER PRIMARY KEY, item_id INTEGER, quantity INTEGER, price INTEGER );
INSERT INTO item ( name, description ) VALUES ( 'Big Monitor', 'Flat screen. Billions of colors.' );
INSERT INTO item ( name, description ) VALUES ( 'Tower computer', 'Really fast. Cool looking case.' );
INSERT INTO item ( name, description ) VALUES ( 'External storage', 'Lots of space for your data.' );
INSERT INTO item ( name, description ) VALUES ( 'Printer', 'Small and fast.' );
INSERT INTO inventory ( item_id, quantity ) VALUES ( 1, 127 );
INSERT INTO inventory ( item_id, quantity ) VALUES ( 2, 42 );
INSERT INTO inventory ( item_id, quantity ) VALUES ( 3, 12 );
INSERT INTO inventory ( item_id, quantity ) VALUES ( 4, 768 );
SELECT it.id, it.name, inv.quantity FROM inventory AS inv
  JOIN item AS it ON it.id = inv.item_id
  ORDER BY it.name;

BEGIN;
	INSERT INTO sale ( item_id, quantity, price ) VALUES ( 4, 12, 1995 );
	UPDATE inventory SET quantity = ( SELECT quantity FROM inventory WHERE id = 4 ) - 12 WHERE id = 4;
COMMIT;

SELECT it.id, it.name, inv.quantity FROM inventory AS inv
  JOIN item AS it ON it.id = inv.item_id
  ORDER BY it.name;

SELECT it.name, s.quantity FROM sale AS s
  JOIN item AS it ON it.id = s.item_id
  ORDER BY it.name;

-- ###

CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );


-- ROLLBACK
DROP TABLE IF EXISTS t;
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
SELECT * FROM t;
BEGIN;
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
ROLLBACK;  -- OR COMMIT
SELECT * FROM t;

-- SAVEPOINT
http://www.sqlite.org/lang_savepoint.html

DROP TABLE IF EXISTS t;
CREATE TABLE t ( a, b );
SAVEPOINT 'one';
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
SAVEPOINT 'two';
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
INSERT INTO t VALUES ( 'onetwothreefourfive', 'sixseveneightnineten' );
ROLLBACK TO 'two';
SAVEPOINT 'three';
INSERT INTO t VALUES ( 'sixseveneightnineten', 'abcdefg' );
INSERT INTO t VALUES ( 'sixseveneightnineten', 'abcdefg' );
INSERT INTO t VALUES ( 'sixseveneightnineten', 'abcdefg' );
INSERT INTO t VALUES ( 'sixseveneightnineten', 'abcdefg' );
INSERT INTO t VALUES ( 'sixseveneightnineten', 'abcdefg' );
RELEASE 'three';
SELECT * FROM t;


