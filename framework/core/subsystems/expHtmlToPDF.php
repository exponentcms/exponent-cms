<?php
##################################################
#
# Copyright (c) 2004-2022 OIC Group, Inc.
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

/** @define "BASE" "../../../" */
/**
 * This is the class expHtmlToPDF
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expHtmlToPDF extends expSubsystem
{
    /**
     * Private use variables.
     */
    public  $pdf = null;  // pdf engine object
    protected $size = HTMLTOPDF_PAPER;
    protected $orient = 'portrait';

    /**
     * Force the client to download PDF file.
     */
    public static $PDF_DOWNLOAD = 'D';
    /**
     * Returns the PDF file as a string.
     */
    public static $PDF_ASSTRING = 'S';
    /**
     * When possible, force the client to embed (display) PDF file.
     */
    public static $PDF_EMBEDDED = 'I';
    /**
     * PDF file is saved into the server space. The path is returned.
     */
    public static $PDF_SAVEFILE = 'F';
    /**
     * PDF generated as landscape (vertical).
     */
    public static $PDF_PORTRAIT = 'portrait';
    /**
     * PDF generated as landscape (horizontal).
     */
    public static $PDF_LANDSCAPE = 'landscape';

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        $installed = false;
        if (class_exists(HTMLTOPDF_ENGINE)) {
            $engine = HTMLTOPDF_ENGINE;
            $installed = $engine::installed();
        }
        return $installed;
    }

    /**
     * Constructor: initialize a pdf file using selected engine.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (HTMLTOPDF_ENGINE !== 'none') {
            $engine = HTMLTOPDF_ENGINE;
            $this->pdf = new $engine($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html, $use_file);
        }
    }

    /**
     * Render and Return PDF using various options.
     *
     * @param string $mode How to output (constants from this same class).
     * @param string $file The PDF's filename (the usage depends on $mode.
     *
     * @return string|boolean Depending on $mode, this may be success (boolean) or PDF (string).
     */
    public function createpdf($mode, $file)
    {
        if (!empty($this->pdf)) {
            return $this->pdf->createpdf($mode, $file);
        }
    }

    /**
     * Convert HTML to PDF.
     */
    public function render()
    {
        if (!empty($this->pdf)) {
            return $this->pdf->render();
        }
    }

    /**
     * Set html content.
     *
     * @param string $html New html content. It *replaces* any previous content.
     */
    public function set_html($html)
    {
        if (!empty($this->pdf)) {
            $this->pdf->set_html($html);
        }
    }

    /**
     * Set html content by file.
     *
     * @param string $htmlfile the html file to use
     */
    public function set_html_file($htmlfile)
    {
        if (!empty($this->pdf)) {
            $this->pdf->set_html_file($htmlfile);
        }
    }


    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     *
     * @param string $orientation orientation of paper
     */
    public function set_orientation($orientation)
    {
        if (!empty($this->pdf)) {
            $this->orient = $orientation;
            $this->pdf->set_orientation($this->orient);
        }
    }

    /**
     * Set page/paper size.
     *
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size)
    {
        if (!empty($this->pdf)) {
            $this->size = $size;
            $this->pdf->set_page_size($this->size);
        }
    }

    /**
     * Whether to print in grayscale or not.
     * By default it is OFF.
     *
     * @param $mode
     *
     * @internal param \True $boolean to print in grayscale, false in full color.
     */
    public function set_grayscale($mode)
    {
        if (!empty($this->pdf)) {
            $this->pdf->set_grayscale($mode);
        }
    }

}

// Automated configuration. Modify these if they fail. (they shouldn't ;) )
//$GLOBALS['WKPDF_BASE_PATH']=str_replace(str_replace('\\','/',getcwd().'/'),'',dirname(str_replace('\\','/',__FILE__))).'/';
$GLOBALS['WKPDF_BASE_PATH'] = '';
$GLOBALS['WKPDF_BASE_SITE'] = 'http://' . $_SERVER['SERVER_NAME'] . '/';

