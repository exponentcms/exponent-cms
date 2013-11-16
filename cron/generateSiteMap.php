<?php
##################################################
#
# Copyright (c) 2004-2013 OIC Group, Inc.
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

    //Initialized the exponent
    require_once('../exponent.php');

    //processing flags
    $include_mobile = false;
    $include_images = false;
    $include_products = true;

	//Get the filename to be use
    $filename = BASE . 'sitemap.xml';
    //standard mobile tag
    $mobile_tag = '    <mobile:mobile/>'.chr(13).chr(10);
    //frequency
    $freq = "weekly";
    $priority = "0.5";

    //Header of the xml file
    $content="<?xml version='1.0' encoding='UTF-8'?>".chr(13).chr(10);
    $content.="<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'";                                                   // standard sitemap
    if ($include_images) $content.=chr(13).chr(10)."        xmlns:image='http://www.google.com/schemas/sitemap-image/1.1'";    // image sitemap
    if ($include_mobile) $content.=chr(13).chr(10)."        xmlns:mobile='http://www.google.com/schemas/sitemap-mobile/1.0'";   // mobile sitemap
    $content.=">".chr(13).chr(10);

	 //Check if the file exist
	if (!$handle = fopen($filename, 'w')) {
		echo "Cannot open file ($filename)";
		exit;
	}
	
	//Check if the file is writable
	if (fwrite($handle, $content) == FALSE) {
		$action_msg = "ER";
	}
    
    $count=0;
    $images=0;
    $columns = '';
    
	//Get all the sections
    if ($include_images) {
        $image_sections = $db->selectObjects('sectionref',"module LIKE '%photo%'");
        foreach ($image_sections as $key=>$image_section) {
            $section = $db->selectColumn('section','sef_name','public = 1 and active = 1 and id='.$image_section->section);
            $image_sections[$key]->sef_name = $section[0];
        }
        $ph = new photo();
    }
    $sections = $db->selectColumn('section','sef_name','public = 1 and active = 1');
	foreach ($sections as $item) {
		
		$columns = '<url>'.chr(13).chr(10);
	
		$columns.='    <loc>';
		$columns.=URL_FULL.$item;
		$columns.='</loc>'.chr(13).chr(10);

        if ($include_mobile) $columns.=$mobile_tag;

		$columns.='    <lastmod>';
		$columns.= date('Y-m-d');
		$columns.='</lastmod>'.chr(13).chr(10);
		
		$columns.='    <changefreq>';
		$columns.= $freq;
		$columns.='</changefreq>'.chr(13).chr(10);

		$columns.='    <priority>';
		$columns.= $priority;
		$columns.='</priority>'.chr(13).chr(10);

        if ($include_images) {
            foreach ($image_sections as $image_sef) {
                if ($image_sef->sef_name == $item) {
                    $loc = serialize(expCore::makeLocation('photo',$image_sef->source));
                    $photos = $ph->find('all',"location_data='".$loc."'");
                    foreach ($photos as $photo) {
                        if (file_exists(BASE.$photo->expFile[0]->directory.$photo->expFile[0]->filename)) {
                            $columns.='    <image:image>'.chr(13).chr(10);
                            $columns.='        <image:loc>';
                            $columns.=$photo->expFile[0]->url;
                            $columns.='</image:loc>'.chr(13).chr(10);
                            $columns.='        <image:title>';
                            $columns.=$photo->title;
                            $columns.='</image:title>'.chr(13).chr(10);
                            $columns.='        <image:caption>';
                            $columns.=$photo->alt;
                            $columns.='</image:caption>'.chr(13).chr(10);
                            $columns.='    </image:image>'.chr(13).chr(10);
                            $images++;
                        }
                    }
                }
            }
        }

		$columns.='</url>';
		$count++;

		// Write the section content to our opened file.
		if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
			$action_msg = "ER";
		}
	}

    if ($include_products) {
        //Get all the active product categories
        $categories = $db->selectColumn('storeCategories','sef_url','is_active = 1');
        foreach ($categories as $item) {

            $columns = '<url>'.chr(13).chr(10);

            $columns.='    <loc>';
            $columns.=URL_FULL.'store/showall/title/'.$item;
            $columns.='</loc>'.chr(13).chr(10);

            if ($include_mobile) $columns.=$mobile_tag;

            $columns.='    <lastmod>';
            $columns.= date('Y-m-d');
            $columns.='</lastmod>'.chr(13).chr(10);

            $columns.='    <changefreq>';
            $columns.= $freq;
            $columns.='</changefreq>'.chr(13).chr(10);

            $columns.='    <priority>';
            $columns.= $priority;
            $columns.='</priority>'.chr(13).chr(10);

            $columns.='</url>';
            $count++;

            // Write the active categories to our opened file.
            if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
                $action_msg = "ER";
            }
        }

        //Get all the active products
        $products = $db->selectColumn('product','sef_url','(active_type = 0 or active_type = 1) and parent_id = 0');
        foreach ($products as $item) {

            $columns = '<url>'.chr(13).chr(10);

            $columns.='    <loc>';
            $columns.=URL_FULL.'store/show/title/'.$item;
            $columns.='</loc>'.chr(13).chr(10);

            if ($include_mobile) $columns.=$mobile_tag;

            $columns.='    <lastmod>';
            $columns.= date('Y-m-d');
            $columns.='</lastmod>'.chr(13).chr(10);

            $columns.='    <changefreq>';
            $columns.= $freq;
            $columns.='</changefreq>'.chr(13).chr(10);

            $columns.='    <priority>';
            $columns.= $priority;
            $columns.='</priority>'.chr(13).chr(10);

            $columns.='</url>';
            $count++;

            // Write all the active products to our opened file.
            if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
                $action_msg = "ER";
            }
        }
    }
	
    $content='</urlset>'.chr(13).chr(10);

    // Write the footer to our opened file.
    if (fwrite($handle, $content) == FALSE) {
        $action_msg = "ER";
    }
    $action_msg = "SC";
    fclose($handle);        
    echo "\r\nGenerated $count link(s) in the sitemap.\r\n";
    if ($include_images) echo "\r\nwith $images images.\r\n";

?>