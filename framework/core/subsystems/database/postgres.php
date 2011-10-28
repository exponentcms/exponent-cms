<?php
/**
 *  This file is part of Exponent
 *  Exponent is free software; you can redistribute
 *  it and/or modify it under the terms of the GNU
 *  General Public License as published by the Free
 *  Software Foundation; either version 2 of the
 *  License, or (at your option) any later version.
 *
 * The file that holds the postgres_database class
 *
 * @link http://www.gnu.org/licenses/gpl.txt GPL http://www.gnu.org/licenses/gpl.txt
 * @package Exponent-CMS
 */
/** @define "BASE" "../.." */

/**
 * This is the class postgres_database
 *
 * This is the Postgres-specific implementation of the database class.
 * @copyright 2004-2011 OIC Group, Inc.
 * @author Written and Designed by James Hunt
 * @version 2.0.0
 * @subpackage Database = Postgres
 * @package Subsystems
 */

class postgres_database extends database {
	var $error = "";
	var $in_error = false;

	/**
	 * @internal
	 * @param $res
	 */
	function checkError($res) {
		if ($res === false) {
			$in_error = true;
			$error = pg_last_error();
		} else if (pg_result_status($res) == PGSQL_FATAL_ERROR) {
			$in_error = true;
			$error = pg_result_error($res);
		} else {
			$error = "";
			$in_error = false;
		}
	}
	
	function isValid() {
		return ($this->connection != null);
	}

//	function connect($username,$password,$hostname,$database,$new = false) {
	function __construct($username,$password,$hostname,$database,$new = false) {
  		if( function_exists( 'pg_connect')){
			$host_data = explode(":",$hostname);
			$hostname = $host_data[0];
			$port = $hostname[1];
      
      if ($password != "" ) {
        if ($hostname == "localhost") $dsn = "user=$username password=$password dbname=$database";
			  else $dsn = "host=$hostname user=$username password=$password dbname=$database";
      } else {
        if ($hostname == "localhost") $dsn = "user=$username dbname=$database";
			  else $dsn = "host=$hostname user=$username dbname=$database";
      }
			
			if ($new) {
				$this->connection = pg_connect($dsn,PGSQL_CONNECT_FORCE_NEW);
			} else {
				$this->connection = pg_connect($dsn);
			}
	
			@trigger_error( $this->connection );
			
			$this->prefix = DB_TABLE_PREFIX . '_';
		}
	}
	
	function createTable($tablename,$datadef,$info) {
		$sql = "CREATE TABLE \"".$this->prefix."$tablename\" (";
		$alter_sql = array();
		
		foreach ($datadef as $name=>$def) {
			$sql .= $this->fieldSQL($name,$def) . ",";
			// PostGres is stupid, you cant specify NOT NULL in the Create Table
			if (!isset($def[DB_INCREMENT]) || !$def[DB_INCREMENT]) {
				$alter_sql[] = 'ALTER TABLE "'.$this->prefix.$tablename . '" ALTER COLUMN "'.$name.'" SET NOT NULL';
				$default = null;
				if (isset($def[DB_DEFAULT])) $default = $def[DB_DEFAULT];
				if (isset($def[DB_PRIMARY]) && $def[DB_PRIMARY] == true) $primary[] = $name;
				if (isset($def[DB_INDEX]) && ($def[DB_INDEX] > 0)) {
					if ($def[DB_FIELD_TYPE] == DB_DEF_STRING) {
						$index[$name] = $def[DB_INDEX];
					}
					else {
						$index[$name] = 0;
					}
				}
				if (isset($def[DB_UNIQUE])) {
					if (!isset($unique[$def[DB_UNIQUE]])) $unique[$def[DB_UNIQUE]] = array();
					$unique[$def[DB_UNIQUE]][] = $name;
				}
				else {
					switch ($def[DB_FIELD_TYPE]) {
						case DB_DEF_ID:
						case DB_DEF_INTEGER:
						case DB_DEF_TIMESTAMP:
						case DB_DEF_DECIMAL:
						case DB_DEF_BOOLEAN:
							$default = 0;
							break;
						default:
							$default = '';
							break;
					}
				}
				$alter_sql[] = 'ALTER TABLE "'.$this->prefix.$tablename . '" ALTER COLUMN "'.$name.'" SET DEFAULT '."'".$default."'";				
			}
		}
		$sql = substr($sql,0,-1);
		if (count($primary)) {
			$sql .= ", PRIMARY KEY(" . implode(",",$primary) . ")";
		}
		foreach ($unique as $key=>$value) {
			$sql .= ", UNIQUE ".$key." ( " . implode(",",$value) . ")";
		}
		$sql .= ")";
		pg_query($this->connection,$sql);
		foreach ($alter_sql as $sql) {
			#echo '//'.$sql.'<br />';
			pg_query($this->connection,$sql);
		}
		foreach ($index as $key=>$value) {
			$indexes_sql = "CREATE INDEX ".$key."_idx ON ".$this->prefix."$tablename (" .$key. ");";
			eLog($indexes_sql);
			pg_query($indexes_sql);
		}
		
	}
	
