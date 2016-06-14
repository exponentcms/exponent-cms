<?php

 /** Convention foreign keys: plugin for Adminer
 * Links for foreign keys by convention user_id => users.id. Useful for Ruby On Rails like standard schema conventions.
 * @author Ivan Nečas, @inecas
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
 class ConventionForeignKeys {

         function foreignKeys($table) {
           $ret = array();
           foreach(fields($table) as $field => $args){
//               if(ereg("^(.*)_id$", $field, $args)){
               if(preg_match("^(.*)_id$^", $field, $args)){
                   if ($table == DB_TABLE_PREFIX . '_' . 'product' && $args[0] == 'product_type_id') {
                   } else {
                       $ret[] = array("table" => DB_TABLE_PREFIX . '_' . $args[1], "source" => array($field), "target" => array("id"));
                   }
               } elseif(preg_match("~poster|editor~", $field, $args)) {
                   $ret[] = array("table" => DB_TABLE_PREFIX . '_' . 'user', "source" => array($field), "target" => array("id"));
               }
           }
           return $ret;
         }
 }
 ?>
