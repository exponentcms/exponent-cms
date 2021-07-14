<?php
##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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

/**
 * This is the expDatabase subsystem
 * Handles all database abstraction in Exponent.
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
class expDatabase {

    /**
     * Connect to the Exponent database
     *
     * This function attempts to connect to the exponent database,
     * and then returns the database object to the caller.
     *
     * @param string $username the database username
     * @param string $password the database password
     * @param string $hostname the url of the database server
     * @param string $database the name of the database
     * @param string $dbclass
     * @param bool $new
     * @param null $log
     *
     * @return \database the database object
     */
	public static function connect($username,$password,$hostname,$database,$dbclass = '',$new=false,$log=null) {
		if (!defined('DB_ENGINE')) {
			$backends = array_keys(self::backends(1));
			if (count($backends)) {
				define('DB_ENGINE',$backends[0]);
			} else {
				define('DB_ENGINE','NOTSUPPORTED');
			}
		}
		(include_once(BASE.'framework/core/subsystems/database/'.DB_ENGINE.'.php')) or exit(gt('None of the installed Exponent Database Backends will work with this server\'s version of PHP.'));
		if ($dbclass == '' || $dbclass == null) $dbclass = DB_ENGINE;
		(include_once(BASE.'framework/core/subsystems/database/'.$dbclass.'.php')) or exit(gt('The specified database backend').'  ('.$dbclass.') '.gt('is not supported by Exponent'));
		$dbclass .= '_database';
		if ($username !== 'not_configured') {
            $newdb = new $dbclass($username,$password,$hostname,$database,$new,$log);
            if (!$newdb->tableExists('user')) {
                $newdb->havedb = false;
            }
        } else {
            $newdb = new stdClass();
            $newdb->havedb = false;
        }
		return $newdb;
	}

	/**
	 * List all available database backends
	 *
	 * This function looks for available database engines,
	 * and then returns an array to the caller.
	 *
	 * @param int $valid_only
	 * @return array An associative array of engine identifiers.
	 *	The internal engine name is the key, and the external
	 *	descriptive name is the value.
	 */
	public static function backends($valid_only = 1) {
		$options = array();
		$dh = opendir(BASE.'framework/core/subsystems/database');
		while (($file = readdir($dh)) !== false) {
			if (is_file(BASE.'framework/core/subsystems/database/'.$file) && is_readable(BASE.'framework/core/subsystems/database/'.$file) && substr($file,-9,9) === '.info.php') {
				$info = include(BASE.'framework/core/subsystems/database/'.$file);
				if ($info['is_valid'] == 1 || !$valid_only) {
					$options[substr($file,0,-9)] = $info['name'];
				}
			}
		}
		return $options;
	}

    public static function fix_table_names() {
        global $db;

        // fix table names
        $tablenames = array (
            'content_expcats'=>'content_expCats',
            'content_expcomments'=>'content_expComments',
            'content_expdefinablefields'=>'content_expDefinableFields',
            'content_expdefinablefields_value'=>'content_expDefinableFields_value',
            'content_expfiles'=>'content_expFiles',
            'content_expratings'=>'content_expRatings',
            'content_expsimplenote'=>'content_expSimpleNote',
            'content_exptags'=>'content_expTags',
            'expcats'=>'expCats',
            'expcomments'=>'expComments',
            'expdefinablefields'=>'expDefinableFields',
            'expealerts'=>'expeAlerts',
            'expealerts_temp'=>'expeAlerts_temp',
            'expfiles'=>'expFiles',
            'expratings'=>'expRatings',
            'exprss'=>'expRss',
            'expsimplenote'=>'expSimpleNote',
            'exptags'=>'expTags',
			'bing_product_types_storecategories'=>'bing_product_types_storeCategories',
			'google_product_types_storecategories'=>'google_product_types_storeCategories',
			'nextag_product_types_storecategories'=>'nextag_product_types_storeCategories',
			'pricegrabber_product_types_storecategories'=>'pricegrabber_product_types_storeCategories',
			'shopping_product_types_storecategories'=>'shopping_product_types_storeCategories',
			'shopzilla_product_types_storecategories'=>'shopzilla_product_types_storeCategories',
			'crosssellitem_product'=>'crosssellItem_product',
			'product_storecategories'=>'product_storeCategories',
			'storecategories'=>'storeCategories',
        );

        $renamed = array();
        foreach ($tablenames as $oldtablename=>$newtablename) {
            if (!$db->tableExists($newtablename)) {
                $db->sql('RENAME TABLE ' . $db->tableStmt($oldtablename) . ' TO ' . $db->tableStmt($newtablename));
                $renamed[] = $newtablename;
            }
        }
        return $renamed;
    }

    public static function install_dbtables($aggressive=false, $workflow=ENABLE_WORKFLOW) {
   	    global $db;

   		expSession::clearAllUsersSessionCache();
   		$tables = array();

   		// first the core definitions
   		$coredefs = BASE.'framework/core/definitions';
   		if (is_readable($coredefs)) {
   			$dh = opendir($coredefs);
   			while (($file = readdir($dh)) !== false) {
   				if (is_readable("$coredefs/$file") && is_file("$coredefs/$file") && substr($file,-4,4) === ".php" && substr($file,-9,9) !== ".info.php") {
   					$tablename = substr($file,0,-4);
   					$dd = include("$coredefs/$file");
   					$info = null;
   					if (is_readable("$coredefs/$tablename.info.php")) $info = include("$coredefs/$tablename.info.php");
   					if (!$db->tableExists($tablename)) {
   						foreach ($db->createTable($tablename, $dd, $info) as $key=>$status) {
   							$tables[$key] = $status;
   						}
   					} else {
   						foreach ($db->alterTable($tablename, $dd, $info, $aggressive) as $key=>$status) {
//							if (isset($tables[$key])) echo "$tablename, $key<br>";  //FIXME we shouldn't echo this, already installed?
   							if ($status == TABLE_ALTER_FAILED){
   								$tables[$key] = $status;
   							} else {
   								$tables[$key] = ($status == TABLE_ALTER_NOT_NEEDED ? DATABASE_TABLE_EXISTED : DATABASE_TABLE_ALTERED);
   							}

   						}
   					}
   				}
   			}
   		}

   		// then search for module definitions
   		$moddefs = array(
   			BASE."framework/modules",
            BASE.'themes/'.DISPLAY_THEME.'/modules',
        );
        $models = expModules::initializeModels();
   		foreach ($moddefs as $moddef) {
   			if (is_readable($moddef)) {
   				$dh = opendir($moddef);
   				while (($file = readdir($dh)) !== false) {
   					if (is_dir($moddef.'/'.$file) && ($file !== '..' && $file !== '.')) {
   						$dirpath = $moddef.'/'.$file.'/definitions';
   						if (file_exists($dirpath)) {
   							$def_dir = opendir($dirpath);
   							while (($def = readdir($def_dir)) !== false) {
   	//							eDebug("$dirpath/$def");
   								if (is_readable("$dirpath/$def") && is_file("$dirpath/$def") && substr($def,-4,4) === ".php" && substr($def,-9,9) !== ".info.php") {
   									$tablename = substr($def,0,-4);
   									$dd = include("$dirpath/$def");
   									$info = null;
//                                    foreach ($models as $modelname=>$modelpath) {
                                    $rev_aggressive = $aggressive;
                                    // add workflow fields
                                    if (!empty($models[substr($def,0,-4)])) {
                                        $modelname = substr($def,0,-4);
                                        $model = new $modelname();
                                        if ($model->supports_revisions && $workflow) {
                                            $dd['revision_id'] = array(
                                                DB_FIELD_TYPE=>DB_DEF_INTEGER,
                                                DB_PRIMARY=>true,
                                                DB_DEFAULT=>1,
                                            );
                                            $dd['approved'] = array(
                                                DB_FIELD_TYPE=>DB_DEF_BOOLEAN
                                            );
                                            $rev_aggressive = true;
                                        }
                                    }
   									if (is_readable("$dirpath/$tablename.info.php")) $info = include("$dirpath/$tablename.info.php");
   									if (!$db->tableExists($tablename)) {
   										foreach ($db->createTable($tablename, $dd, $info) as $key=>$status) {
   											$tables[$key] = $status;
   										}
   									} else {
   										foreach ($db->alterTable($tablename, $dd, $info, $rev_aggressive) as $key=>$status) {
//											if (isset($tables[$key])) echo "$tablename, $key<br>";  //FIXME we shouldn't echo this, already installed?
   											if ($status == TABLE_ALTER_FAILED){
   												$tables[$key] = $status;
   											} else {
   												$tables[$key] = ($status == TABLE_ALTER_NOT_NEEDED ? DATABASE_TABLE_EXISTED : DATABASE_TABLE_ALTERED);
   											}

   										}
   									}
   								}
   							}
   						}
   					}
   				}
   			}
   		}
   		return $tables;
   	}