	function fieldSQL($name,$def) {
		$sql = '"'.$name.'"';
		if (!isset($def[DB_FIELD_TYPE])) {
			return false;
		}
		$type = $def[DB_FIELD_TYPE];
		if (isset($def[DB_INCREMENT]) && $def[DB_INCREMENT]) {
			$sql .= " serial";
		} else if ($type == DB_DEF_ID) {
			$sql .= " int8";
		} else if ($type == DB_DEF_INTEGER) {
			$sql .= " numeric";
		} else if ($type == DB_DEF_TIMESTAMP) {
			$sql .= " int4";
		} else if ($type == DB_DEF_BOOLEAN) {
			$sql .= " int4";
		} else if ($type == DB_DEF_STRING) {
			$sql .= " text";
		} else if ($type == DB_DEF_DECIMAL) {
			$sql .= " float4";
		} else {
			return false; // must specify known FIELD_TYPE
		}
		
		//if (isset($def[DB_PRIMARY]) && $def[DB_PRIMARY]) $sql .= " PRIMARY KEY";
		
		if (isset($def[DB_DEFAULT])) $sql .= " DEFAULT '" . $def[DB_DEFAULT] . "'";
		
		return $sql;
	}
	
	function alterTable($tablename,$newdatadef,$info,$aggressive = false) {
		$dd = $this->getDataDefinition($tablename);
		$modified = false;
		
		if ($aggressive) {
			$oldcols = array_diff_assoc($dd, $newdatadef);
			if (count($oldcols)) {
				$modified = true;
				foreach ($oldcols as $name=>$def) {
					$sql = "ALTER TABLE " . $this->prefix . $tablename . ' DROP COLUMN "' . $name .'"';
					pg_query($this->connection,$sql);
				}
			}
		}
		
		$diff = array_diff_assoc($newdatadef,$dd);
		if (count($diff)) {
			$modified = true;
			foreach ($diff as $name=>$def) {
				$sql = 'ALTER TABLE "' . $this->prefix . $tablename . '" ADD COLUMN ' . $this->fieldSQL($name,$def);
				#echo $sql .'<br />';
				pg_query($this->connection,$sql);
			}
		}
		
		if ($modified) {
			return TABLE_ALTER_SUCCEEDED;
		} else {
			return TABLE_ALTER_NOT_NEEDED;
		}
	}
	
	function dropTable($table) {
		$sql = "DROP TABLE ".$this->prefix.$table;
		pg_query($this->connection,$sql);
	}
	
	function sql($sql, $escape = true) {
		$res = pg_query($sql);
		return $res;
	}
	
