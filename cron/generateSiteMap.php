<?php
/**
* This is meant to be called from cron. 
* It will send off the ealerts.
*/
      
    require_once("bootstrap.php");    
    
    $filename = EXP_PATH . 'sitemap.xml';    
   
    $content="<?xml version='1.0' encoding='UTF-8'?>".chr(13).chr(10);
    $content.="<urlset xmlns='http://www.sitemaps.org/schemas/sitemap/0.9'>".chr(13).chr(10);
    
    if (is_writable($filename))
    {
        if (!$handle = fopen($filename, 'w')) {
            echo "Cannot open file ($filename)";
            exit;
        }
        // Write$somecontent to our opened file.
        if (fwrite($handle, $content) == FALSE) {
            $action_msg = "ER";
        }
    }
    else
    {
        echo "$filename is not writeable.";
    }
    
    $count=0;
    $columns = '';
    
    //$ps = new product(); 
    //$prodCount = $ps->find('count','parent_id=0 AND (availability_type=0 OR availability_type=1)');  
    //print "\n" . $prodCount . "\n";
    //print "\n";
    
    $sections = $db->selectObjectsBySql('SELECT sef_name FROM exponent_section WHERE public = 1 and active = 1');

	//Sections
	foreach ($sections as $item) {            
		
		$columns = '<url>'.chr(13).chr(10);

	
		$columns.='<loc>';
		$columns.="http://www.militaryuniformsupply.com/".$item->sef_name;
		$columns.='</loc>'.chr(13).chr(10);

		$columns.='<lastmod>';
		$columns.= date('Y-m-d');
		$columns.='</lastmod>'.chr(13).chr(10);
		
		$columns.='<changefreq>';
		$columns.= "weekly";
		$columns.='</changefreq>'.chr(13).chr(10);

		$columns.='<priority>';
		$columns.= "0.5";
		$columns.='</priority>'.chr(13).chr(10);
		
		$columns.='</url>'.chr(13).chr(10);
		$count++;
		//size            
		// Write$somecontent to our opened file.
		if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
			$action_msg = "ER";
		}
	}

	//Categories
	$categories = $db->selectObjectsBySql('SELECT sef_url FROM exponent_storeCategories WHERE is_active = 1');
	foreach ($categories as $item) {            
		
		$columns = '<url>'.chr(13).chr(10);
	
		$columns.='<loc>';
		$columns.="http://www.militaryuniformsupply.com/".$item->sef_url;
		$columns.='</loc>'.chr(13).chr(10);

		$columns.='<lastmod>';
		$columns.= date('Y-m-d');
		$columns.='</lastmod>'.chr(13).chr(10);
		
		$columns.='<changefreq>';
		$columns.= "weekly";
		$columns.='</changefreq>'.chr(13).chr(10);

		$columns.='<priority>';
		$columns.= "0.5";
		$columns.='</priority>'.chr(13).chr(10);
		
		$columns.='</url>'.chr(13).chr(10);
		$count++;
		//size            
		// Write$somecontent to our opened file.
		if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
			$action_msg = "ER";
		}
	}
	
	
	//Products
	$products = $db->selectObjectsBySql("SELECT sef_url FROM exponent_product WHERE (active_type = 0 or active_type = 1) and parent_id = 0");
	foreach ($products as $item) {            
		
		$columns = '<url>'.chr(13).chr(10);
	
		$columns.='<loc>';
		$columns.="http://www.militaryuniformsupply.com/".$item->sef_url;
		$columns.='</loc>'.chr(13).chr(10);

		$columns.='<lastmod>';
		$columns.= date('Y-m-d');
		$columns.='</lastmod>'.chr(13).chr(10);
		
		$columns.='<changefreq>';
		$columns.= "weekly";
		$columns.='</changefreq>'.chr(13).chr(10);

		$columns.='<priority>';
		$columns.= "0.5";
		$columns.='</priority>'.chr(13).chr(10);
		
		$columns.='</url>'.chr(13).chr(10);
		$count++;
		//size            
		// Write$somecontent to our opened file.
		if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
			$action_msg = "ER";
		}
	}
	
    $content='</urlset>'.chr(13).chr(10);

    // Write$somecontent to our opened file.
    if (fwrite($handle, $content) == FALSE) {
        $action_msg = "ER";
    }
    $action_msg = "SC";
    fclose($handle);        
    echo "\r\nGenerated $count products in the feed.\r\n";       
	
?>