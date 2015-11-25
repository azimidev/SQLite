-- TRIGGERS
CREATE TABLE customer ( id INTEGER PRIMARY KEY, name TEXT, last_order_id INT );
CREATE TABLE sale ( id INTEGER PRIMARY KEY, item_id INT, customer_id INT, quan INT, price INT );
INSERT INTO customer (name) VALUES ('Amir');
INSERT INTO customer (name) VALUES ('Shadi');
INSERT INTO customer (name) VALUES ('Mahdi');
SELECT * FROM customer;

CREATE TRIGGER newsale AFTER INSERT ON sale
    BEGIN
        UPDATE customer SET last_order_id = NEW.id WHERE customer.id = NEW.customer_id;
    END;
SELECT * FROM sqlite_master;

INSERT INTO sale (item_id, customer_id, quan, price) VALUES (1, 3, 5, 1995);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (2, 2, 3, 1495);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (3, 1, 1, 2995);
SELECT * FROM sale;
SELECT * FROM customer;

DROP TRIGGER newsale;

-- LOGGING TRANSACTIONS

CREATE TABLE customer ( id INTEGER PRIMARY KEY, name TEXT, last_order_id INT );
CREATE TABLE sale ( id INTEGER PRIMARY KEY, item_id INT, customer_id INT, quan INT, price INT );
CREATE TABLE triggerlog ( id INTEGER PRIMARY KEY, stamp TEXT, event TEXT, triggername TEXT, tablename TEXT, table_id INT);
INSERT INTO customer (name) VALUES ('Amir');
INSERT INTO customer (name) VALUES ('Shadi');
INSERT INTO customer (name) VALUES ('Mahdi');
SELECT * FROM customer;

CREATE TRIGGER newsale AFTER INSERT ON sale
    BEGIN
        UPDATE customer SET last_order_id = NEW.id WHERE customer.id = NEW.customer_id;
        INSERT INTO triggerlog (stamp, event, triggername, tablename, table_id)
            VALUES (DATETIME('now'), 'UPDATE last_order_id', 'newsale', 'customer', NEW.customer_id);
    END;

INSERT INTO sale (item_id, customer_id, quan, price) VALUES (1, 3, 5, 1995);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (2, 2, 3, 1495);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (3, 1, 1, 2995);
SELECT * FROM sale;
SELECT * FROM customer;
SELECT * FROM triggerlog;

-- IMPROVING PERFORMANCE
CREATE TABLE customer ( id INTEGER PRIMARY KEY, name TEXT, last_order_id INT );
CREATE TABLE sale ( id INTEGER PRIMARY KEY, item_id INT, customer_id INT, quan INT, price INT);
CREATE TABLE item ( id INTEGER PRIMARY KEY, name TEXT, description TEXT );
CREATE TABLE report (id INTEGER PRIMARY KEY, item TEXT, customer TEXT, quan INT, price INT );
INSERT INTO customer (name) VALUES ('Amir');
INSERT INTO customer (name) VALUES ('Shadi');
INSERT INTO customer (name) VALUES ('Mahdi');
INSERT INTO item ( name, description ) VALUES ( 'Big Monitor', 'Flat screen. Billions of colors.' );
INSERT INTO item ( name, description ) VALUES ( 'Tower computer', 'Really fast. Cool looking case.' );
INSERT INTO item ( name, description ) VALUES ( 'External storage', 'Lots of space for your data.' );
SELECT * FROM customer;
SELECT * FROM item;

CREATE TRIGGER newsale AFTER INSERT ON sale
    BEGIN
        UPDATE customer SET last_order_id = NEW.id WHERE customer.id = NEW.customer_id;
        INSERT INTO report (item, customer, quan, price) 
            SELECT i.name, c.name, NEW.quan, NEW.price
                FROM item AS i
                JOIN customer AS c
                    ON c.id = NEW.customer_id
                WHERE i.id = NEW.item_id;
    END;

INSERT INTO sale (item_id, customer_id, quan, price) VALUES (1, 3, 5, 1995);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (2, 2, 3, 1495);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (3, 1, 1, 2995);
SELECT * FROM report;

-- PREVENTING UPDATES

CREATE TABLE customer ( id integer primary key, name TEXT, last_order_id INT );
CREATE TABLE sale ( id integer primary key, item_id INT, customer_id INTEGER, quan INT, price INT,
    reconciled INT );
INSERT INTO customer (name) VALUES ('Amir');
INSERT INTO customer (name) VALUES ('Shadi');
INSERT INTO customer (name) VALUES ('Mahdi');
INSERT INTO sale (item_id, customer_id, quan, price, reconciled) VALUES (1, 3, 5, 1995, 0);
INSERT INTO sale (item_id, customer_id, quan, price, reconciled) VALUES (2, 2, 3, 1495, 1);
INSERT INTO sale (item_id, customer_id, quan, price, reconciled) VALUES (3, 1, 1, 2995, 0);
SELECT * FROM sale;

CREATE TRIGGER update_sale BEFORE UPDATE ON sale
    BEGIN
        SELECT RAISE(ROLLBACK, 'cannot update table "sale"') FROM sale
            WHERE id = NEW.id AND reconciled = 1;
    END;

UPDATE sale SET quan = 9 WHERE id = 1;
UPDATE sale SET quan = 9 WHERE id = 2;
UPDATE sale SET quan = 9 WHERE id = 3;
SELECT * FROM sale;

-- TIMESTAMPS
CREATE TABLE customer ( id integer primary key, name TEXT, last_order_id INT, stamp TEXT );
CREATE TABLE sale ( id integer primary key, item_id INT, customer_id INTEGER, quan INT, price INT, stamp TEXT );
CREATE TABLE log ( id integer primary key, stamp TEXT, event TEXT, username TEXT, tablename TEXT, table_id INT);
INSERT INTO customer (name) VALUES ('Amir');
INSERT INTO customer (name) VALUES ('Shadi');
INSERT INTO customer (name) VALUES ('Mahdi');
SELECT * FROM customer;

CREATE TRIGGER newsale AFTER INSERT ON sale
    BEGIN
        UPDATE sale SET stamp = DATETIME('now') WHERE id = NEW.id;
        UPDATE customer SET last_order_id = NEW.id, stamp = DATETIME('now') WHERE customer.id = NEW.customer_id;
        INSERT INTO log (stamp, event, username, tablename, table_id)
            VALUES (DATETIME('now'), 'INSERT sale', 'TRIGGER', 'sale', NEW.id);
    END;

BEGIN;
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (1, 3, 5, 1995);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (2, 2, 3, 1495);
INSERT INTO sale (item_id, customer_id, quan, price) VALUES (3, 1, 1, 2995);
COMMIT;

SELECT * FROM sale;
SELECT * FROM customer;
SELECT * FROM log;