	function selectObjects($table,$where = null) {
		$sql = "SELECT * FROM " . $this->prefix.$table;
		if ($where != null) $sql .= " WHERE $where";
		$res = @pg_query($sql);
		$this->checkError($res);
		
		$records = array();
		for ($i = 0; $i < @pg_num_rows($res); $i++) {
			$records[] = pg_fetch_object($res);
		}
		@pg_free_result($res);
		return $records;
	}
	
	function selectObjectsIndexedArray($table,$where = null) {
		$sql = "SELECT * FROM " . $this->prefix.$table;
		if ($where != null) $sql .= " WHERE $where";
		$res = pg_query($sql);
		$this->checkError($res);
		
		$records = array();
		for ($i = 0; $i < pg_num_rows($res); $i++) {
			$o = pg_fetch_object($res);
			$records[$o->id] = $o;
		}
		pg_free_result($res);
		return $records;
	}
	
	function countObjects($table,$where = null) {
		$sql = "SELECT COUNT(*) as num FROM " . $this->prefix . $table;
		if ($where != null) $sql .= " WHERE $where";
		$res = pg_query($this->connection,$sql);
		$this->checkError($res);
		if ($res !== FALSE) {
			$num = pg_fetch_object($res);
			pg_free_result($res);
			return $num->num;
		} else return 0;
	}
	
	function selectObject($table,$where) {
		$sql = "SELECT * FROM " . $this->prefix . $table . " WHERE $where";
		#echo $sql.'<br />';
		$res = pg_query($this->connection,$sql);
		$this->checkError($res);
		if ($res == null) return null;
		return pg_fetch_object($res);
	}
	
	function insertObject($object,$table) {
		$sql = "INSERT INTO " . $this->prefix.$table . " (";
		$values = ") VALUES (";
		foreach (get_object_vars($object) as $var=>$val) {
			$sql .= "$var,";
			$values .= "'".str_replace("'","\\'",$val)."',";
		}
		if (pg_query($this->connection,substr($sql,0,-1).substr($values,0,-1) . ")") !== false) {
			$sql = "SELECT last_value FROM " . $this->prefix.$table ."_id_seq";
			$res = @pg_query($this->connection,$sql);
			if ($res) {
				$o = pg_fetch_object($res);
				pg_free_result($res);
				return $o->last_value;
			} else return 0;
		} else return 0;
	}
	
	function delete($table,$where = null) {
		$sql = "DELETE FROM " . $this->prefix . $table;
		if ($where != null) $sql .= " WHERE " . $where;
		pg_query($this->connection,$sql);
	}
	
	function updateObject($object,$table,$where=null) {
		$sql = "UPDATE " . $this->prefix.$table . " SET ";
		foreach (get_object_vars($object) as $var=>$val) {
			$sql .= "$var='".str_replace("'","\\'",$val)."',";
		}
		$sql = substr($sql,0,-1) . " WHERE ";
		if ($where != null) $sql .= $where;
		else $sql .= "id=" . $object->id;
		
		#echo '//'.$sql.'<br />';
		return (pg_query($this->connection,$sql) != false);
	}
	
	function max($table,$attribute,$groupfields = null,$where = null) {
		if (is_array($groupfields)) $groupfields = implode(",",$groupfields);
		$sql = "SELECT MAX($attribute) as fieldmax FROM " . $this->prefix . "$table";
		if ($where != null) $sql .= " WHERE $where";
		if ($groupfields != null) $sql .= " GROUP BY $groupfields";
		$res = pg_query($this->connection,$sql);
		if ($res == null) return null;
		$o = pg_fetch_object($res);
		pg_free_result($res);
		if (!$o) return null;
		return $o->fieldmax;
	}
	