//}
//
//class SSP {  //note tacked into expDatabase for standardization and customized for Exponent

	/**
	 * Create the data output array for the DataTables rows
	 *
	 *  @param  array $columns Column information array
	 *  @param  array $data    Data from the SQL get
	 *  @return array          Formatted data in a row based format
	 */
	static function data_output ( $columns, $data )
	{
		$out = array();

		for ( $i=0, $ien=count($data) ; $i<$ien ; $i++ ) {
			$row = array();

			for ( $j=0, $jen=count($columns) ; $j<$jen ; $j++ ) {
				$column = $columns[$j];

				// Is there a formatter?
				if ( isset( $column['formatter'] ) ) {
                    if(empty($column['db'])){
                        $row[ $column['dt'] ] = $column['formatter']( $data[$i] );
                    }
                    else{
					$row[ $column['dt'] ] = $column['formatter']( $data[$i][ $column['db'] ], $data[$i] );
				}
				}
				else {
                    if(!empty($column['db'])){
					$row[ $column['dt'] ] = $data[$i][ $columns[$j]['db'] ];
				}
                    else{
                        $row[ $column['dt'] ] = "";
                    }
				}
			}

			$out[] = $row;
		}

		return $out;
	}

	/**
	 * Paging
	 *
	 * Construct the LIMIT clause for server-side processing SQL query
	 *
	 *  @param  array $request Data sent to server by DataTables
	 *  @param  array $columns Column information array
	 *  @return string SQL limit clause
	 */
	static function limit ( $request )
	{
	    global $db;

	    return $db::limit_pdo($request);

//		$limit = '';
//
//		if ( isset($request['start']) && $request['length'] != -1 ) {
//			$limit = "LIMIT ".(int)($request['start']).", ".(int)($request['length']);
//		}
//
//		return $limit;
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
	static function order ( $request, $columns )
	{
	    global $db;

	    return $db::order_pdo($request, $columns);

//		$order = '';
//
//		if ( isset($request['order']) && count($request['order']) ) {
//			$orderBy = array();
//			$dtColumns = self::pluck( $columns, 'dt' );
//
//			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
//				// Convert the column index into the column data property
//				$columnIdx = (int)($request['order'][$i]['column']);
//				$requestColumn = $request['columns'][$columnIdx];
//
//				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
//				$column = $columns[ $columnIdx ];
//
////				if ( $requestColumn['orderable'] === 'true' ) {  //fixme allows us to initially sort an unsortable column
//					$dir = $request['order'][$i]['dir'] === 'asc' ?
//						'ASC' :
//						'DESC';
//
//					$orderBy[] = '`'.$column['db'].'` '.$dir;
////				}
//			}
//
//            if ( count( $orderBy ) ) {
//                $order = 'ORDER BY '.implode(', ', $orderBy);
//            }
//		}
//
//		return $order;
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
	static function filter ( $request, $columns, &$bindings )
	{
        global $db;

   	    return $db::filter_pdo( $request, $columns, $bindings);

//		$globalSearch = array();
//		$columnSearch = array();
//		$dtColumns = self::pluck( $columns, 'dt' );
//
//		if ( isset($request['search']) && $request['search']['value'] != '' ) {
//			$str = $request['search']['value'];
//
//			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
//				$requestColumn = $request['columns'][$i];
//				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
//				$column = $columns[ $columnIdx ];
//
//				if ( $requestColumn['searchable'] === 'true' ) {
//                    if (stripos($str, '-yadcf_delim-') !== false) {
//                        $val = explode('-yadcf_delim-', $str);
//                        if (empty($val[0])) {
//                            $val[0] = '0';
//                        } elseif (expDateTime::is_date($val[0])) {
//                            $val[0] = strtotime($val[0]);
//                        }
//                        if (empty($val[1])) {
//                            $val[1] = time();
//                        } elseif (expDateTime::is_date($val[1])) {
//                            $val[1] = strtotime($val[1]);
//                        }
//                        $binding0 = self::bind( $bindings, $val[0], PDO::PARAM_STR );
//                        $binding1 = self::bind( $bindings, $val[1], PDO::PARAM_STR );
//                        $globalSearch[] = "`".$column['db']."` BETWEEN ".$binding0." AND ".$binding1;
//                    } else {
//                        $binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
//                        $globalSearch[] = "`".$column['db']."` LIKE ".$binding;
//                    }
//				}
//			}
//		}
//
//		// Individual column filtering
//		if ( isset( $request['columns'] ) ) {
//			for ( $i=0, $ien=count($request['columns']) ; $i<$ien ; $i++ ) {
//				$requestColumn = $request['columns'][$i];
//				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
//				$column = $columns[ $columnIdx ];
//
//				$str = $requestColumn['search']['value'];
//
//				if ( $requestColumn['searchable'] === 'true' && $str != '' ) {
//				    if (stripos($str, '-yadcf_delim-') !== false) {
//				        $val = explode('-yadcf_delim-', $str);
//                        if (empty($val[0])) {
//                            $val[0] = '0';
//                        } elseif (expDateTime::is_date($val[0])) {
//                            $val[0] = strtotime($val[0]);
//                        }
//                        if (empty($val[1])) {
//                            $val[1] = time();
//                        } elseif (expDateTime::is_date($val[1])) {
//                            $val[1] = strtotime($val[1]);
//                        }
//                        $binding0 = self::bind( $bindings, $val[0], PDO::PARAM_STR );
//                        $binding1 = self::bind( $bindings, $val[1], PDO::PARAM_STR );
//                        $columnSearch[] = "`".$column['db']."` BETWEEN ".$binding0." AND ".$binding1;
//                    } else {
//                        $binding = self::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
//                        $columnSearch[] = "`".$column['db']."` LIKE ".$binding;
//                    }
//				}
//			}
//		}
//
//		// Combine the filters into a single string
//		$where = '';
//
//		if ( count( $globalSearch ) ) {
//			$where = '('.implode(' OR ', $globalSearch).')';
//		}
//
//		if ( count( $columnSearch ) ) {
//			$where = $where === '' ?
//				implode(' AND ', $columnSearch) :
//				$where .' AND '. implode(' AND ', $columnSearch);
//		}
//
//		if ( $where !== '' ) {
//			$where = 'WHERE '.$where;
//		}
//
//		return $where;
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
	static function simple ( $request, $table, $primaryKey, $columns )
	{
	    global $db;

	    return $db::simple_pdo($request, $table, $primaryKey, $columns);

//		$bindings = array();
//		$dbpdo = self::sql_connect();
//
//		// Build the SQL query string from the request
//		$limit = self::limit( $request );
//		$order = self::order( $request, $columns );
//		$where = self::filter( $request, $columns, $bindings );
//
//		// Main query to actually get the data
//		$data = self::sql_exec( $dbpdo, $bindings,
//			"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
//			 FROM $table
//			 $where
//			 $order
//			 $limit"
//		);
//
//		// Data set length after filtering
//		$resFilterLength = self::sql_exec( $dbpdo, $bindings,
//			"SELECT COUNT(`{$primaryKey}`)
//			 FROM   $table
//			 $where"
//		);
//		$recordsFiltered = $resFilterLength[0][0];
//
//		// Total data set length
//		$resTotalLength = self::sql_exec( $dbpdo,
//			"SELECT COUNT(`{$primaryKey}`)
//			 FROM   $table"
//		);
//		$recordsTotal = $resTotalLength[0][0];
//
//		/*
//		 * Output
//		 */
//		return array(
//			"draw"            => isset ( $request['draw'] ) ?
//                                 (int)( $request['draw'] ) :
//                                 0,
//			"recordsTotal"    => (int)( $recordsTotal ),
//			"recordsFiltered" => (int)( $recordsFiltered ),
//			"data"            => self::data_output( $columns, $data )
//		);
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
    static function complex ( $request, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )
   	{
   	    global $db;

   	    return $db::complex_pdo($request, $table, $primaryKey, $columns, $whereResult, $whereAll);

//   		$bindings = array();
//        $dbpdo = self::sql_connect();
//   		$whereAllSql = '';
//
//   		// Build the SQL query string from the request
//   		$limit = self::limit( $request );
//   		$order = self::order( $request, $columns );
//   		$where = self::filter( $request, $columns, $bindings );
//
//   		$whereResult = self::_flatten( $whereResult );
//   		$whereAll = self::_flatten( $whereAll );
//
//   		if ( $whereResult ) {
//   			$where = $where ?
//   				$where .' AND '.$whereResult :
//   				'WHERE '.$whereResult;
//   		}
//
//   		if ( $whereAll ) {
//   			$where = $where ?
//   				$where .' AND '.$whereAll :
//   				'WHERE '.$whereAll;
//
//   			$whereAllSql = 'WHERE '.$whereAll;
//   		}
//
//   		// Main query to actually get the data
//   		$data = self::sql_exec( $dbpdo, $bindings,
//   			"SELECT `".implode("`, `", self::pluck($columns, 'db'))."`
//   			 FROM $table
//   			 $where
//   			 $order
//   			 $limit"
//   		);
//
//   		// Data set length after filtering
//   		$resFilterLength = self::sql_exec( $dbpdo, $bindings,
//   			"SELECT COUNT(`{$primaryKey}`)
//   			 FROM   $table
//   			 $where"
//   		);
//   		$recordsFiltered = $resFilterLength[0][0];
//
//   		// Total data set length
//   		$resTotalLength = self::sql_exec( $dbpdo, $bindings,
//   			"SELECT COUNT(`{$primaryKey}`)
//   			 FROM   $table ".
//   			 $whereAllSql
//   		);
//   		$recordsTotal = $resTotalLength[0][0];
//
//   		/*
//   		 * Output
//   		 */
//   		return array(
//   			"draw"            => isset ( $request['draw'] ) ?
//                                 (int)( $request['draw'] ) :
//                                 0,
//   			"recordsTotal"    => (int)( $recordsTotal ),
//   			"recordsFiltered" => (int)( $recordsFiltered ),
//   			"data"            => self::data_output( $columns, $data )
//   		);
   	}

	/**
	 * Connect to the database by PDO
	 *
	 * @return PDO Database connection handle
	 */
	static function sql_connect ()
	{
	    global $db;

	    return $db->sql_connect_pdo();
	}

	/**
	 * Execute an SQL query on the database
	 *
	 * @param  resource/PDO $dbpdo  Database handler
	 * @param  array    $bindings Array of PDO binding values from bind() to be
	 *   used for safely escaping strings. Note that this can be given as the
	 *   SQL query string if no bindings are required.
	 * @param  string   $sql SQL query to execute.
	 * @return array         Result from the query (all rows)
	 */
	static function sql_exec ( $dbpdo, $bindings, $sql=null )
	{
		// Argument shifting
		if ( $sql === null ) {
			$sql = $bindings;
		}

		$stmt = $dbpdo->prepare( $sql );
		//echo $sql;

		// Bind parameters
		if ( is_array( $bindings ) ) {
			for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
				$binding = $bindings[$i];
				$stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
			}
		}

		// Execute
		try {
			$stmt->execute();
		}
		catch (PDOException $e) {
			self::fatal( "An SQL error occurred: ".$e->getMessage() );
		}

		// Return all
		return $stmt->fetchAll( PDO::FETCH_BOTH );
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Internal methods
	 */

	/**
	 * Throw a fatal error.
	 *
	 * This writes out an error message in a JSON string which DataTables will
	 * see and show to the user in the browser.
	 *
	 * @param  string $msg Message to send to the client
	 */
	static function fatal ( $msg )
	{
		echo json_encode( array(
			"error" => $msg
		) );

		exit(0);
	}

	/**
	 * Create a PDO binding key which can be used for escaping variables safely
	 * when executing a query with sql_exec()
	 *
	 * @param  array &$a    Array of bindings
	 * @param  *      $val  Value to bind
	 * @param  int    $type PDO field type
	 * @return string       Bound key to be used in the SQL where this parameter
	 *   would be used.
	 */
	static function bind ( &$a, $val, $type )
	{
		$key = ':binding_'.count( $a );

		$a[] = array(
			'key' => $key,
			'val' => $val,
			'type' => $type
		);

		return $key;
	}


	/**
	 * Pull a particular property from each assoc. array in a numeric array,
	 * returning and array of the property values from each item.
	 *
	 *  @param  array  $a    Array to get data from
	 *  @param  string $prop Property to read
	 *  @return array        Array of property values
	 */
	static function pluck ( $a, $prop )
	{
		$out = array();

		for ( $i=0, $len=count($a) ; $i<$len ; $i++ ) {
            if(empty($a[$i][$prop])){
                continue;
			}
			//removing the $out array index confuses the filter method in doing proper binding,
			//adding it ensures that the array data are mapped correctly
			$out[$i] = $a[$i][$prop];
		}

		return $out;
	}


	/**
	 * Return a string from an array or a string
	 *
	 * @param  array|string $a Array to join
	 * @param  string $join Glue for the concatenation
	 * @return string Joined string
	 */
	static function _flatten ( $a, $join = ' AND ' )
	{
		if ( ! $a ) {
			return '';
		}
		elseif ( $a && is_array($a) ) {
			return implode( $join, $a );
		}
		return $a;
	}
}

/**
* This is the abstract class database
*
* This is the generic implementation of the database class.
* @subpackage Database
* @package Subsystems
*/
abstract class database {

	/**
	* @var string $connection Database connection string
	*/
	var $connection = null;
	/**
	* @var boolean $havedb
	*/
	var $havedb = false;
	/**
	* @var string $prefix Database prefix
	*/
	var $prefix = "";

	/**
	 * Make a connection to the Database Server
	 *
	 * Takes the supplied credentials (username / password) and tries to
	 * connect to the server and select the given database.  All the rules
	 * governing database connect also govern this method.
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

	//	function connect ($username, $password, $hostname, $database, $new=false) {
	abstract function __construct($username, $password, $hostname, $database, $new=false);

	/** Begin SSP Methods */
    /**
     * Connect to the database by PDO
   	 *
   	 * @return PDO Database connection handle
   	 */
   	abstract function sql_connect_pdo();

    /**
   	 * Paging
   	 *
   	 * Construct the LIMIT clause for server-side processing SQL query
   	 *
   	 *  @param  array $request Data sent to server by DataTables
   	 *  @return string SQL limit clause
   	 */
   	static function limit_pdo ( $request )
   	{
   		$limit = '';

   		if ( isset($request['start']) && $request['length'] != -1 ) {
   			$limit = "LIMIT ".(int)($request['start']).", ".(int)($request['length']);
   		}

   		return $limit;
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
   	static function order_pdo ( $request, $columns )
   	{
   		$order = '';

   		if ( isset($request['order']) && count($request['order']) ) {
   			$orderBy = array();
   			$dtColumns = expDatabase::pluck( $columns, 'dt' );

   			for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
   				// Convert the column index into the column data property
   				$columnIdx = (int)($request['order'][$i]['column']);
   				$requestColumn = $request['columns'][$columnIdx];

   				$columnIdx = array_search( $requestColumn['data'], $dtColumns );
   				$column = $columns[ $columnIdx ];

   //				if ( $requestColumn['orderable'] === 'true' ) {  //fixme allows us to initially sort an unsortable column
   					$dir = $request['order'][$i]['dir'] === 'asc' ?
   						'ASC' :
   						'DESC';

   					$orderBy[] = '`'.$column['db'].'` '.$dir;
   //				}
   			}

               if ( count( $orderBy ) ) {
                   $order = 'ORDER BY '.implode(', ', $orderBy);
               }
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
                           $globalSearch[] = "`".$column['db']."` BETWEEN ".$binding0." AND ".$binding1;
                       } else {
                           $binding = expDatabase::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                           $globalSearch[] = "`".$column['db']."` LIKE ".$binding;
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
                           $columnSearch[] = "`".$column['db']."` BETWEEN ".$binding0." AND ".$binding1;
                       } else {
                           $binding = expDatabase::bind( $bindings, '%'.$str.'%', PDO::PARAM_STR );
                           $columnSearch[] = "`".$column['db']."` LIKE ".$binding;
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
   	static function simple_pdo ( $request, $table, $primaryKey, $columns )
   	{
   		$bindings = array();
   		$dbpdo = expDatabase::sql_connect();

   		// Build the SQL query string from the request
   		$limit = expDatabase::limit( $request );
   		$order = expDatabase::order( $request, $columns );
   		$where = expDatabase::filter( $request, $columns, $bindings );

   		// Main query to actually get the data
   		$data = expDatabase::sql_exec( $dbpdo, $bindings,
   			"SELECT `".implode("`, `", expDatabase::pluck($columns, 'db'))."`
   			 FROM $table
   			 $where
   			 $order
   			 $limit"
   		);

   		// Data set length after filtering
   		$resFilterLength = expDatabase::sql_exec( $dbpdo, $bindings,
   			"SELECT COUNT(`{$primaryKey}`)
   			 FROM   $table
   			 $where"
   		);
   		$recordsFiltered = $resFilterLength[0][0];

   		// Total data set length
   		$resTotalLength = expDatabase::sql_exec( $dbpdo,
   			"SELECT COUNT(`{$primaryKey}`)
   			 FROM   $table"
   		);
   		$recordsTotal = $resTotalLength[0][0];

   		/*
   		 * Output
   		 */
   		return array(
   			"draw"            => isset ( $request['draw'] ) ?
                                    (int)( $request['draw'] ) :
                                    0,
   			"recordsTotal"    => (int)( $recordsTotal ),
   			"recordsFiltered" => (int)( $recordsFiltered ),
   			"data"            => expDatabase::data_output( $columns, $data )
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
       static function complex_pdo ( $request, $table, $primaryKey, $columns, $whereResult=null, $whereAll=null )
      	{
      		$bindings = array();
            $dbpdo = expDatabase::sql_connect();
      		$whereAllSql = '';

      		// Build the SQL query string from the request
      		$limit = expDatabase::limit( $request );
      		$order = expDatabase::order( $request, $columns );
      		$where = expDatabase::filter( $request, $columns, $bindings );

      		$whereResult = expDatabase::_flatten( $whereResult );
      		$whereAll = expDatabase::_flatten( $whereAll );

      		if ( $whereResult ) {
      			$where = $where ?
      				$where .' AND '.$whereResult :
      				'WHERE '.$whereResult;
      		}

      		if ( $whereAll ) {
      			$where = $where ?
      				$where .' AND '.$whereAll :
      				'WHERE '.$whereAll;

      			$whereAllSql = 'WHERE '.$whereAll;
      		}

      		// Main query to actually get the data
      		$data = expDatabase::sql_exec( $dbpdo, $bindings,
      			"SELECT `".implode("`, `", expDatabase::pluck($columns, 'db'))."`
      			 FROM $table
      			 $where
      			 $order
      			 $limit"
      		);

      		// Data set length after filtering
      		$resFilterLength = expDatabase::sql_exec( $dbpdo, $bindings,
      			"SELECT COUNT(`{$primaryKey}`)
      			 FROM   $table
      			 $where"
      		);
      		$recordsFiltered = $resFilterLength[0][0];

      		// Total data set length
      		$resTotalLength = expDatabase::sql_exec( $dbpdo, $bindings,
      			"SELECT COUNT(`{$primaryKey}`)
      			 FROM   $table ".
      			 $whereAllSql
      		);
      		$recordsTotal = $resTotalLength[0][0];

      		/*
      		 * Output
      		 */
      		return array(
      			"draw"            => isset ( $request['draw'] ) ?
                                    (int)( $request['draw'] ) :
                                    0,
      			"recordsTotal"    => (int)( $recordsTotal ),
      			"recordsFiltered" => (int)( $recordsFiltered ),
      			"data"            => expDatabase::data_output( $columns, $data )
      		);
      	}
        /** End SSP Methods */

	   /**
	    * Create a new Table
	    *
	    * Creates a new database table, according to the passed data definition.
	    *
	    * This function abides by the Exponent Data Definition Language, and interprets
	    * its general structure for databases.
	    *
	    * @param string $tablename The name of the table to create
	    * @param array $datadef The data definition to create, expressed in
	    *   the Exponent Data Definition Language.
	    * @param array $info Information about the table itself.
	    * @return array
	 */
	abstract function createTable($tablename, $datadef, $info);

	/**
	* This is an internal function for use only within the database class
	* @internal Internal
	* @param  $name
	* @param  $def
	* @return bool|string
	*/
	function fieldSQL($name, $def) {
	    $sql = "`$name`";
	    if (!isset($def[DB_FIELD_TYPE])) {
	        return false;
	    }
	    $type = $def[DB_FIELD_TYPE];
	    if ($type === DB_DEF_ID) {
	        $sql .= " INT(11)";
	    } else if ($type === DB_DEF_BOOLEAN) {
	        $sql .= " TINYINT(1)";
	    } else if ($type === DB_DEF_TIMESTAMP) {
	        $sql .= " INT(14)";
        } else if ($type === DB_DEF_DATETIME) {
   	        $sql .= " DATETIME";
	    } else if ($type === DB_DEF_INTEGER) {
	        $sql .= " INT(8)";
	    } else if ($type === DB_DEF_STRING) {
	        if (isset($def[DB_FIELD_LEN]) && is_int($def[DB_FIELD_LEN])) {
	            $len = $def[DB_FIELD_LEN];
	            if ($len < 256)
	                $sql .= " VARCHAR($len)";
	            else if ($len < 65536)
	                $sql .= " TEXT";
	            else if ($len < 16777216)
	                $sql .= " MEDIUMTEXT";
	            else
	                $sql .= " LONGTEXT";
	        } else {  // default size of 'TEXT'instead of error
                $sql .= " TEXT";
	        }
	    } else if ($type === DB_DEF_DECIMAL) {
	        $sql .= " DOUBLE";
	    } else {
	        return false; // must specify known FIELD_TYPE
	    }
	    if ($type === DB_DEF_ID || $type === DB_DEF_BOOLEAN || !empty($def[DB_NOTNULL]) || !empty($def[DB_PRIMARY])) {
            $sql .= " NOT NULL";
        } else {
            $sql .= " NULL";
        }
	    if (isset($def[DB_DEFAULT]))
	        $sql .= " DEFAULT '" . $def[DB_DEFAULT] . "'";
        else if ($type == DB_DEF_BOOLEAN || ($type === DB_DEF_ID && empty($def[DB_PRIMARY])))
            $sql .= " DEFAULT 0";
	    if (isset($def[DB_INCREMENT]) && $def[DB_INCREMENT])
	        $sql .= " AUTO_INCREMENT";
	    return $sql;
	}

    /**
   	* Return the tablename for the database
   	*
   	* Returns a full table name for the database.
   	*
   	* @param string $tablename The name of the table
   	* @return string
   	*/
    abstract function tableStmt($tablename);

    /**
     * Return the limit statement for the database
     *
     * Returns a correct limit statement for the database.
     *
     * @param int $count The number of records to return
     * @param int $offset The offset to the first record to return
     * @return string
     */
    abstract function limitStmt($count, $offset=0);

    /**
     * Return the unixtime to date statement for the database
     *
     * Returns a correct unixtime to date statement for the database.
     *
     * @param string $column_name The name of the data column to convert
     * @return string
     */
    abstract function datetimeStmt($column_name);

    /**
     * Return the number to currency statement for the database
     *
     * Returns a correct number to currency statement for the database.
     *
     * @param string $column_name The name of the data column to convert
     * @return string
     */
    abstract function currencyStmt($column_name);

    /**
     * Return a sql statement with keywords wrapped for the database
     *
     * Returns a keyword wrapped sql statement for the database.
     *
     * @param string $sql The sql statement to check for keyword wrap
     * @return string
     */
    abstract function wrapStmt($sql);

	/**
	* Switch field values between two entries in a  Table
	*
	* Switches values between two table entries for things like swapping rank, etc...
	* @param  $table
	* @param  $field
	* @param  $a
	* @param  $b
	* @param null $additional_where
	* @return bool
	*/
	function switchValues($table, $field, $a, $b, $additional_where = null) {
	    if ($additional_where == null) {
	        $additional_where = '1';
	    }
        $a = (int)($a);
        $b = (int)($b);
	    $object_a = $this->selectObject($table, "$field='$a' AND $additional_where");
	    $object_b = $this->selectObject($table, "$field='$b' AND $additional_where");

	    if ($object_a && $object_b) {
	        $tmp = $object_a->$field;
	        $object_a->$field = $object_b->$field;
	        $object_b->$field = $tmp;

	        $this->updateObject($object_a, $table);
	        $this->updateObject($object_b, $table);

	        return true;
	    } else {
	        return false;
	    }
	}

	/**
	* Checks to see if the connection for this database object is valid.
	* @return bool True if the connection can be used to execute SQL queries.
	*/
	function isValid() {
	    return ($this->connection != null && $this->havedb);
	}

	/**
	* Test the privileges of the user account for the connection.
	* Tests run include:
	* <ul>
	* <li>CREATE TABLE</li>
	* <li>INSERT</li>
	* <li>SELECT</li>
	* <li>UPDATE</li>
	* <li>DELETE</li>
	* <li>ALTER TABLE</li>
	* <li>DROP TABLE</li>
	* </ul>
	* These tests must be performed in order, for logical reasons.  Execution
	* terminates when the first test fails, and the status flag array is returned then.
	* Returns an array of status flags.  Key is the test name.  Value is a boolean,
	* true if the test succeeded, and false if it failed.
	* @return array
	*/
	function testPrivileges() {
	    $status = array();

	    $tablename = "___testertable" . uniqid("");
	    $dd = array(
	        "id" => array(
	            DB_FIELD_TYPE => DB_DEF_ID,
	            DB_PRIMARY => true,
	            DB_INCREMENT => true),
	        "name" => array(
	            DB_FIELD_TYPE => DB_DEF_STRING,
	            DB_FIELD_LEN => 100)
	    );

	    $this->createTable($tablename, $dd, array());
	    if (!$this->tableExists($tablename)) {
	        $status["CREATE TABLE"] = false;
	        return $status;
	    } else
	        $status["CREATE TABLE"] = true;

	    $o = new stdClass();
	    $o->name = "Testing Name";
	    $insert_id = $this->insertObject($o, $tablename);
	    if ($insert_id == 0) {
	        $status["INSERT"] = false;
	        return $status;
	    } else
	        $status["INSERT"] = true;

	    $o = $this->selectObject($tablename, "id=" . $insert_id);
	    if ($o == null || $o->name !== "Testing Name") {
	        $status["SELECT"] = false;
	        return $status;
	    } else
	        $status["SELECT"] = true;

	    $o->name = "Testing 2";
	    if (!$this->updateObject($o, $tablename)) {
	        $status["UPDATE"] = false;
	        return $status;
	    } else
	        $status["UPDATE"] = true;

	    $this->delete($tablename, "id=" . $insert_id);
	    $o = $this->selectObject($tablename, "id=" . $insert_id);
	    if ($o != null) {
	        $status["DELETE"] = false;
	        return $status;
	    } else
	        $status["DELETE"] = true;

	    $dd["thirdcol"] = array(
	        DB_FIELD_TYPE => DB_DEF_TIMESTAMP);

	    $this->alterTable($tablename, $dd, array());
	    $o = new stdClass();
	    $o->name = "Alter Test";
	    $o->thirdcol = "Third Column";
	    if (!$this->insertObject($o, $tablename)) {
	        $status["ALTER TABLE"] = false;
	        return $status;
	    } else
	        $status["ALTER TABLE"] = true;

	    $this->dropTable($tablename);
	    if ($this->tableExists($tablename)) {
	        $status["DROP TABLE"] = false;
	        return $status;
	    } else
	        $status["DROP TABLE"] = true;

	    foreach ($this->getTables() as $t) {
	        if (substr($t, 0, 14 + strlen($this->prefix)) == $this->prefix . "___testertable")
	            $this->dropTable($t);
	    }

	    return $status;
	}

	/**
	* Alter an existing table
	*
	* Alters the structure of an existing database table to conform to the passed
	* data definition.
	*
	* This function abides by the Exponent Data Definition Language, and interprets
	* its general structure for databases.
	*
	* @param string $tablename The name of the table to alter
	* @param array $newdatadef The new data definition for the table.
	*   This is expressed in the Exponent Data Definition Language
	* @param array $info Information about the table itself.
	* @param bool $aggressive Whether or not to aggressively update the table definition.
	*   An aggressive update will drop columns in the table that are not in the Exponent definition.
	* @return array
	*/
	abstract function alterTable($tablename, $newdatadef, $info, $aggressive = false);

	/**
	* Drop a table from the database
	*
	* Removes an existing table from the database. Returns true if the table was dropped, false if there
	* was an error returned by the database server.
	*
	* @param string $table The name of the table to drop.
	* @return bool
	*/
	abstract function dropTable($table);

	/**
	 * Run raw SQL.  Returns true if the query succeeded, and false
	 *   if an error was returned from the database server.
	 *
	 * <div style="color:red">If you can help it, do not use this function.  It presents Database Portability Issues.</div>
	 *
	 * Runs a straight SQL query on the database.  This is not a
	 * very portable way of dealing with the database, and is only
	 * provided as a last resort.
	 *
	 * @param string $sql The SQL query to run
	 * @param bool $escape
	 * @return mixed
	 */
	abstract function sql($sql, $escape = true);

	/**
	 * Toggle a boolean value in a Table Entry
	 *
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @return void
	 */
	function toggle($table, $col, $where=null) {
	    $obj = $this->selectObject($table, $where);
	    $obj->$col = ($obj->$col == 0) ? 1 : 0;
	    $this->updateObject($obj, $table);
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
	abstract function columnUpdate($table, $col, $val, $where=1);

	/**
	 * @param  $object
	 * @param  $table
	 * @param  $col
	 * @param int|null $where
	 * @return bool
	 */
	function setUniqueFlag($object, $table, $col, $where=1) {
	    if (isset($object->id)) {
	        $this->sql("UPDATE " . $this->tableStmt($table) . " SET " . $col . "=0 WHERE " . $where);
	        $this->sql("UPDATE " . $this->tableStmt($table) . " SET " . $col . "=1 WHERE id=" . $object->id);
	        return true;
	    }
	    return false;
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
	abstract function selectObjects($table, $where = null, $orderby = null, $is_revisioned=false, $needs_approval=false, $user=null);

	/**
	 * @param  $terms
	 * @param null $where
     * @return array
	 */
    abstract function selectSearch($terms, $where = null);

    /**
     * @param null $colsA
     * @param null $colsB
     * @param  $tableA
     * @param  $tableB
     * @param  $keyA
     * @param null $keyB
     * @param null $where
     * @param null $orderby
     */
	function selectAndJoinObjects($colsA=null, $colsB=null, $tableA=null, $tableB=null, $keyA=null, $keyB=null, $where = null, $orderby = null) {  //FIXME never used

	}

	/**
	 * Select a single object by sql
	 *
	 * @param  $sql
	 * @return null|void
	 */
	abstract function selectObjectBySql($sql);

	/**
	 * Select a series of objects by sql
	 *
	 * @param  $sql
	 * @return array
	 */
	abstract function selectObjectsBySql($sql);

	/**
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @param null $orderby
	 * @param bool $distinct
	 * @return array
	 */
	abstract function selectColumn($table, $col, $where = null, $orderby = null, $distinct=false);

    /**
     * @param  $table
     * @param  $col
     * @param null $where
     */
	function selectSum($table, $col, $where = null) {  //FIXME never used

	}

	/**
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @param null $orderby
	 * @return array
	 */
	abstract function selectDropdown($table, $col, $where = null, $orderby = null);

	/**
	 * @param  $table
	 * @param  $col
	 * @param null $where
	 * @return null
	 */
	abstract function selectValue($table, $col, $where=null);

	/**
	 * @param  $sql
	 * @return null
	 */
	function selectValueBySql($sql) {  //FIXME never used

	}

	/**
	* This function takes an array of indexes and returns an array with the objects associated with each id
	* @param  $table
	* @param array $array
	* @param null $orderby
	* @return array
	*/
	function selectObjectsInArray($table, $array=array(), $orderby=null) {
	    $where = 'id IN ' . implode(",", $array);
	    $res = $this->selectObjects($table, $where, $orderby);
	    return $res;
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
	abstract function selectObjectsIndexedArray($table, $where = null, $orderby = null);

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
	abstract function countObjects($table, $where = null, $is_revisioned=false, $needs_approval=false, $user=null);

	/**
	* Count Objects matching a given criteria using raw sql
	*
	* @param string $sql The sql query to be run
	* @return int
	*/
	abstract function countObjectsBySql($sql);

	/**
	* Count Objects matching a given criteria using raw sql
	*
	* @param string $sql The sql query to be run
	* @return int|void
	*/
	function queryRows($sql) { //FIXME never used

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
	* @return object|null|void
	*/
	abstract function selectObject($table, $where);

	/**
	 * @param $table
	 * @param string $lockType
	 * @return mixed
	 */
	abstract function lockTable($table,$lockType="WRITE");

	/**
	 * @return mixed
	 */
	abstract function unlockTables();

	/**
	* Insert an Object into some table in the Database
	*
	* This method will return the ID assigned to the new record by database.  Note that
	* object attributes starting with an underscore ('_') will be ignored and NOT inserted
	* into the table as a field value.
	*
	* @param object $object The object to insert.
	* @param string $table The logical table name to insert into.  This does not include the table prefix, which
	*    is automagically prepended for you.
	* @return int|void
	*/
	abstract function insertObject($object, $table);

	/**
	* Delete one or more objects from the given table.
	*
	* @param string $table The name of the table to delete from.
	* @param string $where Criteria for determining which record(s) to delete.
	* @return mixed
	*/
	abstract function delete($table, $where = null);

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
	abstract function updateObject($object, $table, $where=null, $identifier='id', $is_revisioned=false);

    /**
     * Reduces table item revisions to a passed total
     *
     * @param string $table The name of the table to trim
     * @param integer $id The item id
     * @param integer $num The number of revisions to retain
     * @param int|string $workflow is workflow turned on (or force)
     */
    public function trim_revisions($table, $id, $num, $workflow=ENABLE_WORKFLOW) {
        if ($workflow && $num) {
            $max_revision = $this->max($table, 'revision_id', null, 'id='.$id);
            $max_approved = $this->max($table, 'revision_id', null, 'id='.$id.' AND approved=1');
            $min_revision = $this->min($table, 'revision_id', null, 'id='.$id);
            if ($max_revision == null) {
                return;
            }
            if (($max_revision - $num) > $max_approved) {
                $approved_max = ' AND revision_id < ' . $max_approved;  // never delete most recent approved item
            } else {
                $approved_max = '';
            }
            if ($max_revision - $min_revision >= $num) {
                $this->delete($table, 'id=' . $id . ' AND revision_id <= ' . ($max_revision - $num) . $approved_max);
            }
            if (!empty($approved_max)) {
                // we've trimmed all the fat below the newest approved item, now trim the dead wood above it
                $this->delete($table, 'id=' . $id . ' AND revision_id <= ' . ($max_revision - $num + 1) . ' AND revision_id > ' . $max_approved);
            }
        }
    }

	/**
	 * Find the maximum value of a field.  This is similar to a standard
	 * SELECT MAX(field) ... query.
	 *
	 * @param string $table The name of the table to select from.
	 * @param string $attribute The attribute name to find a maximum value for.
	 * @param string $groupfields A comma-separated list of fields (or a single field) name, used
	 *    for a GROUP BY clause.  This can also be passed as an array of fields.
	 * @param string $where Optional criteria for narrowing the result set.
	 * @return mixed
	 */
	abstract function max($table, $attribute, $groupfields = null, $where = null);

	/**
	 * Find the minimum value of a field.  This is similar to a standard
	 * SELECT MIN(field) ... query.
	 *
	 * @internal Internal
	 * @param string $table The name of the table to select from.
	 * @param string $attribute The attribute name to find a minimum value for.
	 * @param string $groupfields A comma-separated list of fields (or a single field) name, used
	 *    for a GROUP BY clause.  This can also be passed as an array of fields.
	 * @param string $where Optional criteria for narrowing the result set.
	 * @return null
	 */
	abstract function min($table, $attribute, $groupfields = null, $where = null);

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
	abstract function increment($table, $field, $step, $where = null);

	/**
	* Decrement a numeric table field in a table.
	*
	* @param string $table The name of the table to decrement in.
	* @param string $field The field to decrement.
	* @param integer $step The step value.  Usually 1.  This can be negative, to
	*    increment, but the increment() method is preferred, for readability.
	* @param string $where Optional criteria to determine which records to update.
	*/

	function decrement($table, $field, $step, $where = null) {
	    $this->increment($table, $field, -1 * $step, $where);
	}

	/**
	* Check to see if the named table exists in the database.
	* Returns true if the table exists, and false if it doesn't.
	*
	* @param string $table Name of the table to look for.
	* @return bool
	*/
	abstract function tableExists($table);

    /**
   	* Check to see if the named column within a table exists in the database.
   	* Returns true if the column exists, and false if it doesn't.
   	*
   	* @param string $table Name of the table to look in.
    * @param string $col Name of the column to look for.
   	* @return bool
   	*/
   	abstract function columnExists($table, $col);

	/**
	* Get a list of all tables in the database.  Optionally, only the tables
	* in the current logical database (tables with the same prefix) can
	* be retrieved.
	*
	* @param bool $prefixed_only Whether to return only the tables
	*    for the logical database, or all tables in the physical database.
	* @return array
	*/
	abstract function getTables($prefixed_only=true);

	/**
	* Runs whatever table optimization routines the database engine supports.
	*
	* @param string $table The name of the table to optimize.
	* @return bool
	*/
	abstract function optimize($table);

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
	function tableInfo($table) {  //FIXME never used

	}

	/**
	* Check whether or not a table in the database is empty (0 rows).
	* Returns tue of the specified table has no rows, and false if otherwise.
	*
	* @param string $table Name of the table to check.
	* @return bool
	*/
	function tableIsEmpty($table) {
	    return ($this->countObjects($table) == 0);
	}

	/**
	* Returns table information for all tables in the database.
	* This function effectively calls tableInfo() on each table found.
	* @return array
	*/
	abstract function databaseInfo();

	/**
	* This is an internal function for use only within the database database class
	* @internal Internal
	* @param  $status
	* @return null
	*/
	function translateTableStatus($status) {
	    $data = new stdClass();
	    $data->rows = $status->Rows;
	    $data->average_row_lenth = $status->Avg_row_length;
	    $data->data_overhead = $status->Data_free;
	    $data->data_total = $status->Data_length;

	    return $data;
	}

    /**
     * @param  $table
     */
	function describeTable($table) { //FIXME never used

	}

	/**
	* Build a data definition from a pre-existing table.  This is used
	* to intelligently alter tables that have already been installed.
	*
	* @param string $table The name of the table to get a data definition for.
	* @return array|null
	*/
	abstract function getDataDefinition($table);

	/**
	* This is an internal function for use only within the database class
	* @internal Internal
	* @param  $fieldObj
	* @return int
	*/
	function getDDFieldType($fieldObj) {
	    $type = strtolower($fieldObj->Type);

	    if ($type === "int(11)")
	        return DB_DEF_ID;
	    if ($type === "int(8)")
	        return DB_DEF_INTEGER;
	    elseif ($type === "tinyint(1)")
	        return DB_DEF_BOOLEAN;
	    elseif ($type === "int(14)")
	        return DB_DEF_TIMESTAMP;
        elseif ($type === "datetime")
  	        return DB_DEF_TIMESTAMP;
	    //else if (substr($type,5) == "double")
            //return DB_DEF_DECIMAL;
	    elseif ($type === "double")
	        return DB_DEF_DECIMAL;
	    // Strings
	    elseif ($type === "text" || $type === "mediumtext" || $type === "longtext" || strpos($type, "varchar(") !== false) {
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
	    $type = strtolower($fieldObj->Type);
	    if ($type === "text")
	        return 65535;
	    else if ($type === "mediumtext")
	        return 16777215;
	    else if ($type === "longtext")
	        return 16777216;
	    else if (strpos($type, "varchar(") !== false) {
	        return str_replace(array("varchar(", ")"), "", $type) + 0;
	    } else {
            return 256;
        }
	}

	/**
	* This is an internal function for use only within the database class
	* @internal Internal
	* @param  $fieldObj
	* @return int|mixed
	*/
	function getDDKey($fieldObj) {
	    $key = strtolower($fieldObj->Key);
	    if ($key === "pri")
	        return DB_PRIMARY;
	    else if ($key === "uni") {
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
	    $auto = strtolower($fieldObj->Extra);
	    if ($auto === "auto_increment") {
	        return true;
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
	function getDDIsNull($fieldObj) {
	    $null = strtolower($fieldObj->Null);
	    if ($null === "yes") {
	        return true;
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
	function getDDDefault($fieldObj) {
		return strtolower($fieldObj->Default);
	}

	/**
	* Returns an error message from the database server.  This is intended to be
	* used by the implementers of the database wrapper, so that certain
	* cryptic error messages can be reworded.
	* @return string
	*/
	abstract function error();

	/**
	* Checks whether the database connection has experienced an error.
	* @return bool
	*/
	abstract function inError();

	/**
	 * Escape a string based on the database connection
	 * @param $string
	 * @return string
	 */
	abstract function escapeString($string);

    /**
   	 * Attempt to prevent a sql injection
   	 * @param $string
   	 * @return string
   	 */
   	function injectProof($string) {
   	    $quotes = substr_count("'", $string);
        if ($quotes % 2 != 0)
            $string = $this->escapeString($string);
        $dquotes = substr_count('"', $string);
        if ($dquotes % 2 != 0)
            $string = $this->escapeString($string);
        return $string;
    }

	/**
	 * Create a SQL "limit" phrase
	 *
	 * @param  $num
	 * @param  $offset
	 * @return string
	 */
	function limit($num, $offset) {
	    return ' LIMIT ' . $offset . ',' . $num . ' ';
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
	abstract function selectArrays($table, $where = null, $orderby = null, $is_revisioned=false, $needs_approval=false, $user=null);

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
	abstract function selectArraysBySql($sql);

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
	abstract function selectArray($table, $where = null, $orderby = null, $is_revisioned=false, $needs_approval=false, $user=null);

    /**
     * Instantiate objects from selected records from the database
     *
     * @param string $table The name of the table/object to look at
     * @param string $where Criteria used to narrow the result set.  If this
     *                      is specified as null, then no criteria is applied, and all objects are
     *                      returned
     * @param        $classname
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
	abstract function selectExpObjects($table=null, $where=null, $classname=null, $get_assoc=true, $get_attached=true, $except=array(), $cascade_except=false, $order=null, $limitsql=null, $is_revisioned=false, $needs_approval=false, $user=null);

    /**
     * Instantiate objects from selected records from the database
     *
     * @param string $sql The sql statement to run on the model/classname
     * @param string $classname Can be $this->baseclassname
     * Returns an array of fields
     * @param bool $get_assoc
     * @param bool $get_attached
     */
	function selectExpObjectsBySql($sql, $classname, $get_assoc=true, $get_attached=true) {  //FIXME never used

	}

	/**
	 * @param  $table
	 * @return array
	 */
	function selectNestedTree($table) {
	    $sql = 'SELECT node.*, (COUNT(parent.sef_url) - 1) AS depth
            FROM ' . $this->tableStmt($table) . ' AS node,
            ' . $this->tableStmt($table) . ' AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            GROUP BY node.sef_url, node.id, node.title, node.body, node.is_active, node.is_events, node.hide_closed_events, node.canonical,
            node.meta_title, node.meta_keywords, node.meta_description, node.noindex, node.nofollow, node.items_per_page, node.expFiles_id, node.
            rgt, node.lft, node.parent_id, node.poster, node.created_at, node.editor, node.edited_at, node.location_data
            ORDER BY node.lft';
	    return $this->selectObjectsBySql($sql);
	}

	function selectFormattedNestedTree($table) {
		$sql = "SELECT CONCAT( REPEAT( '&#160;&#160;&#160;', (COUNT(parent.title) -1) ), node.title) AS title, node.id
				FROM " . $this->tableStmt($table) . " as node, " . $this->tableStmt($table) . " as parent
				WHERE node.lft BETWEEN parent.lft and parent.rgt
				GROUP BY node.title, node.id
				ORDER BY node.lft";

		return $this->selectObjectsBySql($sql);
	}

	/**
	 * @param  $table
	 * @param  $start
	 * @param  $width
	 * @return void
	 */
	function adjustNestedTreeFrom($table, $start, $width) {
	    $table = $this->tableStmt($table);
	    $this->sql('UPDATE ' . $table . ' SET rgt = rgt + ' . $width . ' WHERE rgt >=' . $start);
	    $this->sql('UPDATE ' . $table . ' SET lft = lft + ' . $width . ' WHERE lft >=' . $start);
	    //eDebug('UPDATE \''.$table.'\' SET rgt = rgt + '.$width.' WHERE rgt >='.$start);
	    //eDebug('UPDATE \''.$table.'\' SET lft = lft + '.$width.' WHERE lft >='.$start);
	}

	/**
	 * @param  $table
	 * @param  $lft
	 * @param  $rgt
	 * @param  $width
	 * @return void
	 */
	function adjustNestedTreeBetween($table, $lft, $rgt, $width) {
	    $table = $this->tableStmt($table);
	    $this->sql('UPDATE ' . $table . ' SET rgt = rgt + ' . $width . ' WHERE rgt BETWEEN ' . $lft . ' AND ' . $rgt);
	    $this->sql('UPDATE ' . $table . ' SET lft = lft + ' . $width . ' WHERE lft BETWEEN ' . $lft . ' AND ' . $rgt);
	    //eDebug('UPDATE \''.$table.'\' SET rgt = rgt + '.$width.' WHERE rgt BETWEEN '.$lft.' AND '.$rgt);
	    //eDebug('UPDATE \''.$table.'\' SET lft = lft + '.$width.' WHERE lft BETWEEN '.$lft.' AND '.$rgt);
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
                  ( SELECT node.*, (COUNT(parent.title) - 1) AS depth
                      FROM ' . $table . ' AS node,
                      ' . $table . ' AS parent
                      WHERE node.lft BETWEEN parent.lft
                      AND parent.rgt AND node.' . $where . '
                      GROUP BY node.title
                      ORDER BY node.lft )
   	           AS sub_tree
   	           WHERE node.lft BETWEEN parent.lft AND parent.rgt
   	           AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
   	           AND sub_parent.title = sub_tree.title
   	           GROUP BY node.title
   	           ORDER BY node.lft;';

   	    return $this->selectObjectsBySql($sql);
   	}

	/**
	 * @param  $table
	 * @param  $lft
	 * @param  $rgt
	 * @return void
	 */
	function deleteNestedNode($table, $lft, $rgt) {
	    $table = $this->tableStmt($table);

	    $width = ($rgt - $lft) + 1;
	    $this->sql('DELETE FROM ' . $table . ' WHERE lft BETWEEN ' . $lft . ' AND ' . $rgt);
	    $this->sql('UPDATE ' . $table . ' SET rgt = rgt - ' . $width . ' WHERE rgt > ' . $rgt);
	    $this->sql('UPDATE ' . $table . ' SET lft = lft - ' . $width . ' WHERE lft > ' . $rgt);
	}

	/**
	 * @param  $table
	 * @param null $node
	 * @return array
	 */
	function selectPathToNestedNode($table, $node=null) {
	    if (empty($node))
	        return array();

	    $where = is_numeric($node) ? 'id=' . $node : 'title="' . $node . '"';
	    $sql = 'SELECT parent.*
            FROM ' . $this->tableStmt($table) . ' AS node,
            ' . $this->tableStmt($table) . ' AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            AND node.' . $where . '
            ORDER BY parent.lft;';
	    return $this->selectObjectsBySql($sql);
	}

	/**
	 * @param  $table
	 * @param null $node
	 * @return array
	 */
	function selectNestedNodeParent($table, $node=null) {
	    global $db;

	    if (empty($node))
	        return array();

	    $where = is_numeric($node) ? 'id=' . $node : 'title="' . $node . '"';
	    $sql = 'SELECT parent.*
            FROM ' . $this->tableStmt($table) . ' AS node,
            ' . $this->tableStmt($table) . ' AS parent
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
            AND node.' . $where . '
            ORDER BY parent.lft DESC
            ' . $db->limitStmt(1, 1) . ';';
	    $parent_array = $this->selectObjectsBySql($sql);
	    return $parent_array[0];
	}

	/**
	 * @param  $table
	 * @param null $node
	 * @return array
	 */
	function selectNestedNodeChildren($table, $node=null) {
	    if (empty($node))
	        return array();

        $table = $this->tableStmt($table);
	    $where = is_numeric($node) ? 'node.id=' . $node : 'node.title="' . $node . '"';
	    $sql = '
            SELECT node.*, (COUNT(parent.title) - (sub_tree.depth + 1)) AS depth
            FROM ' . $table . ' AS node,
                ' . $table . ' AS parent,
                ' . $table . ' AS sub_parent,
                (
                    SELECT node.*, (COUNT(parent.title) - 1) AS depth
                    FROM ' . $table . ' AS node,
                    ' . $table . ' AS parent
                    WHERE node.lft BETWEEN parent.lft AND parent.rgt
                    AND ' . $where . '
                    GROUP BY node.title, node.id, node.body, node.sef_url, node.is_active, node.is_events, node.hide_closed_events, node.canonical,
                    node.meta_title, node.meta_keywords, node.meta_description, node.noindex, node.nofollow, node.items_per_page, node.expFiles_id, node.
                    rgt, node.lft, node.parent_id, node.poster, node.created_at, node.editor, node.edited_at, node.location_data
                    ORDER BY node.lft
                )AS sub_tree
            WHERE node.lft BETWEEN parent.lft AND parent.rgt
                AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
                AND sub_parent.title = sub_tree.title
            GROUP BY node.title, node.id, node.body, node.sef_url, node.is_active, node.is_events, node.hide_closed_events, node.canonical,
            node.meta_title, node.meta_keywords, node.meta_description, node.noindex, node.nofollow, node.items_per_page, node.expFiles_id, node.
            rgt, node.lft, node.parent_id, node.poster, node.created_at, node.editor, node.edited_at, node.location_data
            HAVING depth = 1
            ORDER BY node.lft;';
	    $children = $this->selectObjectsBySql($sql);
	    return $children;
	}

	/**
	 * This function returns all the text columns in the given table
	 * @param $table
	 * @return array
	 */
	abstract function getTextColumns($table);

}

?>