
-- SIMPLE SUBSELECT
DROP TABLE IF EXISTS t;
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'NY0123', 'US4567' );
INSERT INTO t VALUES ( 'AZ9437', 'GB1234' );
INSERT INTO t VALUES ( 'CA1279', 'FR5678' );
SELECT SUBSTR(b, 1, 2) AS Country, SUBSTR(b, 3) AS CoValue FROM t;

SELECT co.Name, tt.CoValue FROM (
    SELECT SUBSTR(a, 1, 2) AS State, SUBSTR(a, 3) AS StValue,
      SUBSTR(b, 1, 2) AS Country, SUBSTR(b, 3) AS CoValue FROM t
  ) AS tt
  JOIN Country AS co ON tt.Country = co.Code2;

-- SEARCHING WITHIN A RESULT SET
DROP TABLE IF EXISTS t;
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'NY0123', 'US4567' );
INSERT INTO t VALUES ( 'AZ9437', 'GB1234' );
INSERT INTO t VALUES ( 'CA1279', 'FR5678' );
SELECT SUBSTR(a, 1, 2) AS State, SUBSTR(a, 3) AS StValue,
  SUBSTR(b, 1, 2) AS Country, SUBSTR(b, 3) AS CoValue FROM t;

SELECT co.Name AS Country, ci.Name AS City FROM City AS ci
  JOIN Country AS co ON ci.CountryCode = co.Code
  WHERE co.Code2 IN (
    SELECT  SUBSTR(b, 1, 2)FROM t
  );


-- JOINED SUBSELECT
SELECT a.artist AS artist, a.title AS album, t.title AS track, t.track_number AS trackno, t.duration
  FROM track AS t JOIN album AS a ON a.id = t.album_id;

SELECT a.artist AS artist, a.title AS album, t.title AS track, t.track_number AS trackno, 
    t.duration / 60 AS m, t.duration % 60 AS s
  FROM track AS t JOIN album AS a ON a.id = t.album_id;

SELECT artist, album, track, trackno, 
  m || ':' || CASE WHEN s < 10 THEN '0' || s ELSE s END AS duration
  FROM (
    SELECT a.artist AS artist, a.title AS album, t.title AS track, t.track_number AS trackno, 
        t.duration / 60 AS m, t.duration % 60 AS s
      FROM track AS t JOIN album AS a ON a.id = t.album_id
    ) ORDER BY artist, album, trackno;

-- SIMPLE VIEW
DROP TABLE IF EXISTS t;
CREATE TABLE t ( a, b );
INSERT INTO t VALUES ( 'NY0123', 'US4567' );
INSERT INTO t VALUES ( 'AZ9437', 'GB1234' );
INSERT INTO t VALUES ( 'CA1279', 'FR5678' );
SELECT SUBSTR(a, 1, 2) AS State, SUBSTR(a, 3) AS StValue,
  SUBSTR(b, 1, 2) AS Country, SUBSTR(b, 3) AS CoValue FROM t;

DROP VIEW IF EXISTS unpackData;
CREATE VIEW unpackData AS
  SELECT SUBSTR(a, 1, 2) AS State, SUBSTR(a, 3) AS StValue,
    SUBSTR(b, 1, 2) AS Country, SUBSTR(b, 3) AS CoValue FROM t;

SELECT * FROM unpackData AS tt;

SELECT co.Name, tt.CoValue FROM unpackData AS tt
  JOIN Country AS co ON tt.Country = co.Code2;

-- JOINED VIEW
SELECT a.artist AS artist, a.title AS album, t.title AS track, t.track_number AS trackno, 
    t.duration / 60 AS m, t.duration % 60 AS s
  FROM track AS t JOIN album AS a ON a.id = t.album_id;

DROP VIEW IF EXISTS JoinedAlbum;
CREATE VIEW JoinedAlbum AS
    SELECT a.artist AS artist, a.title AS album, t.title AS track, t.track_number AS trackno, 
        t.duration / 60 AS m, t.duration % 60 AS s
      FROM track AS t JOIN album AS a ON a.id = t.album_id;

SELECT artist, album, track, trackno, 
    m || ':' || CASE WHEN s < 10 THEN '0' || s ELSE s END AS duration
    FROM JoinedAlbum;
