<?php
/*
php5 class (will not work in php4)
for detecting bitrate and duration of regular mp3 files (not VBR files)
*/

//-----------------------------------------------------------------------------
class mp3file
{
    protected $block;
    protected $blockpos;
    protected $blockmax;
    protected $blocksize;
    protected $fd;
    protected $bitpos;
    protected $mp3data;

    public function __construct($filename)
    {
        $this->powarr  = array(0=>1,1=>2,2=>4,3=>8,4=>16,5=>32,6=>64,7=>128);
        $this->blockmax= 1024;
        
        $this->mp3data = array();
        $this->mp3data['Filesize'] = filesize($filename);

        $this->fd = fopen($filename,'rb');
        $this->prefetchblock();
        $this->readmp3frame();
//        $this->get_meta_tags();
        $this->mp3_get_tags();
    }
    public function __destruct()
    {
        fclose($this->fd);
    }
    //-------------------
    public function get_metadata()
    {
        return $this->mp3data;
    }
    protected function readmp3frame()
    {
        $iscbrmp3=true;
        if ($this->startswithid3())
            $this->skipid3tag();
        else if ($this->containsvbrxing())
        {
            $this->mp3data['Encoding'] = 'VBR';
            $iscbrmp3=false;
        }
        else if ($this->startswithpk())
        {
            $this->mp3data['Encoding'] = 'Unknown';
            $iscbrmp3=false;
        }
    
        if ($iscbrmp3)
        {
            $i = 0;
            $max=5000;
            //look in 5000 bytes... 
            //the largest framesize is 4609bytes(256kbps@8000Hz  mp3)
            for($i=0; $i<$max; $i++)
            {
                //looking for 1111 1111 111 (frame synchronization bits)                
                if ($this->getnextbyte()==0xFF)
                    if ($this->getnextbit() && $this->getnextbit() && $this->getnextbit())
                        break;
            }
            if ($i==$max)
                $iscbrmp3=false;
        }
    
        if ($iscbrmp3)
        {
            $this->mp3data['Encoding'         ] = 'CBR';
            $this->mp3data['MPEG version'     ] = $this->getnextbits(2);
            $this->mp3data['Layer Description'] = $this->getnextbits(2);
            $this->mp3data['Protection Bit'   ] = $this->getnextbits(1);
            $this->mp3data['Bitrate Index'    ] = $this->getnextbits(4);
            $this->mp3data['Sampling Freq Idx'] = $this->getnextbits(2);
            $this->mp3data['Padding Bit'      ] = $this->getnextbits(1);
            $this->mp3data['Private Bit'      ] = $this->getnextbits(1);
            $this->mp3data['Channel Mode'     ] = $this->getnextbits(2);
            $this->mp3data['Mode Extension'   ] = $this->getnextbits(2);
            $this->mp3data['Copyright'        ] = $this->getnextbits(1);
            $this->mp3data['Original Media'   ] = $this->getnextbits(1);
            $this->mp3data['Emphasis'         ] = $this->getnextbits(1);
            $this->mp3data['Bitrate'          ] = mp3file::bitratelookup($this->mp3data);
            $this->mp3data['Sampling Rate'    ] = mp3file::samplelookup($this->mp3data);
            $this->mp3data['Frame Size'       ] = mp3file::getframesize($this->mp3data);
            $this->mp3data['Length'           ] = mp3file::getduration($this->mp3data,$this->tell2());
            $this->mp3data['Length mm:ss'     ] = mp3file::seconds_to_mmss($this->mp3data['Length']);
            
            if ($this->mp3data['Bitrate'      ]=='bad'     ||
                $this->mp3data['Bitrate'      ]=='free'    ||
                $this->mp3data['Sampling Rate']=='unknown' ||
                $this->mp3data['Frame Size'   ]=='unknown' ||
                $this->mp3data['Length'     ]=='unknown')
            $this->mp3data = array('Filesize'=>$this->mp3data['Filesize'], 'Encoding'=>'Unknown');
        }
        else
        {
            if(!isset($this->mp3data['Encoding']))
                $this->mp3data['Encoding'] = 'Unknown';
        }
    }
    protected function tell()
    {
        return ftell($this->fd);
    }
    protected function tell2()
    {
        return ftell($this->fd)-$this->blockmax +$this->blockpos-1;
    }
    protected function startswithid3()
    {
        return ($this->block[1]==73 && //I
                $this->block[2]==68 && //D
                $this->block[3]==51);  //3
    }
    protected function startswithpk()
    {
        return ($this->block[1]==80 && //P
                $this->block[2]==75);  //K
    }
    protected function containsvbrxing()
    {
        //echo "<!--".$this->block[37]." ".$this->block[38]."-->";
        //echo "<!--".$this->block[39]." ".$this->block[40]."-->";
        return(
               ($this->block[37]==88  && //X 0x58
                $this->block[38]==105 && //i 0x69
                $this->block[39]==110 && //n 0x6E
                $this->block[40]==103)   //g 0x67
/*               || 
               ($this->block[21]==88  && //X 0x58
                $this->block[22]==105 && //i 0x69
                $this->block[23]==110 && //n 0x6E
                $this->block[24]==103)   //g 0x67*/
              );   

    } 
    protected function debugbytes()
    {
        for($j=0; $j<10; $j++)
        {
            for($i=0; $i<8; $i++)
            {
                if ($i==4) echo " ";
                echo $this->getnextbit();
            }
            echo "<BR>";
        }
    }
    protected function prefetchblock()
    {
        $block = fread($this->fd, $this->blockmax);
        $this->blocksize = strlen($block);
        $this->block = unpack("C*", $block);
        $this->blockpos=0;
    }
    protected function skipid3tag()
    {
        $bits=$this->getnextbits(24);//ID3
        $bits.=$this->getnextbits(24);//v.v flags

        //3 bytes 1 version byte 2 byte flags
        $arr = array();
        $arr['ID3v2 Major version'] = bindec(substr($bits,24,8));
        $arr['ID3v2 Minor version'] = bindec(substr($bits,32,8));
        $arr['ID3v2 flags'        ] = bindec(substr($bits,40,8));
        if (substr($bits,40,1)) $arr['Unsynchronisation']=true;
        if (substr($bits,41,1)) $arr['Extended header']=true;
        if (substr($bits,42,1)) $arr['Experimental indicator']=true;
        if (substr($bits,43,1)) $arr['Footer present']=true;

        $size = "";
        for($i=0; $i<4; $i++)
        {
            $this->getnextbit();//skip this bit, should be 0
            $size.= $this->getnextbits(7);
        }

        $arr['ID3v2 Tags Size']=bindec($size);//now the size is in bytes;
        if ($arr['ID3v2 Tags Size'] - $this->blockmax>0)
        {
            fseek($this->fd, $arr['ID3v2 Tags Size']+10 );
            $this->prefetchblock();
            if (isset($arr['Footer present']) && $arr['Footer present'])
            {
                for($i=0; $i<10; $i++)
                    $this->getnextbyte();//10 footer bytes
            }
        }
        else
        {
            for($i=0; $i<$arr['ID3v2 Tags Size']; $i++)
                $this->getnextbyte();
        }
    }

