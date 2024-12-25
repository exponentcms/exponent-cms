<?php
##################################################
#
# Copyright (c) 2004-2025 OIC Group, Inc.
#
# This file is part of Exponent
#
# Exponent is free software; you can redistribute
# it and/or modify it under the terms of the GNU
# General Public License as published by the Free
# Software Foundation; either version 2 of the
# License, or (at your option) any later version.
#
# GPL: http://www.gnu.org/licenses/gpl.txt
#
##################################################

/** @define "BASE" "../.." */
/**
 * This is the class sqlsvr_database
 *
 * This is the SQL Server specific implementation of the database class.
 * @package Subsystems
 * @subpackage Database
 */
class sqlsvr_database extends database {

    /**
     * Make a connection to the Database Server
     *
     * Takes the supplied credentials (username / password) and tries to
     * connect to the server and select the given database.
     *
     * @param string $username The username to connect to the server as.
     * @param string $password The password for $username
     * @param string $hostname The hostname of the database server.  If
     *   localhost is specified, a local socket connection will be attempted.
     * @param string $database The name of the database to use.  Multi-database
     *   sites are still not yet supported.
     * @param bool $new Whether or not to force the PHP connection function to establish
     *   a distinctly new connection handle to the server.
     */

	function __construct($username, $password, $hostname, $database, $new=false) {
		if (false !== strpos($hostname, ':')) {
			list ( $host, $port ) = @explode (":", $hostname);
		} else {
            $host = $hostname;
        }
		if ($this->connection = sqlsrv_connect($host, array('UID'=>$username, 'PWD'=>$password, 'Database'=>$database))) {
			$this->havedb = true;
		}
		$this->prefix = DB_TABLE_PREFIX . '_';
        $server_info = sqlsrv_server_info($this->connection);
        $this->version = 'SQL Server ' . $server_info['SQLServerVersion'];
	}

    /** Begin SSP Methods */
    /**
     * Connect to the database by PDO
   	 *
   	 * @return PDO Database connection handle
   	 */
   	public function sql_connect_pdo()	{
   		try {
                $dbpdo = @new PDO("sqlsrv:server=" . DB_HOST . ";Database=" . DB_NAME . "",
                    DB_USER,
                    DB_PASS,
                    array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
                );
   		}
   		catch (PDOException $e) {
   			expDatabase::fatal(
   				"An error occurred while connecting to the database. ".
   				"The error reported by the server was: ".$e->getMessage()
   			);
   		}

   		return $dbpdo;
   	}

    /**
   	 * Paging
   	 *
   	 * Construct the LIMIT clause for server-side processing SQL query
   	 *
   	 *  @param  array $request Data sent to server by DataTables
   	 *  @return string SQL limit clause
   	 */
    static function limit_pdo($request)
    {
        $limit = '';
        if (isset($request['start']) && $request['length'] != -1) {
            $limit = "ORDER BY [LINE] OFFSET " . (int)$request['start'] . " ROWS FETCH NEXT " . (int)$request['length'] . " ROWS ONLY";
        }
        // limit and order conflict when using sql server.
        // so duplicate the functionality in ORDER and switch on/off as needed based on ORDER
        if (isset($request['order'])) {
            $limit = '';    // if there is an ORDER request then clear the limit
            return $limit;    // because the ORDER function will handle the LIMIT
        } else {
            return $limit;
        }
    }

    /**
   	 * Ordering
   	 *
   	 * Construct the ORDER BY clause for server-side processing SQL query
   	 *
   	 *  @param  array $request Data sent to server by DataTables
   	 *  @param  array $columns Column information array
   	 *  @return string SQL order by clause
   	 */
    static function order_pdo($request, $columns)
    {
        $order = '';
        if (isset($request['order']) && count($request['order'])) {
            $orderBy = array();
            $dtColumns = expDatabase::pluck($columns, 'dt');
            for ($i = 0, $ien = count($request['order']); $i < $ien; $i++) {
                // Convert the column index into the column data property
                $columnIdx = (int)$request['order'][$i]['column'];
                $requestColumn = $request['columns'][$columnIdx];
                $columnIdx = array_search($requestColumn['data'], $dtColumns);
                $column = $columns[$columnIdx];
//                if ($requestColumn['orderable'] === 'true') {  //fixme allows us to initially sort an unsortable column
                    $dir = $request['order'][$i]['dir'] === 'asc' ?
                        'ASC' :
                        'DESC';
                    $orderBy[] = '[' . $column['db'] . '] ' . $dir;   // revised for SQL Server
//                }
            }
            // see "static function limit" above to explain the next line.
            $order = "ORDER BY " . implode(', ', $orderBy) . " OFFSET " . (int)($request['start']) . " ROWS FETCH NEXT " . (int)($request['length']) . " ROWS ONLY";
        }
        return $order;
    }

    /**
   	 * Searching / Filtering
   	 *
   	 * Construct the WHERE clause for server-side processing SQL query.
   	 *
   	 * NOTE this does not match the built-in DataTables filtering which does it
   	 * word by word on any field. It's possible to do here performance on large
   	 * databases would be very poor
   	 *
   	 *  @param  array $request Data sent to server by DataTables
   	 *  @param  array $columns Column information array
   	 *  @param  array $bindings Array of values for PDO bindings, used in the
   	 *    sql_exec() function
   	 *  @return string SQL where clause
   	 */
   	static function filter_pdo ( $request, $columns, &$bindings )
   	{
   		$globalSearch = array();
   		$columnSearch = array();
   		$dtColumns = expDatabase::pluck( $columns, 'dt' );

   		if ( isset($request['search']) && $request['search']['value'] != '' ) {
   			$str = $request['search']['value'];

   			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
   				$requestColumn = $request['columns'][$i];
   				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
   				$column = $columns[ $columnIdx ];

   				if ( $requestColumn['searchable'] === 'true' ) {
                       if (stripos($str, '-yadcf_delim-') !== false) {
                           $val = explode('-yadcf_delim-', $str);
                           if (empty($val[0])) {
                               $val[0] = '0';
                           } elseif (expDateTime::is_date($val[0])) {
                               $val[0] = strtotime($val[0]);
                           }
                           if (empty($val[1])) {
                               $val[1] = time();
                           } elseif (expDateTime::is_date($val[1])) {
                               $val[1] = strtotime($val[1]);
                           }
                           $binding0 = expDatabase::bind( $bindings, $val[0], PDO::PARAM_STR );
                           $binding1 = expDatabase::bind( $bindings, $val[1], PDO::PARAM_STR );
                           $globalSearch[] = "[".$column['db']."] BETWEEN ".$binding0." AND ".$binding1;
                       } else {
                           $binding = expDatabase::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                           $globalSearch[] = "[".$column['db']."] LIKE ".$binding;
                       }
   				}
   			}
   		}