/**
 * @author    Christian Sciberras
 * @see       <a href="http://code.google.com/p/wkhtmltopdf/">http://code.google.com/p/wkhtmltopdf/</a>
 * @copyright 2010 Christian Sciberras / Covac Software.
 * @license   None. There are no restrictions on use, however keep copyright intact.
 *   Modification is allowed, keep track of modifications below in this comment block.
 * @example
 *   <font color="#008800"><i>//-- Create sample PDF and embed in browser. --//</i></font><br>
 *   <br>
 *   <font color="#008800"><i>// Include WKPDF class.</i></font><br>
 *   <font color="#0000FF">require_once</font>(<font color="#FF0000">'wkhtmltopdf/wkhtmltopdf.php'</font>);<br>
 *   <font color="#008800"><i>// Create PDF object.</i></font><br>
 *   <font color="#EE00EE">$pdf</font>=new <b>WKPDF</b>();<br>
 *   <font color="#008800"><i>// Set PDF's HTML</i></font><br>
 *   <font color="#EE00EE">$pdf</font>-><font color="#0000FF">set_html</font>(<font color="#FF0000">'Hello &lt;b&gt;Mars&lt;/b&gt;!'</font>);<br>
 *   <font color="#008800"><i>// Convert HTML to PDF</i></font><br>
 *   <font color="#EE00EE">$pdf</font>-><font color="#0000FF">render</font>();<br>
 *   <font color="#008800"><i>// Output PDF. The file name is suggested to the browser.</i></font><br>
 *   <font color="#EE00EE">$pdf</font>-><font color="#0000FF">output</font>(<b>WKPDF</b>::<font color="#EE00EE">$PDF_EMBEDDED</font>,<font color="#FF0000">'sample.pdf'</font>);<br>
 * @version
 *   0.0 Chris - Created class.<br>
 *   0.1 Chris - Variable paths fixes.<br>
 *   0.2 Chris - Better error handlng (via exceptions).<br>
 * <font color="#FF0000"><b>IMPORTANT: Make sure that there is a folder in %LIBRARY_PATH%/tmp that is writable!</b></font>
 * <br><br>
 * <b>Features/Bugs/Contact</b><br>
 * Found a bug? Want a modification? Contact me at <a href="mailto:uuf6429@gmail.com">uuf6429@gmail.com</a> or <a href="mailto:contact@covac-software.com">contact@covac-software.com</a>...
 *   guaranteed to get a reply within 2 hours at most (daytime GMT+1).
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expWKPDF extends expHtmlToPDF
{
    /**
     * Private use variables.
     */
    private $html = '';
    private $cmd = '';
    private $tmp = '';
    private $status = '';
    private $toc = false;
    private $copies = 1;
    private $grayscale = false;
    private $title = '';
    private static $cpu = '';

    /**
     * Advanced execution routine.
     *
     * @param string $cmd   The command to execute.
     * @param string $input Any input not in arguments.
     *
     * @return array An array of execution data; stdout, stderr and return "error" code.
     */
    private static function _pipeExec($cmd, $input = '')
    {
        $proc = proc_open(
            $cmd,
            array(0 => array('pipe', 'r'), 1 => array('pipe', 'w'), 2 => array('pipe', 'w')),
            $pipes
        );
        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        $rtn = proc_close($proc);
        return array(
            'stdout' => $stdout,
            'stderr' => $stderr,
            'return' => $rtn
        );
    }

    /**
     * Function that attempts to return the kind of CPU.
     *
     * @throws Exception
     * @return string CPU kind ('amd64' or 'i386').
     */
    private static function _getCPU()
    {
        if (self::$cpu == '') {
            if (`grep -i amd /proc/cpuinfo` != '') {
                self::$cpu = 'amd64';
            } elseif (`grep -i intel /proc/cpuinfo` != '') {
                self::$cpu = 'i386';
            } else {
                throw new Exception('WKPDF couldn\'t determine CPU ("' . `grep -i vendor_id /proc/cpuinfo` . '").');
            }
        }
        return self::$cpu;
    }

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        $cmd = HTMLTOPDF_PATH;
        return file_exists($cmd);
    }

    /**
     * Constructor: initialize command line and reserve temporary file.
     *
     * @param string $paper_size
     * @param string $orientation
     * @param null $html
     * @param bool $use_file
     *
     * @throws Exception
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        $this->cmd = HTMLTOPDF_PATH; //.self::_getCPU();
        if (!file_exists($this->cmd)) {
            throw new Exception('WKPDF static executable "' . htmlspecialchars(
                    $this->cmd,
                    ENT_QUOTES
                ) . '" was not found.');
        }
        do {
            //$this->tmp=$GLOBALS['WKPDF_BASE_PATH'].'tmp/'.mt_rand().'.html';
            $this->tmp = HTMLTOPDF_PATH_TMP . mt_rand() . '.html';
        } while (file_exists($this->tmp));
//do{
        //        //$this->tmp=$GLOBALS['WKPDF_BASE_PATH'].'tmp/'.mt_rand().'.html';
        //        $this->tmp_rendered='/home/military/dev/tmp/'.mt_rand().'.pdf';
        //} while(file_exists($this->tmp_rendered));
        $this->size = $paper_size;
        $this->orient = ucfirst($orientation);
        if (!empty($html)) {
            if (empty($use_file)) {
                $this->set_html($html);
            } else {
                $this->set_html(file_get_contents($html));
            }
        }
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     *
     * @param string $mode Use constants from this class.
     */
    public function set_orientation($mode)
    {
        $this->orient = $mode;
    }

    /**
     * Set page/paper size.
     *
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size)
    {
        $this->size = $size;
    }

    /**
     * Whether to automatically generate a TOC (table of contents) or not.
     * By default TOC is disabled.
     *
     * @param boolean $enabled True use TOC, false disable TOC.
     */
    public function set_toc($enabled)
    {
        $this->toc = $enabled;
    }

    /**
     * Set the number of copies to be printed.
     * By default it is one.
     *
     * @param integer $count Number of page copies.
     */
    public function set_copies($count)
    {
        $this->copies = $count;
    }

    /**
     * Whether to print in grayscale or not.
     * By default it is OFF.
     *
     * @param $mode
     *
     * @internal param \True $boolean to print in grayscale, false in full color.
     */
    public function set_grayscale($mode)
    {
        $this->grayscale = $mode;
    }

    /**
     * Set PDF title. If empty, HTML <title> of first document is used.
     * By default it is empty.
     *
     * @param $text
     *
     * @internal param \Title $string text.
     */
    public function set_title($text)
    {
        $this->title = $text;
    }

    /**
     * Set html content.
     *
     * @param string $html New html content. It *replaces* any previous content.
     */
    public function set_html($html)
    {
        $this->html = $html;
        file_put_contents($this->tmp, $html);
    }

    /**
     * Set html content by file.
     *
     * @param $file
     *
     * @internal param string $html New html content. It *replaces* any previous content.
     */
    public function set_html_file($file)
    {
        $html = file_get_contents(BASE . $file);
        $this->set_html($html);
    }

    /**
     * Returns WKPDF print status.
     *
     * @return string WPDF print status.
     */
    public function get_status()
    {
        return $this->status;
    }

    /**
     * Attempts to return the library's full help.
     *
     * @return string WKHTMLTOPDF HTML help.
     */
    public function get_help()
    {
        $tmp = self::_pipeExec('"' . $this->cmd . '" --extended-help');
        return $tmp['stdout'];
    }

    /**
     * Convert HTML to PDF.
     *
     * @throws Exception
     */
    public function render()
    {
        //$web=$GLOBALS['WKPDF_BASE_SITE'].$GLOBALS['WKPDF_BASE_PATH'].'tmp/'.basename($this->tmp);
        $web = HTMLTOPDF_PATH_TMP . basename($this->tmp);
        $issueCmd = '"' . $this->cmd . '"'
            . (($this->copies > 1) ? ' --copies ' . $this->copies : '') // number of copies
            . ' --orientation ' . $this->orient // orientation
            . ' --page-size ' . $this->size // page size
            . ($this->toc ? ' --toc' : '') // table of contents
            . ($this->grayscale ? ' --grayscale' : '') // grayscale
            . (($this->title != '') ? ' --title "' . $this->title . '"' : '') // title
            //.' "'.$web.'" '.$this->tmp_rendered;   // URL and optional to write to STDOUT
            . ' "' . $web . '" -'; //.$this->tmp_rendered;   // URL and optional to write to STDOUT
//eDebug($issueCmd,true);
        $this->pdf = self::_pipeExec($issueCmd);

        if (strpos(
                strtolower($this->pdf['stderr']),
                'error'
            ) !== false
        ) {
            throw new Exception('WKPDF system error: <pre>' . $this->pdf['stderr'] . '</pre>');
        }
        if ($this->pdf['stdout'] == '') {
            throw new Exception('WKPDF didn\'t return any data. <pre>' . $this->pdf['stderr'] . '</pre>');
        }
        if (((int)$this->pdf['return']) > 2) {
            throw new Exception('WKPDF shell error, return code ' . (int)$this->pdf['return'] . '.');
        }
        $this->status = $this->pdf['stderr'];
        $this->pdf = $this->pdf['stdout'];
        unlink($this->tmp);
    }

    /**
     * Return PDF with various options.
     *
     * @param string $mode How two output (constants from this same class).
     * @param string $file The PDF's filename (the usage depends on $mode.
     *
     * @throws Exception
     * @return string|boolean Depending on $mode, this may be success (boolean) or PDF (string).
     */
    public function createpdf($mode, $file)
    {
        $this->render();
        switch ($mode) {
            case self::$PDF_DOWNLOAD:
                if (!headers_sent()) {
//	eDebug($this,true);
                    header('Content-Description: File Transfer');
                    header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
                    header('Pragma: public');
//                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    // force download dialog
                    header('Content-Type: application/force-download');
                    header('Content-Type: application/octet-stream', false);
                    header('Content-Type: application/download', false);
                    header('Content-Type: application/pdf', false);
//                    header('Content-Type: application/pdf', false);
                    // use the Content-Disposition header to supply a recommended filename
                    header('Content-Disposition: attachment; filename="' . basename($file) . '";');
                    header('Content-Transfer-Encoding: binary');
                    header('Content-Length: ' . strlen($this->pdf));
                    //header('Content-Length: '.filesize($this->tmp_rendered));
                    echo $this->pdf;
                    //echo readfile($this->tmp_rendered);
                } else {
                    throw new Exception('WKPDF download headers were already sent.');
                }
                break;
            case self::$PDF_ASSTRING:
                return $this->pdf;
                break;
            case self::$PDF_EMBEDDED:
                if (!headers_sent()) {
                    header('Content-Type: application/pdf');
                    header('Cache-Control: public, must-revalidate, max-age=0'); // HTTP/1.1
                    header('Pragma: public');
//                    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
                    header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
                    header('Content-Length: ' . strlen($this->pdf));
                    header('Content-Disposition: inline; filename="' . basename($file) . '";');
                    echo $this->pdf;
                } else {
                    throw new Exception('WKPDF embed headers were already sent.');
                }
                break;
            case self::$PDF_SAVEFILE:
                return file_put_contents($file, $this->pdf);
                break;
            default:
                throw new Exception('WKPDF invalid mode "' . htmlspecialchars($mode, ENT_QUOTES) . '".');
        }
        return false;
    }
}