    protected function getnextbit()
    {
        if ($this->bitpos==8)
            return false;

        $b=0;
        $whichbit = 7-$this->bitpos;
        $mult = $this->powarr[$whichbit]; //$mult = pow(2,7-$this->pos);
        $b = $this->block[$this->blockpos+1] & $mult;
        $b = $b >> $whichbit;
        $this->bitpos++;

        if ($this->bitpos==8)
        {
            $this->blockpos++;
                
            if ($this->blockpos==$this->blockmax) //end of block reached
            {
                $this->prefetchblock();
            }
            else if ($this->blockpos==$this->blocksize) 
            {//end of short block reached (shorter than blockmax)
                return;//eof 
            }
            
            $this->bitpos=0;
        }
        return $b;
    }
    protected function getnextbits($n=1)
    {
        $b="";
        for($i=0; $i<$n; $i++)
            $b.=$this->getnextbit();
        return $b;
    }
    protected function getnextbyte()
    {
        if ($this->blockpos>=$this->blocksize)
            return;

        $this->bitpos=0;
        $b=$this->block[$this->blockpos+1];
        $this->blockpos++;
        return $b;
    }
    //-----------------------------------------------------------------------------
    public static function is_layer1(&$mp3) { return ($mp3['Layer Description']=='11'); }
    public static function is_layer2(&$mp3) { return ($mp3['Layer Description']=='10'); }
    public static function is_layer3(&$mp3) { return ($mp3['Layer Description']=='01'); }
    public static function is_mpeg10(&$mp3)  { return ($mp3['MPEG version']=='11'); }
    public static function is_mpeg20(&$mp3)  { return ($mp3['MPEG version']=='10'); }
    public static function is_mpeg25(&$mp3)  { return ($mp3['MPEG version']=='00'); }
    public static function is_mpeg20or25(&$mp3)  { return ($mp3['MPEG version']{1}=='0'); }
    //-----------------------------------------------------------------------------
    public static function bitratelookup(&$mp3)
    {
        //bits               V1,L1  V1,L2  V1,L3  V2,L1  V2,L2&L3
        $array = array();
        $array['0000']=array('free','free','free','free','free');
        $array['0001']=array(  '32',  '32',  '32',  '32',   '8');
        $array['0010']=array(  '64',  '48',  '40',  '48',  '16');
        $array['0011']=array(  '96',  '56',  '48',  '56',  '24');
        $array['0100']=array( '128',  '64',  '56',  '64',  '32');
        $array['0101']=array( '160',  '80',  '64',  '80',  '40');
        $array['0110']=array( '192',  '96',  '80',  '96',  '48');
        $array['0111']=array( '224', '112',  '96', '112',  '56');
        $array['1000']=array( '256', '128', '112', '128',  '64');
        $array['1001']=array( '288', '160', '128', '144',  '80');
        $array['1010']=array( '320', '192', '160', '160',  '96');
        $array['1011']=array( '352', '224', '192', '176', '112');
        $array['1100']=array( '384', '256', '224', '192', '128');
        $array['1101']=array( '416', '320', '256', '224', '144');
        $array['1110']=array( '448', '384', '320', '256', '160');
        $array['1111']=array( 'bad', 'bad', 'bad', 'bad', 'bad');
        
        $whichcolumn=-1;
        if      (mp3file::is_mpeg10($mp3) && mp3file::is_layer1($mp3) )//V1,L1
            $whichcolumn=0;
        else if (mp3file::is_mpeg10($mp3) && mp3file::is_layer2($mp3) )//V1,L2
            $whichcolumn=1;
        else if (mp3file::is_mpeg10($mp3) && mp3file::is_layer3($mp3) )//V1,L3
            $whichcolumn=2;
        else if (mp3file::is_mpeg20or25($mp3) && mp3file::is_layer1($mp3) )//V2,L1
            $whichcolumn=3;
        else if (mp3file::is_mpeg20or25($mp3) && (mp3file::is_layer2($mp3) || mp3file::is_layer3($mp3)) )
            $whichcolumn=4;//V2,   L2||L3 
        
        if (isset($array[$mp3['Bitrate Index']][$whichcolumn]))
            return $array[$mp3['Bitrate Index']][$whichcolumn];
        else 
            return "bad";
    }
    //-----------------------------------------------------------------------------
    public static function samplelookup(&$mp3)
    {
        //bits               MPEG1   MPEG2   MPEG2.5
        $array = array();
        $array['00'] =array('44100','22050','11025');
        $array['01'] =array('48000','24000','12000');
        $array['10'] =array('32000','16000','8000');
        $array['11'] =array('res','res','res');
        
        $whichcolumn=-1;
        if      (mp3file::is_mpeg10($mp3))
            $whichcolumn=0;
        else if (mp3file::is_mpeg20($mp3))
            $whichcolumn=1;
        else if (mp3file::is_mpeg25($mp3))
            $whichcolumn=2;
        
        if (isset($array[$mp3['Sampling Freq Idx']][$whichcolumn]))
            return $array[$mp3['Sampling Freq Idx']][$whichcolumn];
        else 
            return 'unknown';
    }
    //-----------------------------------------------------------------------------
    public static function getframesize(&$mp3)
    {
        if ($mp3['Sampling Rate']>0)
        {
            return  ceil((144 * $mp3['Bitrate']*1000)/$mp3['Sampling Rate']) + $mp3['Padding Bit'];
        }
        return 'unknown';
    }
    //-----------------------------------------------------------------------------
    public static function getduration(&$mp3,$startat)
    {
        if ($mp3['Bitrate']>0)
        {
            $KBps = ($mp3['Bitrate']*1000)/8;
            $datasize = ($mp3['Filesize'] - ($startat/8));
            $length = $datasize / $KBps;
            return sprintf("%d", $length);
        }
        return "unknown";
    }
    //-----------------------------------------------------------------------------
    public static function seconds_to_mmss($duration)
    {
        $trailSeconds = $duration % 60;
       	$minutes = floor($duration / 60);
       	if ($minutes >= 60) {
       		$hour = floor($minutes / 60);
       		$minutes = $minutes % 60;
//       		return $hour . ':' . $minutes . ':' . $trailSeconds;
            return sprintf("%02d:%02d:%02d", $hour, $minutes, $trailSeconds);
       	} else {
//            return $minutes . ':' . $trailSeconds;
            return sprintf("%02d:%02d", $minutes, $trailSeconds);
       	}
    }

