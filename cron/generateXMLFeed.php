<?php
/**
* This is meant to be called from cron. 
* It will send off the ealerts.
*/
      
    require_once("bootstrap.php");    
    
    $filename = EXP_PATH . 'datafeed.xml';    
   
    $content="<?xml version='1.0' encoding='UTF-8'?>".chr(13).chr(10);
    $content.="<rss version='2.0' xmlns:g='http://base.google.com/ns/1.0'>".chr(13).chr(10);
    $content.='<channel>'.chr(13).chr(10);
    $content.='<title>Military Uniform Supply</title>'.chr(13).chr(10);
    $content.='<link>http://www.militaryuniformsupply.com</link>'.chr(13).chr(10);
    $content.='<description>Military Uniform Supply and Military Clothing Sales, including BDUs, DCUs, ACUs, ABUs Multicam, combat boots, and more.</description>'.chr(13).chr(10);
    
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
    
    $products = $db->selectObjectsBySql('SELECT DISTINCT(p.id),active_type,availability_type,quantity, model,feed_title,feed_body,google_product_type,sef_url,base_price,use_special_price, special_price,f.directory,f.filename, c.title as company FROM exponent_product p
    LEFT JOIN exponent_content_expFiles cf ON
         p.id = cf.content_id 
    LEFT JOIN exponent_expFiles f ON
        cf.expfiles_id = f.id 
    LEFT JOIN exponent_companies c ON
        c.id = p.companies_id
    WHERE parent_id=0 AND (availability_type=0 OR availability_type=1 OR availability_type=2) AND(active_type=0 OR active_type=1) AND sef_url != "" AND cf.subtype="mainimage" ORDER BY p.title ASC');

    //echo "Count: " . count($products);            
    /*for($x=0; $x< =10; $x++)
    {
        $products = $ps->find('all','parent_id=0 AND (availability_type=0 OR availability_type=1)','id DESC',10,$count);            
        $columns = ''; Multicam<U+0084>Â¢
        */
	//$counter='';
        $counter = array();
        $prodflipper[] = array();
        $prodflipper2[] = array();
        $prodflipper3[] = array();
        echo "\r\nPre Count: " . count($products). "\r\n";;  
        
        foreach ($products as $p1)
        {
            $prodflipper[$p1->id] = $p1;
        }
        echo "Flip Count 1: " . count($prodflipper). "\r\n";;  
        
        foreach ($prodflipper as $p2)
        {
            @$prodflipper2[$p2->sef_url] = $p2;
        }
        echo "Flip Count 2: " . count($prodflipper2). "\r\n";;  
        
        foreach ($prodflipper2 as $p3)
        {
            @$prodflipper3[$p3->model] = $p3;
        }
        echo "Flip Count 3: " . count($prodflipper3). "\r\n";;  
        
        foreach ($prodflipper3 as $prod)
        {            
            if (empty($prod->sef_url) || empty($prod->feed_title) || empty($prod->model) || empty($prod->feed_body)) continue;
            
            if(in_array($prod->sef_url,$counter) || isset($counter[$prod->id]))
            {
                echo "No no..." . $prod->id . "\r\n";
                continue;
            }
            else
                $counter[$prod->id]  = $prod->sef_url;
                
            $count++;
			//Get the google type categories, I used selectArraysBySql since a product can have more than 1 google taxonomy
			$google_types_res = $db->selectArraysBySql("SELECT exponent_google_product_types.title FROM exponent_google_product_types, exponent_product, exponent_product_storeCategories, exponent_google_product_types_storeCategories 
														WHERE exponent_google_product_types.id = google_product_types_id and exponent_google_product_types_storeCategories.storecategories_id = exponent_product_storeCategories.storecategories_id and 
														exponent_product.id = exponent_product_storeCategories.product_id and exponent_product.id = {$prod->id}");
			$google_types = '';

			if(count($google_types_res) > 0) {
				$google_types_array = array();
				foreach($google_types_res as $item) {
					$google_types_array[] = $item['title'];
				}
				$google_types = implode(' > ', $google_types_array);
			}
		
			
            $columns = '<item>'.chr(13).chr(10);

            $columns.='<title>';            
   	        $prod->feed_title = parseAndTrim(html_entity_decode(strip_tags($prod->feed_title)));
			$prod->feed_title = htmlspecialchars($prod->feed_title);
			$prod->feed_title = onlyreadables($prod->feed_title);
			
			$prod->google_product_type = parseAndTrim(html_entity_decode(strip_tags($prod->google_product_type)));
			$prod->google_product_type = htmlspecialchars($prod->google_product_type);
			$prod->google_product_type = onlyreadables($prod->google_product_type);
            //if(ctype_upper($prod->feed_title))
            //{
                //$prod->feed_title = ucwords(strtolower($prod->feed_title));
                //echo "Updated...:";
            //}
            //echo $prod->feed_title  .chr(13).chr(10);
            //continue; 
            
            $columns.= $prod->feed_title;
            $columns.='</title>'.chr(13).chr(10);

            $columns.='<link>';
            $columns.="http://www.militaryuniformsupply.com/".strip_tags($prod->sef_url);
            $columns.='</link>'.chr(13).chr(10);

            $columns.='<description>';
            $columns.= str_ireplace('&','<![CDATA[&]]>',$prod->feed_body);
            $columns.='</description>'.chr(13).chr(10);
            
            //eDebug($prod);
            //$prod->getAssociatedObjects();
              
            $columns.='<g:image_link>';
            $columns.= "http://www.militaryuniformsupply.com/".$prod->directory . $prod->filename;
            $columns.='</g:image_link>'.chr(13).chr(10);

            $columns.='<g:price>';
            $columns.= $prod->base_price;
            $columns.='</g:price>'.chr(13).chr(10);
            
            if($prod->use_special_price && !empty($prod->special_price))
            {
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

            if(!empty($prod->company))
            {
                $columns.='<g:brand>';
                $columns.=$prod->company;            
                $columns.='</g:brand>'.chr(13).chr(10);
            }
            
            if($prod->active_type == 0)
            {
                if ($prod->availability_type == 0)
                {
                    $columns.='<g:availability>in stock</g:availability>'.chr(13).chr(10);
                    $columns.='<g:quantity>99</g:quantity>'.chr(13).chr(10);
                }
                else if ($prod->availability_type == 1)
                {
                    $columns.='<g:availability>limited availability</g:availability>'.chr(13).chr(10);
                }
                else if ($prod->availability_type == 2 && $prod->quantity <= 0)
                {
                    $columns.='<g:availability>out of stock</g:availability>'.chr(13).chr(10);
                    $columns.='<g:quantity>0</g:quantity>'.chr(13).chr(10);
                }
                else if ($prod->availability_type == 2 && $prod->quantity > 0)
                {
                    $columns.='<g:availability>in stock</g:availability>'.chr(13).chr(10);
                    $columns.='<g:quantity>'.$prod->quantity. '</g:quantity>'.chr(13).chr(10);
                }
            }
            else if ($prod->active_type == 1)
            {
                $columns.='<g:availability>out of stock</g:availability>'.chr(13).chr(10);
                $columns.='<g:quantity>0</g:quantity>'.chr(13).chr(10);
            }
           
		    if(!empty($prod->google_product_type)) {
				$columns.='<g:product_type>';
				$columns.= parseAndTrim($prod->google_product_type);
				$columns.='</g:product_type>'.chr(13).chr(10);
			}
            //$breadcrumb = $prod->storeCategory[0]->getEcomSubcategories();
            //eDebug($breadcrumb,true);
            $columns.='</item>'.chr(13).chr(10);
      
            //size            
            // Write$somecontent to our opened file.
            if (fwrite($handle, $columns.chr(13).chr(10)) == FALSE) {
                $action_msg = "ER";
            }
        }
    //}
            
    $content='</channel>'.chr(13).chr(10);
    $content.='</rss>'.chr(13).chr(10);

    // Write$somecontent to our opened file.
    if (fwrite($handle, $content) == FALSE) {
        $action_msg = "ER";
    }
    $action_msg = "SC";
    fclose($handle);        
    echo "\r\nGenerated $count products in the feed.\r\n";       
    
    function parseAndTrim($str)
    {      
        $str = str_replace("<br>","",$str);
        $str = str_replace("</br>","",$str);
        $str = str_replace("<br/>","",$str);
        $str = str_replace("<br />","",$str);
        $str = str_replace("&quot;",'"',$str);
        $str = str_replace("&#39;","'",$str);
        $str = str_replace("&rsquo;","'",$str);
        $str = str_replace("&lsquo;","'",$str);        
        $str = str_replace("&#174;","",$str);
        $str = str_replace("–","-", $str);
        $str = str_replace("—","-", $str); 
        $str = str_replace("”", '"', $str);
        $str = str_replace("&rdquo;",'"', $str);
        $str = str_replace("“", '"', $str);
        $str = str_replace("&ldquo;",'"', $str);
        $str = str_replace("\r\n"," ",$str); 
        $str = str_replace("¼"," 1/4",$str);
        $str = str_replace("&#188;"," 1/4", $str);
        $str = str_replace("½"," 1/2",$str);
        $str = str_replace("&#189;"," 1/2",$str);
        $str = str_replace("¾"," 3/4",$str);
        $str = str_replace("&#190;"," 3/4",$str);
        $str = str_replace("™", "(TM)", $str);
        $str = str_replace("&trade;","(TM)", $str);
        $str = str_replace("&reg;","(R)", $str);
        $str = str_replace("®","(R)",$str);        
        $str = str_replace("&","&amp;",$str);      
		$str = str_replace(">","&gt;",$str);      		
        return trim($str);
    }     
    
    function pullTitle(&$prod)
    {
        $prod->feed_title = str_ireplace('***CLOSEOUT***','',$prod->title);
        $prod->feed_title = str_ireplace('CLEARANCE!!','',$prod->feed_title);
        $prod->feed_title = str_ireplace('<br>','',$prod->feed_title);
        $prod->feed_title = str_ireplace('- New Low Price Buy Here and Save','',$prod->feed_title);
        $prod->feed_title = str_ireplace('- New Low Price. Buy Here and Save.','',$prod->feed_title);
        $prod->feed_title = str_ireplace('- New Low Price.....Buy here and save','',$prod->feed_title);
        $prod->feed_title = str_ireplace('New Low Price Buy Here and Save','',$prod->feed_title);
        $prod->feed_title = str_ireplace('- New Low Price and Save','',$prod->feed_title);
        $prod->feed_title = str_ireplace('- Overstock Sale Buy Now and Save','',$prod->feed_title);
        $prod->feed_title = str_ireplace('- New Low Price','',$prod->feed_title);
        $prod->feed_title = str_ireplace('New Low Price','',$prod->feed_title);
        $prod->feed_title = str_ireplace('.......Buy here and save','',$prod->feed_title);
        $prod->feed_title = str_ireplace('Buy Here and Save','',$prod->feed_title);
        $prod->feed_title = str_ireplace('- SUPER SALE!','',$prod->feed_title);
        $prod->feed_title = trim($prod->feed_title);     
        return $prod;       
    }
	
	
	function onlyreadables($string) {
		for ($i=0;$i<strlen($string);$i++) {
			$chr = $string{$i};
			$ord = ord($chr);
			if ($ord<32 or $ord>126) {
			$chr = "~";
			$string{$i} = $chr;
			}
		}
		return str_replace("~", "", $string);
	}
?>