/**
 * This is the class expDOMPDF
 * a wrapper for using dompdf v0.6.2
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expDOMPDF extends expHtmlToPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return file_exists(BASE . 'external/dompdf/dompdf.php');
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/dompdf/dompdf.php')) {
            if (!file_exists(BASE . 'tmp/ttfontdata')) expFile::makeDirectory('tmp/ttfontdata');
            require_once(BASE . 'external/dompdf/dompdf_config.inc.php');
            $this->pdf = new DOMPDF();
            $this->size = strtolower($paper_size);
            $this->orient = $orientation;
            $this->pdf->set_paper($this->size, $this->orient);
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->load_html_file($html);
                } else {
                    $this->pdf->load_html($html);
                }
            }
        }
    }

    /**
     * Render and Return PDF using various options.
     *
     * @param string $mode How to output (constants from this same class).
     * @param string $file The PDF's filename (the usage depends on $mode.
     *
     * @return string|boolean Depending on $mode, this may be success (boolean) or PDF (string).
     */
    public function createpdf($mode, $file)
    {
        $this->pdf->render();
        switch ($mode) {
            case self::$PDF_ASSTRING:
                return $this->pdf->output();
                break;
            case self::$PDF_EMBEDDED:
                $this->pdf->stream($file, array('Attachment' => false));
                break;
            case self::$PDF_SAVEFILE:
                return file_put_contents($file, $this->pdf->output());
                break;
            case self::$PDF_DOWNLOAD:
                $this->pdf->stream($file, array('Attachment' => true));
                break;
            default:
                $this->pdf->stream($file, array('Attachment' => HTMLTOPDF_OUTPUT));
        }
        return true;
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     *
     * @param string $orientation orientation of paper
     */
    public function set_orientation($orientation)
    {
        $this->orient = $orientation;
        $this->pdf->set_paper($this->size, $this->orient);
    }

    /**
     * Set page/paper size.
     *
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size)
    {
        $this->size = strtolower($size);
        $this->pdf->set_paper($this->size, $this->orient);
    }

    /**
     * Set page/paper size.
     *
     * @param string $size        Formal paper size (eg; A4, letter...)
     * @param string $orientation orientation of paper
     */
    public function set_paper($size, $orientation = 'portrait')
    {
        if (!empty($size)) {
            $this->size = strtolower($size);
        }
        if (!empty($orientation)) {
            $this->orient = $orientation;
        }
        $this->pdf->set_paper($this->size, $this->orient);
    }

    /**
     * Set html content by string.
     *
     * @param string $html html content.
     * @param null   $encoding
     */
    public function set_html($html, $encoding = null)
    {
        $this->pdf->load_html($html, $encoding);
    }

    /**
     * Set html content by file.
     *
     * @param string $htmlfile the html file to use
     */
    public function set_html_file($htmlfile)
    {
        $this->pdf->load_html_file($htmlfile);
    }

    /**
     * Convert HTML to PDF.
     */
    public function render()
    {
        $this->pdf->render();
    }

    /**
     * Streams the PDF to the client.
     *
     * @param string $filename the pdf file to output
     * @param array  $options  options
     *                         'compress' = > 1 or 0 - apply content stream compression, this is on (1) by default
     *                         ◦   'Attachment' => 1 or 0 - if 1, force the browser to open a download dialog, on (1) by default
     */
    public function stream($filename, $options = null)
    {
        $this->pdf->stream($filename, $options);
    }

    /**
     * Return PDF as a string.
     *
     * @param int $compress compress the output
     *
     * @return string
     */
    public function output($compress = null)
    {
        return $this->pdf->output($compress);
    }

    public function set_grayscale($mode)
    {
    }

}