    // Get ID3 meta tags

    protected function get_meta_tags() {
        $this->mp3data = array_merge($this->mp3data, $this->getTagsInfo($this->getTagsInfo()));
    }

    // variables
    var $aTV23 = array( // array of possible sys tags (for last version of ID3)
        'TIT2',
        'TALB',
        'TPE1',
        'TPE2',
        'TRCK',
        'TYER',
        'TLEN',
        'USLT',
        'TPOS',
        'TCON',
        'TENC',
        'TCOP',
        'TPUB',
        'TOPE',
        'WXXX',
        'COMM',
        'TCOM'
    );
    var $aTV23t = array( // array of titles for sys tags
        'Title',
        'Album',
        'Author',
        'AlbumAuthor',
        'Track',
        'Year',
        'Length',
        'Lyric',
        'Desc',
        'Genre',
        'Encoded',
        'Copyright',
        'Publisher',
        'OriginalArtist',
        'URL',
        'Comments',
        'Composer'
    );
    var $aTV22 = array( // array of possible sys tags (for old version of ID3)
        'TT2',
        'TAL',
        'TP1',
        'TRK',
        'TYE',
        'TLE',
        'ULT'
    );
    var $aTV22t = array( // array of titles for sys tags
        'Title',
        'Album',
        'Author',
        'Track',
        'Year',
        'Length',
        'Lyric'
    );

