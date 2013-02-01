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

/**
 * This is the class expHtmlToPDF2
 * a wrapper for using dompdf
 *
 * @package Subsystems
 * @subpackage Subsystems
 */
/** @define "BASE" "../../.." */

class expHtmlToPDF2 {
    /**
     * Private use variables.
     */
    private $pdf=null;
    private $size='A4';
    private $orient='portrait';

    /**
     * Force the client to download PDF file.
     */
    public static $PDF_DOWNLOAD='D';
    /**
     * Returns the PDF file as a string.
     */
    public static $PDF_ASSTRING='S';
    /**
     * When possible, force the client to embed (display) PDF file.
     */
    public static $PDF_EMBEDDED='I';
    /**
     * PDF file is saved into the server space. The path is returned.
     */
    public static $PDF_SAVEFILE='F';
    /**
     * PDF generated as landscape (vertical).
     */
    public static $PDF_PORTRAIT='portrait';
    /**
     * PDF generated as landscape (horizontal).
     */
    public static $PDF_LANDSCAPE='landscape';

    /**
     * Constructor: initialize a pdf file file.
     * @param string $paper_size page size
     * @param string $orientation page orientation
     * @param string $html html code for page
     * @param bool $use_file a flag to show $html is an html file location to be loaded
     */
    public function __construct($paper_size="A4", $orientation="portrait",$html,$use_file=false) {
        if (file_exists(BASE.'external/dompdf/dompdf.php')) {
            require_once(BASE.'external/dompdf/dompdf_config.inc.php');
            $this->pdf = new DOMPDF();
            $this->size = $paper_size;
            $this->orient = $orientation;
            $this->pdf->set_paper($this->size,$this->orient);
            if (!empty($html)) {
                if ($use_file) {
                    $this->pdf->load_html_file($html);
                } else {
                    $this->pdf->load_html($html);
                }
            }
        } else {
            return null;
        }
    }

    /**
     * Render and Return PDF using various options.
     * @param string $mode How to output (constants from this same class).
     * @param string $file The PDF's filename (the usage depends on $mode.
     * @return string|boolean Depending on $mode, this may be success (boolean) or PDF (string).
     */
    public function createpdf($mode,$file){
        $this->pdf->render();
        switch($mode){
            case self::$PDF_ASSTRING:
                return $this->pdf->output();
                break;
            case self::$PDF_EMBEDDED:
                $this->pdf->stream($file,array('Attachment'=>false));
                break;
            case self::$PDF_SAVEFILE:
                return file_put_contents($file,$this->pdf->output());
                break;
            case self::$PDF_DOWNLOAD:
                $this->pdf->stream($file,array('Attachment'=>true));
                break;
            default:
                $this->pdf->stream($file,array('Attachment'=>HTML2PDF_OUTPUT));
        }
        return true;
    }

    /**
     * Set orientation, use constants from this class.
     * By default orientation is portrait.
     * @param string $orientation orientation of paper
     */
    public function set_orientation($orientation){
        $this->orient = $orientation;
        $this->pdf->set_paper($this->size,$this->orient);
    }

    /**
     * Set page/paper size.
     * By default page size is A4.
     * @param string $size Formal paper size (eg; A4, letter...)
     */
    public function set_page_size($size){
        $this->size = $size;
        $this->pdf->set_paper($this->size,$this->orient);
    }

    /**
     * Set page/paper size.
     * By default page size is A4.
     * @param string $size Formal paper size (eg; A4, letter...)
     * @param string $orientation orientation of paper
     */
    public function set_paper($size,$orientation='portrait'){
        if (!empty($size)) $this->size = $size;
        if (!empty($orientation)) $this->orient = $orientation;
        $this->pdf->set_paper($this->size,$this->orient);
    }

    /**
     * Set html content by string.
     * @param string $html html content.
     * @param null $encoding
     */
    public function set_html($html,$encoding=null){
        $this->pdf->load_html($html,$encoding);
    }

    /**
     * Set html content by file.
     * @param string $htmlfile the html file to use
     */
    public function set_html_file($htmlfile){
        $this->pdf->load_html_file($htmlfile);
    }

    /**
     * Convert HTML to PDF.
     */
    public function render(){
        $this->pdf->render();
    }

    /**
     * Return PDF as a string.
     * @param string $filename the pdf file to output
     * @param array $options options
     *   'compress' = > 1 or 0 - apply content stream compression, this is on (1) by default
     ◦   'Attachment' => 1 or 0 - if 1, force the browser to open a download dialog, on (1) by default
     */
    public function stream($filename,$options=null){
        $this->pdf->stream($filename,$options);
    }

    /**
     * Return PDF as a string.
     * @param int $compress compress the output
     * @return string
     */
    public function output($compress=null){
        return $this->pdf->output($compress);
    }

}

?>