/**
 * This is the class expDOMPDF070
 * a wrapper for using dompdf v0.7.0
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expDOMPDF070 extends expDOMPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return file_exists(BASE . 'external/dompdf070/autoload.inc.php');
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/dompdf070/autoload.inc.php')) {
            if (!file_exists(BASE . 'tmp/ttfontdata'))
                expFile::makeDirectory('tmp/ttfontdata');
            /**
             * Disable link creation
             *
             * If this setting is set to true, DOMPDF will not embed active links
             *
             * @var bool
             */
            define("DOMPDF_DISABLE_LINKS", true);
            require_once(BASE . 'external/dompdf070/autoload.inc.php');
            $this->pdf = new Dompdf\Dompdf();
            $this->size = strtolower($paper_size);
            $this->orient = $orientation;
            $this->isRemoteEnabled = true;
            $this->isJavascriptEnabled = false;
            $this->pdf->setPaper($this->size, $this->orient);
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->loadHtmlFile($html);
                } else {
                    $this->pdf->loadHtml($html);
                }
            }
        }
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     *
     * @param string $orientation orientation of paper
     */
    public function set_orientation($orientation)
    {
        $this->orient = $orientation;
        $this->pdf->setPaper($this->size, $this->orient);
    }

    /**
     * Set page/paper size.
     *
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size)
    {
        $this->size = strtolower($size);
        $this->pdf->setPaper($this->size, $this->orient);
    }

    /**
     * Set page/paper size.
     *
     * @param string $size        Formal paper size (eg; A4, letter...)
     * @param string $orientation orientation of paper
     */
    public function set_paper($size, $orientation = 'portrait')
    {
        if (!empty($size)) {
            $this->size = strtolower($size);
        }
        if (!empty($orientation)) {
            $this->orient = $orientation;
        }
        $this->pdf->setPaper($this->size, $this->orient);
    }

    /**
     * Set html content by string.
     *
     * @param string $html html content.
     * @param null   $encoding
     */
    public function set_html($html, $encoding = null)
    {
        $this->pdf->loadHtml($html, $encoding);
    }

    /**
     * Set html content by file.
     *
     * @param string $htmlfile the html file to use
     */
    public function set_html_file($htmlfile)
    {
        $this->pdf->loadHtmlFile($htmlfile);
    }

}