    // functions
    function getTagsInfo() {
        // read source file
        $iFSize = $this->mp3data['Filesize'];
        fseek($this->fd,0);
        $sSrc = fread($this->fd,$iFSize);
        $aInfo = array();

        // obtain base info
        if (substr($sSrc,0,3) == 'ID3') {
//            $aInfo['FileName'] = $sFilepath;
            $aInfo['ID3Version'] = hexdec(bin2hex(substr($sSrc,3,1))).'.'.hexdec(bin2hex(substr($sSrc,4,1)));
        } else {
            return $aInfo;
        }

        // passing through possible tags of idv2 (v3 and v4)
        if ($aInfo['ID3Version'] == '4.0' || $aInfo['ID3Version'] == '3.0') {
            for ($i = 0; $i < count($this->aTV23); $i++) {
                if (strpos($sSrc, $this->aTV23[$i].chr(0)) != FALSE) {

                    $s = '';
                    $iPos = strpos($sSrc, $this->aTV23[$i].chr(0));
                    $iLen = hexdec(bin2hex(substr($sSrc,($iPos + 5),3)));

                    $data = substr($sSrc, $iPos, 9 + $iLen + 1);
                    for ($a = 0; $a < strlen($data); $a++) {
                        $char = substr($data, $a, 1);
                        if ($char >= ' ' && $char <= '~')
                            $s .= $char;
                    }
                    if (substr($s, 0, 4) == $this->aTV23[$i]) {
                        $iSL = 4;
                        if ($this->aTV23[$i] == 'USLT') {
                            $iSL = 7;
//                        } elseif ($this->aTV23[$i] == 'TALB') {
//                            $iSL = 5;
                        } elseif ($this->aTV23[$i] == 'COMM') {
                            $iSL = 8;
                        } elseif ($this->aTV23[$i] == 'TENC') {
                            $iSL = 6;
                        }
                        $aInfo[$this->aTV23t[$i]] = substr($s, $iSL);
                    }
                }
            }
        }

        // passing through possible tags of idv2 (v2)
        if($aInfo['ID3Version'] == '2.0') {
            for ($i = 0; $i < count($this->aTV22); $i++) {
                if (strpos($sSrc, $this->aTV22[$i].chr(0)) != FALSE) {

                    $s = '';
                    $iPos = strpos($sSrc, $this->aTV22[$i].chr(0));
                    $iLen = hexdec(bin2hex(substr($sSrc,($iPos + 3),3)));

                    $data = substr($sSrc, $iPos, 6 + $iLen);
                    for ($a = 0; $a < strlen($data); $a++) {
                        $char = substr($data, $a, 1);
                        if ($char >= ' ' && $char <= '~')
                            $s .= $char;
                    }

                    if (substr($s, 0, 3) == $this->aTV22[$i]) {
                        $iSL = 3;
                        if ($this->aTV22[$i] == 'ULT') {
                            $iSL = 6;
                        }
                        $aInfo[$this->aTV22t[$i]] = substr($s, $iSL);
                    }
                }
            }
        }
        return $aInfo;
    }

