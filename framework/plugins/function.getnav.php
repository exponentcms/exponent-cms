<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by Phillip Ball
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
 * Smarty plugin
 * @package Smarty-Plugins
 * @subpackage Function
 */

/**
 * Smarty {getnav} function plugin
 *
 * Type:     function<br>
 * Name:     getnav<br>
 * Purpose:  get and assign navigation structure
 *
 * @param         $params
 * @param \Smarty $smarty
 * @return bool
 */
function smarty_function_getnav($params,&$smarty) {
	global $sections;

	if($params['type']=="parent"){
		
		foreach($sections as $key=>$value){
			$rekeyed[$value->id] = $value;
		}
		
		$linkInQuestion = $rekeyed[$params['of']];
		
		$nav = $rekeyed[$linkInQuestion->parent];
		
		
	}

	if($params['type']=="siblingsandchildren"){

		foreach($sections as $key=>$value){
			$rekeyed[$value->id] = $value;
		}


		$linkInQuestion = $rekeyed[$params['of']];
	//	echo $linkInQuestion->depth;

		foreach ($sections as $key=>$value) {
			if ($value->depth < $linkInQuestion->depth){
				if($value->parent == 0) {
					$nav[] = $value;
				}else{
					foreach ($value->parents as $parent){
						if($parent == $value->id){
							$nav[] = $value ;
						}
					}

				}
			}

		}
	}
	if($params['type']=="allsubchildren"){
		
		foreach($sections as $key=>$value){
			$rekeyed[$value->id] = $value;
		}
		
		
		$linkInQuestion = $rekeyed[$params['of']];
		
		foreach ($sections as $key=>$value) {
			
			foreach ($value->parents as $pkey=>$parent){
				if($parent == $linkInQuestion->id){
					$nav[] = $value ;
				}
			}
		
		}
	//	eDebug($nav);
		
	}
	if($params['type']=="siblingsandchildren"){
		
		foreach($sections as $key=>$value){
			$rekeyed[$value->id] = $value;
		}
		
		
		$linkInQuestion = $rekeyed[$params['of']];
	//	echo $linkInQuestion->depth;
		
		foreach ($sections as $key=>$value) {
			if ($value->depth < $linkInQuestion->depth){
				if($value->parent == 0) {
					$nav[] = $value;
				}else{
					foreach ($value->parents as $parent){
						if($parent == $value->id){
							$nav[] = $value ;
						}
					}
				
				}
			}
			
		}
	}
	if($params['type']=="siblings"){
		
		foreach($sections as $key=>$value){
			$rekeyed[$value->id] = $value;
		}
		
		
		$linkInQuestion = $rekeyed[$params['of']];
		foreach ($sections as $key=>$value) {
			if($value->parent == $linkInQuestion->parent) {
				$nav[] = $value;
			}			
		}
		
		
	}
	if($params['type']=="children"){
		
		foreach($sections as $key=>$value){
			$rekeyed[$value->id] = $value;
		}
		
		
		$linkInQuestion = $rekeyed[$params['of']];
		foreach ($sections as $key=>$value) {
			if($value->parent == $linkInQuestion->id) {
				$nav[] = $value;
			}			
		}
		
		
	}
	if($params['type']=="haschildren"){
		
		foreach($sections as $key=>$value){
			$rekeyed[$value->id] = $value;
		}
		
		
		$linkInQuestion = $rekeyed[$params['of']];
		foreach ($sections as $key=>$value) {
			if($value->parent == $linkInQuestion->id) {
				$tmp[] = $value;
			}			
		}
		if (count($tmp)>0){
			$nav = 1;
		}else{
			$nav = 0;
		}
		
	}
	
	$nav = (!empty($params['json'])) ? json_encode($nav) : $nav;
	if (isset($params['assign'])) $smarty->assign($params['assign'],$nav);
}

?>