	function min($table,$attribute,$groupfields = null,$where = null) {
		if (is_array($groupfields)) $groupfields = implode(",",$groupfields);
		$sql = "SELECT MIN($attribute) as fieldmin FROM " . $this->prefix . "$table";
		if ($where != null) $sql .= " WHERE $where";
		if ($groupfields != null) $sql .= " GROUP BY $groupfields";
		$res = pg_query($this->connection,$sql);
		if ($res == null) return null;
		$o = pg_fetch_object($res);
		pg_free_result($res);
		if (!$o) return null;
		return $o->fieldmin;
	}
	
	function switchValues($table,$field,$a,$b,$additional_where = null) {
		if ($additional_where == null) {
			$additional_where = 'true';
		}
		$object_a = $this->selectObject($table,"$field='$a' AND $additional_where");
		$object_b = $this->selectObject($table,"$field='$b' AND $additional_where");
		
		if ($object_a && $object_b) {
			$tmp = $object_a->$field;
			$object_a->$field = $object_b->$field;
			$object_b->$field = $tmp;
			
			$this->updateObject($object_a,$table);
			$this->updateObject($object_b,$table);
			
			return true;
		} else {
			return false;
		}
	}
	
	function tableExists($table) {
		$sql = "SELECT COUNT(relname) as num FROM pg_catalog.pg_class JOIN pg_catalog.pg_namespace ON (relnamespace = pg_namespace.oid) WHERE relkind IN ('r') AND nspname = 'public' AND relname = '".$this->prefix.$table."'";
		$res = pg_query($this->connection,$sql);
		$this->checkError($res);
		if ($res) {
			$o = pg_fetch_object($res);
			pg_free_result($res);
			return ($o->num != 0);
		} else return false;
	}
	
	function getTables($prefixed_only=true) {
		$sql = "SELECT relname as tablename FROM pg_catalog.pg_class JOIN pg_catalog.pg_namespace ON (relnamespace = pg_namespace.oid) WHERE relkind IN ('r') AND nspname = 'public'";
		$res = pg_query($this->connection,$sql);
		$this->checkError($res);
		$tables = array();
		for ($i = 0; $i < pg_num_rows($res); $i++) {
			$o = pg_fetch_object($res);
			if ($prefixed_only && substr($o->tablename,0,strlen($this->prefix)) == $this->prefix) {
				$tables[] = $o->tablename;
			} else if (!$prefixed_only) {
				$tables[] = $o->tablename;
			}
		}
		pg_free_result($res);
		return $tables;
	}
	
	function optimize($table) {
		$sql = 'VACUUM FULL "'. $this->prefix.$table.'"';
		pg_query($this->connection,$sql);
	}
	
	function tableInfo($table) {
	// Logic here
		$sql = "SELECT relpages * 8192 AS data_total FROM pg_class WHERE relnamespace = (SELECT oid FROM pg_namespace WHERE nspname = 'public') AND relname='$table'";
		$res = pg_query($this->connection,$sql);
		if ($res == null) return $this->translateTableStatus(null);
		$sizeobj = pg_fetch_object($res);
		pg_free_result($res);
		$sizeobj->rows = $this->countObjects($table);
		if ($sizeobj->rows) {
			$sizeobj->average_row_length = $sizeobj->data_total / $sizeobj->rows;
		} else {
			$sizeobj->average_row_length = 0;
		}
		$sizeobj->data_overhead = 0;
		return $sizeobj;
	}
	
	function tableIsEmpty($table) {
		return ($this->countObjects($table) == 0);
	}
	
	function databaseInfo() {
		$stat = array();
		$i = strlen($this->prefix);
		foreach ($this->getTables(true) as $table) {
			$stat[substr($table,$i)] = $this->tableInfo($table);
		}
		return $stat;
	}
	
