<?php

/*
 * PDO MySqlDatabase Class
 * Connect to database
 * Create prepared statements
 * Bind values
 * Return rows and results
 */

class Database
{
    private $host;
    private $user;
    private $pass;
    private $dbname;

    private $conn;
    private $stmt;
    private $error;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        global $CONF;
        // Initialize params
        $this->host = $CONF->DB_HOST;
        $this->user = $CONF->DB_USER;
        $this->pass = $CONF->DB_PASS;
        $this->dbname = $CONF->DB_NAME;
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Create PDO instance
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    // Prepare statement with query

    /**
     * @param $sql
     */
    public function query($sql)
    {
        $this->stmt = $this->conn->prepare($sql);
    }

    // Bind values

    /**
     * @param $param
     * @param $value
     * @param null $type
     */
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    // Execute the prepared statement

    /**
     * @return mixed
     */
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Get result set as array of objects

    /**
     * @return mixed
     */
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get single record as object

    /**
     * @return mixed
     */
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Get row count

    /**
     * @return mixed
     */
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    /**
     * @return bool
     */
    public function close()
    {
        $this->error = null;
        $this->stmt = null;
        return true;
    }
}