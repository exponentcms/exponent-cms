<?php

##################################################
#
# Copyright (c) 2004-2011 OIC Group, Inc.
# Written and Designed by James Hunt
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

if (!defined("EXPONENT")) exit("");

//exponent_flow_set(SYS_FLOW_PUBLIC,SYS_FLOW_ACTION);
// id & date_id set if single event, else
//   src & time (opt?) set for longer list/month, etc...
if (isset($_GET['date_id']) || isset($_GET['src'])) {
	$loc = exponent_core_makeLocation('calendarmodule',$_GET['src'],'');
	$locsql = "(location_data='".serialize($loc)."'";
	$config = $db->selectObject("calendarmodule_config","location_data='".serialize($loc)."'");
	if (isset($config->enable_ical) && $config->enable_ical) {
		if (!defined("SYS_DATETIME")) include_once(BASE."subsystems/datetime.php");

		if (isset($_GET['date_id'])) {  // get single specific event only
			$dates = array($db->selectObject("eventdate","id=".intval($_GET['date_id'])));
			$Filename = "Event-" . $_GET['date_id'];
		} else {
			if (!empty($config->aggregate)) {
				$locations = unserialize($config->aggregate);
				foreach ($locations as $source) {
					$tmploc = null;
					$tmploc->mod = 'calendarmodule';
					$tmploc->src = $source;
					$tmploc->int = '';
					$locsql .= " OR location_data='".serialize($tmploc)."'";
				}
			}
			$locsql .= ')';

			if (!function_exists("exponent_datetime_startOfDayTimestamp")) {
				if (!defined("SYS_DATETIME")) include_once(BASE."subsystems/datetime.php");               
			}		
			$day = exponent_datetime_startOfDayTimestamp(time());
			if (isset($config->rss_limit) && ($config->rss_limit > 0)) {
				$rsslimit = " AND date <= " . ($day + ($config->rss_limit * 86400));
			} else {
				$rsslimit = "";
			}

			$cats = $db->selectObjectsIndexedArray("category");
			$cats[0] = null;
			$cats[0]->name = 'None';
			
			if (isset($_GET['time'])) {
				$time = $_GET['time'];  // get current month's events
				$dates = $db->selectObjects("eventdate",$locsql." AND date >= ".exponent_datetime_startOfMonthTimestamp($time)." AND date <= ".exponent_datetime_endOfMonthTimestamp($time));
			} else {
				$time = date('U',strtotime("midnight -1 month",time()));  // previous month also
				$dates = $db->selectObjects("eventdate",$locsql." AND date >= ".exponent_datetime_startOfDayTimestamp($time).$rsslimit);
			}
			$title = $db->selectValue('container', 'title', "internal='".serialize($loc)."'");
			$Filename = preg_replace('/\s+/','',$title);  // without whitespace
		}	

		if (!function_exists("quoted_printable_encode")) {
			function quoted_printable_encode($input, $line_max = 75) { 
			   $hex = array('0','1','2','3','4','5','6','7', 
									  '8','9','A','B','C','D','E','F'); 
			   $lines = preg_split("/(?:\r\n|\r|\n)/", $input); 
			   $linebreak = "=0D=0A=\r\n"; 
			   /* the linebreak also counts as characters in the mime_qp_long_line 
				* rule of spam-assassin */ 
			   $line_max = $line_max - strlen($linebreak); 
			   $escape = "="; 
			   $output = ""; 
			   $cur_conv_line = ""; 
			   $length = 0; 
			   $whitespace_pos = 0; 
			   $addtl_chars = 0; 

			   // iterate lines 
			   for ($j=0; $j<count($lines); $j++) { 
				 $line = $lines[$j]; 
				 $linlen = strlen($line); 

				 // iterate chars 
				 for ($i = 0; $i < $linlen; $i++) { 
				   $c = substr($line, $i, 1); 
				   $dec = ord($c); 

				   $length++; 

				   if ($dec == 32) { 
					  // space occurring at end of line, need to encode 
					  if (($i == ($linlen - 1))) { 
						 $c = "=20"; 
						 $length += 2; 
					  } 

					  $addtl_chars = 0; 
					  $whitespace_pos = $i; 
				   } elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { 
					  $h2 = floor($dec/16); $h1 = floor($dec%16); 
					  $c = $escape . $hex["$h2"] . $hex["$h1"]; 
					  $length += 2; 
					  $addtl_chars += 2; 
				   } 

				   // length for wordwrap exceeded, get a newline into the text 
				   if ($length >= $line_max) { 
					 $cur_conv_line .= $c; 

					 // read only up to the whitespace for the current line 
					 $whitesp_diff = $i - $whitespace_pos + $addtl_chars; 

					/* the text after the whitespace will have to be read 
					 * again ( + any additional characters that came into 
					 * existence as a result of the encoding process after the whitespace) 
					 * 
					 * Also, do not start at 0, if there was *no* whitespace in 
					 * the whole line */ 
					 if (($i + $addtl_chars) > $whitesp_diff) { 
						$output .= substr($cur_conv_line, 0, (strlen($cur_conv_line) - 
									   $whitesp_diff)) . $linebreak; 
						$i =  $i - $whitesp_diff + $addtl_chars; 
					  } else { 
						$output .= $cur_conv_line . $linebreak; 
					  } 

					$cur_conv_line = ""; 
					$length = 0; 
					$whitespace_pos = 0; 
				  } else { 
					// length for wordwrap not reached, continue reading 
					$cur_conv_line .= $c; 
				  } 
				} // end of for 

				$length = 0; 
				$whitespace_pos = 0; 
				$output .= $cur_conv_line; 
				$cur_conv_line = ""; 

				if ($j<=count($lines)-1) { 
				  $output .= $linebreak; 
				} 
			  } // end for 

			  return trim($output); 
			} // end quoted_printable_encode 
		}
		
	//	$tz = "America/New_York";
		$tz = DISPLAY_DEFAULT_TIMEZONE;
		$msg = "BEGIN:VCALENDAR\n";
		$msg .= "VERSION:2.0\n";  // version for iCalendar files vs vCalendar files
		$msg .= "CALSCALE:GREGORIAN\n";
		$msg .= "METHOD: PUBLISH\n";  
		$msg .= "PRODID:<-//ExponentCMS//EN>\n";
		if (isset($config->rss_cachetime) && ($config->rss_cachetime > 0)) {
			$msg .= "X-PUBLISHED-TTL:PT".$config->rss_cachetime."M\n";
		}
		$msg .= "X-WR-CALNAME:$Filename\n";
		
		$items = calendarmodule::_getEventsForDates($dates);

		for ($i = 0; $i < count($items); $i++) {

			// Convert events stored in local time to GMT
			$eventstart = new DateTime(date('r',$items[$i]->eventstart),new DateTimeZone($tz));
			$eventstart->setTimezone(new DateTimeZone('GMT')); 
			$eventend = new DateTime(date('r',$items[$i]->eventend),new DateTimeZone($tz));
			$eventend->setTimezone(new DateTimeZone('GMT')); 
			if ($items[$i]->is_allday) {
				$dtstart = "DTSTART;VALUE=DATE:" . date("Ymd", $items[$i]->eventstart) . "\n";			
				$dtend = "DTEND;VALUE=DATE:" . date("Ymd", strtotime("midnight +1 day",$items[$i]->eventstart)) . "\n";			
			} else {
				$dtstart = "DTSTART;VALUE=DATE-TIME:" . $eventstart->format("Ymd\THi00") . "Z\n";
				if($items[$i]->eventend) {
					$dtend = "DTEND;VALUE=DATE-TIME:" . $eventend->format("Ymd\THi00") . "Z\n";
				} else {
					$dtend = "DTEND;VALUE=DATE-TIME:" . $eventstart->format("Ymd\THi00") . "Z\n";
				}
			}

			if (!isset($_GET['style'])) {
				// it's going to Outlook so remove all formatting from body text
		//		$body = chop(strip_tags(str_replace(array("<br />","<br>","br/>","\r","\n"),"\r\n",$items[$i]->body)));
		//		$body = chop(strip_tags(str_replace(array("<br />","<br>","br/>"),"\r",$items[$i]->body)));
		//		$body = str_replace(array("\r","\n"), "=0D=0A=", $body);
				$body = chop(strip_tags(str_replace(array("<br />","<br>","br/>","</p>"),"\n",$items[$i]->body)));
				$body = str_replace(array("\r"),"",$body);
				$body = str_replace(array("&#160;")," ",$body);
				$body = convert_smart_quotes($body);
				$body = quoted_printable_encode($body);
		//		$body = str_replace(array("\n"), "=0D=0A", $body);

				// $body = chop(strip_tags(str_replace(array("<br />","<br>","br/>"),"\r",$items[$i]->body)));
				// $body = wordwrap($body);
				// $body = str_replace("\n","\n  ",$body);

		//		$body = chop(strip_tags(str_replace(array("<br />","<br>","br/>"),"\r",$items[$i]->body)));
		//		$body = chop(strip_tags(str_replace(array("<br />","<br>","br/>"),"\n",$items[$i]->body)));
		//		$body = str_replace(array("\r","\n"), "=0D=0A=", $body);
		//		$body = str_replace(array("\r"), "=0D=0A=", $body);
		//		$body = str_replace(array("\r","\n"), "\r\n", $body);
				
			} elseif ($_GET['style'] == "g") {
				// It's going to Google (doesn't like quoted-printable, but likes html breaks)
				$body = $items[$i]->body;
				$body = chop(strip_tags(str_replace(array("<br />","<br>","br/>","</p>"),"\n",$items[$i]->body)));
//				$body = chop(strip_tags($items[$i]->body,"<br><p>"));
				$body = str_replace(array("\r"),"",$body);
				$body = str_replace(array("&#160;")," ",$body);
				$body = convert_smart_quotes($body);
				$body = str_replace(array("\n"),"<br />",$body);
			} else {
				// It's going elsewhere (doesn't like quoted-printable)
				$body = $items[$i]->body;
				$body = chop(strip_tags(str_replace(array("<br />","<br>","br/>","</p>"),"\n",$items[$i]->body)));
//				$body = chop(strip_tags($items[$i]->body,"<br><p>"));
				$body = str_replace(array("\r"),"",$body);
				$body = str_replace(array("&#160;")," ",$body);
				$body = convert_smart_quotes($body);
				$body = str_replace(array("\n")," -- ",$body);
			}
			$title = $items[$i]->title;

			$msg .= "BEGIN:VEVENT\n";
			$msg .= $dtstart . $dtend;
			$msg .= "UID:" . $items[$i]->eventdate->id . "\n";
			$msg .= "DTSTAMP:" . date("Ymd\THis", time()) . "Z\n";
			if($title) { $msg .= "SUMMARY:$title\n";}
//			if($body) { $msg .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:".quoted_printable_encode($body)."\n";}
			if($body) { $msg .= "DESCRIPTION;ENCODING=QUOTED-PRINTABLE:".$body."\n";}
		//	if($link_url) { $msg .= "URL: $link_url\n";}
			$msg .= "CATEGORIES:".$cats[$items[$i]->category_id]->name."\n";
			$msg .= "END:VEVENT\n";      
		}
		$msg .= "END:VCALENDAR";			

		// Kick it out as a file download
		ob_end_clean();

	//	$mime_type = (EXPONENT_USER_BROWSER == 'IE' || EXPONENT_USER_BROWSER == 'OPERA') ? 'application/octet-stream;' : "text/x-vCalendar";
	//	$mime_type = "text/x-vCalendar";
		$mime_type = "text/Calendar";
		header('Content-Type: ' . $mime_type);
		header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
		header("Content-length: ".strlen($msg));
		header('Content-Transfer-Encoding: binary');
		header('Content-Encoding:');
	//	header("Content-Disposition: inline; filename=".$Filename.".ics");
		header('Content-Disposition: attachment; filename="' . $Filename . '.ics"');
		// IE need specific headers
	//	if (EXPONENT_USER_BROWSER == 'IE') {
			header('Cache-Control: no-cache, must-revalidate');
			header('Pragma: public');
			header('Vary: User-Agent');
	//	} else {
			header('Pragma: no-cache');
	//	}
		echo $msg;
		exit();
	} else {
		echo SITE_404_HTML;
	}
} else {
	echo SITE_404_HTML;
}

function convert_smart_quotes($str) {
	 // $search = array(chr(145),
					 // chr(146),
					 // chr(147),
					 // chr(148),
					 // chr(150),
					 // chr(151),
					 // chr(133),
					 // chr(149));
	 // $replace = array("'z",
					  // "'z",
					  // "\"z",
					  // "\"z",
					  // "-z",
					  // "-z",
					  // "...",
					  // "&bull;");
	 // return str_replace($search, $replace, $str);

	$find[] = '“';  // left side double smart quote
	$find[] = '”';  // right side double smart quote
	$find[] = '‘';  // left side single smart quote
	$find[] = '’';  // right side single smart quote
	$find[] = '…';  // elipsis
	$find[] = '—';  // em dash
	$find[] = '–';  // en dash

	$replace[] = '"';
	$replace[] = '"';
	$replace[] = "'";
	$replace[] = "'";
	$replace[] = "...";
	$replace[] = "-";
	$replace[] = "-";

	return str_replace($find, $replace, $str);
}
	 
?>
