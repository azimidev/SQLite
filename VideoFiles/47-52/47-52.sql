
-- ORDER BY
SELECT * FROM Country
SELECT * FROM Country ORDER BY Region
SELECT * FROM Country ORDER BY Region, Population
SELECT * FROM Country ORDER BY Region, Population DESC

-- DISTINCT
SELECT DISTINCT Region FROM Country;

SELECT DISTINCT CountryCode FROM City;
SELECT DISTINCT CountryCode, District FROM City;

-- indexes

DROP INDEX IF EXISTS co_code;
DROP INDEX IF EXISTS ci_code;
CREATE INDEX IF NOT EXISTS co_code ON Country (Code);
CREATE INDEX IF NOT EXISTS ci_code ON City (CountryCode);

SELECT co.name, AVG(ci.Population) AS AvgPop
    FROM City as ci
    JOIN Country AS co
        ON co.Code = ci.CountryCode
    GROUP BY ci.CountryCode
    ORDER BY AvgPop DESC;

-- PRIMARY KEY

CREATE TABLE t ( code PRIMARY KEY, value TEXT, ycode UNIQUE );
SELECT * FROM SQLITE_MASTER;
INSERT INTO t VALUES ( 'a', 'thing one', 'one' );
INSERT INTO t VALUES ( 'b', 'thing two', 'two' );
INSERT INTO t VALUES ( 'c', 'thing three', 'three' );
INSERT INTO t VALUES ( 'd', 'thing four', 'four' );
INSERT INTO t VALUES ( 'e', 'thing five', 'five' );
SELECT * FROM t;

-- INTEGER PRIMARY KEY

CREATE TABLE t ( id INTEGER PRIMARY KEY AUTOINCREMENT, a, b, c );
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
DELETE FROM t WHERE ID = 5;
INSERT INTO t (a, b, c) VALUES ('a', 'b', 'c');
SELECT * FROM t;
SELECT * FROM SQLITE_MASTER;
SELECT * FROM SQLITE_SEQUENCE;

