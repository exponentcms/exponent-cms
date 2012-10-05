<?php
/***************************************************************************

FeedCreator class v1.8.0dev-iTunes
originally (c) Kai Blankenhorn
www.bitfolge.de
kaib@bitfolge.de
v1.3 work by Scott Reynen (scott@randomchaos.com) and Kai Blankenhorn
v1.5 OPML support by Dirk Clemens
v1.7.2-mod on-the-fly feed generation by Fabian Wolf (info@f2w.de)
v1.7.2-ppt ATOM 1.0 support by Mohammad Hafiz bin Ismail (mypapit@gmail.com)

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA

****************************************************************************

***************************************************************************
*          A little setup                                                 *
**************************************************************************/
// Added for Exponent
// your local timezone, set to "" to disable or for GMT
define("TIME_ZONE",date('O',time()));

/**
 * Version string.
 **/
// define("FEEDCREATOR_VERSION", "FeedCreator 1.8.0dev-iTunes");
define("FEEDCREATOR_VERSION", 'Exponent Content Management System - '.expVersion::getVersion(true));

/**
 * An Enclosure is a part of an Item
 *
 * @author Steven Pothoven <steven@pothoven.net>
 * @since 1.7.2-podcast
 */
class Enclosure {
   /**
    * Attributes of an enclosure
    */
   var $url, $length, $type = "audio/mpeg"; 
}

/**
 * iTunes extensions to RSS 2.0
 *
 * @author Steven Pothoven <steven@pothoven.net>
 * @since 1.7.2-iTunes
 */
class iTunes {
   /**
    * This tag can only be populated using iTunes specific categories.
    */
   var $category, $subcategory;

   /**
    * This tag should be used to note whether or not your Podcast contains explicit material.
    * There are 2 possible values for this tag: Yes or No
    */
   var $explicit;

   /*
    * At the Channel level, this tag is a short description that provides general information about the Podcast. It will appear next to your Podcast as users browse through listings of Podcasts
    * At the Item level, this tag is a short description that provides specific information for each episode.
    * Limited to 255 characters or less, plain text, no HTML
    */
   var $subtitle;

   /*
    * At the Channel level, this tag is a long description that will appear next to your Podcast cover art when a user selects your Podcast.
    * At the Item level, this tag is a long description that will be displayed in an expanded window when users click on an episode.
    * Limited to 4000 characters or less, plain text, no HTML
    */
   var $summary;

   /*
    * At the Channel level this tag contains the name of the person or company that is most widely attributed to publishing the Podcast and will be displayed immediately underneath the title of the Podcast.
    * If applicable, at the item level, this tag can contain information about the person(s) featured on a specific episode.
    */
   var $author;

   /*
    * This tag is for informational purposes only and will allow users to know the duration prior to download
    * The tag is formatted: HH:MM:SS
    */
   var $duration;

   /*
    * This tag allows users to search on text keywords
    * Limited to 255 characters or less, plain text, no HTML, words must be separated by spaces
    */
   var $keywords;

   /*
    * This tag contains the e-mail address that will be used to contact the owner of the Podcast for communication specifically about their Podcast on iTunes.
    * Required element specifying the email address of the owner.
    */
   var $owner_email;

   /*
    * Optional element specifying the name of the owner.
    */
   var $owner_name;

   /*
    * This tag specifies the artwork for the Channel and Item(s). This artwork can be larger than the maximum allowed by RSS.
    * Preferred size: 300 x 300 at 72 dpi
    * Minimum size: 170 pixels x 170 pixels square at 72 dpi
    * Format: JPG, PNG, uncompressed
    */
   var $image;

   /*
    * This tag is used to block a podcast or an episode within a podcast from being posted to iTunes. Only use this tag when you want a podcast or an episode to appear within the iTunes podcast directory.
    */
   var $block;
}

/**
 * A FeedItem is a part of a FeedCreator feed.
 *
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.3
 */
class FeedItem extends HtmlDescribable {
	/**
	 * Mandatory attributes of an item.
	 */
	var $title, $description, $link;
	
	/**
	 * Optional attributes of an item.
	 */
	var $author, $authorEmail, $authorURL,$image, $category = Array(), $comments, $commentsRSS, $commentsCount, $guid, $source, $creator, $contributor;

    /**
     * Support for iTunes
     */
    var $itunes;
	
	/**
	 * Publishing date of an item. May be in one of the following formats:
	 *
	 *	RFC 822:
	 *	"Mon, 20 Jan 03 18:05:41 +0400"
	 *	"20 Jan 03 18:05:41 +0000"
	 *
	 *	ISO 8601:
	 *	"2003-01-20T18:05:41+04:00"
	 *
	 *	Unix:
	 *	1043082341
	 */
	var $date;
	
	/**
	 * Add <enclosure> element tag RSS 2.0, supported by ATOM 1.0 too
	 * modified by : Mohammad Hafiz bin Ismail (mypapit@gmail.com)
	 *
	 *
	 * display :
	 * <enclosure length="17691" url="http://something.com/picture.jpg" type="image/jpeg" />
	 *
	 */
	var $enclosure;

	/**
	 * Any additional elements to include as an associated array. All $key => $value pairs
	 * will be included unencoded in the feed item in the form
	 *     <$key>$value</$key>
	 * Again: No encoding will be used! This means you can invalidate or enhance the feed
	 * if $value contains markup. This may be abused to embed tags not implemented by
	 * the FeedCreator class used.
	 */
	var $additionalElements = Array();

	// on hold
	// var $source;
}

class EnclosureItem extends HtmlDescribable {
	/*
	*
	* core variables
	*
	**/
	var $url,$length,$type;

	/*
	*
	* supported by ATOM 1.0 only
	*
	*/

	var $language, $title;
	/*
	* For use with another extension like Yahoo mRSS
	* Warning :
	* These variables might not show up in
	* later release / not finalize yet!
	*
	*
	* var $width, $height, $title, $description, $keywords, $thumburl;
	*/

	var $additionalElements = Array();

}

/**
 * An FeedImage may be added to a FeedCreator feed.
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.3
 */
class FeedImage extends HtmlDescribable {
	/**
	 * Mandatory attributes of an image.
	 */
	var $title, $url, $link;
	
	/**
	 * Optional attributes of an image.
	 */
	var $width, $height, $description;
}

/**
 * An HtmlDescribable is an item within a feed that can have a description that may
 * include HTML markup.
 */
class HtmlDescribable {
	/**
	 * Indicates whether the description field should be rendered in HTML.
	 */
	var $descriptionHtmlSyndicated;
	
	/**
	 * Indicates whether and to how many characters a description should be truncated.
	 */
	var $descriptionTruncSize;
	
	/**
	 * Returns a formatted description field, depending on descriptionHtmlSyndicated and
	 * $descriptionTruncSize properties
	 * @return    string    the formatted description  
	 */
	function getDescription() {
		$descriptionField = new FeedHtmlField($this->description);
		$descriptionField->syndicateHtml = $this->descriptionHtmlSyndicated;
		$descriptionField->truncSize = $this->descriptionTruncSize;
		return $descriptionField->output();
	}

}

/**
 * An FeedHtmlField describes and generates
 * a feed, item or image html field (probably a description). Output is 
 * generated based on $truncSize, $syndicateHtml properties.
 * @author Pascal Van Hecke <feedcreator.class.php@vanhecke.info>
 * @version 1.6
 */
class FeedHtmlField {
	/**
	 * Mandatory attributes of a FeedHtmlField.
	 */
	var $rawFieldContent;
	
	/**
	 * Optional attributes of a FeedHtmlField.
	 * 
	 */
	var $truncSize, $syndicateHtml;

	/**
	 * Creates a new instance of FeedHtmlField.
	 * @param $parFieldContent
	 *
	 * @internal param $string : if given, sets the rawFieldContent property
	 */
	function FeedHtmlField($parFieldContent) {
		if ($parFieldContent) {
			$this->rawFieldContent = $parFieldContent;
		}
	}

