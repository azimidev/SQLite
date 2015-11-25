<?php

class bwSQLite3 {
    const VERSION = "1.0.9";
    private $dbh = NULL;
    private $sth = NULL;
    private $timer = NULL;
    private $table_name;

    public function __construct( $filename = ':memory:', $table_name = NULL )
    {
        $this->dbh = new PDO('sqlite:' . $filename, 'unused', 'unused', 
            array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ));
        if($table_name) $this->table_name = $table_name;
    }

    // table_name setter/getter
    public function table_name($name = NULL)
    {
        if($name) $this->table_name = $name;
        return $this->table_name;
    }

    // dbh (PDO::PDO handle) setter/getter
    public function dbh($h = NULL)
    {
        if($h) $this->dbh = $h;
        return $this->dbh;
    }

    // sth (PDO::Statement handle) setter/getter
    public function sth($h = NULL)
    {
        if($h) $this->sth = $h;
        return $this->sth;
    }

    // sql_do( query [, arg[, ...]] )
    // perform SQL without a result set
    // returns count of rows affected
    public function sql_do()
    {
        $args = func_get_args();
        if(count($args) < 1) throw new PDOException('sql_do - missing query');
        $stmt = $this->dbh->prepare(array_shift($args));
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    // sql_do_multiple( query )
    // perform SQL without a result set
    // returns count of rows affected
    public function sql_do_multiple($query)
    {
        return $this->dbh->exec($query);
    }

    // sql_query( query [, arg[, ...]] )
    // returns iterable statement handle
    // sets up a query for get_next()
    public function sql_query()
    {
        $args = func_get_args();
        if(count($args) < 1) throw new PDOException('sql_query - missing query');
        $this->sth = $this->dbh->prepare(array_shift($args));
        $this->sth->setFetchMode(PDO::FETCH_ASSOC);
        $this->sth->execute($args);
        return $this->sth;
    }

    // sql_query_all( query [, arg[, ...]] )
    // returns fetchAll result (be careful!)
    public function sql_query_all()
    {
        $args = func_get_args();
        if(count($args) < 1) throw new PDOException('sql_query_all - missing query');
        $stmt = $this->dbh->prepare(array_shift($args));
        $stmt->execute($args);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // sql_query_row( query [, arg[, ...]] )
    // returns one row from fetch
    public function sql_query_row()
    {
        $args = func_get_args();
        if(count($args) < 1) throw new PDOException('sql_query_row - missing query');
        $stmt = $this->dbh->prepare(array_shift($args));
        $stmt->execute($args);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // sql_query_value( query [, arg[, ...]] )
    // returns one value
    public function sql_query_value()
    {
        $args = func_get_args();
        if(count($args) < 1) throw new PDOException('sql_query_value - missing query');
        $stmt = $this->dbh->prepare(array_shift($args));
        $stmt->execute($args);
        return $stmt->fetchColumn();
    }

    // begin_transaction()
    public function begin_transaction()
    {
        $this->dbh->beginTransaction();
    }
    
    // commit()
    public function commit()
    {
        $this->dbh->commit();
    }

    // get_rec( id )
    // returns assoc_array
    public function get_rec( $id )
    {
        if($this->table_name === NULL) throw new PDOException('get_rec - missing table');
        $stmt = $this->dbh->prepare('SELECT * FROM ' . $this->table_name . ' WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // get_recs()
    // CRUD query to get all rows
    // returns iterable statement handle
    // sets up statement handle for get_next()
    public function get_recs()
    {
        if($this->table_name === NULL) throw new PDOException('get_recs - missing table');
        $this->sth = $this->dbh->prepare('SELECT * FROM ' . $this->table_name . ' ORDER BY id');
        $this->sth->setFetchMode(PDO::FETCH_ASSOC);
        $this->sth->execute();
        return $this->sth;
    }

    // insert()
    // inserts a CRUD record
    // returns new id value
    public function insert($rec)
    {
        if($this->table_name === NULL) throw new PDOException('insert - missing table');
        $keys = array_keys($rec);
        $vals = array_values($rec);
        $query = 'INSERT INTO ' . $this->table_name .
            ' (' . join(', ', $keys) . ') VALUES' .
            ' (' . join(', ', array_fill(0, count($vals), '?')) . ')';
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($vals);
        return $this->dbh->lastInsertId();
    }

    // update(id, rec)
    // updates a CRUD record
    // returns count of rows affected
    public function update($id, $rec)
    {
        if($this->table_name === NULL) throw new PDOException('insert - missing table');
        if(array_key_exists('id', $rec)) unset($rec['id']); // updating the id column would be bad
        $keys = array_keys($rec);
        $vals = array_values($rec);
        $sql_assignments = array();
        foreach($keys as $k) array_push($sql_assignments, "$k = ?");
        $query = 'UPDATE ' . $this->table_name .
            ' SET ' . join(', ', $sql_assignments) . 
            ' WHERE id = ?';
        $stmt = $this->dbh->prepare($query);
        $vals[] = $id;  // append id to the parameter list
        $stmt->execute($vals);
        return $stmt->rowCount();
    }

    // delete( id )
    // deletes a CRUD row
    // returns count of rows affected
    public function delete( $id )
    {
        if($this->table_name === NULL) throw new PDOException('delete - missing table');
        $stmt = $this->dbh->prepare('DELETE FROM ' . $this->table_name . ' WHERE id = ?');
        $stmt->execute(array($id));
        return $stmt->rowCount();
    }

    // get_next()
    // pseudo-iterator function to fetch results from a saved statement handle
    public function get_next()
    {
        if($this->sth === NULL) return FALSE;
        $rc = $this->sth->fetch(PDO::FETCH_ASSOC);
        if($rc === FALSE) $this->sth = NULL;
        return $rc;
    }

    // count_recs()
    // returns count of recs in this->table_name
    public function count_recs()
    {
        if($this->table_name === NULL) throw new PDOException('count_recs missing table');
        return $stmt = $this->sql_query_value('SELECT COUNT(*) FROM ' . $this->table_name);
    }
    
    // table_exists( table_name )
    // returns TRUE or FALSE
    public function table_exists( $table_name = NULL )
    {
        if($table_name === NULL) $table_name = $this->table_name;
        if($this->sql_query_value("SELECT 1 FROM sqlite_master WHERE type = 'table' AND tbl_name = ?", $table_name))
            return TRUE;
        else return FALSE;
    }

    // version( s )
    // s = default (NULL) -- return bwSQLite3::VERSION constant
    // s = 'sqlite3' return sqlite3 version
    public function version( $s = NULL )
    {
        if($s == 'sqlite3') {
            return $this->sql_query_value('SELECT sqlite_version()');
        } else {
            return bwSQLite3::VERSION;
        }
    }

    // timer_start()
    // marks time for the timer
    public function timer_start()
    {
        $this->timer = microtime(TRUE);
    }

    // timer()
    // returns elapsed time in milliseconds as a number formatted string
    public function timer()
    {
        if($this->timer === NULL) return NULL;
        $t = microtime(TRUE) - $this->timer;
        $this->timer = NULL;
        return number_format($t * 1000, 2);
    }

}

?>