    function mp3_get_tags()
    {
    	// http://www.seabreezecomputers.com/tips/mp3_id3_tag.htm
    	$id3_tags = array(); // Function returns an array of id3 mp3 tags for $file

        // Look for ID3v2 - http://id3.org/id3v2.3.0 or http://id3.org/id3v2.4.0-frames
        $tags_array = array( // first ID3v2.3 and ID3v2.4 tags
                            'TIT2' => 'title', 'TALB' => 'album', 'TPE1' => 'artist',
                            'TYER' => 'year', 'COMM' => 'comment', 'TCON' => 'genre', 'TLEN' => 'length',
                            // second ID3v2.2 tags - http://id3.org/id3v2-00
                            'TT2' => 'title', 'TAL' => 'album', 'TP1' => 'artist',
                            'TYE' => 'year', 'TCO' => 'genre', 'TLE' => 'length'
                            );
        $null = chr(0); // the stop bit or null in ID3 tags is the first ASCII character or chr(0)
        fseek($this->fd,0);  // set file pointer back to beginning
        $data = fread($this->fd, 10); // 10 = Size of header - http://id3.org/id3v2.4.0-structure
        if (substr($data,0,3) == 'ID3') // If first 3 bytes == "ID3"
        {
            $id3_major_version = hexdec(bin2hex(substr($data,3,1)));
            $id3_tags["id3_tag_version"] = "2.".$id3_major_version;
            $id3_revision = hexdec(bin2hex(substr($data,4,1)));
            $id3_flags = decbin(ord(substr($data,5,1))); // 8 flag bits (first 4 may be set)
            $id3_flags = str_pad($id3_flags, 8, 0, STR_PAD_LEFT);
            $footer_flag = $id3_flags[3]; // footer flag is 4th flag bit
            // Calculate size of header including all tags and extended header and footer
            $mb_size = ord(substr($data,6,1)); // each number here is equal to 2 Megabytes
            $kb_size = ord(substr($data,7,1)); // each number here is equal to 16 Kilobytes
            $byte128_size = ord(substr($data,8,1)); // each number here is equal to 128 Bytes
            $byte_size = ord(substr($data,9,1)); // each number here is equal to 1 Byte
            $total_size = ($mb_size * 2097152) + ($kb_size * 16384) + ($byte128_size * 128) + $byte_size;
            //fseek($handle, 0, SEEK_SET); // Read file from beginning // Version 2.0 - Removed all fseek for remote file support
            //$data = fread($handle, 10 + $total_size + ($footer_flag * 10));
            $data .= stream_get_contents($this->fd, $total_size + ($footer_flag * 10)); // Version 2.0 - Using stream_get_contents instead of fread // Version 2.0a - Removed extra 10 + before $total_size
            foreach ($tags_array as $key => $value)
            {
                if ($id3_major_version == 3 || $id3_major_version == 4)
                    $tag_header_length = 10;
                else // if ($id3_major_version == 2)
                    $tag_header_length = 6;
                if ($tag_pos = strpos($data, $key.$null))
                {
                    $tag_abbr = trim(substr($data, $tag_pos, 4)); // tag abbreviation
                    $content_length = hexdec(bin2hex(substr($data, $tag_pos + ($tag_header_length/2),3)));
                    $content = trim(substr($data, $tag_pos + $tag_header_length, $content_length));
                    $tag_content = "";
                    for ($i = 0; $i < strlen($content); $i++)
                        if($content[$i] >= " " && $content[$i] <= "~") $tag_content .= $content[$i];
                    $id3_tags[$value] = $tag_content; // Ex: $id3_tags['title'] = "Song Title";
                    if ($value == 'comment')
                        $id3_tags[$value] = substr($id3_tags[$value], 3);  // skip over language
                }
            }
            if ($id3_major_version != 2) // Version 2.0
                $data = ""; // wipe out data so we only add to it if it is ID3v1 below with: $data .= fread($handle, 10);
            //else // ID3v1
            //	fseek($handle, 0, SEEK_SET); // Read file from beginning // Version 2.0 - Removed all fseek for remote file support
        }

    	// Look for ID3v1 - http://id3.org/ID3v1
    	if (!isset($id3_major_version)) // if we didn't alreay find ID3v2 v3 or v4 tags
    	{
    		$id3_tags["id3_tag_version"] = 1; // Version 2.0
    		while (!feof($this->fd))
    		{
    			//$data .= fread($handle, 128);
    			$data .= stream_get_contents($this->fd, 128); // Version 2.0 - Using stream_get_contents instead of fread

    		}
    		$data = substr($data, -128);  // Get just last 128 bytes of file
    		if(substr($data, 0, 3) == "TAG") // If first 3 bytes == "TAG"
    		{
    			$id3_tags["title"] = trim(substr($data, 3, 30));
    			$id3_tags["artist"] = trim(substr($data, 33, 30));
    			$id3_tags["album"] = trim(substr($data, 63, 30));
    			$id3_tags["year"] = trim(substr($data, 93, 4));
    			$id3_tags["comment"] = trim(substr($data, 97, 30));
    			$id3_tags["genre"] = ord(trim(substr($data, 127, 1))); // http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/ID3.html
    		}
    	}

        $this->mp3data = array_merge($this->mp3data, $id3_tags);
    } // end function mp3_get_tags($file)