	/**
	 * Creates the right output, depending on $truncSize, $syndicateHtml properties.
	 * @return string    the formatted field
	 */
	function output() {
		// when field available and syndicated in html we assume 
		// - valid html in $rawFieldContent and we enclose in CDATA tags
		// - no truncation (truncating risks producing invalid html)
		if (!$this->rawFieldContent) {
			$result = "";
		}	elseif ($this->syndicateHtml) {
			$result = "<![CDATA[".$this->rawFieldContent."]]>";
		} else {
			if ($this->truncSize and is_int($this->truncSize)) {
				$result = FeedCreator::iTrunc(htmlspecialchars($this->rawFieldContent),$this->truncSize);
			} else {
				$result = htmlspecialchars($this->rawFieldContent);
			}
		}
		return $result;
	}

}

/**
 * UniversalFeedCreator lets you choose during runtime which
 * format to build.
 * For general usage of a feed class, see the FeedCreator class
 * below or the example above.
 *
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class UniversalFeedCreator extends FeedCreator {
	var $_feed;
	
	function _setMIME() {
		//switch (strtoupper($format)) {
		header('Content-type: ' . $this->contentType .'; charset=' . $this->encoding, true);
	}

	function _setFormat($format) {
		switch (strtoupper($format)) {
			
			case "2.0":
			case "RSS": //added 8 Jan 2007
				// fall through
			case "RSS2.0":
				$this->_feed = new RSSCreator20();
				break;
			
			case "1.0":
				// fall through
			case "RSS1.0":
				$this->_feed = new RSSCreator10();
				break;
			
			case "0.91":
				// fall through
			case "RSS0.91":
				$this->_feed = new RSSCreator091();
				break;
			
			case "PIE0.1":
				$this->_feed = new PIECreator01();
				break;
			
			case "MBOX":
				$this->_feed = new MBOXCreator();
				break;
			
			case "OPML":
				$this->_feed = new OPMLCreator();
				break;
				
			case "ATOM":
				// fall through: always the latest ATOM version
			case "ATOM1.0":
				$this->_feed = new AtomCreator10();
				break;

			case "ATOM0.3":
				$this->_feed = new AtomCreator03();
				break;
				
			case "HTML":
				$this->_feed = new HTMLCreator();
				break;
			
			case "JS":
				// fall through
			case "JAVASCRIPT":
				$this->_feed = new JSCreator();
				break;

			case "PODCAST":
				$this->_feed = new PodcastCreator();
				break;			
			default:
				$this->_feed = new RSSCreator091();
				break;
		}
        
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			// prevent overwriting of properties "contentType", "encoding"; do not copy "_feed" itself
			if (!in_array($key, array("_feed", "contentType", "encoding"))) {
				$this->_feed->{$key} = $this->{$key};
			}
		}
	}

	/**
	 * Creates a syndication feed based on the items previously added.
	 *
	 * @see        FeedCreator::addItem()
	 * @param string $format
	 *
	 * @internal param \format $string format the feed should comply to. Valid values are:
	 *			"PIE0.1", "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM0.3", "HTML", "JS"
	 * @return    string    the contents of the feed.
	 */
	function createFeed($format = "RSS0.91") {
		$this->_setFormat($format);
		return $this->_feed->createFeed();
	}

	/**
	 * Saves this feed as a file on the local disk. After the file is saved, an HTTP redirect
	 * header may be sent to redirect the use to the newly created file.
	 * @since 1.4
	 *
	 * @param string $format
	 * @param string $filename
	 * @param bool $displayContents
	 *
	 * @internal param \format $string format the feed should comply to. Valid values are:
	 *			"PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM", "ATOM0.3", "HTML", "JS"
	 *
	 * @internal param \filename $string optional	the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
	 *
	 * @internal param \displayContents $boolean optional	send the content of the file or not. If true, the file will be sent in the body of the response.
	 */
	function saveFeed($format="RSS0.91", $filename="", $displayContents=true) {
		$this->_setFormat($format);
		$this->_feed->saveFeed($filename, $displayContents);
	}

    /**
     * Turns on caching and checks if there is a recent version of this feed in the cache.
     * If there is, an HTTP redirect header is sent.
     * To effectively use caching, you should create the FeedCreator object and call this method
     * before anything else, especially before you do the time consuming task to build the feed
     * (web fetching, for example).
     *
     * @param string $format format the feed should comply to. Valid values are:
     *       "PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM0.3".
     * @param string $filename optional the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
     * @param int    $timeout optional the timeout in seconds before a cached version is refreshed (defaults to 3600 = 1 hour)
     */
   function useCached($format="RSS0.91", $filename="", $timeout=3600) {
      $this->_setFormat($format);
      $this->_feed->useCached($filename, $timeout);
   }

    /**
     * Outputs feed to the browser - needed for on-the-fly feed generation (like it is done in WordPress, etc.)
     *
     * @param string $format format the feed should comply to. Valid values are:
     *   "PIE0.1" (deprecated), "mbox", "RSS0.91", "RSS1.0", "RSS2.0", "OPML", "ATOM0.3".
     *
     * @return void
     */
   function outputFeed($format='RSS0.91') {
        $this->_setFormat($format);
        $this->_setMIME($format);
        $this->_feed->outputFeed();
    }

}

/**
 * FeedCreator is the abstract base implementation for concrete
 * implementations that implement a specific format of syndication.
 *
 * @abstract
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 * @since 1.4
 */
class FeedCreator extends HtmlDescribable {

	/**
	 * Mandatory attributes of a feed.
	 */
	var $title, $description, $link;

	/**
	 * Optional attributes of a feed.
	 */
	var $syndicationURL, $image, $language, $copyright, $pubDate, $lastBuildDate, $editor, $editorEmail, $webmaster, $category, $docs, $ttl, $rating, $skipHours, $skipDays;

	/**
	* The url of the external xsl stylesheet used to format the naked rss feed.
	* Ignored in the output when empty.
	*/
	var $xslStyleSheet = "";

    /**
	* The url of the external css stylesheet used to format the naked syndication feed.
	* Ignored in the output when empty.
     */
	var $cssStyleSheet = "";
	
	/**
	 * @access private
	 */
	var $items = Array();

	/**
	 * This feed's MIME content type.
	 * @since 1.4
	 * @access private
	 */
	var $contentType = "application/xml";

	/**
	 * This feed's character encoding.
	 * @since 1.6.1
	 *
	 * var $encoding = "ISO-8859-1"; //original :p
	 */
	var $encoding = "utf-8";

	/*
	 * Generator string
	 *
	 */

	 var $generator = "info@mypapit.net";

	/**
	 * Any additional elements to include as an associated array. All $key => $value pairs
	 * will be included unencoded in the feed in the form
	 *     <$key>$value</$key>
	 * Again: No encoding will be used! This means you can invalidate or enhance the feed
	 * if $value contains markup. This may be abused to embed tags not implemented by
	 * the FeedCreator class used.
	 */
	var $additionalElements = Array();

	/**
	 * Adds an FeedItem to the feed.
	 *
	 * @param $item
	 *
	 * @internal param \FeedItem $object $item The FeedItem to add to the feed.
	 * @access public
	 */
	function addItem($item) {
		$this->items[] = $item;
	}

	/**
	 *
	 *
	 *
	 **/
	 function version() {

	 	return FEEDCREATOR_VERSION." (".$this->generator.")";
	 }

	/**
	 * Truncates a string to a certain length at the most sensible point.
	 * First, if there's a '.' character near the end of the string, the string is truncated after this character.
	 * If there is no '.', the string is truncated after the last ' ' character.
	 * If the string is truncated, " ..." is appended.
	 * If the string is already shorter than $length, it is returned unchanged.
	 *
	 * @static
	 * @param string    string A string to be truncated.
	 * @param $length
	 *
	 * @internal param \length $int the maximum length the string should be truncated to
	 * @return string    the truncated string
	 */
	public static function  iTrunc($string, $length) {
		if (strlen($string)<=$length) {
			return $string;
		}
		
		$pos = strrpos($string,".");
		if ($pos>=$length-4) {
			$string = substr($string,0,$length-4);
			$pos = strrpos($string,".");
		}
		if ($pos>=$length*0.4) {
			return substr($string,0,$pos+1)." ...";
		}
		
		$pos = strrpos($string," ");
		if ($pos>=$length-4) {
			$string = substr($string,0,$length-4);
			$pos = strrpos($string," ");
		}
		if ($pos>=$length*0.4) {
			return substr($string,0,$pos)." ...";
		}
		
		return substr($string,0,$length-4)." ...";
			
	}

