<?php

/*
 * PDO MySqlDatabase Class
 * Connect to database
 * Create prepared statements
 * Bind values
 * Return rows and results
 */
/** SQL_PARAMS_NAMED - Bitmask, indicates :name type parameters are supported by db backend. */
define('SQL_PARAMS_NAMED', 1);

/** SQL_PARAMS_QM - Bitmask, indicates ? type parameters are supported by db backend. */
define('SQL_PARAMS_QM', 2);

/** SQL_PARAMS_DOLLAR - Bitmask, indicates $1, $2, ... type parameters are supported by db backend. */
define('SQL_PARAMS_DOLLAR', 4);

/** SQL_QUERY_SELECT - Normal select query, reading only. */
define('SQL_QUERY_SELECT', 1);

/** SQL_QUERY_INSERT - Insert select query, writing. */
define('SQL_QUERY_INSERT', 2);

/** SQL_QUERY_UPDATE - Update select query, writing. */
define('SQL_QUERY_UPDATE', 3);

/** SQL_QUERY_STRUCTURE - Query changing db structure, writing. */
define('SQL_QUERY_STRUCTURE', 4);

/** SQL_QUERY_AUX - Auxiliary query done by driver, setting connection config, getting table info, etc. */
define('SQL_QUERY_AUX', 5);

class Database
{
    private $connectionInfo;
    private $serverName;
    private $stmt;
    private $error;
    private $conn;
    private $query;
    private $params;
    private $rows;

    /**
     * Database constructor.
     */
    public function __construct()
    {
        global $CONF;
        // Initialize params
        $this->serverName = $CONF->DB_HOST;
        $this->connectionInfo = array("UID" => $CONF->DB_USER, "PWD" => $CONF->DB_PASS, "Database" => $CONF->DB_NAME);
        $this->conn = sqlsrv_connect($this->serverName, $this->connectionInfo);
        if (!$this->conn) {
            echo "Connection could not be established.<br />";
            die(print_r(sqlsrv_errors(), true));
        }
    }

    /**
     * Prepare statement with query
     * @param $sql
     */
    public function query($sql)
    {
        $this->query = $sql;
    }

    /**
     * Bind values
     * @param $param
     * @param $value
     * @param null $type
     */
    public function bind($param, $value, $type = null)
    {
        $this->params[str_replace(':', '', $param)] = $value;
    }

    /**
     * Execute the prepared statement
     * @throws Exception
     */
    public function execute()
    {
        list($sql, $params, $type) = $this->fix_sql_params($this->query, $this->params);
        $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
        $this->stmt = sqlsrv_query($this->conn, $sql, $params, $options);
        // Make the first (and in this case, only) row of the result set available for reading.
        if ($this->stmt === false) {
            print(print_r(sqlsrv_errors()));
        }
    }

    // Get result set as array of objects
    public function resultSet()
    {
        $this->execute();
        $i = 0;
        while ($row = sqlsrv_fetch_array($this->stmt, SQLSRV_FETCH_ASSOC)) {
            $this->rows[$i] = $row;
            $i++;
        }
        return $this->rows;
    }

    // Get single record as object
    public function single()
    {
        $this->execute();
        return sqlsrv_fetch_object($this->stmt);
    }

    /**
     * Get row count
     * @return int
     */
    public function rowCount()
    {
        $i = 0;
        while ($row = sqlsrv_fetch_array($this->stmt, SQLSRV_FETCH_ASSOC)) {
            $this->rows[$i] = $row;
            $i++;
        }
        var_dump($this->rows);
        return $i;
    }

    /**
     * @return bool
     */
    public function close()
    {
        $this->query = null;
        $this->params = null;
        $this->stmt = null;
        $this->error = null;
        return true;
    }