	function getDataDefinition($table) {
		$sql = <<<ENDSQL
SELECT
	a.attnum,
	a.attname AS field,
	t.typname AS type,
	a.attlen AS length,
	a.atttypmod AS lengthvar,
	a.attnotnull AS notnull
FROM
	pg_class c,
	pg_attribute a,
	pg_type t
WHERE
	c.relname = '{$this->prefix}$table'
	and a.attnum > 0
	and a.attrelid = c.oid
	and a.atttypid = t.oid
ORDER BY a.attnum
ENDSQL;
		
		$dd = array();
		
		$res = pg_query($this->connection,$sql);
		$this->checkError($res);
		for ($i = 0; $i < pg_num_rows($res); $i++) {
			$o = pg_fetch_object($res);
			
			$fld = array();
			
			switch ($o->type) {
				case "int8":
					$fld[DB_FIELD_TYPE] = DB_DEF_ID;
					break;
				case "text":
					$fld[DB_FIELD_TYPE] = DB_DEF_STRING;
					$fld[DB_FIELD_LEN] = 100;
					break;
				case "numeric":
					$fld[DB_FIELD_TYPE] = DB_DEF_INTEGER;
					break;
				case "bit":
					$fld[DB_FIELD_TYPE] = DB_DEF_BOOLEAN;
					break;
				case "int4":
					$fld[DB_FIELD_TYPE] = DB_DEF_TIMESTAMP;
					break;
				case "float4":
					$fld[DB_FIELD_TYPE] = DB_DEF_DECIMAL;
					break;
			}
			
			$dd[$o->field] = $fld;
		}
		
		return $dd;
	}
	
	function increment($table,$field,$step,$where = null) {
		if ($where == null) $where = 'true';
		$sql = "UPDATE ".$this->prefix."$table SET $field=$field+$step WHERE $where";
		return pg_query($this->connection,$sql);
	}
	
	function decrement($table,$field,$step,$where = null) {
		return $this->increment($table,$field,-1*$step,$where);
	}
	
	function testPrivileges() {
		$status = array();
		
		$tablename = "___testertable".uniqid("");
		$dd = array(
			"id"=>array(
				DB_FIELD_TYPE=>DB_DEF_ID,
				DB_PRIMARY=>true,
				DB_INCREMENT=>true),
			"name"=>array(
				DB_FIELD_TYPE=>DB_DEF_STRING,
				DB_FIELD_LEN=>100)
		);
		
		$this->createTable($tablename,$dd,array());
		if (!$this->tableExists($tablename)) {
			$status["CREATE TABLE"] = false;
			return $status;
		} else $status["CREATE TABLE"] = true;
		
		$o = null;
		$o->name = "Testing Name";
		$insert_id = $this->insertObject($o,$tablename);
		if ($insert_id == 0) {
			$status["INSERT"] = false;
			return $status;
		} else $status["INSERT"] = true;
		
		$o = $this->selectObject($tablename,"id=".$insert_id);
		if ($o == null || $o->name != "Testing Name") {
			$status["SELECT"] = false;
			return $status;
		} else $status["SELECT"] = true;
		
		$o->name = "Testing 2";
		if (!$this->updateObject($o,$tablename)) {
			$status["UPDATE"] = false;
			return $status;
		} else $status["UPDATE"] = true;
		
		$this->delete($tablename,"id=".$insert_id);
		$o = $this->selectObject($tablename,"id=".$insert_id);
		if ($o != null) {
			$status["DELETE"] = false;
			return $status;
		} else $status["DELETE"] = true;
		
		$dd["thirdcol"] = array(
			DB_FIELD_TYPE=>DB_DEF_TIMESTAMP);
		
		$this->alterTable($tablename,$dd,array());
		$o = null;
		$o->name = "Alter Test";
		$o->thirdcol = time();
		if (!$this->insertObject($o,$tablename)) {
			$status["ALTER TABLE"] = false;
			return $status;
		} else $status["ALTER TABLE"] = true;
		
		$this->dropTable($tablename);
		if ($this->tableExists($tablename)) {
			$status["DROP TABLE"] = false;
			return $status;
		} else $status["DROP TABLE"] = true;
		
		foreach ($this->getTables() as $t) {
			if (substr($t,0,14+strlen($this->prefix)) == $this->prefix."___testertable") $this->dropTable($t);
		}
		
		return $status;
	}
	