	/**
	 * Creates a comment indicating the generator of this feed.
	 * The format of this comment seems to be recognized by
	 * Syndic8.com.
	 * @return string
	 */
	function _createGeneratorComment() {
		return "<!-- generator=\"".$this->version()."\" -->\n";
	}

    /**
     * Creates a string containing all additional elements specified in
     * $additionalElements.
     * @param array $elements an associative array containing key => value pairs
     * @param string $indentString a string that will be inserted before every generated line
     * @return    string    the XML tags corresponding to $additionalElements
     */
	function _createAdditionalElements($elements, $indentString="") {
		$ae = "";
		if (is_array($elements)) {
			foreach($elements AS $key => $value) {
				$ae.= $indentString."<$key>$value</$key>\n";
			}
		}
		return $ae;
	}
	
	function _createStylesheetReferences() {
		$xml = "";
		if ($this->cssStyleSheet) $xml .= "<?xml-stylesheet href=\"".$this->cssStyleSheet."\" type=\"text/css\"?>\n";
		if ($this->xslStyleSheet) $xml .= "<?xml-stylesheet href=\"".$this->xslStyleSheet."\" type=\"text/xsl\"?>\n";
		return $xml;
	}

	/**
	 * Builds the feed's text.
	 * @abstract
	 * @return    string    the feed's complete text 
	 */
	function createFeed() {
	}
	
	/**
	 * Generate a filename for the feed cache file. The result will be $_SERVER["PHP_SELF"] with the extension changed to .xml.
	 * For example:
	 * 
	 * echo $_SERVER["PHP_SELF"]."\n";
	 * echo FeedCreator::_generateFilename();
	 * 
	 * would produce:
	 * 
	 * /rss/latestnews.php
	 * latestnews.xml
	 *
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return substr($fileInfo["basename"],0,-(strlen($fileInfo["extension"])+1)).".xml";
	}

	/**
	 * @since 1.4
	 * @access private
	 * @param $filename
	 */
	function _redirect($filename) {
		// attention, heavily-commented-out-area
		
		// maybe use this in addition to file time checking
		//Header("Expires: ".date("r",time()+$this->_timeout));
		
		/* no caching at all, doesn't seem to work as good:
		Header("Cache-Control: no-cache");
		Header("Pragma: no-cache");
		*/
		
		// HTTP redirect, some feed readers' simple HTTP implementations don't follow it
		//Header("Location: ".$filename);

		Header("Content-Type: ".$this->contentType."; charset=".$this->encoding."; filename=".basename($filename));
		Header("Content-Disposition: inline; filename=".basename($filename));
		readfile($filename, "r");
		die();
	}

    /**
     * Turns on caching and checks if there is a recent version of this feed in the cache.
     * If there is, an HTTP redirect header is sent.
     * To effectively use caching, you should create the FeedCreator object and call this method
     * before anything else, especially before you do the time consuming task to build the feed
     * (web fetching, for example).
     * @since 1.4
     * @param string $filename the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
     * @param int $timeout     the timeout in seconds before a cached version is refreshed (defaults to 3600 = 1 hour)
     *
     */
	function useCached($filename="", $timeout=3600) {
		$this->_timeout = $timeout;
		if ($filename=="") {
			$filename = $this->_generateFilename();
		}
		if (file_exists($filename) AND (time()-filemtime($filename) < $timeout)) {
			$this->_redirect($filename);
		}
	}

    /**
     * Saves this feed as a file on the local disk. After the file is saved, a redirect
     * header may be sent to redirect the user to the newly created file.
     * @since 1.4
     *
     * @param string $filename the filename where a recent version of the feed is saved. If not specified, the filename is $_SERVER["PHP_SELF"] with the extension changed to .xml (see _generateFilename()).
     * @param bool $displayContents
     *
     * @internal param bool $redirect optional    send an HTTP redirect header or not. If true, the user will be automatically redirected to the created file.
     */
	function saveFeed($filename="", $displayContents=true) {
		if ($filename=="") {
			$filename = $this->_generateFilename();
		}
		$feedFile = fopen($filename, "w+");
		if ($feedFile) {
			fputs($feedFile,$this->createFeed());
			fclose($feedFile);
			if ($displayContents) {
				$this->_redirect($filename);
			}
		} else {
			echo "<br /><strong>Error creating feed file, please check write permissions.</strong><br />";
		}
	}
	
	/**
	 * Outputs this feed directly to the browser - for on-the-fly feed generation
	 * @since 1.7.2-mod
	 *
	 * still missing: proper header output - currently you have to add it manually
	 */
	function outputFeed() {
		echo $this->createFeed();
}

	function setEncoding($encoding="utf-8") {
		$this->encoding = $encoding;

	}

}

/**
 * FeedDate is an internal class that stores a date for a feed or feed item.
 * Usually, you won't need to use this.
 */
class FeedDate {
	var $unix;
	
	/**
	 * Creates a new instance of FeedDate representing a given date.
	 * Accepts RFC 822, ISO 8601 date formats as well as unix time stamps.
	 * @param mixed $dateString optional the date this FeedDate will represent. If not specified, the current date and time is used.
	 */
	function FeedDate($dateString="") {
		if ($dateString=="") $dateString = date("r");
		
		if (is_numeric($dateString)) {
			$this->unix = $dateString;
			return;
		}
		if (preg_match("~(?:(?:Mon|Tue|Wed|Thu|Fri|Sat|Sun),\\s+)?(\\d{1,2})\\s+([a-zA-Z]{3})\\s+(\\d{4})\\s+(\\d{2}):(\\d{2}):(\\d{2})\\s+(.*)~",$dateString,$matches)) {
			$months = Array("Jan"=>1,"Feb"=>2,"Mar"=>3,"Apr"=>4,"May"=>5,"Jun"=>6,"Jul"=>7,"Aug"=>8,"Sep"=>9,"Oct"=>10,"Nov"=>11,"Dec"=>12);
			$this->unix = mktime($matches[4],$matches[5],$matches[6],$months[$matches[2]],$matches[1],$matches[3]);
			if (substr($matches[7],0,1)=='+' OR substr($matches[7],0,1)=='-') {
				$tzOffset = (substr($matches[7],0,3) * 60 . substr($matches[7],-2)) * 60;
			} else {
				if (strlen($matches[7])==1) {
					$oneHour = 3600;
					$ord = ord($matches[7]);
					if ($ord < ord("M")) {
						$tzOffset = (ord("A") - $ord - 1) * $oneHour;
					} elseif ($ord >= ord("M") AND $matches[7]!="Z") {
						$tzOffset = ($ord - ord("M")) * $oneHour;
					} elseif ($matches[7]=="Z") {
						$tzOffset = 0;
					}
				}
				switch ($matches[7]) {
					case "UT":
					case "GMT":	$tzOffset = 0;
				}
			}
			$this->unix += $tzOffset;
			return;
		}
		if (preg_match("~(\\d{4})-(\\d{2})-(\\d{2})T(\\d{2}):(\\d{2}):(\\d{2})(.*)~",$dateString,$matches)) {
			$this->unix = mktime($matches[4],$matches[5],$matches[6],$matches[2],$matches[3],$matches[1]);
			if (substr($matches[7],0,1)=='+' OR substr($matches[7],0,1)=='-') {
				$tzOffset = (substr($matches[7],0,3) * 60 . substr($matches[7],-2)) * 60;
			} else {
				if ($matches[7]=="Z") {
					$tzOffset = 0;
				}
			}
			$this->unix += $tzOffset;
			return;
		}
		$this->unix = 0;
	}

	/**
	 * Gets the date stored in this FeedDate as an RFC 822 date.
	 *
	 * @return string a date in RFC 822 format
	 */
	function rfc822() {
		//return gmdate("r",$this->unix);
		$date = gmdate("D, d M Y H:i:s", $this->unix);

		if (TIME_ZONE!="") {
			$date .= " ".str_replace(":","",TIME_ZONE);
		} else {
			$date .= " ".str_replace(":","","GMT");
		}
		return $date;
	}
	
	/**
	 * Gets the date stored in this FeedDate as an ISO 8601 date.
	 *
	 * @return string a date in ISO 8601 format
	 */
	function iso8601() {
		$date = gmdate("Y-m-d\TH:i:sO",$this->unix);
		$date = substr($date,0,22) . ':' . substr($date,-2);
		if (TIME_ZONE!="") $date = str_replace("+00:00",TIME_ZONE,$date);
		return $date;
	}
	
