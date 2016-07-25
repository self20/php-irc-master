<?php
class mysql
{
    private $dbIndex;
    private $prefix;
    private $queries = 0;
    private $isConnected = false;
    private $user;
    private $pass;
    private $database;
    private $host;
    private $port;
    public function __construct($host, $database, $user, $pass, $prefix, $port = 3306) {
        $this->user = $user;
        $this->pass = $pass;
        $this->host = $host;
        $this->database = $database;
        $this->port = $port;
        $db = new mysqli($host, $user, $pass, $database);
        $this->prefix = $prefix;
        $this->dbIndex = $db;
        $this->isConnected = true;
    }
    public function getError() {
        return (@mysqli_error($this->dbIndex));
    }
    public function isConnected() {
        return $this->isConnected;
    }
    private function fixVar($id, &$values) {
        
        return mysqli_real_escape_string($this->dbIndex, $values[intval($id) - 1]);
    }
    public function query($query, $values = array()) {
        if ($this->ping()) {
            if (!is_array($values)) $values = array($values);
            $query = preg_replace('/\[([0-9]+)]/e', "\$this->fixVar(\\1, \$values)", $query);
            $this->queries++;
            $data = mysqli_query($this->dbIndex, $query);
            if (!$data) {
                return false;
            }
            return $data;
        } else {
            return false;
        }
    }
    public function queryFetch($query, $values = array()) {
        if ($this->ping()) {
            if (!is_array($values)) $values = array($values);
            $query = preg_replace('/\[([0-9]+)]/e', "\$this->fixVar(\\1, &\$values)", $query);
            $this->queries++;
            $data = mysqli_query($this->dbIndex, $query);
            if (!$data) {
                return false;
            }
            return mysqli_fetch_array($data);
        } else {
            return false;
        }
    }
    public function fetchArray($toFetch) {
        return mysqli_fetch_array($toFetch);
    }
    public function fetchRow($toFetch) {
        return mysqli_fetch_row($toFetch);
    }
    public function close() {
        mysqli_close($this->dbIndex);
    }
    public function lastID() {
        return mysqli_insert_id();
    }
    public function numRows($toFetch) {
        return mysqli_num_rows($toFetch);
    }
    public function numQueries() {
        return $this->queries;
    }
    public function ping() {
        if (mysqli_ping($this->dbIndex)) {
            return true;
        } else {
            $db = new mysqli($this->host, $this->user, $this->pass, $this->database);
            $this->dbIndex = $db;
            $this->isConnected = true;
            if (!mysqli_ping($this->dbIndex)) {
                $this->isConnected = false;
                return false;
            } else {
                $this->isConnected = true;
                return true;
            }
        }
    }
}
?>