   		// Individual column filtering
   		if ( isset( $request['columns'] ) ) {
   			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
   				$requestColumn = $request['columns'][$i];
   				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
   				$column = $columns[ $columnIdx ];

   				$str = $requestColumn['search']['value'];

   				if ( $requestColumn['searchable'] === 'true' && $str != '' ) {
   				    if (stripos($str, '-yadcf_delim-') !== false) {
   				        $val = explode('-yadcf_delim-', $str);
                           if (empty($val[0])) {
                               $val[0] = '0';
                           } elseif (expDateTime::is_date($val[0])) {
                               $val[0] = strtotime($val[0]);
                           }
                           if (empty($val[1])) {
                               $val[1] = time();
                           } elseif (expDateTime::is_date($val[1])) {
                               $val[1] = strtotime($val[1]);
                           }
                           $binding0 = expDatabase::bind( $bindings, $val[0], PDO::PARAM_STR );
                           $binding1 = expDatabase::bind( $bindings, $val[1], PDO::PARAM_STR );
                           $columnSearch[] = "[".$column['db']."] BETWEEN ".$binding0." AND ".$binding1;
                       } else {
                           $binding = expDatabase::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                           $columnSearch[] = "[".$column['db']."] LIKE ".$binding;
                       }
   				}
   			}
   		}

   		// Combine the filters into a single string
   		$where = '';

   		if ( count( $globalSearch ) ) {
   			$where = '('.implode(' OR ', $globalSearch).')';
   		}

   		if ( count( $columnSearch ) ) {
   			$where = $where === '' ?
   				implode(' AND ', $columnSearch) :
   				$where .' AND '. implode(' AND ', $columnSearch);
   		}

   		if ( $where !== '' ) {
   			$where = 'WHERE '.$where;
   		}

   		return $where;
   	}

    /**
   	 * Perform the SQL queries needed for an server-side processing requested,
   	 * utilising the helper functions of this class, limit(), order() and
   	 * filter() among others. The returned array is ready to be encoded as JSON
   	 * in response to an SSP request, or can be modified if needed before
   	 * sending back to the client.
   	 *
   	 *  @param  array $request Data sent to server by DataTables
   	 *  @param  string $table SQL table to query
   	 *  @param  string $primaryKey Primary key of the table
   	 *  @param  array $columns Column information array
   	 *  @return array          Server-side processing response array
   	 */
    static function simple_pdo($request, $table, $primaryKey, $columns)
    {
        $bindings = array();
        $dbpdo = expDatabase::sql_connect();
        // Build the SQL query string from the request
        $limit = self::limit_pdo($request);
        $order = self::order_pdo($request, $columns);
        $where = self::filter_pdo($request, $columns, $bindings);

        // Main query to actually get the data
        $data = expDatabase::sql_exec($dbpdo, $bindings,
            "SET NOCOUNT ON SELECT " . implode(", ", expDatabase::pluck($columns, 'db')) . " FROM $table $where $order $limit");

        // Data set length after filtering  the $where will update info OR will be blank when not doing a search
        $resFilterLength = expDatabase::sql_exec($dbpdo, $bindings,
            "SET NOCOUNT ON SELECT " . implode(", ", expDatabase::pluck($columns, 'db')) . " FROM $table $where ");
        $recordsFiltered = count($resFilterLength);

        // Total data set length
        $resTotalLength = expDatabase::sql_exec($dbpdo, "SET NOCOUNT ON SELECT COUNT({$primaryKey}) FROM $table");
        $recordsTotal = $resTotalLength[0][0];

        /*  Output   */
        return array(
            "draw" => (int)($request['draw']),
            "recordsTotal" => (int)($recordsTotal),
            "recordsFiltered" => (int)($recordsFiltered),
            "data" => expDatabase::data_output($columns, $data)
        );
    }

    /**
   	 * The difference between this method and the `simple` one, is that you can
   	 * apply additional `where` conditions to the SQL queries. These can be in
   	 * one of two forms:
   	 *
   	 * * 'Result condition' - This is applied to the result set, but not the
   	 *   overall paging information query - i.e. it will not effect the number
   	 *   of records that a user sees they can have access to. This should be
   	 *   used when you want apply a filtering condition that the user has sent.
   	 * * 'All condition' - This is applied to all queries that are made and
   	 *   reduces the number of records that the user can access. This should be
   	 *   used in conditions where you don't want the user to ever have access to
   	 *   particular records (for example, restricting by a login id).
   	 *
   	 *  @param  array $request Data sent to server by DataTables
   	 *  @param  string $table SQL table to query
   	 *  @param  string $primaryKey Primary key of the table
   	 *  @param  array $columns Column information array
   	 *  @param  string $whereResult WHERE condition to apply to the result set
   	 *  @param  string $whereAll WHERE condition to apply to all queries
        *
   	 *  @return array          Server-side processing response array
   	 */
    static function complex_pdo($request, $table, $primaryKey, $columns, $whereResult = null, $whereAll=null)
    {
        $bindings = array();
        $dbpdo = expDatabase::sql_connect();
        $localWhereResult = array();
        $localWhereAll = array();
        $whereAllSql = '';

        // Build the SQL query string from the request
        $limit = self::limit_pdo($request);
        $order = self::order_pdo($request, $columns);
        $where = self::filter_pdo($request, $columns, $bindings);

        $whereResult = expDatabase::_flatten($whereResult);
        $whereAll = expDatabase::_flatten($whereAll);

        if ($whereResult) {
            $where = $where ?
                $where . ' AND ' . $whereResult :
                'WHERE ' . $whereResult;
        }

        if ($whereAll) {
            $where = $where ?
                $where . ' AND ' . $whereAll :
                'WHERE ' . $whereAll;

            //$whereAllSql = 'WHERE '.$whereAll;
        }

        // Main query to actually get the data
        $data = expDatabase::sql_exec($dbpdo, $bindings,
//        "SET NOCOUNT ON SELECT ".implode(", ", expDatabase::pluck($columns, 'db'))." FROM $table $where $order $limit" );
            "SET NOCOUNT ON SELECT * FROM $table $where $order $limit");

        // Data set length after filtering
        $resFilterLength = expDatabase::sql_exec($dbpdo, $bindings,
            "SELECT count({$primaryKey}) FROM $table $where");
        $recordsFiltered = $resFilterLength[0][0];

        // Total data set length
        $resTotalLength = expDatabase::sql_exec($dbpdo, "SELECT COUNT({$primaryKey}) FROM $table");
        $recordsTotal = $resTotalLength[0][0];

        /*
         * Output
         */
        return array(
            "draw" => isset ($request['draw']) ?
                (int)($request['draw']) :
                0,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => expDatabase::data_output($columns, $data)
        );
    }
    /** End SSP Methods */

    /**
   	* Return the tablename for the database
   	*
   	* Returns a full table name for the database.
   	*
   	* @param string $tablename The name of the table
   	* @return string
   	*/
    function tableStmt($tablename) {
        if (DB_SCHEMA) {
            $dbsc = ".[" . DB_SCHEMA . "]";
        } else {
            $dbsc = "";
        }
   	    return "[" . DB_NAME . "]" . $dbsc . ".[" . $this->prefix . $tablename . "]";
    }

    /**
   	* Return the limit statement for the database
   	*
   	* Returns a correct limit statement for the database.
   	*
     * @param int $count The number of records to return
     * @param int $offset The offset to the first record to return
   	* @return string
   	*/
    function limitStmt($count, $offset=0) {
        return ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $count . ' ROWS ONLY';
    }

    /**
     * Return the unixtime to date statement for the database
     *
     * Returns a correct unixtime to date statement for the database.
     *
     * @param string $column_name The name of the data column to convert
     * @return string
     */
    function datetimeStmt($column_name) {
        return "CONVERT(nvarchar, DATEADD(s, " . $column_name . ", '1/1/1970'), 22)";
    }

    /**
     * Return the number to currency statement for the database
     *
     * Returns a correct number to currency statement for the database.
     *
     * @param string $column_name The name of the data column to convert
     * @return string
     */
    function currencyStmt($column_name) {
        return "FORMAT(" . $column_name . ",'C')";
    }

    /**
     * Return a sql statement with keywords wrapped for the database
     *
     * Returns a keyword wrapped sql statement for the database.
     *
     * @param string $sql The sql statement to check for keyword wrap
     * @return string
     */
    function wrapStmt($sql) {
        return str_ireplace('external', '[external]', $sql);
    }

    /**
     * Create a new Table
     *
     * Creates a new database table, according to the passed data definition.
     *
     * This function abides by the Exponent Data Definition Language, and interprets
     * its general structure.
     *
     * @param string $tablename The name of the table to create
     * @param array $datadef The data definition to create, expressed in
     *   the Exponent Data Definition Language.
     * @param array $info Information about the table itself.
     * @return array
	 */
	function createTable($tablename, $datadef, $info) {
		if (!is_array($info))
            $info = array(); // Initialize for later use.

		$sql = "CREATE TABLE " . $this->tableStmt($tablename) . " (";
		$primary = array();
		$fulltext = array();
		$unique = array();
		$index = array();
		foreach ($datadef as $name=>$def) {
			if ($def != null) {
				$sql .= $this->fieldSQL($name,$def) . ",";
				if (!empty($def[DB_PRIMARY]))  $primary[] = $name;
				if (!empty($def[DB_FULLTEXT])) $fulltext[] = $name;
				if (isset($def[DB_INDEX]) && ($def[DB_INDEX] > 0)) {
					if ($def[DB_FIELD_TYPE] == DB_DEF_STRING) {
						$index[$name] = $def[DB_INDEX];
                    } else {
                        $index[$name] = 0;
                    }
                }
                if (isset($def[DB_UNIQUE])) {
                    if (!isset($unique[$def[DB_UNIQUE]]))
                        $unique[$def[DB_UNIQUE]] = array();
                    $unique[$def[DB_UNIQUE]][] = $name;
                }
            }
        }
        $sql = substr($sql, 0, -1);
        if (count($primary)) {
            $sql .= ", CONSTRAINT PK_" . $tablename . "_" . implode("_", $primary) . " PRIMARY KEY ([" . implode("] , [", $primary) . "])";
        }
        if (!empty($unique)) foreach ($unique as $key => $value) {
            $sql .= ", CONSTRAINT IX_" . $tablename . "_" . $key . " UNIQUE ( [" . implode("] , [", $value) . "])";
        }
        foreach ($index as $key => $value) {
            $sql .= ", INDEX IX_" . $tablename . "_" . $key . " ([" . $key . "])";
        }
        $sql .= ")";
//        if (defined('DB_ENCODING')) {
//            $db_encoding = DB_ENCODING;
//        } else {
//            $db_encoding = 'utf8 COLLATE utf8_unicode_ci';
//        }
//        $sql .= " CHARACTER SET " . $db_encoding;
//
//        if (isset($info[DB_TABLE_COMMENT])) {
//            $sql .= " COMMENT = '" . $info[DB_TABLE_COMMENT] . "'";
//        }

        @sqlsrv_query($this->connection, $sql);

        if (count($fulltext)) {
//            $sql .= ", FULLTEXT '" . $fulltext[0] . "'" . "( '" . implode("' , '", $fulltext) . "')";
            $ftsql = "CREATE FULLTEXT CATALOG FT_" . $tablename . " AS DEFAULT; ";
            $ftsql .= "CREATE FULLTEXT INDEX ON " . $this->tableStmt($tablename) . "([" . implode("] , [", $fulltext) . "])
            KEY INDEX PK_" . $tablename . "_" . implode("_", $primary) . "
            ON FT_" . $tablename . "
            WITH STOPLIST = SYSTEM;";
            @sqlsrv_query($this->connection, $ftsql);
        }

        $return = array(
            $tablename => ($this->tableExists($tablename) ? DATABASE_TABLE_INSTALLED : DATABASE_TABLE_FAILED)
        );

        return $return;
    }

    /**
   	* This is an internal function for use only within the database class
   	* @internal Internal
   	* @param  $name
   	* @param  $def
   	* @return bool|string
   	*/
   	function fieldSQL($name, $def) {
   	    $sql = "[$name]";
   	    if (!isset($def[DB_FIELD_TYPE])) {
   	        return false;
   	    }
   	    $type = $def[DB_FIELD_TYPE];
   	    if ($type == DB_DEF_ID) {
   	        $sql .= " INT";
   	    } else if ($type == DB_DEF_BOOLEAN) {
   	        $sql .= " SMALLINT"; //" BIT";
   	    } else if ($type == DB_DEF_TIMESTAMP) {
   	        $sql .= " INT";
        } else if ($type == DB_DEF_DATETIME) {
      	    $sql .= " DATETIME2";
   	    } else if ($type == DB_DEF_INTEGER) {
   	        $sql .= " INT";
   	    } else if ($type == DB_DEF_STRING) {
   	        if (isset($def[DB_FIELD_LEN]) && is_int($def[DB_FIELD_LEN])) {
   	            $len = $def[DB_FIELD_LEN];
   	            if ($len < 4000)
                    $sql .= " NVARCHAR($len)";
   	            else
                    $sql .= " NVARCHAR(MAX)";

   	        } else {  // default size of 'TEXT'instead of error
               $sql .= " NVARCHAR(MAX)";
   	        }
   	    } else if ($type == DB_DEF_DECIMAL) {
   	        $sql .= " FLOAT(53)";
   	    } else {
   	        return false; // must specify known FIELD_TYPE
   	    }
//   	    $sql .= " NOT NULL";
   	    if (isset($def[DB_DEFAULT]))
   	        $sql .= " DEFAULT '" . $def[DB_DEFAULT] . "'";

   	    if (isset($def[DB_INCREMENT]) && $def[DB_INCREMENT])
   	        $sql .= " IDENTITY";
   	    return $sql;
   	}

    /**
     * Alter an existing table
     *
     * Alters the structure of an existing database table to conform to the passed
     * data definition.
     *
     * This function abides by the Exponent Data Definition Language, and interprets
     * its general structure.
     *
     * @param string $tablename The name of the table to alter
     * @param array $newdatadef The new data definition for the table.
     *   This is expressed in the Exponent Data Definition Language
     * @param array $info Information about the table itself.
     * @param bool $aggressive Whether or not to aggressively update the table definition.
     *   An aggressive update will drop columns in the table that are not in the Exponent definition.
     * @return array
     */
    function alterTable($tablename, $newdatadef, $info, $aggressive = false) {
        if ($this->havedb == true)
            expSession::clearAllUsersSessionCache();
        $dd = $this->getDataDefinition($tablename);
        $modified = false;

        // collect any indexes & keys to the table
        $primary = array();
        $fulltext = array();
        $unique = array();
        $index = array();
        foreach ($newdatadef as $name=>$def) {
            if ($def != null) {
                if (!empty($def[DB_PRIMARY]))  $primary[] = $name;
                if (!empty($def[DB_FULLTEXT])) $fulltext[] = $name;
                if (isset($def[DB_INDEX]) && ($def[DB_INDEX] > 0)) {
                    if ($def[DB_FIELD_TYPE] == DB_DEF_STRING) {
                          $index[$name] = $def[DB_INDEX];
                      } else {
                          $index[$name] = 0;
                      }
                  }
                  if (isset($def[DB_UNIQUE])) {
                      if (!isset($unique[$def[DB_UNIQUE]]))
                          $unique[$def[DB_UNIQUE]] = array();
                      $unique[$def[DB_UNIQUE]][] = $name;
                }
            }
            if (!empty($def[DB_NOTNULL]) || $def[DB_FIELD_TYPE] === DB_DEF_ID || $def[DB_FIELD_TYPE] === DB_DEF_BOOLEAN || (!empty($def[DB_PRIMARY]) && $def[DB_PRIMARY] === true)) {
                $newdatadef[$name][DB_NOTNULL] = true;
            } else {
                $newdatadef[$name][DB_NOTNULL] = false;
            }
            if (($def[DB_FIELD_TYPE] === DB_DEF_ID && empty($def[DB_PRIMARY])) || $def[DB_FIELD_TYPE] === DB_DEF_BOOLEAN) {
                $newdatadef[$name][DB_DEFAULT] = 0;
            }
        }

        //Drop any old columns from the table if aggressive mode is set.
        if ($aggressive) {
            //update primary keys to 'release' columns
            $sql = "ALTER TABLE " . $this->tableStmt($tablename) . " ";
            if (count($primary)) {
                $sql .= " DROP CONSTRAINT PK_" . $tablename . "_" . implode("_", $primary) . ", ADD CONSTRAINT PK_" . $tablename . "_" . implode("_", $primary) . " PRIMARY KEY ([" . implode("] , [", $primary) . "])";
            }
            @sqlsrv_query($this->connection, $sql);

            if (is_array($newdatadef) && is_array($dd)) {
                $oldcols = @array_diff_assoc($dd, $newdatadef);
                if (count($oldcols)) {
                    $sql = "ALTER TABLE " . $this->tableStmt($tablename) . " ";
                    foreach ($oldcols as $name => $def) {
                        $sql .= " DROP COLUMN " . $name . ",";
                    }
                    $sql = substr($sql, 0, -1);
                    @sqlsrv_query($this->connection, $sql);
                    $modified = true;
                }
            }
        }

        //Add any new columns to the table
        if (is_array($newdatadef) && is_array($dd)) {
            $diff = @array_diff_assoc($newdatadef, $dd);
            if (count($diff)) {
                $sql = "ALTER TABLE " . $this->tableStmt($tablename) . " ";
                foreach ($diff as $name => $def) {
                    $sql .= " ADD " . $this->fieldSQL($name, $def) . ",";
                }
                $sql = substr($sql, 0, -1);
                @sqlsrv_query($this->connection, $sql);
                $modified = true;
            }

            // alter any existing columns here
            $diff_c = @expCore::array_diff_assoc_recursive($newdatadef, $dd);
            $sql = "ALTER TABLE " . $this->tableStmt($tablename) . " ";
            $changed = false;
            if (is_array($diff_c)) {
                foreach ($diff_c as $name => $def) {
                    if (!array_key_exists($name, $diff) && (isset($def[DB_FIELD_TYPE]) || isset($def[DB_FIELD_LEN]) || isset($def[DB_DEFAULT]) || isset($def[DB_INCREMENT]) || isset($def[DB_NOTNULL]))) {  // wasn't a new column
                        if (count($def) == 1 && $newdatadef[$name][DB_FIELD_TYPE] == DB_DEF_STRING) {
                            //check for actual lengths vs. exp placeholder lengths
                            $newlen = $newdatadef[$name][DB_FIELD_LEN];
                            $len = $dd[$name][DB_FIELD_LEN];
                            if ($len >= 16777216 && $newlen >= 16777216) {
                                continue;
                            }
                            if ($len >= 65536 && $newlen >= 65536) {
                                continue;
                            }
                            if ($len >= 256 && $newlen >= 256) {
                                continue;
                            }
                        }
                        $changed = true;
                        $sql .= ' ALTER COLUMN ' . $this->fieldSQL($name,$newdatadef[$name]) . ",";
                    }
                }
            }
            if ($changed) {
                $sql = substr($sql, 0, -1);
                @sqlsrv_query($this->connection, $sql);
                $modified = true;
            }
        }

        //Add any new indexes & keys to the table
//        $sql = "ALTER" . (empty($aggressive) ? "" : " IGNORE") . " TABLE " . $this->tableStmt($tablename);
        $sql = "ALTER TABLE " . $this->tableStmt($tablename) . " ";

        $sep = false;
        if (count($primary)) {
            $sql .= " DROP CONSTRAINT PK_" . $tablename . "_" . implode("_",$primary) . ", ADD CONSTRAINT PK_" . $tablename . "_" . implode("_", $primary) . " PRIMARY KEY ( [" . implode("] , [",$primary) . "] )";
            $sep = true;
        }
        if (!empty($unique)) foreach ($unique as $key=>$value) {
            if ($sep) $sql .= ' ,';
            $sql .= ", ADD CONSTRAINT IX_" . $tablename . "_" . $key . " UNIQUE ( [" . implode("] , [", $value) . "])";
            $sep = true;
        }

        foreach ($index as $key => $value) {
            // drop the index first so we don't get dupes
            $drop = "DROP INDEX IX_" . $tablename . "_" . $key . " ON " . $this->tableStmt($tablename);
            @sqlsrv_query($this->connection, $drop);

            // re-add the index
            if ($sep) $sql .= ' ,';
//            $sql .= " ADD INDEX ('" . $key . "')";  //FIXME we don't add column length??
            $sql .= " CREATE INDEX IX_" . $tablename . "_" . $key . " ON " . $this->tableStmt($tablename) . " ([" . $key . "]" . (($value > 0) ? "([" . $value . "])" : "") . ")";
            $sep = true;
        }

        @sqlsrv_query($this->connection, $sql);

        if (count($fulltext)) {
            //fixme we'll need more error check and undoing before we can (re)create
            $ftsql = "DROP FULLTEXT INDEX ON " . $this->tableStmt($tablename) . "; ";
            $ftsql .= "IF NOT EXISTS (SELECT 1 FROM sys.fulltext_catalogs WHERE name = 'FT_" . $tablename . "')
                       CREATE FULLTEXT CATALOG FT_" . $tablename . "; ";
            $ftsql .= "CREATE FULLTEXT INDEX ON " . $this->tableStmt($tablename) . "([" . implode("] , [", $fulltext) . "])
            KEY INDEX PK_" . $tablename . "_" . implode("_", $primary) . "
            ON FT_" . $tablename . "
            WITH STOPLIST = SYSTEM;";
            @sqlsrv_query($this->connection, $ftsql);
        }

        //Get the return code
        $return = array(
            $tablename => ($modified ? TABLE_ALTER_SUCCEEDED : TABLE_ALTER_NOT_NEEDED)
        );

        return $return;
    }

    /**
     * Drop a table from the database
     *
     * Removes an existing table from the database. Returns true if the table was dropped, false if there
     * was an error returned by the server.
     *
     * @param string $table The name of the table to drop.
     * @return bool
     */
    function dropTable($table) {
        return @sqlsrv_query($this->connection, "DROP TABLE " . $this->tableStmt($table)) !== false;
    }

    /**
     * Run raw SQL.  Returns true if the query succeeded, and false
     *   if an error was returned from the server.
     *
     * <div style="color:red">If you can help it, do not use this function.  It presents Database Portability Issues.</div>
     *
     * Runs a straight SQL query on the database.  This is not a
     * very portable way of dealing with the database, and is only
     * provided as a last resort.
     *
     * @param string $sql The SQL query to run
	 * @param bool $escape Indicates if the query will be escape
     * @return mixed
     */
    function sql($sql, $escape = true) {
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
		if($escape == true) {
			$res = @sqlsrv_query($this->connection, expString::escape($sql, true));
		} else {
			$res = @sqlsrv_query($this->connection, $sql);
		}
        return $res;
    }

	/**
	 * Update a column in all records in a table
	 *
	 * @param  $table
	 * @param  $col
	 * @param $val
	 * @param int|null $where
	 * @return void
	 */
    function columnUpdate($table, $col, $val, $where='1=1') {
        $res = @sqlsrv_query($this->connection, "UPDATE " . $this->tableStmt($table) . " SET [$col]='" . $val . "' WHERE $where", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        /*if ($res == null)
            return array();
        $objects = array();
        for ($i = 0; $i < sqlsrv_num_rows($res); $i++)
            $objects[] = sqlsrv_fetch_object($res);*/
        //return $objects;
    }

    /**
     * Select a series of objects
     *
     * Selects a set of objects from the database.  Because of the way
     * Exponent handles objects and database tables, this is akin to
     * SELECTing a set of records from a database table.  Returns an
     * array of objects, in any random order.
     *
     * @param string $table The name of the table/object to look at
     * @param string $where Criteria used to narrow the result set.  If this
     *   is specified as null, then no criteria is applied, and all objects are
     *   returned
     * @param null $orderby
     * @param bool $is_revisioned
     * @param bool $needs_approval
     * @param null $user
     *
     * @return array
     */
    function selectObjects($table, $where = null, $orderby = null, $is_revisioned=false, $needs_approval=false, $user=null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        else {
            $where = $this->injectProof($where);
            $where = $this->wrapStmt($where);
        }
        $as = '';
        if ($is_revisioned) {
            $where .= " AND revision_id=(SELECT MAX(revision_id) FROM " . $this->tableStmt($table) . " WHERE id = rev.id ";
            if ($needs_approval) {
                if (!empty($user)) {
                    $where .= ' AND (approved=1 AND ((poster!=0 AND poster=' . $user . ') OR (editor!=0 AND editor=' . $user . ')))';
                } else {
                    $where .= ' AND (approved=1)';
                }
            }
            $where .= ")";
            $as = ' AS rev';
        }
        if ($orderby == null)
            $orderby = '';
        else
            $orderby = "ORDER BY " . $orderby;

        $res = @sqlsrv_query($this->connection, "SELECT * FROM " . $this->tableStmt($table) . $as . " WHERE $where $orderby", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $objects = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++)
            $objects[] = sqlsrv_fetch_object($res);
        return $objects;
    }

	/**
	 * @param  $terms
	 * @param null $where
	 * @return array
	 */
    function selectSearch($terms, $where = null) {
        if ($where == null)
            $where = "1=1";
//        $sql = "SELECT *, MATCH (s.title, s.body, s.keywords) AGAINST ('" . $terms . "*') AS score FROM " . $this->tableStmt('search') . " AS s ";
//        $sql .= "WHERE ";
//        if (ECOM) {
//            $search_type = ecomconfig::getConfig('ecom_search_results');
//            if ($search_type === 'ecom') {
//                $sql .= "ref_module = 'product' AND ";
//            } elseif ($search_type === 'products') {
//                $sql .= "ref_type = 'product' AND ";
//            }
//        }
//        $sql .= "MATCH (title, body, keywords) AGAINST ('" . $terms . "*' IN BOOLEAN MODE) ";
        $sql = "SELECT *, KEY_TBL.RANK as score FROM " . $this->tableStmt('search') . " AS ms ";
        $sql .= "INNER JOIN CONTAINSTABLE(" . $this->tableStmt('search') . ", (title, body, keywords), '" . $terms . "') AS KEY_TBL ";
        $sql .= "ON ms.ID = KEY_TBL.[KEY] ";
        $sql .= "WHERE KEY_TBL.RANK > 0";
        if (ECOM) {
            $search_type = ecomconfig::getConfig('ecom_search_results');
            if ($search_type === 'ecom') {
                $sql .= " AND ref_module = 'product'";
            } elseif ($search_type === 'products') {
                $sql .= " AND ref_type = 'product'";
            }
        }
        $res = @sqlsrv_query($this->connection, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
//        if( $res === false) {
//            $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
//            foreach( $errors as $error ) {
//                 echo "Error: ".$error['message']."\n";
//            }
//            die;
//        }
        if ($res == null)
            return array();
        $objects = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++)
            $objects[] = sqlsrv_fetch_object($res);
        return $objects;
    }

	/**
	 * @param null $colsA
	 * @param null $colsB
	 * @param  $tableA
	 * @param  $tableB
	 * @param  $keyA
	 * @param null $keyB
	 * @param null $where
	 * @param null $orderby
	 * @return array'
	 */
    function selectAndJoinObjects($colsA=null, $colsB=null, $tableA=null, $tableB=null, $keyA=null, $keyB=null, $where = null, $orderby = null) {
        $sql = 'SELECT ';
        if ($colsA != null) {
            if (!is_array($colsA)) {
                $sql .= 'a.' . $colsA . ', ';
            } else {
                foreach ($colsA as $colA) {
                    $sql .= 'a.' . $colA . ', ';
                }
            }
        } else {
            $sql .= ' a.*, ';
        }

        if ($colsB != null) {
            if (!is_array($colsB)) {
                $sql .= 'b.' . $colsB . ' ';
            } else {
                $i = 1;
                foreach ($colsB as $colB) {
                    $sql .= 'b.' . $colB;
                    if ($i < count($colsB))
                        $sql .= ', ';
                    $i++;
                }
            }
        } else {
            $sql .= ' b.* ';
        }

        $sql .= ' FROM ' . $this->tableStmt($tableA) . ' a JOIN ' . $this->tableStmt($tableB) . ' b ';
        $sql .= $keyB === null ? 'USING(' . $keyA . ')' : 'ON a.' . $keyA . ' = b.' . $keyB;

        if ($where == null || $where == "1")
            $where = "1=1";
        if ($orderby == null)
            $orderby = '';
        else
            $orderby = "ORDER BY " . $orderby;

        $res = @sqlsrv_query($this->connection, $sql . " WHERE $where $orderby", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $objects = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++)
            $objects[] = sqlsrv_fetch_object($res);
        return $objects;
    }

	/**
     * Select a single object by sql
     *
	 * @param  $sql
	 * @return null|array
	 */
    function selectObjectBySql($sql) {
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
        $res = @sqlsrv_query($this->connection, $this->injectProof($sql));
        if ($res == null)
            return null;
        return sqlsrv_fetch_object($res);
    }

	/**
     * Select a series of objects by sql
     *
	 * @param  $sql
	 * @return array
	 */
    function selectObjectsBySql($sql) {
        if (strpos($sql, ' IF') !== false) {
            $sql = str_ireplace(' IF', ' IIF', $sql);
        }
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
        $res = @sqlsrv_query($this->connection, $this->injectProof($sql), array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $objects = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++)
            $objects[] = sqlsrv_fetch_object($res);
        return $objects;
    }

	/**
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @param null $orderby
	 * @param bool $distinct
	 * @return array
	 */
    function selectColumn($table, $col, $where = null, $orderby = null, $distinct=false) {
        if ($where == null || $where == "1")
            $where = "1=1";
        if ($orderby == null)
            $orderby = '';
        else
            $orderby = "ORDER BY " . $orderby;
        $dist = empty($distinct) ? '' : 'DISTINCT ';

        $res = @sqlsrv_query($this->connection, "SELECT " . $dist . $col . " FROM " . $this->tableStmt($table) . " WHERE $where $orderby", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $resarray = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++) {
            $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_NUMERIC);
            $resarray[$i] = $row[0];
        }
        return $resarray;
    }

	/**
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @return int
	 */
    function selectSum($table, $col, $where = null) {
        if ($where == null || $where == "1")
            $where = "1=1";

        $res = @sqlsrv_query($this->connection, "SELECT SUM(" . $col . ") FROM " . $this->tableStmt($table) . " WHERE $where", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return 0;
        $resarray = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++) {
            $row = sqlsrv_fetch_array($res, SQLSRV_FETCH_NUMERIC);
            $resarray[$i] = $row[0];
        }
        return $resarray[0];
    }

	/**
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @param null $orderby
	 * @return array
	 */
    function selectDropdown($table, $col, $where = null, $orderby = null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        if ($orderby == null)
            $orderby = '';
        else
            $orderby = "ORDER BY " . $orderby;

        $res = @sqlsrv_query($this->connection, "SELECT * FROM " . $this->tableStmt($table) . " WHERE $where $orderby", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $resarray = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++) {
            $row = sqlsrv_fetch_object($res);
            $resarray[$row->id] = $row->$col;
        }
        return $resarray;
    }

	/**
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @return null
	 */
    function selectValue($table, $col, $where=null) {
        if ($where == null || $where == "1")
            $where = "1=1";
//        $where = str_replace("'", "''", $where);
//        $where = str_replace('"', "'", $where);
        $sql = "SELECT TOP 1 " . $col . " FROM " . $this->tableStmt($table) . " WHERE $where";
        $res = @sqlsrv_query($this->connection, $sql);

        if ($res == null)
            return null;
        $obj = sqlsrv_fetch_object($res);
        if (is_object($obj)) {
            return $obj->$col;
        } else {
            return null;
        }
    }

	/**
	 * @param  $sql
	 * @return null
	 */
    function selectValueBySql($sql) {
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
        $res = $this->sql($sql);
        if ($res == null)
            return null;
        $r = sqlsrv_fetch_array ($res, SQLSRV_FETCH_NUMERIC);
        if (is_array($r)) {
            return $r[0];
        } else {
            return null;
        }
    }

    /**
     * Select a series of objects, and return by ID
     *
     * Selects a set of objects from the database.  Because of the way
     * Exponent handles objects and database tables, this is akin to
     * SELECTing a set of records from a database table. Returns an
     * array of objects, in any random order.  The indices of the array
     * are the IDs of the objects.
     *
     * @param string $table The name of the table/object to look at
     * @param string $where Criteria used to narrow the result set.  If this
     *   is specified as null, then no criteria is applied, and all objects are
     *   returned
     * @param null $orderby
     * @return array
     */
    function selectObjectsIndexedArray($table, $where = null, $orderby = null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        else
            $where = $this->injectProof($where);
        if ($orderby == null)
            $orderby = '';
        else
            $orderby = "ORDER BY " . $orderby;
        $res = @sqlsrv_query($this->connection, "SELECT * FROM " . $this->tableStmt($table) . " WHERE $where $orderby", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));

        if ($res == null)
            return array();
        $objects = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++) {
            $o = sqlsrv_fetch_object($res);
            $objects[$o->id] = $o;
        }
        return $objects;
    }

    /**
     * Count Objects matching a given criteria
     *
     * @param string $table The name of the table to count objects in.
     * @param string $where Criteria for counting.
     * @param bool $is_revisioned
     * @param bool $needs_approval
     * @param null $user
     *
     * @return int
     */
    function countObjects($table, $where = null, $is_revisioned=false, $needs_approval=false, $user=null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        $as = '';
        if ($is_revisioned) {
            $where .= " AND revision_id=(SELECT MAX(revision_id) FROM " . $this->tableStmt($table) . " WHERE id = rev.id ";
            if ($needs_approval) {
                if (!empty($user)) {
                    $where .= ' AND (approved=1 AND ((poster!=0 AND poster=' . $user . ') OR (editor!=0 AND editor=' . $user . ')))';
                } else {
                    $where .= ' AND (approved=1)';
                }
            }
            $where .= ")";
            $as = ' AS rev';
        }
        $res = @sqlsrv_query($this->connection, "SELECT COUNT(*) as c FROM " . $this->tableStmt($table) . $as . " WHERE $where");
        if ($res == null)
            return 0;
        $obj = sqlsrv_fetch_object($res);
        return $obj->c;
    }

    /**
     * Count Objects matching a given criteria using raw sql
     *
     * @param string $sql The sql query to be run
     * @return int
     */
    function countObjectsBySql($sql) {
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
        $res = @sqlsrv_query($this->connection, $sql);
        if ($res == null)
            return 0;
        $obj = sqlsrv_fetch_object($res);
        return $obj->c;
    }

    /**
     * Count Objects matching a given criteria using raw sql
     *
     * @param string $sql The sql query to be run
     * @return int|void
     */
    function queryRows($sql) {
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
        $res = @sqlsrv_query($this->connection, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        return empty($res) ? 0 : sqlsrv_num_rows($res);
    }

    /**
     * Select a single object.
     *
     * Selects an objects from the database.  Because of the way
     * Exponent handles objects and database tables, this is akin to
     * SELECTing a single record from a database table. Returns the
     * first record/object found (in the case of multiple-result queries,
     * there is no way to determine which of the set will be returned).
     * If no record(s) match the query, null is returned.
     *
     * @param string $table The name of the table/object to look at
     * @param string $where Criteria used to narrow the result set.
     * @return object/null|void
     */
    function selectObject($table, $where) {
        if ($where == null || $where == "1")
            $where = "1=1";
        $where = $this->injectProof($where);
        $res = sqlsrv_query($this->connection, "SELECT TOP 1 * FROM " . $this->tableStmt($table) . " WHERE $where");
        if ($res == null)
            return null;
        return sqlsrv_fetch_object($res);
    }

	/**
	 * @param $table
	 * @param string $lockType
	 * @return mixed
	 */
	function lockTable($table,$lockType="WRITE") {
        $sql = "LOCK TABLES " . $this->tableStmt($table) . " $lockType";

        $res = sqlsrv_query($this->connection, $sql);
        return $res;
    }

	/**
	 * @return mixed
	 */
	function unlockTables() {
        $sql = "UNLOCK TABLES";

        $res = sqlsrv_query($this->connection, $sql);
        return $res;
    }

	/**
     * Insert an Object into some table in the Database
     *
     * This method will return the ID assigned to the new record.  Note that
     * object attributes starting with an underscore ('_') will be ignored and NOT inserted
     * into the table as a field value.
     *
     * @param object $object The object to insert.
     * @param string $table The logical table name to insert into.  This does not include the table prefix, which
     *    is automagically prepended for you.
     * @return integer|void
     */
    function insertObject($object, $table) {
        //if ($table=="text") eDebug($object,true);
        $sql = "INSERT INTO " . $this->tableStmt($table) . " (";
        $values = ") VALUES (";
        $newinsert = false;
        foreach (get_object_vars($object) as $var => $val) {
            //We do not want to save any fields that start with an '_'
            if ($var[0] !== '_') {
                $sql .= "[$var],";
                if ($values !== ") VALUES (") {
                    $values .= ",";
                }
                if (is_bool($val) || $val === null) {
                    // we have to insert literals for strict mode
                    if ($val === null) {
                        $values .= "NULL";
                    } elseif ($val === true) {
                        $values .= "TRUE";
                    } elseif ($val === false) {
                        $values .= "FALSE";
                    }
                } elseif ($val === '') {
                    // we have to insert literals for strict mode
                    $values .= "''";
                } else {
                    $values .= "'" . str_replace("'", "''", $val) . "'";
                }
            } elseif ($var === 'id' && $val === null) {
                $newinsert = true;
            }
        }
        $sql = substr($sql, 0, -1) . substr($values, 0) . ")";
        //if($table=='text')eDebug($sql,true);
        if (property_exists($object, 'id') && !$newinsert)
            $sql = 'SET IDENTITY_INSERT ' . $this->tableStmt($table) . ' ON; ' . $sql . '; SET IDENTITY_INSERT ' . $this->tableStmt($table) . ' OFF;';
        if (@sqlsrv_query($this->connection, $sql) !== false) {
            $sql = "SELECT @@IDENTITY AS 'Identity'";
            $queryID = @sqlsrv_query($this->connection, $sql);
            sqlsrv_fetch($queryID);
            $id =  sqlsrv_get_field($queryID, 0);
            return $id;
        } else {
            if (DEVELOPMENT) {
                eLog($sql . ' - ' . $this->error(), 'insertObject Error');
            }
            return 0;
        }
    }

    /**
     * Delete one or more objects from the given table.
     *
     * @param string $table The name of the table to delete from.
     * @param string $where Criteria for determining which record(s) to delete.
     * @return mixed
     */
    function delete($table, $where = null) {
        if ($where != null) {
            if ($where == "1")
                $where = "1=1";
            $res = @sqlsrv_query($this->connection, "DELETE FROM " . $this->tableStmt($table) . " WHERE $where");
            return $res;
        } else {
            $res = @sqlsrv_query($this->connection, "TRUNCATE TABLE " . $this->tableStmt($table));
            return $res;
        }
    }

    /**
     * Update one or more objects in the database.
     *
     * This function will only update the attributes of the resulting record(s)
     * that are also member attributes of the $object object.
     *
     * @param object $object An object specifying the fields and values for updating.
     *    In most cases, this will be the altered object originally returned from one of
     *    the select* methods.
     * @param string $table The table to update in.
     * @param string $where Optional criteria used to narrow the result set.
     * @param string $identifier
     * @param bool $is_revisioned
     * @return bool|int|void
     */
    function updateObject($object, $table, $where=null, $identifier='id', $is_revisioned=false) {

        if ($is_revisioned) {
            $object->revision_id++;
            //if ($table=="text") eDebug($object);
            $res = $this->insertObject($object, $table);
            //if ($table=="text") eDebug($object,true);
            $this->trim_revisions($table, $object->$identifier, WORKFLOW_REVISION_LIMIT);
            return $res;
        }
        $sql = "UPDATE " . $this->tableStmt($table) . " SET ";
        foreach (get_object_vars($object) as $var => $val) {
            //We do not want to save any fields that start with an '_'
            //if($is_revisioned && $var=='revision_id') $val++;
            if ($var[0] !== '_') {
                if ($var != $identifier) {
                    if (is_array($val) || is_object($val)) {
                        $val = serialize($val);
                        $sql .= "[$var]='" . str_replace("'", "''", $val) . "',";
                    } else {
                        if (is_bool($val) || $val === null ) {
                            // we have to insert literals for strict mode
                            if ($val === null) {
                                $sql .= "[$var]=NULL,";
                            } elseif ($val === true) {
                                $sql .= "[$var]=TRUE,";
                            } elseif ($val === false) {
                                $sql .= "[$var]=FALSE,";
                            }
                        } elseif ($val !== '') {
                            // we have to insert literals for strict mode
                            $sql .= "[$var]='',";
                        } else {
                            $sql .= "[$var]='" . str_replace("'", "''", $val) . "',";
                        }
                    }
                }
            }
        }
        $sql = substr($sql, 0, -1) . " WHERE ";
        if ($where != null) {
            if ($where == "1")
                $where = "1=1";
            $sql .= $this->injectProof($where);
        }
        else
            $sql .= "[" . $identifier . "]=" . $object->$identifier;
        if (isset($object->revision_id)) {
            $sql .= ' AND revision_id=' . $object->revision_id;
        }
        //if ($table == 'text') eDebug($sql,true);
        $res = (@sqlsrv_query($this->connection, $sql) != false);
        if (DEVELOPMENT && !$res)
            eLog($sql . ' - ' . $this->error(), 'updateObject Error');
        return $res;
    }

	/**
	 * Find the maximum value of a field.  This is similar to a standard
	 * SELECT MAX(field) ... query.
	 *
	 * @param string $table The name of the table to select from.
	 * @param string $attribute The attribute name to find a maximum value for.
	 * @param string|array $groupfields A comma-separated list of fields (or a single field) name, used
	 *    for a GROUP BY clause.  This can also be passed as an array of fields.
	 * @param string $where Optional criteria for narrowing the result set.
	 * @return mixed
	 */
    function max($table, $attribute, $groupfields = null, $where = null) {
        if (is_array($groupfields))
            $groupfields = implode(",", $groupfields);
        $sql = "SELECT MAX($attribute) as fieldmax FROM " . $this->tableStmt($table);
        if ($where != null) {
            if ($where == "1")
                $where = "1=1";
            $sql .= " WHERE $where";
        }
        if ($groupfields != null)
            $sql .= " GROUP BY $groupfields";

        $res = @sqlsrv_query($this->connection, $sql);

        if ($res != null)
            $res = sqlsrv_fetch_object($res);
        if (!$res)
            return null;
        return $res->fieldmax;
    }

	/**
	 * Find the minimum value of a field.  This is similar to a standard
	 * SELECT MIN(field) ... query.
	 *
	 * @internal Internal
	 * @param string $table The name of the table to select from.
	 * @param string $attribute The attribute name to find a minimum value for.
	 * @param string|array $groupfields A comma-separated list of fields (or a single field) name, used
	 *    for a GROUP BY clause.  This can also be passed as an array of fields.
	 * @param string $where Optional criteria for narrowing the result set.
	 * @return null
	 */
    function min($table, $attribute, $groupfields = null, $where = null) {
        if (is_array($groupfields))
            $groupfields = implode(",", $groupfields);
        $sql = "SELECT MIN($attribute) as fieldmin FROM " . $this->tableStmt($table);
        if ($where != null) {
            if ($where == "1")
                $where = "1=1";
            $sql .= " WHERE $where";
        }
        if ($groupfields != null)
            $sql .= " GROUP BY $groupfields";

        $res = @sqlsrv_query($this->connection, $sql);

        if ($res != null)
            $res = sqlsrv_fetch_object($res);
        if (!$res)
            return null;
        return $res->fieldmin;
    }

    /**
     * Increment a numeric table field in a table.
     *
     * @param string $table The name of the table to increment in.
     * @param string $field The field to increment.
     * @param integer $step The step value.  Usually 1.  This can be negative, to
     *    decrement, but the decrement() method is preferred, for readability.
     * @param string $where Optional criteria to determine which records to update.
     * @return mixed
     */
    function increment($table, $field, $step, $where = null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        $sql = "UPDATE " . $this->tableStmt($table) . " SET [$field]=[$field]+$step WHERE $where";
        return @sqlsrv_query($this->connection, $sql);
    }

    /**
     * Check to see if the named table exists in the database.
     * Returns true if the table exists, and false if it doesn't.
     *
     * @param string $table Name of the table to look for.
     * @return bool
     */
    function tableExists($table) {
        $res = @sqlsrv_query($this->connection, "SELECT TOP 1 * FROM " . $this->tableStmt($table));
        return ($res != null);
    }

    /**
   	* Check to see if the named column within a table exists in the database.
   	* Returns true if the column exists, and false if it doesn't.
   	*
   	* @param string $table Name of the table to look in.
    * @param string $col Name of the column to look for.
   	* @return bool
   	*/
   	 function columnExists($table, $col) {
         // does the column exist?
//         $result = @mysqli_query($this->connection, "SHOW COLUMNS FROM `" . $this->prefix . "$table` LIKE '$col'");
//         if (!@mysqli_num_rows($result))
//             return false;
//         else
//             return true;
     }

    /**
     * Get a list of all tables in the database.  Optionally, only the tables
     * in the current logical database (tables with the same prefix) can
     * be retrieved.
     *
     * @param bool $prefixed_only Whether to return only the tables
     *    for the logical database, or all tables in the physical database.
     * @return array
     */
    function getTables($prefixed_only=true) {
        $res = @sqlsrv_query($this->connection, "SELECT * FROM information_schema.tables;", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $tables = array();
        for ($i = 0; $res && $i < sqlsrv_num_rows($res); $i++) {
            $tmp = sqlsrv_fetch_array($res);
            if ($prefixed_only && substr($tmp[0], 0, strlen($this->prefix)) == $this->prefix) {
                $tables[] = $tmp[0];
            } else if (!$prefixed_only) {
                $tables[] = $tmp[0];
            }
        }
        return $tables;
    }

    /**
     * Runs whatever table optimization routines the database engine supports.
     *
     * @param string $table The name of the table to optimize.
     * @return bool
     */
    function optimize($table) {
        $res = (@sqlsrv_query($this->connection, "OPTIMIZE " . $this->tableStmt($table)) != false);
        return $res;
    }

    /**
     * Retrieve table information for a named table.
     * Returns an object, with the following attributes:
     * <ul>
     * <li><b>rows</b> -- The number of rows in the table.</li>
     * <li><b>average_row_length</b> -- The average storage size of a row in the table.</li>
     * <li><b>data_total</b> -- How much total disk space is used by the table.</li>
     * <li><b>data_overhead</b> -- How much storage space in the table is unused (for compacting purposes)</li>
     * </ul>
     * @param  $table
     * @return null
     */
    function tableInfo($table) {
        $sql = "SHOW TABLE STATUS LIKE '" . $this->prefix . "$table'";
        $res = @sqlsrv_query($this->connection, $sql);
        if (!$res)
            return null;
        return $this->translateTableStatus(sqlsrv_fetch_object($res));
    }

    /**
     * Returns table information for all tables in the database.
     * This function effectively calls tableInfo() on each table found.
     * @return array
     */
    function databaseInfo() {
//        $sql = "SHOW TABLE STATUS";
        $res = @sqlsrv_query($this->connection, "SHOW TABLE STATUS LIKE '" . $this->prefix . "%'", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $info = array();
        for ($i = 0; $res && $i < sqlsrv_num_rows($res); $i++) {
            $obj = sqlsrv_fetch_object($res);
            $info[substr($obj->Name, strlen($this->prefix))] = $this->translateTableStatus($obj);
        }
        return $info;
    }

	/**
	 * @param  $table
	 * @return array
	 */
    function describeTable($table) {
        if (!$this->tableExists($table))
            return array();
        $res = @sqlsrv_query($this->connection, "DESCRIBE " . $this->tableStmt($table), array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $dd = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++) {
            $fieldObj = sqlsrv_fetch_object($res);

            $fieldObj->ExpFieldType = $this->getDDFieldType($fieldObj);
            if ($fieldObj->ExpFieldType == DB_DEF_STRING) {
                $fieldObj->ExpFieldLength = $this->getDDStringLen($fieldObj);
            }

            $dd[$fieldObj->Field] = $fieldObj;
        }

        return $dd;
    }

    /**
     * Build a data definition from a pre-existing table.  This is used
     * to intelligently alter tables that have already been installed.
     *
     * @param string $table The name of the table to get a data definition for.
     * @return array|null
     */
    function getDataDefinition($table) {
        // check if we have a cached version of this table description.
        if (expSession::issetTableCache($table))
            return expSession::getTableCache($table);

        // make sure the table exists
        if (!$this->tableExists($table))
            return array();

        $describe_sql = "Select SC.name AS 'Field', ISC.DATA_TYPE AS 'Type', SC.max_length AS 'Length', ISC.COLUMN_DEFAULT AS 'Default', SC.IS_NULLABLE AS 'Null', I.is_primary_key AS 'Key', I.is_unique AS 'Unique', SC.is_identity AS 'Auto_Increment'
        From sys.columns AS SC
        LEFT JOIN sys.index_columns AS IC
        ON IC.object_id = OBJECT_ID('" . $this->tableStmt($table) . "') AND
        IC.column_id = SC.column_id
        LEFT JOIN sys.indexes AS I
        ON I.object_id = OBJECT_ID('" . $this->tableStmt($table) . "') AND
        IC.index_id = I.index_id
        LEFT JOIN information_schema.columns ISC
        ON ISC.TABLE_NAME = '" . $this->prefix . $table . "'
        AND ISC.COLUMN_NAME = SC.name
        WHERE SC.object_id = OBJECT_ID('" . $this->tableStmt($table) . "')";
//        $res = @sqlsrv_query($this->connection, "Select * From INFORMATION_SCHEMA.COLUMNS Where TABLE_NAME = [" . $this->prefix . "$table]");
        $res = @sqlsrv_query($this->connection, $describe_sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $dd = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++) {
            $fieldObj = sqlsrv_fetch_object($res);
            $field = array();
            $field[DB_FIELD_TYPE] = $this->getDDFieldType($fieldObj);
            if ($field[DB_FIELD_TYPE] == DB_DEF_STRING) {
                $field[DB_FIELD_LEN] = $this->getDDStringLen($fieldObj);
            }
            //additional field attributes
            $default = $this->getDDDefault($fieldObj);
            if ($default != null)
                $field[DB_DEFAULT] = $default;
            $field[DB_INCREMENT] = $this->getDDAutoIncrement($fieldObj);
            $key = $this->getDDKey($fieldObj);
            if ($key)
                $field[$key] = true;
            if ($fieldObj->Null === "NO")
                $field[DB_NOTNULL] = true;
            else
                $field[DB_NOTNULL] = false;

            $dd[$fieldObj->Field] = $field;
        }

        // save this table description to cache so we don't need to go the DB next time.
        expSession::setTableCache($table, $dd);
        return $dd;
    }

    /**
   	* This is an internal function for use only within the database class
   	* @internal Internal
   	* @param  $fieldObj
   	* @return int
   	*/
   	function getDDFieldType($fieldObj) {
   	    $type = strtolower($fieldObj->Type);

//   	    if ($type === "int)")
//   	        return DB_DEF_ID;
   	    if ($type === "int" || $type == 56)
   	        return DB_DEF_INTEGER;
//   	    elseif ($type === "bit")
        elseif ($type === "smallint" || $type == 52)
   	        return DB_DEF_BOOLEAN;
//   	    elseif ($type === "int(14)")
//   	        return DB_DEF_TIMESTAMP;
   	    elseif ($type === "datetime2")
     	    return DB_DEF_TIMESTAMP;
   	    //else if (substr($type,5) == "double")
               //return DB_DEF_DECIMAL;
   	    elseif ($type === "float" || $type == 62)
   	        return DB_DEF_DECIMAL;
   	    // Strings
   	    elseif ($type === "nvarchar" || $type == 231) {
   	        return DB_DEF_STRING;
   	    } else {
            return DB_DEF_INTEGER;
        }
   	}

   	/**
   	* This is an internal function for use only within the database class
   	* @internal Internal
   	* @param  $fieldObj
   	* @return int|mixed
   	*/
   	function getDDStringLen($fieldObj) {
//   	    $type = strtolower($fieldObj->Type);
//   	    if ($type == "text")
//   	        return 65535;
//   	    else if ($type == "mediumtext")
//   	        return 16777215;
//   	    else if ($type == "longtext")
//   	        return 16777216;
//   	    else if (strpos($type, "varchar(") !== false) {
//   	        return str_replace(array("varchar(", ")"), "", $type) + 0;
//   	    } else {
//              return 256;
//          }
       return $fieldObj->Length;
   	}

   	/**
   	* This is an internal function for use only within the database class
   	* @internal Internal
   	* @param  $fieldObj
   	* @return int|mixed
   	*/
   	function getDDKey($fieldObj) {
//   	    $key = strtolower($fieldObj->Key);
//   	    if ($key == "pri")
//   	        return DB_PRIMARY;
//   	    else if ($key == "uni") {
//   	        return DB_UNIQUE;
//   	    } else {
//              return false;
//          }
        if ($fieldObj->Key == 1)
   	        return DB_PRIMARY;
   	    else if ($fieldObj->Unique == 1) {
   	        return DB_UNIQUE;
   	    } else {
            return false;
        }
   	}

   	/**
   	* This is an internal function for use only within the database class
   	* @internal Internal
   	* @param  $fieldObj
   	* @return int|mixed
   	*/
   	function getDDAutoIncrement($fieldObj) {
//   	    $auto = strtolower($fieldObj->Auto_Increment);
//   	    if ($auto == "auto_increment") {
//   	        return true;
//   	    } else {
//              return false;
//          }
       return $fieldObj->Auto_Increment == 1;
   	}

   	/**
   	* This is an internal function for use only within the database class
   	* @internal Internal
   	* @param  $fieldObj
   	* @return int|mixed
   	*/
   	function getDDIsNull($fieldObj) {
//   	    $null = strtolower($fieldObj->IS_NULLABLE);
//   	    if ($null == "yes") {
//   	        return true;
//   	    } else {
//              return false;
//          }
        return $fieldObj->Null == 1;
   	}

   	/**
   	* This is an internal function for use only within the database class
   	* @internal Internal
   	* @param  $fieldObj
   	* @return int|mixed
   	*/
   	function getDDDefault($fieldObj) {
   		return strtolower($fieldObj->Default);
   	}

    /**
     * Returns an error message from the database server.  This is intended to be
     * used by the implementers of the database wrapper, so that certain
     * cryptic error messages can be reworded.
     * @return string
     */
    function error() {
        if ($this->connection && ($errors = sqlsrv_errors() ) != null) {
            foreach( $errors as $error ) {
                $msg =  "SQLSTATE: ".$error[ 'SQLSTATE']."<br> ";
                $msg .= "code: ".$error[ 'code']."<br> ";
                $msg.= "message: ".$error[ 'message']." ";
                return $msg;
            }
        } else if ($this->connection == false) {
            return gt("Unable to connect to database server");
        } else
            return "";
    }

    /**
     * Checks whether the database connection has experienced an error.
     * @return bool
     */
    function inError() {
        return false;
        return ($this->connection !== null && sqlsrv_errors() !== null);
    }

	/**
	 * Escape a string based on the database connection
	 * @param $string
	 * @return string
	 */
	function escapeString($string) {
	    return expString::escape($string, true);
	}

    /**
     * Select an array of arrays
     *
     * Selects a set of arrays from the database.  Because of the way
     * Exponent handles objects and database tables, this is akin to
     * SELECTing a set of records from a database table.  Returns an
     * array of arrays, in any random order.
     *
     * @param string $table The name of the table/object to look at
     * @param string $where Criteria used to narrow the result set.  If this
     *   is specified as null, then no criteria is applied, and all objects are
     *   returned
     * @param string $orderby
     * @param bool $is_revisioned
     * @param bool $needs_approval
     * @param null $user
     *
     * @return array
     */
    function selectArrays($table, $where = null, $orderby = null, $is_revisioned=false, $needs_approval=false, $user=null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        else
            $where = $this->injectProof($where);
        $as = '';
        if ($is_revisioned) {
            $where .= " AND revision_id=(SELECT MAX(revision_id) FROM " . $this->tableStmt($table) . " WHERE id = rev.id ";
            if ($needs_approval) {
                if (!empty($user)) {
                    $where .= ' AND (approved=1 AND ((poster!=0 AND poster=' . $user . ') OR (editor!=0 AND editor=' . $user . ')))';
                } else {
                    $where .= ' AND (approved=1)';
                }
            }
            $where .= ")";
            $as = ' AS rev';
        }
        if ($orderby == null)
            $orderby = '';
        else
            $orderby = "ORDER BY " . $orderby;

        $res = @sqlsrv_query($this->connection, "SELECT * FROM " . $this->tableStmt($table) . $as . " WHERE $where $orderby", array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $arrays = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++)
            $arrays[] = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
        return $arrays;
    }

    /**
     * Select an array of arrays
     *
     * Selects a set of arrays from the database.  Because of the way
     * Exponent handles objects and database tables, this is akin to
     * SELECTing a set of records from a database table.  Returns an
     * array of arrays, in any random order.
     *
     * @param string $sql The name of the table/object to look at
     * @return array
     */
    function selectArraysBySql($sql) {
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
        $res = @sqlsrv_query($this->connection, $this->injectProof($sql), array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $arrays = array();
        for ($i = 0, $iMax = sqlsrv_num_rows($res); $i < $iMax; $i++)
            $arrays[] = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
        return $arrays;
    }

    /**
     * Select a record from the database as an array
     * Selects a set of arrays from the database.  Because of the way
     * Exponent handles objects and database tables, this is akin to
     * SELECTing a set of records from a database table.  Returns an
     * array of arrays, in any random order.
     *
     * @param string $table The name of the table/object to look at
     * @param string $where Criteria used to narrow the result set.  If this
     *                      is specified as null, then no criteria is applied, and all objects are
     *                      returned
     * @param null $orderby
     * @param bool $is_revisioned
     * @param bool $needs_approval
     * @param null $user
     *
     * @return array|void
     */
    function selectArray($table, $where = null, $orderby = null, $is_revisioned=false, $needs_approval=false, $user=null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        else
            $where = $this->injectProof($where);
        $as = '';
        if ($is_revisioned) {
            $where .= " AND revision_id=(SELECT MAX(revision_id) FROM " . $this->tableStmt($table) . " WHERE id = rev.id ";
            if ($needs_approval) {
                if (!empty($user)) {
                    $where .= ' AND (approved=1 AND ((poster!=0 AND poster=' . $user . ') OR (editor!=0 AND editor=' . $user . ')))';
                } else {
                    $where .= ' AND (approved=1)';
                }
            }
            $where .= ")";
            $as = ' AS rev';
        }
        $orderby = empty($orderby) ? '' : "ORDER BY " . $orderby;
        $sql = "SELECT TOP 1 * FROM " . $this->tableStmt($table) . $as . " WHERE $where $orderby";
        $res = @sqlsrv_query($this->connection, $sql);
        if ($res == null)
            return array();
        return sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
    }

    /**
     * Instantiate objects from selected records from the database
     *
     * @param string $table The name of the table/object to look at
     * @param string $where Criteria used to narrow the result set.  If this
     *                      is specified as null, then no criteria is applied, and all objects are
     *                      returned
     * @param string $classname
     * @param bool $get_assoc
     * @param bool $get_attached
     * @param array $except
     * @param bool $cascade_except
     * @param null $order
     * @param null $limitsql
     * @param bool $is_revisioned
     * @param bool $needs_approval
     * @param null $user
     *
     * @return array
     */
    function selectExpObjects($table=null, $where=null, $classname=null, $get_assoc=true, $get_attached=true, $except=array(), $cascade_except=false, $order=null, $limitsql=null, $is_revisioned=false, $needs_approval=false, $user=null) {
        if ($where == null || $where == "1")
            $where = "1=1";
        else
            $where = $this->injectProof($where);
        $as = '';
        if ($is_revisioned) {
            $where .= " AND revision_id=(SELECT MAX(revision_id) FROM " . $this->tableStmt($table) . " WHERE id = rev.id ";
            if ($needs_approval) {
                if (!empty($user)) {
                    $where .= ' AND (approved=1 AND ((poster!=0 AND poster=' . $user . ') OR (editor!=0 AND editor=' . $user . ')))';
                } else {
                    $where .= ' AND (approved=1)';
                }
            }
            $where .= ")";
            $as = ' AS rev';
        }
        $sql = "SELECT";
        //fixme replace with call to $this->limitStmt();
//        if ($limitsql === ' LIMIT 0,1') {
//            $sql .= ' TOP 1';
//            $limitsql = '';
//        } elseif ($limitsql !== null && $limitsql !== "") {
//            $parse = explode(",", $limitsql);
//            $offset = (int)substr($parse[0], 6);
//            $count = (int) $parse[1];
//            $limitsql = ' OFFSET ' . $offset . ' ROWS FETCH NEXT ' . $count . ' ROWS ONLY';
//        }
        if(strpos($limitsql, 'LIMIT') !== false) {
            eDebug($limitsql);
        }
        $sql .= " * FROM " . $this->tableStmt($table) . $as . " WHERE $where";
        $sql .= empty($order) ? '' : ' ORDER BY ' . $order;
        if (!empty($limitsql)) {
            if (strpos( $limitsql, 'ORDER BY') === false && empty($order)) {
                $sql .= ' ORDER BY id ';
            }
            $sql .= $limitsql;
        }
        $res = @sqlsrv_query($this->connection, $sql, array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $arrays = array();
        $numrows = sqlsrv_num_rows($res);
        for ($i = 0; $i < $numrows; $i++) {  //FIXME this can run us out of memory with too many rows
            $assArr = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC);
            $assArr['except'] = $except;
            if($cascade_except) $assArr['cascade_except'] = $cascade_except;
            $arrays[] = new $classname($assArr, $get_assoc, $get_attached);
        }
        return $arrays;
    }

    /**
     * Instantiate objects from selected records from the database

     * @param string $sql The sql statement to run on the model/classname
     * @param string $classname Can be $this->baseclassname
     * Returns an array of fields
     * @param bool $get_assoc
     * @param bool $get_attached
     * @return array
     */
    function selectExpObjectsBySql($sql, $classname, $get_assoc=true, $get_attached=true) {
        if (strpos($sql, 'OFFSET') !== false && strpos( $sql, 'ORDER BY') === false) {
            $sql = str_ireplace('OFFSET', 'ORDER BY id OFFSET', $sql);
        }
        $res = @sqlsrv_query($this->connection, $this->injectProof($sql), array(), array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        if ($res == null)
            return array();
        $arrays = array();
        $numrows = sqlsrv_num_rows($res);
        for ($i = 0; $i < $numrows; $i++)
            $arrays[] = new $classname(sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC), true, true);
        return $arrays;
    }

	/**
	 * This function returns all the text columns in the given table
	 * @param $table
	 * @return array
	 */
	function getTextColumns($table) {
		$sql = "SHOW COLUMNS FROM " . $this->tableStmt($table) . " WHERE type = 'text' OR type like 'varchar%'";
		$res = @sqlsrv_query($this->connection, $sql);
		if ($res == null)
            return array();
		$records = array();
		while($row = sqlsrv_fetch_object($res)) {
			$records[] = $row->Field;
		}

		return $records;
	}

    function selectFormattedNestedTree($table) {
  		$sql = "SELECT CONCAT( REPLICATE( '&#160;&#160;&#160;', (COUNT(parent.title) -1) ), node.title) AS title, node.id, node.lft
  				FROM " . $this->tableStmt($table) . " as node, " . $this->tableStmt($table) . " as parent
  				WHERE node.lft BETWEEN parent.lft and parent.rgt
  				GROUP BY node.title, node.id, node.lft
  				ORDER BY node.lft";

  		return $this->selectObjectsBySql($sql);
  	}

    /**
	 * @param  $table
	 * @param null $node
	 * @return array
	 */
	function selectNestedBranch($table, $node=null) {
	    if (empty($node))
	        return array();

        $table = $this->tableStmt($table);
	    $where = is_numeric($node) ? 'id=' . $node : 'title="' . $node . '"';
	    $sql = 'SELECT node.*,
	           (COUNT(parent.title) - (sub_tree.depth + 1)) AS depth
	           FROM ' .$table . ' AS node,
	           ' . $table . ' AS parent,
	           ' .$table . ' AS sub_parent,
               ( SELECT TOP 1000 node.*, (COUNT(parent.title) - 1) AS depth
                   FROM ' . $table . ' AS node,
                   ' . $table . ' AS parent
                   WHERE node.lft BETWEEN parent.lft
                   AND parent.rgt AND node.' . $where . '
                   GROUP BY node.title, node.id, node.body, node.sef_url, node.is_active, node.is_events, node.hide_closed_events, node.canonical,
                   node.meta_title, node.meta_keywords, node.meta_description, node.noindex, node.nofollow, node.items_per_page, node.expFiles_id, node.
                   rgt, node.lft, node.parent_id, node.poster, node.created_at, node.editor, node.edited_at, node.location_data, node.original_id
                   ORDER BY node.lft )
	           AS sub_tree
	           WHERE node.lft BETWEEN parent.lft AND parent.rgt
	           AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
	           AND sub_parent.title = sub_tree.title
	           GROUP BY node.title, node.id, node.body, node.sef_url, node.is_active, node.is_events, node.hide_closed_events, node.canonical,
	           node.meta_title, node.meta_keywords, node.meta_description, node.noindex, node.nofollow, node.items_per_page, node.expFiles_id, node.
	           rgt, node.lft, node.parent_id, node.poster, node.created_at, node.editor, node.edited_at, node.location_data, node.original_id, sub_tree.depth
	           ORDER BY node.lft;';

	    return $this->selectObjectsBySql($sql);
	}

}

?>