	/**
	 * Gets the date stored in this FeedDate as unix time stamp.
	 *
	 * @return string a date as a unix time stamp
	 */
	function unix() {
		return $this->unix;
	}
}

/**
 * RSSCreator10 is a FeedCreator that implements RDF Site Summary (RSS) 1.0.
 *
 * @see http://www.purl.org/rss/1.0/
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class RSSCreator10 extends FeedCreator {

	/**
	 * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
	 * The feed will contain all items previously added in the same order.
	 * @return    string    the feed's complete text 
	 */
	function createFeed() {     
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		if ($this->cssStyleSheet=="") {
			$this->cssStyleSheet = "http://www.w3.org/2000/08/w3c-synd/style.css";
		}
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<rdf:RDF\n";
		$feed.= "    xmlns=\"http://purl.org/rss/1.0/\"\n";
		$feed.= "    xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n"; 
		$feed.= "    xmlns:slash=\"http://purl.org/rss/1.0/modules/slash/\"\n";
		$feed.= "    xmlns:dc=\"http://purl.org/dc/elements/1.1/\">\n";
		$feed.= "    <channel rdf:about=\"".$this->syndicationURL."\">\n";
		$feed.= "        <title>".htmlspecialchars($this->title)."</title>\n";
		$feed.= "        <description>".htmlspecialchars($this->description)."</description>\n";
		$feed.= "        <link>".$this->link."</link>\n";
		if ($this->image!=null) {
			$feed.= "        <image rdf:resource=\"".$this->image->url."\" />\n";
		}
		$now = new FeedDate();
		$feed.= "       <dc:date>".htmlspecialchars($now->iso8601())."</dc:date>\n";
		$feed.= "        <items>\n";
		$feed.= "            <rdf:Seq>\n";
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "                <rdf:li rdf:resource=\"".htmlspecialchars($this->items[$i]->link)."\"/>\n";
		}
		$feed.= "            </rdf:Seq>\n";
		$feed.= "        </items>\n";
		$feed.= "    </channel>\n";
		if ($this->image!=null) {
			$feed.= "    <image rdf:about=\"".$this->image->url."\">\n";
			$feed.= "        <title>".$this->image->title."</title>\n";
			$feed.= "        <link>".$this->image->link."</link>\n";
			$feed.= "        <url>".$this->image->url."</url>\n";
			$feed.= "    </image>\n";
		}
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");
		
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "    <item rdf:about=\"".htmlspecialchars($this->items[$i]->link)."\">\n";
			//$feed.= "        <dc:type>Posting</dc:type>\n";
			$feed.= "        <dc:format>text/html</dc:format>\n";
			if ($this->items[$i]->date!=null) {
				$itemDate = new FeedDate($this->items[$i]->date);
				$feed.= "        <dc:date>".htmlspecialchars($itemDate->iso8601())."</dc:date>\n";
			}
			if (!empty($this->items[$i]->source)) {
				$feed.= "        <dc:source>".htmlspecialchars($this->items[$i]->source)."</dc:source>\n";
			}
			if (!empty($this->items[$i]->author)) {
				$feed.= "        <dc:creator>".htmlspecialchars($this->items[$i]->author)."</dc:creator>\n";
			}
			$feed.= "        <title>".htmlspecialchars(strip_tags(strtr($this->items[$i]->title,"\n\r","  ")))."</title>\n";
			$feed.= "        <link>".htmlspecialchars($this->items[$i]->link)."</link>\n";
			$feed.= "        <description>".htmlspecialchars($this->items[$i]->description)."</description>\n";
			$feed.= $this->_createAdditionalElements($this->items[$i]->additionalElements, "        ");
			$feed.= "    </item>\n";
		}
		$feed.= "</rdf:RDF>\n";
		return $feed;
	}
}

/**
 * RSSCreator091 is a FeedCreator that implements RSS 0.91 Spec, revision 3.
 *
 * @see http://my.netscape.com/publish/formats/rss-spec-0.91.html
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class RSSCreator091 extends FeedCreator {

	/**
	 * Stores this RSS feed's version number.
	 * @access private
	 */
	var $RSSVersion;

	/**
	 * Sets an optional XML namespace
	 * @access private
	 */
	var $XMLNS = array();

	function RSSCreator091() {
		$this->_setRSSVersion("0.91");
		$this->contentType = "application/rss+xml";
	}

	/**
	 * Sets this RSS feed's version number.
	 * @access private
	 * @param $version
	 */
	function _setRSSVersion($version) {
		$this->RSSVersion = $version;
	}

	/**
	 * Sets an XML namespace that hos RSS feed
	 * @access private
	 * @param $xmlns
	 */
	function _setXMLNS($xmlns) {
		$this->XMLNS[] = $xmlns;
	}

	/**
	 * Builds the RSS feed's text. The feed will be compliant to RDF Site Summary (RSS) 1.0.
	 * The feed will contain all items previously added in the same order.
	 * @return    string    the feed's complete text 
	 */
	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<rss version=\"".$this->RSSVersion."\" ";
        if (!empty($this->XMLNS)) {
            foreach ($this->XMLNS as $xmlns) {
                $feed.= "    xmlns:".$xmlns."\n";
            }
		}
        $feed.= 'xmlns:content="http://purl.org/rss/1.0/modules/content/" ' ;
        $feed.= 'xmlns:wfw="http://wellformedweb.org/CommentAPI/" ' ;
        $feed.= 'xmlns:dc="http://purl.org/dc/elements/1.1/" ' ;
        $feed.= 'xmlns:atom="http://www.w3.org/2005/Atom" ' ;
        $feed.= 'xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" ' ;
        $feed.= 'xmlns:slash="http://purl.org/rss/1.0/modules/slash/" ' ;
		$feed.= ">\n";
		$feed.= "    <channel>\n";
		$feed.= "        <title>".FeedCreator::iTrunc(htmlspecialchars($this->title),100)."</title>\n";