    function mp3_get_genre_name($genre_id)
    {
    	// See: http://www.sno.phy.queensu.ca/~phil/exiftool/TagNames/ID3.html
    	$genre_names = array("Blues", "Classic Rock", "Country", "Dance", "Disco", "Funk", "Grunge", "Hip-Hop", "Jazz", "Metal", "New Age", "Oldies", "Other", "Pop", "R&B", "Rap", "Reggae", "Rock", "Techno", "Industrial", "Alternative", "Ska", "Death Metal", "Pranks", "Soundtrack", "Euro-Techno", "Ambient", "Trip-Hop", "Vocal", "Jazz+Funk", "Fusion", "Trance", "Classical", "Instrumental", "Acid", "House", "Game", "Sound Clip", "Gospel", "Noise", "Alt. Rock", "Bass", "Soul", "Punk", "Space", "Meditative", "Instrumental Pop", "Instrumental Rock", "Ethnic", "Gothic", "Darkwave", "Techno-Industrial", "Electronic", "Pop-Folk", "Eurodance", "Dream", "Southern Rock", "Comedy", "Cult", "Gangsta Rap", "Top 40", "Christian Rap", "Pop/Funk", "Jungle", "Native American", "Cabaret", "New Wave", "Psychedelic", "Rave", "Showtunes", "Trailer", "Lo-Fi", "Tribal", "Acid Punk", "Acid Jazz", "Polka", "Retro", "Musical", "Rock & Roll", "Hard Rock", "Folk", "Folk-Rock", "National Folk", "Swing", "Fast-Fusion", "Bebop", "Latin", "Revival", "Celtic", "Bluegrass", "Avantgarde", "Gothic Rock", "Progressive Rock", "Psychedelic Rock", "Symphonic Rock", "Slow Rock", "Big Band", "Chorus", "Easy Listening", "Acoustic", "Humour", "Speech", "Chanson", "Opera", "Chamber Music", "Sonata", "Symphony", "Booty Bass", "Primus", "Porn Groove", "Satire", "Slow Jam", "Club", "Tango", "Samba", "Folklore", "Ballad", "Power Ballad", "Rhythmic Soul", "Freestyle", "Duet", "Punk Rock", "Drum Solo", "A Cappella", "Euro-House", "Dance Hall", "Goa", "Drum & Bass", "Club-House", "Hardcore", "Terror", "Indie", "BritPop", "Afro-Punk", "Polsk Punk", "Beat", "Christian Gangsta Rap", "Heavy Metal", "Black Metal", "Crossover", "Contemporary Christian", "Christian Rock", "Merengue", "Salsa", "Thrash Metal", "Anime", "JPop", "Synthpop", "Abstract", "Art Rock", "Baroque", "Bhangra", "Big Beat", "Breakbeat", "Chillout", "Downtempo", "Dub", "EBM", "Eclectic", "Electro", "Electroclash", "Emo", "Experimental", "Garage", "Global", "IDM", "Illbient", "Industro-Goth", "Jam Band", "Krautrock", "Leftfield", "Lounge", "Math Rock", "New Romantic", "Nu-Breakz", "Post-Punk", "Post-Rock", "Psytrance", "Shoegaze", "Space Rock", "Trop Rock", "World Music", "Neoclassical", "Audiobook", "Audio Theatre", "Neue Deutsche Welle", "Podcast", "Indie Rock", "G-Funk", "Dubstep", "Garage Rock", "Psybient");
    	/* According to http://id3.org/id3v2.3.0#Text_information_frames:
    		"Several references can be made in the same frame, e.g. "(51)(39)" */
    	$genres = explode(")", $genre_id);
    	$n = 1;
        $genre_string = '';
    	foreach($genres as $genre_num)
    	{
    			$genre_num = str_replace("(", "", str_replace(")", "", $genre_num)); // remove ( and )
    			if ($n > 1 && !empty($genre_num))
    				$genre_string .= ","; // Separate multiple genres by ,
    			if (is_numeric($genre_num))
    			{
    				if ($genre_num >= 0 && $genre_num <= 191)
    					$genre_string .= $genre_names[$genre_num];
    				else
    					$genre_string .= "None"; // 255 = None
    			}
    			else
    				$genre_string .= $genre_num;
    		$n++;
    	}
    	return($genre_string);
    } // end function mp3_get_genre_name()

}

?>
