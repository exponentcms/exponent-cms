<?php
$maps = array();
$maps[] = array(
	'controller'=>'store',
    'action'=>'showByTitle',
    'url_parts'=>array(
        'title'=>'(.*)'
	),
);

$maps[] = array(
	'controller'=>'store',
    'action'=>'show',
    'url_parts'=>array(
        'title'=>'(.*)'
	),
);

$maps[] = array(
	'controller'=>'store',
    'action'=>'showall',
    'url_parts'=>array(
        'title'=>'(.*)'
	),
);

$maps[] = array(
	'controller'=>'store',
    'action'=>'viewpart',
    'url_parts'=>array(
        'action'=>'viewpart',
        'manufacturer'=>'(.*)',
        'title'=>'(.*)'
	),
);
?>