//        $feed.= '        <atom:link href="'.$this->syndicationURL.'" rel="self" type="application/rss+xml" />'."\n";
		$this->descriptionTruncSize = 500;
		$feed.= "        <description>".$this->getDescription()."</description>\n";
		$feed.= "        <link>".$this->link."</link>\n";
		$now = new FeedDate();
		$feed.= "        <lastBuildDate>".htmlspecialchars($now->rfc822())."</lastBuildDate>\n";
		$feed.= "        <generator>". $this->version()."</generator>\n";

		if (!empty($this->image)) {
			$feed.= "        <image>\n";
			$feed.= "            <url>".$this->image->url."</url>\n"; 
			$feed.= "            <title>".FeedCreator::iTrunc(htmlspecialchars($this->image->title),100)."</title>\n"; 
			$feed.= "            <link>".$this->image->link."</link>\n";
			if (!empty($this->image->width)) {
				$feed.= "            <width>".$this->image->width."</width>\n";
			}
			if (!empty($this->image->height)) {
				$feed.= "            <height>".$this->image->height."</height>\n";
			}
			if (!empty($this->image->description)) {
				$feed.= "            <description>".$this->image->getDescription()."</description>\n";
			}
			$feed.= "        </image>\n";
		}
		if (!empty($this->language)) {
			$feed.= "        <language>".$this->language."</language>\n";
		}
		if (!empty($this->copyright)) {
			$feed.= "        <copyright>".FeedCreator::iTrunc(htmlspecialchars($this->copyright),100)."</copyright>\n";
		}
		if (!empty($this->editor)) {
			$feed.= "        <managingEditor>".FeedCreator::iTrunc(htmlspecialchars($this->editor),100)."</managingEditor>\n";
		}
		if (!empty($this->webmaster)) {
			$feed.= "        <webMaster>".FeedCreator::iTrunc(htmlspecialchars($this->webmaster),100)."</webMaster>\n";
		}
		if (!empty($this->pubDate)) {
			$pubDate = new FeedDate($this->pubDate);
			$feed.= "        <pubDate>".htmlspecialchars($pubDate->rfc822())."</pubDate>\n";
		}
		if (!empty($this->category)) {
			$feed.= "        <category>".htmlspecialchars($this->category)."</category>\n";
		}
		if (!empty($this->docs)) {
			$feed.= "        <docs>".FeedCreator::iTrunc(htmlspecialchars($this->docs),500)."</docs>\n";
		}
		if (!empty($this->ttl)) {
			$feed.= "        <ttl>".htmlspecialchars($this->ttl)."</ttl>\n";
		}
		if (!empty($this->rating)) {
			$feed.= "        <rating>".FeedCreator::iTrunc(htmlspecialchars($this->rating),500)."</rating>\n";
		}
		if (!empty($this->skipHours)) {
			$feed.= "        <skipHours>".htmlspecialchars($this->skipHours)."</skipHours>\n";
		}
		if (!empty($this->skipDays)) {
			$feed.= "        <skipDays>".htmlspecialchars($this->skipDays)."</skipDays>\n";
		}
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");

                /* iTunes add iTunes specific tags */
        if (!empty($this->itunes)) {
			if (!empty($this->itunes->category)) {
				$feed.= "        <itunes:category text=\"".htmlspecialchars($this->itunes->category)."\">\n";
			    if (!empty($this->itunes->subcategory)) {
			        $feed.= "            <itunes:category text=\"".htmlspecialchars($this->itunes->subcategory)."\"/>\n";
			    }
			    $feed.= "        </itunes:category>\n";
			}
			if (!empty($this->itunes->explicit)) {
				$feed.= "        <itunes:explicit>".$this->itunes->explicit."</itunes:explicit>\n";
			}
			if (!empty($this->itunes->subtitle)) {
				$feed.= "        <itunes:subtitle>".htmlspecialchars($this->itunes->subtitle)."</itunes:subtitle>\n";
			}
			if (!empty($this->itunes->summary)) {
				$feed.= "        <itunes:summary>".htmlspecialchars($this->itunes->summary)."</itunes:summary>\n";
			}
			if (!empty($this->itunes->author)) {
				$feed.= "        <itunes:author>".htmlspecialchars($this->itunes->author)."</itunes:author>\n";
			}
			if (!empty($this->itunes->keywords)) {
				$feed.= "        <itunes:keywords>".htmlspecialchars($this->itunes->keywords)."</itunes:keywords>\n";
			}
			if (!empty($this->itunes->owner_email)) {
				$feed.= "        <itunes:owner>\n";
                $feed.= "            <itunes:email>".$this->itunes->owner_email."</itunes:email>\n";
			    if (!empty($this->itunes->owner_name)) {
			        $feed.= "            <itunes:name>".$this->itunes->owner_name."</itunes:name>\n";
			    }
                $feed.= "        </itunes:owner>\n";
			}
			if (!empty($this->itunes->image)) {
				$feed.= "        <itunes:image href=\"".$this->itunes->image."\" />\n";
			}
        }

		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "        <item>\n";
			$feed.= "            <title>".FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)),100)."</title>\n";
			$feed.= "            <link>".str_replace(" ", "%20", htmlspecialchars($this->items[$i]->link))."</link>\n";
			$feed.= "            <description>".$this->items[$i]->getDescription()."</description>\n";
			
			if (!empty($this->items[$i]->author)) {
				if ($this->items[$i]->authorEmail!="") {
					$feed.= "            <author> " . htmlspecialchars($this->items[$i]->authorEmail) . " (".htmlspecialchars($this->items[$i]->author).")</author>\n";
				} else {
				      $feed.= "            <author> no_email@example.com (".htmlspecialchars($this->items[$i]->author).")</author>\n";
			    }
			}
			/*
			// on hold
			if (!empty($this->items[$i]->source)) {
					$feed.= "            <source>".htmlspecialchars($this->items[$i]->source)."</source>\n";
			}
			*/
                  /* podcasts add the enclosure element */
			if (!empty($this->items[$i]->enclosure)) {
				$feed.= "            <enclosure url=\"".str_replace(" ", "%20", htmlspecialchars($this->items[$i]->enclosure->url)).
								"\" length=\"".htmlspecialchars($this->items[$i]->enclosure->length).
								"\" type=\"".htmlspecialchars($this->items[$i]->enclosure->type).
								"\"/>\n";
			}
            /* iTunes add iTunes specific tags */
            if (!empty($this->items[$i]->itunes)) {
				if (!empty($this->items[$i]->itunes->category)) {
					$feed.= "            <itunes:category text=\"".htmlspecialchars($this->items[$i]->itunes->category)."\">\n";
				    if (!empty($this->items[$i]->itunes->subcategory)) {
				   	    $feed.= "                <itunes:category text=\"".htmlspecialchars($this->items[$i]->itunes->subcategory)."\"/>\n";
				    }
				   $feed.= "            </itunes:category>\n";
				}
				if (!empty($this->items[$i]->itunes->explicit)) {
					$feed.= "            <itunes:explicit>".$this->items[$i]->itunes->explicit."</itunes:explicit>\n";
				}
				if (!empty($this->items[$i]->itunes->subtitle)) {
					$feed.= "            <itunes:subtitle>".htmlspecialchars($this->items[$i]->itunes->subtitle)."</itunes:subtitle>\n";
				}
				if (!empty($this->items[$i]->itunes->summary)) {
					$feed.= "            <itunes:summary>".htmlspecialchars($this->items[$i]->itunes->summary)."</itunes:summary>\n";
				}
				if (!empty($this->items[$i]->itunes->author)) {
					$feed.= "            <itunes:author>".htmlspecialchars($this->items[$i]->itunes->author)."</itunes:author>\n";
				}
				if (!empty($this->items[$i]->itunes->keywords)) {
					$feed.= "            <itunes:keywords>".htmlspecialchars($this->items[$i]->itunes->keywords)."</itunes:keywords>\n";
				}
				if (!empty($this->items[$i]->itunes->duration)) {
					$feed.= "            <itunes:duration>".$this->items[$i]->itunes->duration."</itunes:duration>\n";
				}
				if (!empty($this->items[$i]->itunes->image)) {
					$feed.= "            <itunes:link rel=\"image\" type=\"image/jpeg\" href=\"".$this->items[$i]->itunes->image."\">[image]</itunes:link>\n";
				}
            }
			for ($c=0;$c<count($this->items[$i]->category);$c++) {
	            if (!empty($this->items[$i]->category[$c])) {
				    $feed.= "            <category>".htmlspecialchars($this->items[$i]->category[$c])."</category>\n";
				}
			}
			if (!empty($this->items[$i]->comments)) {
				$feed.= "            <comments>".htmlspecialchars($this->items[$i]->comments)."</comments>\n";
			}
            if (!empty($this->items[$i]->commentsRSS)) {
                $feed.= "            <wfw:commentRss>".htmlspecialchars($this->items[$i]->commentsRSS)."</wfw:commentRss>\n";
            }
            if (!empty($this->items[$i]->commentsCount)) {
                $feed.= "            <slash:comments>".$this->items[$i]->commentsCount."</slash:comments>\n";
            }
			if (!empty($this->items[$i]->date)) {
			$itemDate = new FeedDate($this->items[$i]->date);
				$feed.= "            <pubDate>".htmlspecialchars($itemDate->rfc822())."</pubDate>\n";
			}
			if (!empty($this->items[$i]->guid)) {
				$feed.= "            <guid>".htmlspecialchars($this->items[$i]->guid)."</guid>\n";
			}
			$feed.= $this->_createAdditionalElements($this->items[$i]->additionalElements, "        ");
			$feed.= "        </item>\n";
		}
		$feed.= "    </channel>\n";
		$feed.= "</rss>";
		return $feed;
	}
}

/**
 * RSSCreator20 is a FeedCreator that implements RDF Site Summary (RSS) 2.0.
 *
 * @see http://backend.userland.com/rss
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class RSSCreator20 extends RSSCreator091 {

    function RSSCreator20() {
        parent::_setRSSVersion("2.0");
        $this->encoding = "utf-8";
    }
    
}

/**
 * PodcastCreator is a FeedCreator that implements Podcast
 *
 * @see http://backend.userland.com/rss
 * @since 1.7.2-podcast
 * @author Steven Pothoven <steven@pothoven.net>
 */
class PodcastCreator extends RSSCreator20 {  
	function PodcastCreator() {
	    parent::_setRSSVersion("2.0");
        $this->encoding = "utf-8";
	    parent::_setXMLNS("itunes=\"http://www.itunes.com/dtds/podcast-1.0.dtd\"");
	}
}