/**
 * This is the class expDOMPDF080
 * a wrapper for using dompdf v0.8.0+
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expDOMPDF080 extends expDOMPDF070
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed()
    {
        return file_exists(BASE . 'external/dompdf' . DOMPDF8_VERSION . '/autoload.inc.php');
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size page size
     * @param string $orientation page orientation
     * @param string $html html code for page
     * @param bool $use_file a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/dompdf' . DOMPDF8_VERSION . '/autoload.inc.php')) {
            if (!file_exists(BASE . 'tmp/ttfontdata'))
                expFile::makeDirectory('tmp/ttfontdata');
            /**
             * Disable link creation
             *
             * If this setting is set to true, DOMPDF will not embed active links
             *
             * @var bool
             */
            define("DOMPDF_DISABLE_LINKS", true);
            require_once(BASE . 'external/dompdf' . DOMPDF8_VERSION . '/autoload.inc.php');
            $this->pdf = new Dompdf\Dompdf();
            $this->size = $paper_size;
            $this->orient = $orientation;
            $this->isRemoteEnabled = true;
            $this->isJavascriptEnabled = false;
            $this->pdf->setPaper($this->size, $this->orient);
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->loadHtmlFile($html);
                } else {
                    $this->pdf->loadHtml($html);
                }
            }
        }
    }
}

/**
 * This is the class expMPDF
 * a wrapper for using mPDF v5.7
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expMPDF extends expHtmlToPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return file_exists(BASE . 'external/MPDF57/mpdf.php');
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/MPDF57/mpdf.php')) {
            if (!defined("_MPDF_TEMP_PATH")) define("_MPDF_TEMP_PATH", BASE . 'tmp/cache/');
            if (!defined("_MPDF_TTFONTDATAPATH")) define("_MPDF_TTFONTDATAPATH", BASE . 'tmp/ttfontdata/');
            if (!file_exists(BASE . 'tmp/ttfontdata')) expFile::makeDirectory('tmp/ttfontdata');
            ini_set('display_errors', 0);  // warnings must be turned off to work
            require_once(BASE . 'external/MPDF57/mpdf.php');
            $this->size = $paper_size;
            $this->orient = strtoupper($orientation[0]);
            $this->pdf = new mPDF(null, $this->size, 0, 15, 15, 16, 16, 9, 9, $this->orient);
            $this->pdf->setBasePath(URL_BASE);
            $this->pdf->debug = HTMLTOPDF_DEBUG;
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->WriteHTML(file_get_contents($html));
                } else {
                    $this->pdf->WriteHTML($html);
                }
            }
            if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        }
    }

    /**
     * Render and Return PDF using various options.
     *
     * @param string $mode How to output (constants from this same class).
     * @param string $file The PDF's filename (the usage depends on $mode.
     *
     * @return string|boolean Depending on $mode, this may be success (boolean) or PDF (string).
     */
    public function createpdf($mode, $file)
    {
//        $this->pdf->render();
        ini_set('display_errors', 0);  // warnings must be turned off to work
        switch ($mode) {
            case self::$PDF_SAVEFILE:
                return file_put_contents($file, $this->pdf->Output('S'));
                break;
            case self::$PDF_ASSTRING:
            case self::$PDF_EMBEDDED:
            case self::$PDF_DOWNLOAD:
            default:
                $this->pdf->Output($file, $mode);
        }
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        return true;
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     *
     * @param string $orientation orientation of paper
     */
    public function set_orientation($orientation)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->_setPageSize($this->size,$orientation);
        $this->orient = strtoupper($orientation[0]);
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set page/paper size.
     *
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->_setPageSize($size,$this->orient);
        $this->size = $size;
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set page/paper size.
     *
     * @param string $size        Formal paper size (eg; A4, letter...)
     * @param string $orientation orientation of paper
     */
    public function set_paper($size, $orientation = 'portrait')
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->_setPageSize($size,$orientation);
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set html content by string.
     *
     * @param string $html html content.
     * @param null   $encoding
     */
    public function set_html($html, $encoding = null)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->WriteHTML($html, $encoding);
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set html content by file.
     *
     * @param string $htmlfile the html file to use
     */
    public function set_html_file($htmlfile)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->WriteHTML(file_get_contents($htmlfile));
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Convert HTML to PDF.
     */
    public function render()
    {
        // MPDF doesn't require a render call
    }

    /**
     * Streams the PDF to the client.
     *
     * @param string $filename the pdf file to output
     * @param array  $options  options
     *                         'compress' = > 1 or 0 - apply content stream compression, this is on (1) by default
     *                         ◦   'Attachment' => 1 or 0 - if 1, force the browser to open a download dialog, on (1) by default
     */
    public function stream($filename, $options = null)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->Output($filename, 'D');
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Return PDF as a string.
     *
     * @param int $compress compress the output
     *
     * @return string
     */
    public function output($compress = null)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $out = $this->pdf->Output(null, 'S');
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        return $out;
    }

    public function set_grayscale($mode)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        if ($mode) {
            $this->pdf->restrictColorSpace = 1;
        } else {
            $this->pdf->restrictColorSpace = 0;

        }
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

}