    /**
     * Normalizes sql query parameters and verifies parameters.
     * @param string $sql The query or part of it.
     * @param array $params The query parameters.
     * @return array (sql, params, type of params)
     */
    public function fix_sql_params($sql, array $params = null)
    {
        $params = (array)$params; // mke null array if needed
        $allowed_types = null;

        // cast booleans to 1/0 int and detect forbidden objects
        foreach ($params as $key => $value) {
            $this->detect_objects($value);
            $params[$key] = is_bool($value) ? (int)$value : $value;
        }

        // NICOLAS C: Fixed regexp for negative backwards look-ahead of double colons. Thanks for Sam Marshall's help
        $named_count = preg_match_all('/(?<!:):[a-z][a-z0-9_]*/', $sql, $named_matches); // :: used in pgsql casts
        $dollar_count = preg_match_all('/\$[1-9][0-9]*/', $sql, $dollar_matches);
        $q_count = substr_count($sql, '?');

        $count = 0;

        if ($named_count) {
            $type = SQL_PARAMS_NAMED;
            $count = $named_count;

        }
        if ($dollar_count) {
            if ($count) {
                throw new \Exception('Mixed Type Sql Param');
            }
            $type = SQL_PARAMS_DOLLAR;
            $count = $dollar_count;

        }
        if ($q_count) {
            if ($count) {
                throw new \Exception('Mixed Type Sql Param');
            }
            $type = SQL_PARAMS_QM;
            $count = $q_count;

        }

        if (!$count) {
            // ignore params
            if ($allowed_types & SQL_PARAMS_NAMED) {
                return array($sql, array(), SQL_PARAMS_NAMED);
            } else if ($allowed_types & SQL_PARAMS_QM) {
                return array($sql, array(), SQL_PARAMS_QM);
            } else {
                return array($sql, array(), SQL_PARAMS_DOLLAR);
            }
        }

        if ($count > count($params)) {
            $a = new stdClass;
            $a->expected = $count;
            $a->actual = count($params);
            throw new \Exception('Invalid Query Param');
        }

        $target_type = $allowed_types;

        if ($type & $allowed_types) { // bitwise AND
            if ($count == count($params)) {
                if ($type == SQL_PARAMS_QM) {
                    return array($sql, array_values($params), SQL_PARAMS_QM); // 0-based array required
                } else {
                    //better do the validation of names below
                }
            }
            // needs some fixing or validation - there might be more params than needed
            $target_type = $type;
        }

        if ($type == SQL_PARAMS_NAMED) {
            $finalparams = array();
            foreach ($named_matches[0] as $key) {
                $key = trim($key, ':');
                if (!array_key_exists($key, $params)) {
                    throw new \Exception('Missing Key in Sql');
                }
                if (strlen($key) > 30) {
                    throw new \Exception(
                        "Placeholder names must be 30 characters or shorter. '" .
                        $key . "' is too long.");
                }
                $finalparams[$key] = $params[$key];
            }
            if ($count != count($finalparams)) {
                throw new \Exception('Duplicate Param in Sql');
            }

            if ($target_type & SQL_PARAMS_QM) {
                $sql = preg_replace('/(?<!:):[a-z][a-z0-9_]*/', '?', $sql);
                return array($sql, array_values($finalparams), SQL_PARAMS_QM); // 0-based required
            } else if ($target_type & SQL_PARAMS_NAMED) {
                return array($sql, $finalparams, SQL_PARAMS_NAMED);
            } else {  // $type & SQL_PARAMS_DOLLAR
                //lambda-style functions eat memory - we use globals instead :-(
                $this->fix_sql_params_i = 0;

                $sql = preg_replace('/(?<!:):[a-z][a-z0-9_]*/', '?', $sql);
                return array($sql, array_values($finalparams), SQL_PARAMS_DOLLAR); // 0-based required
            }

        } else if ($type == SQL_PARAMS_DOLLAR) {
            if ($target_type & SQL_PARAMS_DOLLAR) {
                return array($sql, array_values($params), SQL_PARAMS_DOLLAR); // 0-based required
            } else if ($target_type & SQL_PARAMS_QM) {
                $sql = preg_replace('/\$[0-9]+/', '?', $sql);
                return array($sql, array_values($params), SQL_PARAMS_QM); // 0-based required
            } else { //$target_type & SQL_PARAMS_NAMED
                $sql = preg_replace('/\$([0-9]+)/', ':param\\1', $sql);
                $finalparams = array();
                foreach ($params as $key => $param) {
                    $key++;
                    $finalparams['param' . $key] = $param;
                }
                return array($sql, $finalparams, SQL_PARAMS_NAMED);
            }

        } else { // $type == SQL_PARAMS_QM
            if (count($params) != $count) {
                $params = array_slice($params, 0, $count);
            }

            if ($target_type & SQL_PARAMS_QM) {
                return array($sql, array_values($params), SQL_PARAMS_QM); // 0-based required
            } else if ($target_type & SQL_PARAMS_NAMED) {
                $finalparams = array();
                $pname = 'param0';
                $parts = explode('?', $sql);
                $sql = array_shift($parts);
                foreach ($parts as $part) {
                    $param = array_shift($params);
                    $pname++;
                    $sql .= ':' . $pname . $part;
                    $finalparams[$pname] = $param;
                }
                return array($sql, $finalparams, SQL_PARAMS_NAMED);
            } else {  // $type & SQL_PARAMS_DOLLAR
                //lambda-style functions eat memory - we use globals instead :-(
                return array($sql, array_values($params), SQL_PARAMS_DOLLAR); // 0-based required
            }
        }
    }

    /**
     * Detects object parameters and throws exception if found
     * @param mixed $value
     * @return void
     * @throws Exception
     */
    protected function detect_objects($value)
    {
        if (is_object($value)) {
            throw new \Exception('Invalid database query parameter value');
        }
    }
}