/**
 * PIECreator01 is a FeedCreator that implements the emerging PIE specification,
 * as in http://intertwingly.net/wiki/pie/Syntax.
 *
 * @deprecated
 * @since 1.3
 * @author Scott Reynen <scott@randomchaos.com> and Kai Blankenhorn <kaib@bitfolge.de>
 */
class PIECreator01 extends FeedCreator {
	
	function PIECreator01() {
		$this->encoding = "utf-8";
	}
    
	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<feed version=\"0.1\" xmlns=\"http://example.com/newformat#\">\n"; 
		$feed.= "    <title>".FeedCreator::iTrunc(htmlspecialchars($this->title),100)."</title>\n";
		$this->truncSize = 500;
		$feed.= "    <subtitle>".$this->getDescription()."</subtitle>\n";
		$feed.= "    <link>".$this->link."</link>\n";
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "    <entry>\n";
			$feed.= "        <title>".FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)),100)."</title>\n";
			$feed.= "        <link>".htmlspecialchars($this->items[$i]->link)."</link>\n";
			$itemDate = new FeedDate($this->items[$i]->date);
			$feed.= "        <created>".htmlspecialchars($itemDate->iso8601())."</created>\n";
			$feed.= "        <issued>".htmlspecialchars($itemDate->iso8601())."</issued>\n";
			$feed.= "        <modified>".htmlspecialchars($itemDate->iso8601())."</modified>\n";
			$feed.= "        <id>".htmlspecialchars($this->items[$i]->guid)."</id>\n";
			if (!empty($this->items[$i]->author)) {
				$feed.= "        <author>\n";
				$feed.= "            <name>".htmlspecialchars($this->items[$i]->author)."</name>\n";
				if (!empty($this->items[$i]->authorEmail)) {
					$feed.= "            <email>".$this->items[$i]->authorEmail."</email>\n";
				}
				$feed.="        </author>\n";
			}
			$feed.= "        <content type=\"text/html\" xml:lang=\"en-us\">\n";
			$feed.= "            <div xmlns:xhtml=\"http://www.w3.org/1999/xhtml\">".$this->items[$i]->getDescription()."</div>\n";
			$feed.= "        </content>\n";
			$feed.= "    </entry>\n";
		}
		$feed.= "</feed>\n";
		return $feed;
	}
}

/**
 * AtomCreator10 is a FeedCreator that implements the atom specification,
 * as in http://www.atomenabled.org/developers/syndication/atom-format-spec.php
 * Please note that just by using AtomCreator10 you won't automatically
 * produce valid atom files. For example, you have to specify either an editor
 * for the feed or an author for every single feed item.
 *
 * Some elements have not been implemented yet. These are (incomplete list):
 * author URL, item author's email and URL, item contents, alternate links,
 * other link content types than text/html. Some of them may be created with
 * AtomCreator10::additionalElements.
 *
 * @see FeedCreator#additionalElements
 * @since 1.7.2-mod (modified)
 * @author Mohammad Hafiz Ismail (mypapit@gmail.com)
 */
 class AtomCreator10 extends FeedCreator {

	function AtomCreator10() {
		$this->contentType = "application/atom+xml";
		$this->encoding = "utf-8";

	}

	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<feed xmlns=\"http://www.w3.org/2005/Atom\"";
		if ($this->language!="") {
			$feed.= " xml:lang=\"".$this->language."\"";
		}
		$feed.= ">\n";
		$feed.= "    <title>".htmlspecialchars($this->title)."</title>\n";
		$feed.= "    <subtitle>".htmlspecialchars($this->description)."</subtitle>\n";
		$feed.= "    <link rel=\"alternate\" type=\"text/html\" href=\"".htmlspecialchars($this->link)."\"/>\n";
		$feed.= "    <id>".htmlspecialchars($this->link)."</id>\n";
		$now = new FeedDate();
		$feed.= "    <updated>".htmlspecialchars($now->iso8601())."</updated>\n";
		if ($this->editor!="") {
			$feed.= "    <author>\n";
			$feed.= "        <name>".$this->editor."</name>\n";
			if ($this->editorEmail!="") {
				$feed.= "        <email>".$this->editorEmail."</email>\n";
			}
			$feed.= "    </author>\n";
		}
		if ($this->category!="") {
					$feed.= "        <category term=\"" . htmlspecialchars($this->category) . "\" />\n";
		}
		if ($this->copyright!="") {
					$feed.= "        <rights>".FeedCreator::iTrunc(htmlspecialchars($this->copyright),100)."</rights>\n";
		}
		$feed.= "    <generator>".$this->version()."</generator>\n";

		$feed.= "<link rel=\"self\" type=\"application/atom+xml\" href=\"". htmlspecialchars($this->syndicationURL). "\" />\n";
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "    <entry>\n";
			$feed.= "        <title>".htmlspecialchars(strip_tags($this->items[$i]->title))."</title>\n";
			$feed.= "        <link rel=\"alternate\" type=\"text/html\" href=\"".htmlspecialchars($this->items[$i]->link)."\"/>\n";
			if ($this->items[$i]->date=="") {
				$this->items[$i]->date = time();
			}
			$itemDate = new FeedDate($this->items[$i]->date);
			$feed.= "        <published>".htmlspecialchars($itemDate->iso8601())."</published>\n";
			$feed.= "        <updated>".htmlspecialchars($itemDate->iso8601())."</updated>\n";


			$tempguid = $this->items[$i]->link;
			if ($this->items[$i]->guid!="") {
				$tempguid = $this->items[$i]->guid;
			}

			$feed.= "        <id>". htmlspecialchars($tempguid)."</id>\n";
			$feed.= $this->_createAdditionalElements($this->items[$i]->additionalElements, "        ");
			if ($this->items[$i]->author!="") {
				$feed.= "        <author>\n";
				$feed.= "            <name>".htmlspecialchars($this->items[$i]->author)."</name>\n";
				if ($this->items[$i]->authorEmail!="") {
				$feed.= "            <email>".htmlspecialchars($this->items[$i]->authorEmail)."</email>\n";
				}

				if ($this->items[$i]->authorURL!="") {
								$feed.= "            <uri>".htmlspecialchars($this->items[$i]->authorURL)."</uri>\n";
				}

				$feed.= "        </author>\n";
			}

			if ($this->category!="") {
								$feed.= "        <category term=\"" . htmlspecialchars($this->items[$i]->category) . "\" />\n";
			}

			if ($this->items[$i]->description!="") {

			/*
			 * ATOM should have at least summary tag, however this implementation may be inaccurate
			 */
			 	$tempdesc = $this->items[$i]->getDescription();
			 	$temptype="";

				if ($this->items[$i]->descriptionHtmlSyndicated){
					$temptype=" type=\"html\"";
					$tempdesc = $this->items[$i]->getDescription();

				}

				if (empty($this->items[$i]->descriptionTruncSize)) {
					$feed.= "        <content". $temptype . ">". $tempdesc ."</content>\n";
				}

				$feed.= "        <summary". $temptype . ">". $tempdesc ."</summary>\n";
			} else {

				$feed.= "	 <summary>no summary</summary>\n";

			}

			if ($this->items[$i]->enclosure != NULL) {
				$feed.="        <link rel=\"enclosure\" href=\"". $this->items[$i]->enclosure->url ."\" type=\"". $this->items[$i]->enclosure->type."\"  length=\"". $this->items[$i]->enclosure->length ."\"";

				if ($this->items[$i]->enclosure->language != ""){
					 $feed .=" xml:lang=\"". $this->items[$i]->enclosure->language . "\" ";
				}

				if ($this->items[$i]->enclosure->title != ""){
					 $feed .=" title=\"". $this->items[$i]->enclosure->title . "\" ";
				}

				$feed .=" /> \n";

			}
			$feed.= "    </entry>\n";
		}
		$feed.= "</feed>\n";
		return $feed;
	}

}

/**
 * AtomCreator03 is a FeedCreator that implements the atom specification,
 * as in http://www.intertwingly.net/wiki/pie/FrontPage.
 * Please note that just by using AtomCreator03 you won't automatically
 * produce valid atom files. For example, you have to specify either an editor
 * for the feed or an author for every single feed item.
 *
 * Some elements have not been implemented yet. These are (incomplete list):
 * author URL, item author's email and URL, item contents, alternate links, 
 * other link content types than text/html. Some of them may be created with
 * AtomCreator03::additionalElements.
 *
 * @see FeedCreator#additionalElements
 * @since 1.6
 * @author Kai Blankenhorn <kaib@bitfolge.de>, Scott Reynen <scott@randomchaos.com>
 */