/**
 * This is the class expMPDF6
 * a wrapper for using mPDF v6.1
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expMPDF6 extends expMPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return file_exists(BASE . 'external/mpdf61/mpdf.php');
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/mpdf61/mpdf.php')) {
            if (!defined("_MPDF_TEMP_PATH")) define("_MPDF_TEMP_PATH", BASE . 'tmp/cache/');
            if (!defined("_MPDF_TTFONTDATAPATH")) define("_MPDF_TTFONTDATAPATH", BASE . 'tmp/ttfontdata/');
            if (!file_exists(BASE . 'tmp/ttfontdata')) expFile::makeDirectory('tmp/ttfontdata');
            ini_set('display_errors', 0);  // warnings must be turned off to work
            require_once(BASE . 'external/mpdf61/mpdf.php');
            $this->size = $paper_size;
            $this->orient = strtoupper($orientation[0]);
            $this->pdf = new mPDF(null, $this->size, 0, 15, 15, 16, 16, 9, 9, $this->orient);
            $this->pdf->setBasePath(URL_BASE);
            $this->pdf->debug = HTMLTOPDF_DEBUG;
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->WriteHTML(file_get_contents($html));
                } else {
                    $this->pdf->WriteHTML($html);
                }
            }
            if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        }
    }

}

/**
 * This is the class expMPDF7
 * a wrapper for using mPDF v7.x
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expMPDF7 extends expMPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return (
            file_exists(BASE . 'external/mpdf-' . MPDF7_VERSION . '/src/Mpdf.php') &&
            file_exists(BASE . 'external/log-' . LOG_VERSION . '/autoload.php')
        );
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/mpdf-' . MPDF7_VERSION . '/src/Mpdf.php') && file_exists(BASE . 'external/log-' . LOG_VERSION . '/autoload.php')) {
            if (!defined("_MPDF_TEMP_PATH")) define("_MPDF_TEMP_PATH", BASE . 'tmp/cache/');
            if (!defined("_MPDF_TTFONTDATAPATH")) define("_MPDF_TTFONTDATAPATH", BASE . 'tmp/ttfontdata/');
            if (!file_exists(BASE . 'tmp/ttfontdata')) expFile::makeDirectory('tmp/ttfontdata');
            ini_set('display_errors', 0);  // warnings must be turned off to work
            require_once(BASE . 'external/log-' . LOG_VERSION . '/autoload.php');
            if (version_compare(PHP_VERSION, '7.0.0', 'lt')) {
                require_once(BASE . 'external/random_compat-' . RANDOM_VERSION . '/lib/random.php');
            }
            require_once(BASE . 'external/mpdf-' . MPDF7_VERSION . '/src/autoload.php');
            $this->size = $paper_size;
            $this->orient = strtoupper($orientation[0]);
            $this->pdf = new Mpdf\Mpdf(array(null, $this->size, 0, 15, 15, 16, 16, 9, 9, $this->orient));
            $this->pdf->setBasePath(URL_BASE);
            $this->pdf->debug = HTMLTOPDF_DEBUG;
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->WriteHTML(file_get_contents($html));
                } else {
                    $this->pdf->WriteHTML($html);
                }
            }
            if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        }
    }

}

/**
 * This is the class expMPDF8
 * a wrapper for using mPDF v8.0.x
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expMPDF8 extends expMPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return (
            file_exists(BASE . 'external/mpdf-' . MPDF8_VERSION . '/src/Mpdf.php') &&
            file_exists(BASE . 'external/log-' . LOG_VERSION . '/autoload.php') &&
            file_exists(BASE . 'external/FPDI-' . FPDI_VERSION . '/src/autoload.php')
        );
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/mpdf-' . MPDF8_VERSION . '/src/Mpdf.php') && file_exists(BASE . 'external/log-' . LOG_VERSION . '/autoload.php')) {
            if (!defined("_MPDF_TEMP_PATH")) define("_MPDF_TEMP_PATH", BASE . 'tmp/cache/');
            if (!defined("_MPDF_TTFONTDATAPATH")) define("_MPDF_TTFONTDATAPATH", BASE . 'tmp/ttfontdata/');
            if (!file_exists(BASE . 'tmp/ttfontdata')) expFile::makeDirectory('tmp/ttfontdata');
            ini_set('display_errors', 0);  // warnings must be turned off to work
            require_once(BASE . 'external/log-' . LOG_VERSION . '/autoload.php');
            if (version_compare(PHP_VERSION, '7.0.0', 'lt')) {
                require_once(BASE . 'external/random_compat-' . RANDOM_VERSION . '/lib/random.php');
            }
            require_once(BASE . 'external/FPDI-' . FPDI_VERSION . '/src/autoload.php');
            require_once(BASE . 'external/mpdf-' . MPDF8_VERSION . '/src/autoload.php');
            $this->size = $paper_size;
            $this->orient = strtoupper($orientation[0]);
            $this->pdf = new Mpdf\Mpdf(array(null, $this->size, 0, 15, 15, 16, 16, 9, 9, $this->orient));
            $this->pdf->setBasePath(URL_BASE);
            $this->pdf->debug = HTMLTOPDF_DEBUG;
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->WriteHTML(file_get_contents($html));
                } else {
                    $this->pdf->WriteHTML($html);
                }
            }
            if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        }
    }

}

/**
 * This is the class expMPDF81
 * a wrapper for using mPDF v8.1.x
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expMPDF81 extends expMPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return (
            file_exists(BASE . 'external/mpdf-' . MPDF8_VERSION . '/src/Mpdf.php') &&
            file_exists(BASE . 'external/log-' . LOG_VERSION . '/autoload.php') &&
            file_exists(BASE . 'external/FPDI-' . FPDI_VERSION . '/src/autoload.php') &&
            file_exists(BASE . 'external/message-factory-1.0.1/src/autoload.php') &&
            file_exists(BASE . 'external/http-message-1.0/src/autoload.php')
        );
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        if (file_exists(BASE . 'external/mpdf-' . MPDF8_VERSION . '/src/Mpdf.php') && file_exists(BASE . 'external/log-' . LOG_VERSION . '/autoload.php')) {
            if (!defined("_MPDF_TEMP_PATH")) define("_MPDF_TEMP_PATH", BASE . 'tmp/cache/');
            if (!defined("_MPDF_TTFONTDATAPATH")) define("_MPDF_TTFONTDATAPATH", BASE . 'tmp/ttfontdata/');
            if (!file_exists(BASE . 'tmp/ttfontdata')) expFile::makeDirectory('tmp/ttfontdata');
            ini_set('display_errors', 0);  // warnings must be turned off to work
            require_once(BASE . 'external/log-' . LOG_VERSION . '/autoload.php');
            if (version_compare(PHP_VERSION, '7.0.0', 'lt')) {
                require_once(BASE . 'external/random_compat-' . RANDOM_VERSION . '/lib/random.php');
            }
            require_once(BASE . 'external/FPDI-' . FPDI_VERSION . '/src/autoload.php');
            require_once(BASE . 'external/message-factory-1.0.1/src/autoload.php');
            require_once(BASE . 'external/http-message-1.0/src/autoload.php');
            require_once(BASE . 'external/mpdf-' . MPDF8_VERSION . '/src/autoload.php');
            $this->size = $paper_size;
            $this->orient = strtoupper($orientation[0]);
            $this->pdf = new Mpdf\Mpdf(array(null, $this->size, 0, 15, 15, 16, 16, 9, 9, $this->orient));
            $this->pdf->setBasePath(URL_BASE);
            $this->pdf->debug = HTMLTOPDF_DEBUG;
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->WriteHTML(file_get_contents($html));
                } else {
                    $this->pdf->WriteHTML($html);
                }
            }
            if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        }
    }

}

/**
 * This is the class expHTML2PDF
 * a wrapper for using html2pdf v4.6.x
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expHTML2PDF extends expHtmlToPDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return file_exists(BASE . 'external/html2pdf/html2pdf.class.php') && file_exists(BASE . 'external/TCPDF/tcpdf.php');
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        $html2pdf_loc = BASE . 'external/html2pdf/';
        $tcpdf_loc = BASE . 'external/TCPDF/';
        if (file_exists($html2pdf_loc . 'html2pdf.class.php') && file_exists($tcpdf_loc . 'tcpdf.php')) {
            if (!file_exists(BASE . 'tmp/ttfontdata'))
                expFile::makeDirectory('tmp/ttfontdata');
            require_once($html2pdf_loc . 'html2pdf.class.php');
            require_once($tcpdf_loc . 'tcpdf.php');
            require_once($html2pdf_loc . '_class/locale.class.php');
            require_once($html2pdf_loc . '_class/myPdf.class.php');
            require_once($html2pdf_loc . '_class/exception.class.php');
            require_once($html2pdf_loc . '_class/parsingCss.class.php');
            require_once($html2pdf_loc . '_class/parsingHtml.class.php');
            $this->size = $paper_size;
            $this->orient = strtoupper($orientation[0]);
            $this->pdf = new HTML2PDF($this->size, $this->orient, substr(LOCALE, 0, 2));
            if (HTMLTOPDF_DEBUG)
                $this->pdf->setModeDebug();
            $this->pdf->setDefaultFont('FreeSans');  // keep from crashing when a requested font isn't available
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->WriteHTML(file_get_contents($html));
                } else {
                    $this->pdf->WriteHTML($html);
                }
            }
            if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        }
    }

    /**
     * Render and Return PDF using various options.
     *
     * @param string $mode How to output (constants from this same class).
     * @param string $file The PDF's filename (the usage depends on $mode.
     *
     * @return string|boolean Depending on $mode, this may be success (boolean) or PDF (string).
     */
    public function createpdf($mode, $file)
    {
//        $this->pdf->render();
        ini_set('display_errors', 0);  // warnings must be turned off to work
        switch ($mode) {
            case self::$PDF_SAVEFILE:
                return file_put_contents($file, $this->pdf->Output('S'));
                break;
            case self::$PDF_ASSTRING:
            case self::$PDF_EMBEDDED:
            case self::$PDF_DOWNLOAD:
            default:
                $this->pdf->Output($file, $mode);
        }
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        return true;
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     *
     * @param string $orientation orientation of paper
     */
    public function set_orientation($orientation)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->orient = strtoupper($orientation[0]);
        $this->pdf->pdf->setDisplayMode('fullpage', $this->size, $this->orient);  //FIXME method no longer exists???
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set page/paper size.
     *
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->_format($size);  //FIXME protected property???
        $this->size = $size;
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set page/paper size.
     *
     * @param string $size        Formal paper size (eg; A4, letter...)
     * @param string $orientation orientation of paper
     */
    public function set_paper($size, $orientation = 'portrait')
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->_format($size);  //FIXME protected property???
        $this->size = $size;
        $this->orient = strtoupper($orientation[0]);
        $this->pdf->pdf->setDisplayMode('fullpage', $this->size, $this->orient);  //FIXME method no longer exists???
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set html content by string.
     *
     * @param string $html html content.
     * @param null   $encoding
     */
    public function set_html($html, $encoding = null)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->WriteHTML($html);
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Set html content by file.
     *
     * @param string $htmlfile the html file to use
     */
    public function set_html_file($htmlfile)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->WriteHTML(file_get_contents($htmlfile));
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Convert HTML to PDF.
     */
    public function render()
    {
        // MPDF doesn't require a render call
    }

    /**
     * Streams the PDF to the client.
     *
     * @param string $filename the pdf file to output
     * @param array  $options  options
     *                         'compress' = > 1 or 0 - apply content stream compression, this is on (1) by default
     *                         ◦   'Attachment' => 1 or 0 - if 1, force the browser to open a download dialog, on (1) by default
     */
    public function stream($filename, $options = null)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $this->pdf->Output($filename, 'D');
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

    /**
     * Return PDF as a string.
     *
     * @param int $compress compress the output
     *
     * @return string
     */
    public function output($compress = null)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        $out = $this->pdf->Output(null, 'S');
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        return $out;
    }

    public function set_grayscale($mode)
    {
        ini_set('display_errors', 0);  // warnings must be turned off to work
        if ($mode) {
            $this->pdf->restrictColorSpace = 1;
        } else {
            $this->pdf->restrictColorSpace = 0;

        }
        if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
    }

}