	function error() {
		return $this->error;
	}
	
	function inError() {
		return ($this->in_error == true);
	}
	
	function limit($num,$offset) {
		return ' LIMIT '.$num.' OFFSET '.$offset.' ';
	}
	
	function toggle($table, $col, $where=null) {
		$obj = $this->selectObject($table, $where);
		$obj->$col = ($obj->$col == 0) ? 1 : 0;
		$this->updateObject($obj, $table);
	}

	function selectSearch($terms, $where = null) {
		if ($where == null) $where = "1";

		$sql = "SELECT *, MATCH (title,body) AGAINST ('".$terms."') from ".$this->prefix."search WHERE MATCH(title,body) against ('".$terms."')";
		$res = pg_query($this->connection,$sql);
		if ($res == null) return array();
		$objects = array();
		for ($i = 0; $i < pg_num_rows($res); $i++) $objects[] = pg_fetch_object($res);
		return $objects;
	}

	function selectAndJoinObjects($colsA=null, $colsB=null, $tableA, $tableB, $keyA, $keyB=null, $where = null,$orderby = null) {
		$sql = 'SELECT ';
		if ($colsA != null) {
			if (!is_array($colsA)) {
				$sql .= 'a.'.$colsA.', ';
			} else {
				foreach ($colsA as $colA) {
					$sql .= 'a.'.$colA.', ';
				}
			}
		} else {
			$sql .= ' a.*, ';
		}

		if ($colsB != null) {
                        if (!is_array($colsB)) {
                                $sql .= 'b.'.$colsB.' ';
                        } else {
				$i = 1;
                                foreach ($colsB as $colB) {
                                        $sql .= 'b.'.$colB;
					if ($i < count($colsB)) $sql .= ', ';
					$i++;
                                }
                        }
                } else {
                        $sql .= ' b.* ';
                }
	
		$sql .= ' FROM '.$this->prefix.$tableA.' a JOIN '.$this->prefix.$tableB.' b ';
		$sql .= is_null($keyB) ? 'USING('.$keyA.')' : 'ON a.'.$keyA.' = b.'.$keyB; 
                
		if ($where == null) $where = "1";
                if ($orderby == null) $orderby = '';
                else $orderby = "ORDER BY " . $orderby;
	
                $res = @pg_query($this->connection,$sql." WHERE $where $orderby");
                if ($res == null) return array();
                $objects = array();
                for ($i = 0; $i < pg_num_rows($res); $i++) $objects[] = pg_fetch_object($res);
                return $objects;
    }

	function selectObjectsBySql($sql) {
                $res = @pg_query($this->connection,$sql);
                if ($res == null) return array();
                $objects = array();
                for ($i = 0; $i < pg_num_rows($res); $i++) $objects[] = pg_fetch_object($res);
                return $objects;
	}

	function selectColumn($table,$col,$where = null,$orderby = null) {
                if ($where == null) $where = "1";
                if ($orderby == null) $orderby = '';
            	else $orderby = "ORDER BY " . $orderby;

                $res = pg_query($this->connection, "SELECT ".$col." FROM `" . $this->prefix . "$table` WHERE $where $orderby");
                if ($res == null) return array();
                $resarray = array();
                for ($i = 0; $i < pg_num_rows($res); $i++){
                        $row = pg_fetch_array($res, PGSQL_NUM);
                        $resarray[$i] = $row[0];
                }
                return $resarray;
    }

	function selectSum($table,$col,$where = null) {
                if ($where == null) $where = "1";

                $res = @pg_query($this->connection,"SELECT SUM(".$col.") FROM `" . $this->prefix . "$table` WHERE $where");
                if ($res == null) return 0;
                $resarray = array();
                for ($i = 0; $i < pg_num_rows($res); $i++){
                        $row = pg_fetch_array($res, PGSQL_NUM);
                        $resarray[$i] = $row[0];
                }
                return $resarray[0];
    }
	