class AtomCreator03 extends FeedCreator {

	function AtomCreator03() {
		$this->contentType = "application/atom+xml";
		$this->encoding = "utf-8";
	}
	
	function createFeed() {
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<feed version=\"0.3\" xmlns:atom=\"http://purl.org/atom/ns#\"";
		if (!empty($this->language)) {
			$feed.= " xml:lang=\"".$this->language."\"";
		}
		$feed.= ">\n"; 
		$feed.= "    <title>".htmlspecialchars($this->title)."</title>\n";
		$feed.= "    <tagline>".htmlspecialchars($this->description)."</tagline>\n";
		$feed.= "    <link rel=\"alternate\" type=\"text/html\" href=\"".htmlspecialchars($this->link)."\"/>\n";
		$feed.= "    <id>".htmlspecialchars($this->link)."</id>\n";
		$now = new FeedDate();
		$feed.= "    <modified>".htmlspecialchars($now->iso8601())."</modified>\n";
		if (!empty($this->editor)) {
			$feed.= "    <author>\n";
			$feed.= "        <name>".$this->editor."</name>\n";
			if (!empty($this->editorEmail)) {
				$feed.= "        <email>".$this->editorEmail."</email>\n";
			}
			$feed.= "    </author>\n";
		}
		$feed.= "    <generator>".$this->version()."</generator>\n";
		$feed.= $this->_createAdditionalElements($this->additionalElements, "    ");
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "    <entry>\n";
			$feed.= "        <title>".htmlspecialchars(strip_tags($this->items[$i]->title))."</title>\n";
			$feed.= "        <link rel=\"alternate\" type=\"text/html\" href=\"".htmlspecialchars($this->items[$i]->link)."\"/>\n";
			if ($this->items[$i]->date=="") {
				$this->items[$i]->date = time();
			}
			$itemDate = new FeedDate($this->items[$i]->date);
			$feed.= "        <created>".htmlspecialchars($itemDate->iso8601())."</created>\n";
			$feed.= "        <issued>".htmlspecialchars($itemDate->iso8601())."</issued>\n";
			$feed.= "        <modified>".htmlspecialchars($itemDate->iso8601())."</modified>\n";
			$feed.= "        <id>".htmlspecialchars($this->items[$i]->link)."</id>\n";
			$feed.= $this->_createAdditionalElements($this->items[$i]->additionalElements, "        ");
			if (!empty($this->items[$i]->author)) {
				$feed.= "        <author>\n";
				$feed.= "            <name>".htmlspecialchars($this->items[$i]->author)."</name>\n";
				$feed.= "        </author>\n";
			}
			if (!empty($this->items[$i]->description)) {
				$feed.= "        <summary>".htmlspecialchars($this->items[$i]->description)."</summary>\n";
			}
			$feed.= "    </entry>\n";
		}
		$feed.= "</feed>\n";
		return $feed;
	}
}

/**
 * MBOXCreator is a FeedCreator that implements the mbox format
 * as described in http://www.qmail.org/man/man5/mbox.html
 *
 * @since 1.3
 * @author Kai Blankenhorn <kaib@bitfolge.de>
 */
class MBOXCreator extends FeedCreator {

	function MBOXCreator() {
		$this->contentType = "text/plain";
		$this->encoding = "ISO-8859-15";
	}
    
	function qp_enc($input = "", $line_max = 76) { 
		$hex = array('0','1','2','3','4','5','6','7','8','9','A','B','C','D','E','F'); 
		$lines = preg_split("/(?:\r\n|\r|\n)/", $input); 
		$eol = "\r\n"; 
		$escape = "="; 
		$output = ""; 
		while( list(, $line) = each($lines) ) { 
			//$line = rtrim($line); // remove trailing white space -> no =20\r\n necessary 
			$linlen = strlen($line); 
			$newline = ""; 
			for($i = 0; $i < $linlen; $i++) { 
				$c = substr($line, $i, 1); 
				$dec = ord($c); 
				if ( ($dec == 32) && ($i == ($linlen - 1)) ) { // convert space at eol only 
					$c = "=20"; 
				} elseif ( ($dec == 61) || ($dec < 32 ) || ($dec > 126) ) { // always encode "\t", which is *not* required 
					$h2 = floor($dec/16); $h1 = floor($dec%16); 
					$c = $escape.$hex["$h2"].$hex["$h1"]; 
				} 
				if ( (strlen($newline) + strlen($c)) >= $line_max ) { // CRLF is not counted 
					$output .= $newline.$escape.$eol; // soft line break; " =\r\n" is okay 
					$newline = ""; 
				} 
				$newline .= $c; 
			} // end of for 
			$output .= $newline.$eol; 
		} 
		return trim($output); 
	}

	/**
	 * Builds the MBOX contents.
	 * @return    string    the feed's complete text 
	 */
	function createFeed() {
		for ($i=0;$i<count($this->items);$i++) {
			if (!empty($this->items[$i]->author)) {
				$from = $this->items[$i]->author;
			} else {
				$from = $this->title;
			}
			$itemDate = new FeedDate($this->items[$i]->date);
			$feed= "From ".strtr(MBOXCreator::qp_enc($from)," ","_")." ".date("D M d H:i:s Y",$itemDate->unix())."\n";
			$feed.= "Content-Type: text/plain;\n";
			$feed.= "	charset=\"".$this->encoding."\"\n";
			$feed.= "Content-Transfer-Encoding: quoted-printable\n";
			$feed.= "Content-Type: text/plain\n";
			$feed.= "From: \"".MBOXCreator::qp_enc($from)."\"\n";
			$feed.= "Date: ".$itemDate->rfc822()."\n";
			$feed.= "Subject: ".MBOXCreator::qp_enc(FeedCreator::iTrunc($this->items[$i]->title,100))."\n";
			$feed.= "\n";
			$body = chunk_split(MBOXCreator::qp_enc($this->items[$i]->description));
			$feed.= preg_replace("~\nFrom ([^\n]*)(\n?)~","\n>From $1$2\n",$body);
			$feed.= "\n";
			$feed.= "\n";
		}
		return $feed;
	}
	
	/**
	 * Generate a filename for the feed cache file. Overridden from FeedCreator to prevent XML data types.
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return substr($fileInfo["basename"],0,-(strlen($fileInfo["extension"])+1)).".mbox";
	}
}

/**
 * OPMLCreator is a FeedCreator that implements OPML 1.0.
 * 
 * @see http://opml.scripting.com/spec
 * @author Dirk Clemens, Kai Blankenhorn
 * @since 1.5
 */
class OPMLCreator extends FeedCreator {

	function OPMLCreator() {
		$this->encoding = "utf-8";
	}
    
	function createFeed() {     
		$feed = "<?xml version=\"1.0\" encoding=\"".$this->encoding."\"?>\n";
		$feed.= $this->_createGeneratorComment();
		$feed.= $this->_createStylesheetReferences();
		$feed.= "<opml xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" version=\"1.0\">\n";
		$feed.= "    <head>\n";
		$feed.= "        <title>".htmlspecialchars($this->title)."</title>\n";
		if (!empty($this->pubDate)) {
			$date = new FeedDate($this->pubDate);
			$feed.= "         <dateCreated>".$date->rfc822()."</dateCreated>\n";
		}
		if (!empty($this->lastBuildDate)) {
			$date = new FeedDate($this->lastBuildDate);
			$feed.= "         <dateModified>".$date->rfc822()."</dateModified>\n";
		}
		if (!empty($this->editor)) {
			$feed.= "         <ownerName>".$this->editor."</ownerName>\n";
		}
		if (!empty($this->editorEmail)) {
			$feed.= "         <ownerEmail>".$this->editorEmail."</ownerEmail>\n";
		}
		$feed.= "    </head>\n";
		$feed.= "    <body>\n";
		for ($i=0;$i<count($this->items);$i++) {
			$feed.= "    <outline type=\"rss\" ";
			$title = htmlspecialchars(strip_tags(strtr($this->items[$i]->title,"\n\r","  ")));
			$feed.= " title=\"".$title."\"";
			$feed.= " text=\"".$title."\"";
			//$feed.= " description=\"".htmlspecialchars($this->items[$i]->description)."\"";
			$feed.= " url=\"".htmlspecialchars($this->items[$i]->link)."\"";

			if ($this->items[$i]->syndicationURL !="") {
				$feed.= " xmlUrl=\"" . $this->items[$i]->syndicationURL . "\"";
			}

			$feed.= "/>\n";
		}
		$feed.= "    </body>\n";
		$feed.= "</opml>\n";
		return $feed;
	}
}

/**
 * HTMLCreator is a FeedCreator that writes an HTML feed file to a specific 
 * location, overriding the createFeed method of the parent FeedCreator.
 * The HTML produced can be included over http by scripting languages, or serve
 * as the source for an IFrame.
 * All output by this class is embedded in <div></div> tags to enable formatting
 * using CSS. 
 *
 * @author Pascal Van Hecke
 * @since 1.7
 */
class HTMLCreator extends FeedCreator {