/**
 * This is the class expHTML2PDF5
 * a wrapper for using html2pdf v5.x
 *
 * @package    Subsystems
 * @subpackage Subsystems
 */
class expHTML2PDF5 extends expHTML2PDF
{

    /**
     * Return status of pdf engine being installed correctly
     */
    public static function installed() {
        return file_exists(BASE . 'external/html2pdf-' . HTML2PDF5_VERSION . '/src/Html2Pdf.php') && file_exists(BASE . 'external/TCPDF-' . TCPDF5_VERSION . '/tcpdf.php');
    }

    /**
     * Constructor: initialize a pdf file file.
     *
     * @param string $paper_size  page size
     * @param string $orientation page orientation
     * @param string $html        html code for page
     * @param bool   $use_file    a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size = HTMLTOPDF_PAPER, $orientation = "portrait", $html = null, $use_file = false)
    {
        $html2pdf_loc = BASE . 'external/html2pdf-' . HTML2PDF5_VERSION . '/src/';
        $tcpdf_loc = BASE . 'external/TCPDF-' . TCPDF5_VERSION . '/';
        if (file_exists($html2pdf_loc . 'Html2Pdf.php') && file_exists($tcpdf_loc . 'tcpdf.php')) {
            if (!file_exists(BASE . 'tmp/ttfontdata'))
                expFile::makeDirectory('tmp/ttfontdata');
            require_once($html2pdf_loc . 'autoload.php');
            require_once($tcpdf_loc . 'tcpdf.php');
            $this->size = $paper_size;
            $this->orient = strtoupper($orientation[0]);
            $this->pdf = new \Spipu\Html2Pdf\Html2Pdf($this->size, $this->orient, substr(LOCALE, 0, 2));
            if (HTMLTOPDF_DEBUG)
                $this->pdf->setModeDebug();
            $this->pdf->setDefaultFont('FreeSans');  // keep from crashing when a requested font isn't available
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->WriteHTML(file_get_contents($html));
                } else {
                    $this->pdf->WriteHTML($html);
                }
            }
            if (DEVELOPMENT) ini_set('display_errors', 1);  // warnings must be turned back on
        }
    }

}

?>