<?php
##################################################
#
# Copyright (c) 2004-2014 OIC Group, Inc.
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
    if (php_sapi_name() == 'cli') {
        $nl = "\n";
        $include_mobile = false;
        $include_images = false;
        $include_videos = false;
        $include_products = false;
        if (!empty($_SERVER['argc'])) for ($ac = 1; $ac < $_SERVER['argc']; $ac++) {
            if ($_SERVER['argv'][$ac] == '-mobile') {
                $include_mobile = true;
            }
            if ($_SERVER['argv'][$ac] == '-images') {
                $include_images = true;
            }
            if ($_SERVER['argv'][$ac] == '-videos') {
                $include_videos = true;
            }
            if ($_SERVER['argv'][$ac] == '-products') {
                $include_products = true;
            }
        }
    } else {
        $nl = '<br>';
        if (!empty($_GET['mobile'])) {
            $include_mobile = true;
        } else {
            $include_mobile = false;
        }
        if (!empty($_GET['images'])) {
            $include_images = true;
        } else {
            $include_images = false;
        }
        if (!empty($_GET['videos'])) {
            $include_videos = true;
        } else {
            $include_videos = false;
        }
        if (!empty($_GET['products'])) {
            $include_products = true;
        } else {
            $include_products = false;
        }
    }

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
    if ($include_videos) $content.=chr(13).chr(10)."        xmlns:video='http://www.google.com/schemas/sitemap-video/1.1'";    // video sitemap
    if ($include_mobile) $content.=chr(13).chr(10)."        xmlns:mobile='http://www.google.com/schemas/sitemap-mobile/1.0'";  // mobile sitemap
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
    $num_images=0;
    $num_videos=0;
    $num_cats=0;
    $num_products=0;
    $columns = '';
    
	//Get all the sections
    if ($include_images) {
        $image_sections = $db->selectObjects('sectionref',"module LIKE '%photo%'");
        foreach ($image_sections as $key=>$image_section) {
            $section = $db->selectColumn('section','sef_name','public = 1 and active = 1 and id='.$image_section->section);
            $image_sections[$key]->sef_name = !empty($section[0]) ? $section[0] : null;
        }
        $ph = new photo();
    }
    if ($include_videos) {
        $media_sections = $db->selectObjects('sectionref',"module LIKE '%media%'");
        foreach ($media_sections as $key=>$media_section) {
            $section = $db->selectColumn('section','sef_name','public = 1 and active = 1 and id='.$media_section->section);
            $media_sections[$key]->sef_name = !empty($section[0]) ? $section[0] : null;
        }
        $md = new media();
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
                            $columns.=htmlspecialchars($photo->title);
                            $columns.='</image:title>'.chr(13).chr(10);
                            $columns.='        <image:caption>';
                            $columns.=htmlspecialchars($photo->alt);
                            $columns.='</image:caption>'.chr(13).chr(10);
                            $columns.='    </image:image>'.chr(13).chr(10);
                            $num_images++;
                        }
                    }
                }
            }
        }

        if ($include_videos) {
            foreach ($media_sections as $video_sef) {
                if ($video_sef->sef_name == $item) {
                    $loc = serialize(expCore::makeLocation('media',$video_sef->source));
                    $videos = $md->find('all',"location_data='".$loc."' AND media_type='file'");
                    foreach ($videos as $video) {
                        if ($video->expFile['media'][0]->mimetype != 'audio/mpeg' && file_exists(BASE.$video->expFile['media'][0]->directory.$video->expFile['media'][0]->filename)) {
                            $columns.='    <video:video>'.chr(13).chr(10);
                            $columns.='        <video:thumbnail_loc>';
                            if (!empty($video->expFile['splash'][0]->url)) {
                                $columns.=$video->expFile['splash'][0]->url;
                            } else {
                                $columns.=URL_FULL.'framework/core/assets/images/default_preview.gif';
                            }
                            $columns.='</video:thumbnail_loc>'.chr(13).chr(10);
                            $mloc = expUnserialize($video->location_data);
                            $columns.='        <video:player_loc>';
                            $columns.=URL_FULL.'media/showall/src/'.$mloc->src;
                            $columns.='</video:player_loc>'.chr(13).chr(10);
                            $columns.='        <video:content_loc>';
                            $columns.=$video->expFile['media'][0]->url;
                            $columns.='</video:content_loc>'.chr(13).chr(10);
                            $columns.='        <video:publication_date>';
                            $columns.=date('c',$video->created_at);
                            $columns.='</video:publication_date>'.chr(13).chr(10);
                            foreach ($video->expTag as $tag) {
                                $columns.='        <video:tag>';
                                $columns.=htmlspecialchars($tag->title);
                                $columns.='</video:tag>'.chr(13).chr(10);
                            }
                            $columns.='        <video:title>';
                            $columns.=htmlspecialchars($video->title);
                            $columns.='</video:title>'.chr(13).chr(10);
                            $columns.='        <video:description>';
                            $columns.=htmlspecialchars($video->body);
                            $columns.='</video:description>'.chr(13).chr(10);
                            $columns.='    </video:video>'.chr(13).chr(10);
                            $num_videos++;
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
            $num_cats++;

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
            $num_products++;

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
    echo "Generated $count link(s)";
    if ($include_images) echo ", $num_images image(s)";
    if ($include_videos) echo ", $num_videos video(s)";
    if ($include_products) echo ", $num_cats product category(s), $num_products product(s)";
    echo " in the sitemap.";

?>