	var $contentType = "text/html";
	
	/**
	 * Contains HTML to be output at the start of the feed's html representation.
	 */
	var $header;
	
	/**
	 * Contains HTML to be output at the end of the feed's html representation.
	 */
	var $footer ;
	
	/**
	 * Contains HTML to be output between entries. A separator is only used in 
	 * case of multiple entries.
	 */
	var $separator;
	
	/**
	 * Used to prefix the stylenames to make sure they are unique 
	 * and do not clash with stylenames on the users' page.
	 */
	var $stylePrefix;
	
	/**
	 * Determines whether the links open in a new window or not.
	 */
	var $openInNewWindow = true;
	
	var $imageAlign ="right";
	
	/**
	 * In case of very simple output you may want to get rid of the style tags,
	 * hence this variable.  There's no equivalent on item level, but of course you can 
	 * add strings to it while iterating over the items ($this->stylelessOutput .= ...)
	 * and when it is non-empty, ONLY the styleless output is printed, the rest is ignored
	 * in the function createFeed().
	 */
	var $stylelessOutput ="";

	/**
	 * Writes the HTML.
	 * @return    string    the scripts's complete text 
	 */
	function createFeed() {
		// if there is styleless output, use the content of this variable and ignore the rest
		if (!empty($this->stylelessOutput)) {
			return $this->stylelessOutput;
		}
		
		//if no stylePrefix is set, generate it yourself depending on the script name
		if ($this->stylePrefix=="") {
			$this->stylePrefix = str_replace(".", "_", $this->_generateFilename())."_";
		}

		//set an openInNewWindow_token_to be inserted or not
		if ($this->openInNewWindow) {
			$targetInsert = " target='_blank'";
		}
		
		// use this array to put the lines in and implode later with "document.write" javascript
		$feedArray = array();
		if ($this->image!=null) {
			$imageStr = "<a href='".$this->image->link."'".$targetInsert.">".
							"<img src='".$this->image->url."' border='0' alt='".
							FeedCreator::iTrunc(htmlspecialchars($this->image->title),100).
							"' align='".$this->imageAlign."' ";
			if ($this->image->width) {
				$imageStr .=" width='".$this->image->width. "' ";
			}
			if ($this->image->height) {
				$imageStr .=" height='".$this->image->height."' ";
			}
			$imageStr .="/></a>";
			$feedArray[] = $imageStr;
		}
		
		if ($this->title) {
			$feedArray[] = "<div class='".$this->stylePrefix."title'><a href='".$this->link."' ".$targetInsert." class='".$this->stylePrefix."title'>".
				FeedCreator::iTrunc(htmlspecialchars($this->title),100)."</a></div>";
		}
		if ($this->getDescription()) {
			$feedArray[] = "<div class='".$this->stylePrefix."description'>".
				str_replace("]]>", "", str_replace("<![CDATA[", "", $this->getDescription())).
				"</div>";
		}
		
		if ($this->header) {
			$feedArray[] = "<div class='".$this->stylePrefix."header'>".$this->header."</div>";
		}
		
		for ($i=0;$i<count($this->items);$i++) {
			if ($this->separator and $i > 0) {
				$feedArray[] = "<div class='".$this->stylePrefix."separator'>".$this->separator."</div>";
			}
			
			if ($this->items[$i]->title) {
				if ($this->items[$i]->link) {
					$feedArray[] = 
						"<div class='".$this->stylePrefix."item_title'><a href='".$this->items[$i]->link."' class='".$this->stylePrefix.
						"item_title'".$targetInsert.">".FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)),100).
						"</a></div>";
				} else {
					$feedArray[] = 
						"<div class='".$this->stylePrefix."item_title'>".
						FeedCreator::iTrunc(htmlspecialchars(strip_tags($this->items[$i]->title)),100).
						"</div>";
				}
			}
			if ($this->items[$i]->getDescription()) {
				$feedArray[] = 
				"<div class='".$this->stylePrefix."item_description'>".
					str_replace("]]>", "", str_replace("<![CDATA[", "", $this->items[$i]->getDescription())).
					"</div>";
			}
		}
		if ($this->footer) {
			$feedArray[] = "<div class='".$this->stylePrefix."footer'>".$this->footer."</div>";
		}
		
		$feed= "".join($feedArray, "\r\n");
		return $feed;
	}
    
	/**
	 * Overrrides parent to produce .html extensions
	 *
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return substr($fileInfo["basename"],0,-(strlen($fileInfo["extension"])+1)).".html";
	}
}	

/**
 * JSCreator is a class that writes a js file to a specific 
 * location, overriding the createFeed method of the parent HTMLCreator.
 *
 * @author Pascal Van Hecke
 */
class JSCreator extends HTMLCreator {
	var $contentType = "text/javascript";
	
	/**
	 * writes the javascript
	 * @return    string    the scripts's complete text 
	 */
	function createFeed() 
	{
		$feed = parent::createFeed();
		$feedArray = explode("\n",$feed);
		
		$jsFeed = "";
		foreach ($feedArray as $value) {
			$jsFeed .= "document.write('".trim(addslashes($value))."');\n";
		}
		return $jsFeed;
	}
    
	/**
	 * Overrrides parent to produce .js extensions
	 *
	 * @return string the feed cache filename
	 * @since 1.4
	 * @access private
	 */
	function _generateFilename() {
		$fileInfo = pathinfo($_SERVER["PHP_SELF"]);
		return substr($fileInfo["basename"],0,-(strlen($fileInfo["extension"])+1)).".js";
	}
	
}	

/*** TEST SCRIPT *********************************************************

//include("feedcreator.class.php"); 

$rss = new UniversalFeedCreator(); 
$rss->useCached(); 
$rss->title = "PHP news"; 
$rss->description = "daily news from the PHP scripting world"; 

//optional
//$rss->descriptionTruncSize = 500;
//$rss->descriptionHtmlSyndicated = true;
//$rss->xslStyleSheet = "http://feedster.com/rss20.xsl";

$rss->link = "http://www.dailyphp.net/news"; 
$rss->feedURL = "http://www.dailyphp.net/".$PHP_SELF; 

$image = new FeedImage(); 
$image->title = "dailyphp.net logo"; 
$image->url = "http://www.dailyphp.net/images/logo.gif"; 
$image->link = "http://www.dailyphp.net"; 
$image->description = "Feed provided by dailyphp.net. Click to visit."; 

//optional
$image->descriptionTruncSize = 500;
$image->descriptionHtmlSyndicated = true;

$rss->image = $image; 

// get your news items from somewhere, e.g. your database: 
//mysql_select_db($dbHost, $dbUser, $dbPass); 
//$res = mysql_query("SELECT * FROM news ORDER BY newsdate DESC"); 
//while ($data = mysql_fetch_object($res)) { 
	$item = new FeedItem(); 
	$item->title = "This is an the test title of an item"; 
	$item->link = "http://localhost/item/"; 
	$item->description = "<b>description in </b><br/>HTML"; 
	
	//optional
	//item->descriptionTruncSize = 500;
	$item->descriptionHtmlSyndicated = true;
	
	$item->date = time(); 
	$item->source = "http://www.dailyphp.net"; 
	$item->author = "John Doe"; 
	 
	$rss->addItem($item); 
//} 

// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1, MBOX, OPML, ATOM0.3, HTML, JS
echo $rss->saveFeed("RSS0.91", "feed.xml"); 

***************************************************************************/

?>