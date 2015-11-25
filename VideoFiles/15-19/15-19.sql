-- INSERT
CREATE TABLE a (a, b, c);
CREATE TABLE b (d, e, f);
INSERT INTO a VALUES ('a', 'b', 'c');
INSERT INTO a VALUES ('a', 'b', 'c');
INSERT INTO a VALUES ('a', 'b', 'c');
INSERT INTO a VALUES ('a', 'b', 'c');
INSERT INTO a VALUES ('a', 'b', 'c');
INSERT INTO b SELECT * FROM a;
SELECT * FROM a;
INSERT INTO b (f, e, d) SELECT * FROM a;
INSERT INTO b (f, e, d) SELECT c, a, b FROM a;
SELECT * FROM b;

-- UPDATE
CREATE TABLE t ( id INTEGER PRIMARY KEY, quote TEXT, byline TEXT );
INSERT INTO t ( quote, byline ) VALUES ( 'Aye Carumba!', 'Bart Simpson' );
INSERT INTO t ( quote, byline ) VALUES ( 'But Bullwinkle, that trick never works!', 'Rocket J. Squirrel' );
INSERT INTO t ( quote, byline ) VALUES ( 'I know.', 'Han Solo' );
INSERT INTO t ( quote, byline ) VALUES ( 'Ahhl be baahk.', 'The Terminator' );
SELECT * FROM t;
UPDATE t SET quote = 'Hasta la vista, baby.' WHERE id = 4;
SELECT * FROM t WHERE id = 4;
UPDATE t SET quote = 'Rosebud.', byline = 'Charles Foster Kane' WHERE id = 4;
SELECT * FROM t WHERE id = 4;

-- SELECT
SELECT * FROM album;
SELECT title, artist, label FROM album;
SELECT artist, title, released FROM album;
SELECT artist, title AS album  FROM album;
    -- will use a lot with JOINs, VIEWs, sub-selects, etc.

SELECT * FROM track
WHERE album_id IN (
  SELECT id FROM album WHERE artist = 'Jimi Hendrix' OR artist = 'Johnny Winter'
);

SELECT a.title AS album, t.title AS track, t.track_number
    FROM album AS a, track AS t
    WHERE a.id = t.album_id
    ORDER BY a.title, t.track_number;

-- JOIN

PRAGMA table_info(CountryLanguage);

SELECT c.Name, l.Language
    FROM CountryLanguage AS l
    JOIN Country AS c
        ON l.CountryCode = c.Code

SELECT c.Name, l.Language
    FROM CountryLanguage AS l
    JOIN Country AS c
        ON l.CountryCode = c.Code
    WHERE c.Name = 'United States'

SELECT a.artist, a.title AS album, t.title AS track, t.track_number
  FROM track as t
  JOIN album AS a
    ON t.album_id = a.id
  ORDER BY a.artist, album, t.track_number

-- DELETE 

SELECT * FROM track WHERE title = 'Fake Track'
DELETE FROM track WHERE title = 'Fake Track'
