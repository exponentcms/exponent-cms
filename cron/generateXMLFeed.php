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
* This is meant to be called from cron. 
* It will send off the ealerts.
*/
	//Initialized the exponent
    require_once("bootstrap.php");    
   
	//Get the filename to be use
    $filename = EXP_PATH . 'datafeed.xml';    
    
	//Header of the xml file
    $content="<?xml version='1.0' encoding='UTF-8'?>".chr(13).chr(10);
    $content.="<rss version='2.0' xmlns:g='http://base.google.com/ns/1.0'>".chr(13).chr(10);
    $content.='<channel>'.chr(13).chr(10);
    $content.='<title>' . SITE_TITLE . '</title>'.chr(13).chr(10);
    $content.='<link>' . URL_FULL .'</link>'.chr(13).chr(10);
    $content.='<description>' . SITE_DESCRIPTION . '</description>'.chr(13).chr(10);
    
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
    $columns = '';
    $sql = 'SELECT DISTINCT(p.id),active_type,availability_type,quantity, model,feed_title,feed_body,google_product_type,p.sef_url,base_price,use_special_price, special_price,f.directory,f.filename, c.title as company, sc.id as storeCategoryId FROM '.DB_TABLE_PREFIX.'_product p
    LEFT JOIN '.DB_TABLE_PREFIX.'_content_expFiles cf ON
         p.id = cf.content_id 
    LEFT JOIN '.DB_TABLE_PREFIX.'_expFiles f ON
        cf.expfiles_id = f.id 
    LEFT JOIN '.DB_TABLE_PREFIX.'_companies c ON
        c.id = p.companies_id
    LEFT JOIN '.DB_TABLE_PREFIX.'_product_storeCategories psc ON
        p.id = psc.product_id
    LEFT JOIN '.DB_TABLE_PREFIX.'_storeCategories sc ON
        psc.storeCategories_id = sc.id
    WHERE p.parent_id=0 AND (availability_type=0 OR availability_type=1 OR availability_type=2) AND(active_type=0 OR active_type=1) AND p.sef_url != "" AND cf.subtype="mainimage" ORDER BY p.title ASC';
    
    $products = $db->selectObjectsBySql($sql);
    $counter = array();
	$prodflipper[] = array();
	$prodflipper2[] = array();
	$prodflipper3[] = array();
	echo "\r\nPre Count: " . count($products). "\r\n";;  
	
	foreach ($products as $p1) {
		@$prodflipper[$p1->id] = $p1;
	}
	echo "Flip Count 1: " . count($prodflipper). "\r\n";;  
	
	foreach ($prodflipper as $p2) {
		@$prodflipper2[$p2->sef_url] = $p2;
	}
	echo "Flip Count 2: " . count($prodflipper2). "\r\n";;  
	
	foreach ($prodflipper2 as $p3) {
		@$prodflipper3[$p3->model] = $p3;
	}
	echo "Flip Count 3: " . count($prodflipper3). "\r\n";;  
	
    //Google
	foreach ($prodflipper3 as $prod) {
	
		if (empty($prod->sef_url) || empty($prod->feed_title) || empty($prod->model) || empty($prod->feed_body)) continue;
		
		if(in_array($prod->sef_url,$counter) || isset($counter[$prod->id])) {
			echo "No no..." . $prod->id . "\r\n";
			continue;
		}
		else
			$counter[$prod->id]  = $prod->sef_url;
			
		$count++;
		//Get the google type categories, I used selectArraysBySql since a product can have more than 1 google taxonomy
		/*$google_types_res = $db->selectArraysBySql("SELECT exponent_google_product_types.title FROM exponent_google_product_types, exponent_product, exponent_product_storeCategories, exponent_google_product_types_storeCategories 
													WHERE exponent_google_product_types.id = google_product_types_id and exponent_google_product_types_storeCategories.storecategories_id = exponent_product_storeCategories.storecategories_id and 
													exponent_product.id = exponent_product_storeCategories.product_id and exponent_product.id = {$prod->id}");
                                                    */
		$google_types = '';

		/*if(count($google_types_res) > 0) {
			$google_types_array = array();
			foreach($google_types_res as $item) {
				$google_types_array[] = $item['title'];
			}
			$google_types = implode(' > ', $google_types_array);
			$google_types = expString::convertXMLFeedSafeChar($google_types);
		}*/
        
        //temporary hack in
        $gpath = $db->selectPathToNestedNode('storeCategories',$prod->storeCategoryId);
            
        $gsql = 'SELECT title FROM '.DB_TABLE_PREFIX.'_google_product_types bpt
            INNER JOIN '.DB_TABLE_PREFIX.'_google_product_types_storeCategories bptsc ON
                bptsc.google_product_types_id = bpt.id
            WHERE bptsc.storecategories_id = ' . $gpath[0]->id;                     
        $g_catObj = $db->selectObjectBySql($gsql);              
       
        if(isset($g_catObj->title))$google_types = $g_catObj->title;                
        //hack
       	
		$columns = '<item>'.chr(13).chr(10);

		$columns.='<title>';            
		$prod->feed_title = expString::convertXMLFeedSafeChar(html_entity_decode(strip_tags($prod->feed_title)));
		$prod->feed_title = htmlspecialchars($prod->feed_title);
		$prod->feed_title = expString::onlyReadables($prod->feed_title);
		
		$prod->google_product_type = expString::convertXMLFeedSafeChar(html_entity_decode(strip_tags($prod->google_product_type)));
		$prod->google_product_type = htmlspecialchars($prod->google_product_type);
		$prod->google_product_type = expString::onlyReadables($prod->google_product_type);

		$columns.= $prod->feed_title;
		$columns.='</title>'.chr(13).chr(10);

		$columns.='<link>';
		$columns.=URL_FULL.strip_tags($prod->sef_url);
		$columns.='</link>'.chr(13).chr(10);

		$columns.='<description>';
		$columns.= str_ireplace('&','<![CDATA[&]]>',$prod->feed_body);
		$columns.='</description>'.chr(13).chr(10);
		
		$columns.='<g:image_link>';
		$columns.= URL_FULL.$prod->directory . $prod->filename;
		$columns.='</g:image_link>'.chr(13).chr(10);

		$columns.='<g:price>';
		$columns.= $prod->base_price;
		$columns.='</g:price>'.chr(13).chr(10);
		
		if($prod->use_special_price && !empty($prod->special_price)) {
			$columns.='<g:sale_price>';
			$columns.= $prod->special_price;
			$columns.='</g:sale_price>'.chr(13).chr(10);            
		}
		
		$columns.='<g:condition>';
		$columns.='new';
		$columns.='</g:condition>'.chr(13).chr(10);

		$columns.='<g:id>';
		$columns.=str_ireplace('&','&amp;',$prod->model);            
		$columns.='</g:id>'.chr(13).chr(10);

		if(!empty($prod->company)) {
			$columns.='<g:brand>';
			$columns.=$prod->company;            
			$columns.='</g:brand>'.chr(13).chr(10);
		}
		
		if($prod->active_type == 0) {
		
			if ($prod->availability_type == 0) {
				$columns.='<g:availability>in stock</g:availability>'.chr(13).chr(10);
				$columns.='<g:quantity>99</g:quantity>'.chr(13).chr(10);
			}
			else if ($prod->availability_type == 1) {
				$columns.='<g:availability>limited availability</g:availability>'.chr(13).chr(10);
			}
			else if ($prod->availability_type == 2 && $prod->quantity <= 0) {
				$columns.='<g:availability>out of stock</g:availability>'.chr(13).chr(10);
				$columns.='<g:quantity>0</g:quantity>'.chr(13).chr(10);
			}
			else if ($prod->availability_type == 2 && $prod->quantity > 0) {
				$columns.='<g:availability>in stock</g:availability>'.chr(13).chr(10);
				$columns.='<g:quantity>'.$prod->quantity. '</g:quantity>'.chr(13).chr(10);
			}
		}
		else if ($prod->active_type == 1) {
			$columns.='<g:availability>out of stock</g:availability>'.chr(13).chr(10);
			$columns.='<g:quantity>0</g:quantity>'.chr(13).chr(10);
		}
	   
		if(!empty($prod->google_product_type)) {
			$columns.='<g:product_type>';
			$columns.= str_ireplace('&','<![CDATA[&]]>',$prod->google_product_type);
			$columns.='</g:product_type>'.chr(13).chr(10);
		} elseif(!empty($google_types)) {
			$columns.='<g:product_type>';
			$columns.= str_ireplace('&','<![CDATA[&]]>',$google_types);
			$columns.='</g:product_type>'.chr(13).chr(10);
		}
		$columns.='</item>'.chr(13).chr(10);
  
		// Write the body data to our opened file.
		if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
			$action_msg = "ER";
		}
	}
            
    $content='</channel>'.chr(13).chr(10);
    $content.='</rss>'.chr(13).chr(10);
    
    // Write the footer data to our opened file.
    if (fwrite($handle, $content) == FALSE) {
        $action_msg = "ER";
    }
    $action_msg = "SC";
    fclose($handle); 
    
    echo "\r\nGenerated $count products in the Google feed.\r\n";       
    
    //end Google 
     
    //Bing
           
    //Get the filename to be use
    $filename = EXP_PATH . 'bingshopping.txt';    
    
    //Header of the xml file
    $header="MPID".chr(9)."Title".chr(9)."ProductURL".chr(9)."Description".chr(9)."ImageURL".chr(9)."Brand".chr(9)."SKU".chr(9)."Price".chr(9);
    $header.="Availability".chr(9)."Condition".chr(9)."MerchantCategory".chr(9)."B_Category".chr(13).chr(10);
    
    //Check if the file exist
    if (!$handle = fopen($filename, 'w')) {
        echo "Cannot open file ($filename)";
        exit;
    }
    
    //Check if the file is writable
    if (fwrite($handle, $header) == FALSE) {
        $action_msg = "ER";
    }
    
    $count=0;
    $counter = array();
    $columns = '';    
    reset($prodflipper3);
    
    foreach ($prodflipper3 as $prod) {
        if (empty($prod->sef_url) || empty($prod->feed_title) || empty($prod->model) || empty($prod->feed_body)) continue;
        
        if(in_array($prod->sef_url,$counter) || isset($counter[$prod->id])) {
            echo "No no..." . $prod->id . "\r\n";
            continue;
        }
        else
            $counter[$prod->id]  = $prod->sef_url;
            
        $count++;
        
        $columns = $prod->id . chr(9);
        
        $prod->feed_title = expString::convertXMLFeedSafeChar(html_entity_decode(strip_tags($prod->feed_title)));
        $prod->feed_title = htmlspecialchars($prod->feed_title);
        $prod->feed_title = expString::onlyReadables($prod->feed_title);
        
        $columns .= $prod->feed_title . chr(9);
        
        $columns.=URL_FULL.strip_tags($prod->sef_url) . chr(9);
        
        $columns.= expString::onlyReadables($prod->feed_body) . chr(9);
        
        $columns.= URL_FULL.$prod->directory . $prod->filename . chr(9);
        
        if(!empty($prod->company)) {           
            $columns.=$prod->company . chr(9);            
        }else{
            $columns.= chr(9);  
        }
        
        $columns .= $prod->model . chr(9);            
        
        if($prod->use_special_price && !empty($prod->special_price)) {            
            $columns.= $prod->special_price . chr(9);           
        } else {
            $columns.= $prod->base_price . chr(9);
        }
        
        if($prod->active_type == 0) {
        
            if ($prod->availability_type == 0) {
                $columns.='In Stock'.chr(9);                
            }
            else if ($prod->availability_type == 1) {
                $columns.='Back-Order'.chr(9);                                              
            }
            else if ($prod->availability_type == 2 && $prod->quantity <= 0) {
                $columns.='Out of Stock'.chr(9);                                              
            }
            else if ($prod->availability_type == 2 && $prod->quantity > 0) {                
                $columns.='In Stock'.chr(9);       
            }
        }
        else if ($prod->active_type == 1) {
            $columns.='Out of Stock'.chr(9);           
        }
        
        $columns.='New' . chr(9);
        
        //merchant category
        $crumb = '';
        $path_titles = array();
        $path = $db->selectPathToNestedNode('storeCategories',$prod->storeCategoryId);
        $path_count = 0;
        $b_cat = '';
        foreach($path as $cat)
        {
            $path_titles[] = $cat->title;
            if($path_count == 0)
            {
                //first one, so get the root b_category''
                $sql = 'SELECT title FROM '.DB_TABLE_PREFIX.'_bing_product_types bpt
                    INNER JOIN '.DB_TABLE_PREFIX.'_bing_product_types_storeCategories bptsc ON
                        bptsc.bing_product_types_id = bpt.id
                    WHERE bptsc.storecategories_id = ' . $cat->id;                     
                $b_catObj = $db->selectObjectBySql($sql);              
                if(isset($b_catObj->title))$b_cat = $b_catObj->title;                
            }
            $path_count ++;            
        }
        $crumb = implode('>',$path_titles);
        $columns.=$crumb . chr(9);
        
        //b_category
        if(empty($b_cat)) $columns .= '';
        else $columns .= $b_cat; 
        
        // Write the body data to our opened file.
        if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
            $action_msg = "ER";
        }
    }
           
    //end Bing
    
    echo "\r\nGenerated $count products in the Bing feed.\r\n";       
         
?>