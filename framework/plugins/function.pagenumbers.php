<?php

##################################################
#
# Copyright (c) 2004-2012 OIC Group, Inc.
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
 * Smarty {pagenumbers} function plugin
 *
 * Type:     function<br>
 * Name:     pagenumbers<br>
 * Purpose:  display a pagination page numbers
 *
 * @param         $params
 * @param \Smarty $smarty
 */
function smarty_function_pagenumbers($params,&$smarty) {
	// initialize a couple of variables
	$page = $params['page']; //just for neater code :-)
	$class = isset($params['class']) ? $params['class'] : 'page-number';
	$activeclass = isset($params['class']) ? $params['class'] : 'page-number-active';

	// We always start with page one.
	if ($page->numberOfPages > 1) {
		if ($page->page == 1) {	
			echo '<span class="'.$class.'">1</a>&#160;';
		} else {
			echo '<a class="'.$class.'" href="#" onclick="page(1)">1</a> ... ';
		}
	}

	//put in the "back steps" if needed
	if ( ($page->page - $page->pageLinksToShow) > $page->pageLinksToShow) {
		$steppage = $page->pageLinksToShow + ($page->page % $page->pageLinksToShow);
		for($steppage; $steppage <= ($page->page - $page->pageLinksToShow); $steppage += $page->pageLinksToShow) {
			echo '<a class="'.$class.'" href="#" onclick="page('.$steppage.')">'.$steppage.'</a> ... ';
		}
	}
	
	// Loop over the rest of the pages to show and echo out the links
	if ($page->numberOfPages <= $page->pageLinksToShow) {
		$start = 2;
		$end = $page->numberOfPages;	
	} else {
		if (($page->numberOfPages - $page->page) >= $page->pageLinksToShow) {
			$start = $page->page == 1 ? 2 : $page->page;
		} elseif ( ($page->numberOfPages - $page->page) < $page->pageLinksToShow) {
			$start = $page->numberOfPages - $page->pageLinksToShow;
		} 
		$end = $start + $page->pageLinksToShow;
	}

	// spit out the page number links
	for($start; $start <= $end; $start++) {
		if ($start != $page->page) {
			echo '<a class="'.$class.'" href="#" onclick="page('.$start.')">'.$start.'</a>';
		} else {
			echo '<span class="'.$class.'">'.$start.'</span>';
		}
		echo "&#160;";
	}

	//put in the "next steps" if needed
        if ( ($page->numberOfPages - $page->pageLinksToShow) > $page->page) {
                for($steppage=$page->page; $steppage <= ($page->numberOfPages); $steppage += $page->pageLinksToShow) {
                        echo '<a class="'.$class.'" href="#" onclick="page('.$steppage.')">'.$steppage.'</a> ... ';
                }
        }
}

?>