	function selectDropdown($table,$col,$where = null,$orderby = null) {
                if ($where == null) $where = "1";
                if ($orderby == null) $orderby = '';
                else $orderby = "ORDER BY " . $orderby;

                $res = @pg_query($this->connection,"SELECT * FROM `" . $this->prefix . "$table` WHERE $where $orderby");
                if ($res == null) return array();
                $resarray = array();
                for ($i = 0; $i < pg_num_rows($res); $i++){
                        $row = pg_fetch_object($res);
                        $resarray[$row->id] = $row->$col;
                }
                return $resarray;
    }

	function selectValue($table,$col,$where=null) {
		if ($where == null) $where = "1";
    	$res = @pg_query($this->connection, "SELECT ".$col." FROM `" . $this->prefix . "$table` WHERE $where LIMIT 0,1");

        if ($res == null) return null;
		$obj = pg_fetch_object($res);
		 if (is_object($obj)) {
                        return $obj->$col;
                } else {
                        return null;
                }
    }

	function selectObjectsInArray($table, $array=array(), $orderby=null) {
		$where = '';
		foreach($array as $array_id) {
			if ($where == '') {
				$where .= 'id='.$array_id;
			} else {
				$where .= ' OR id='.$array_id;
			}
		}

		//eDebug($where);
		$res = $this->selectObjects($table, $where, $orderby);
		return $res;
	}

	function countObjectsBySql($sql) {
                $res = @pg_query($this->connection,$sql);
                if ($res == null) return 0;
                $obj = pg_fetch_object($res);
                return $obj->c;
    }

	function translateTableStatus($status) {
		$data = null;
		$data->rows = $status->Rows;
		$data->average_row_lenth = $status->Avg_row_length;
		$data->data_overhead = $status->Data_free;
		$data->data_total = $status->Data_length;

		return $data;
	}

	function describeTable($table) {
		if (!$this->tableExists($table)) return array();
                $res = @pg_query($this->connection,"DESCRIBE `".$this->prefix."$table`");
                $dd = array();
                for ($i = 0; $i < pg_num_rows($res); $i++) {
                        $fieldObj = pg_fetch_object($res);

                        $fieldObj->ExpFieldType = $this->getDDFieldType($fieldObj);
                        if ($fieldObj->ExpFieldType == DB_DEF_STRING) {
                                $fieldObj->ExpFieldLength = $this->getDDStringLen($fieldObj);
                        }

                        $dd[$fieldObj->Field] = $fieldObj;
                }

                return $dd;
	}
	
	function getDDFieldType($fieldObj) {
		$type = strtolower($fieldObj->Type);

		if ($type == "int(11)") return DB_DEF_ID;
		if ($type == "int(8)") return DB_DEF_INTEGER;
		elseif ($type == "tinyint(1)") return DB_DEF_BOOLEAN;
		elseif ($type == "int(14)") return DB_DEF_TIMESTAMP;
		//else if (substr($type,5) == "double") return DB_DEF_DECIMAL;
		elseif ($type == "double") return DB_DEF_DECIMAL;
		// Strings
		elseif ($type == "text" || $type == "mediumtext" || $type == "longtext" || strpos($type,"varchar(") !== false) {
			return DB_DEF_STRING;
		}
	}
	
	function getDDStringLen($fieldObj) {
		$type = strtolower($fieldObj->Type);
		if ($type == "text") return 65535;
		else if ($type == "mediumtext") return 16777215;
		else if ($type == "longtext") return 16777216;
		else if (strpos($type,"varchar(") !== false) {
			return str_replace(  array("varchar(",")"),  "",$type) + 0;
		}
	}

}

?>
