-- Comparisons
SELECT CountryCode, Name FROM City WHERE CountryCode = 'GBR';

SELECT Name, Population / 1000000 AS 'Pop (MM)' FROM Country
    WHERE Population > 100000000
    ORDER by Population DESC;

SELECT Name, Population FROM Country
  WHERE Population BETWEEN 5000000 AND 10000000
  ORDER BY Population DESC;

SELECT Name FROM Country WHERE NAME BETWEEN 'G' AND 'R' ORDER BY Name;

-- LIKE
SELECT * FROM City WHERE Name LIKE 'z%' ORDER BY Name;
SELECT * FROM City WHERE Name LIKE '_w%' ORDER BY Name;
SELECT * FROM City WHERE Name GLOB 'Z*' ORDER BY Name;
SELECT * FROM City WHERE Name GLOB '?w*' ORDER BY Name;
SELECT * FROM City WHERE Name GLOB '[ZK]w*' ORDER BY Name;
-- Arithmetic operators

SELECT 5 * 30;
SELECT 7 / 3;
SELECT 7.0 / 3;
SELECT 7 % 3;

SELECT Name, Population / 1000000 AS PopMM FROM Country
    WHERE PopMM > 100
    ORDER by PopMM DESC;

SELECT item_id, price FROM sale;
SELECT item_id, CAST(price AS REAL) / 100 AS Price FROM sale;

-- IN
SELECT * FROM City WHERE CountryCode IN ('IRN', 'USA') ORDER BY Name;
SELECT * FROM City
WHERE CountryCode IN (
  SELECT Code FROM Country WHERE Name IN ('United States', 'Iran')
) ORDER BY Name;

-- CASE
CREATE TABLE booltest (a, b);
INSERT INTO booltest VALUES (1, 0);
SELECT * FROM booltest;
SELECT
    CASE WHEN a THEN 'TRUE' ELSE 'FALSE' END as boolA,
    CASE WHEN b THEN 'TRUE' ELSE 'FALSE' END as boolB
FROM booltest;

SELECT artist, album, track, trackno, 
    m || ':' || CASE WHEN s < 10 THEN '0' || s ELSE s END AS duration
    FROM (
        SELECT a.artist AS artist, a.title AS album, t.track_number AS trackno, t.title AS track,
            t.duration / 60 AS m, t.duration % 60 AS s
            FROM track AS t JOIN album AS a ON a.id = t.album_id
            WHERE t.album_id IN (
                SELECT id FROM album WHERE artist IN ('Jimi Hendrix', 'Johnny Winter')
            )
            ORDER BY album, trackno
    );

-- CAST
SELECT TYPEOF(1);
SELECT TYPEOF(CAST(1 AS TEXT));
SELECT TYPEOF(CAST(1 AS REAL));
SELECT TYPEOF(CAST(1 AS NUMERIC));
SELECT TYPEOF(CAST(NULL AS TEXT));


