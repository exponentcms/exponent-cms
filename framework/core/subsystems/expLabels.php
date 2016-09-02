<?php
##################################################
#
# Copyright (c) 2004-2016 OIC Group, Inc.
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
 * Example using defaults for html output in Avery 8160 layout
 *
 * $labels = new expLabels(array(  // config
 *     'layout' => "first_name last_name<br />address_1<br />address_2<br />town, postcode")
 * );
 *
 * // List the addresses to used on the labels
 * // Notice how the array keys correspond to the 'layout' element above
 * $addresses = array(
 *    array(
 *        'first_name' => 'Bobbie',
 *        'last_name' => 'Marley',
 *        'address_1' => '132 Reggae Lane',
 *        'address_2' => 'East Hunting',
 *        'town' => 'Northampton',
 *        'postcode' => 'NN2 5TR'
 *    ),
 *    array(
 *        'first_name' => 'James',
 *        'last_name' => 'Shack',
 *        'address_1' => '23 Leapord Road',
 *        'address_2' => 'Oaklingbury',
 *        'town' => 'Cambridge',
 *        'postcode' => 'CB4 7YT'
 *    ),
 *);
 *
 * // Output the labels to the screen
 * $labels->output($addresses);
 */

/*
***************************************************************************
*   Copyright (C) 2011 by Steve Marks                                     *
*   info@biostall.com                                                     *
*                                                                         *
*   Permission is hereby granted, free of charge, to any person obtaining *
*   a copy of this software and associated documentation files (the       *
*   "Software"), to deal in the Software without restriction, including   *
*   without limitation the rights to use, copy, modify, merge, publish,   *
*   distribute, sublicense, and/or sell copies of the Software, and to    *
*   permit persons to whom the Software is furnished to do so, subject to *
*   the following conditions:                                             *
*                                                                         *
*   The above copyright notice and this permission notice shall be        *
*   included in all copies or substantial portions of the Software.       *
*                                                                         *
*   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,       *
*   EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF    *
*   MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.*
*   IN NO EVENT SHALL THE AUTHORS BE LIABLE FOR ANY CLAIM, DAMAGES OR     *
*   OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, *
*   ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR *
*   OTHER DEALINGS IN THE SOFTWARE.                                       *
***************************************************************************
*/

/**
 *    Create Labels in HTML or Word .xml Format
 *
 * @category   PHP
 * @package    Labels
 * @author     Steve Marks <info@biostall.com>
 * @copyright  2011 Steve Marks
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @version    1.0
 * @link       info@biostall.com
 */
class expLabels
{

    // avery 8160 specs
    var $format = "html"; // word, html or pdf (not working)
    var $unit = "in"; // unit of measure: cm, mm, or in
    var $labels_across = 3; // number of labels horizontally across the page
    var $labels_down = 10; // number of labels vertically down the page
    var $label_height = 1; // the height, of each label
    var $label_width = 2.63; // the width, of each label
    var $pitch_horizontal = 2.75; // the width of the label plus horizontal spacing, of each label
    var $pitch_vertical = 1; // the height of the label plus vertical spacing, of each label
    var $page_margin_top = 0.5; // the top page margin
    var $page_margin_side = 0.19; // the left/right page margin
    var $page_height = 11; // the height of the paper
    var $page_width = 8.5; // the width of the paper
    var $align_horizontal = "left"; // the horizontal justification of each label. left, center or right
    var $align_vertical = "center"; // the vertical alignment of each label. top, center or bottom
    var $padding_left = 0.3; // the left padding, of each label
    var $padding_top = 0; // the top padding, of each label
    var $font_face = "Arial"; // The font face to use
    var $font_size = 12; // The font size, in pt, to use

    var $addresses = array();
    var $layout = ""; // e.g., "first_name last_name<br />address_1<br />address_2<br />town, postcode",

    function __construct($config = array())
    {
        if (count($config) > 0) {
            $this->initialize($config);
        }
    }

    function initialize($config = array())
    {
        foreach ($config as $key => $val) {
            if (isset($this->$key)) {
                $this->$key = $val;
            }
        }
    }

    function output($addresses = array())
    {
        $this->addresses = $addresses;
        $this->labels_total = $this->labels_across * $this->labels_down;

        if (count($this->addresses)) {
            switch ($this->format) {
                case "word": {
                    $this->generate_labels_word();
                    break;
                }
                case "html": {
                    $this->generate_labels_html();
                    break;
                }
                case "pdf": { //FIXME
                    die(gt("PDF output is work in progress. Check back soon :)"));
                    $this->generate_labels_pdf();
                    break;
                }
                default: {
                    flash('notice',gt("Invalid format provided. Must be word, pdf or html"));
                }
            }
        } else {
            flash('notice',gt("No addresses provided"));
        }
    }

    //FIXME need to integrate into expHTMLToPDF
    function generate_labels_pdf()
    {
        // calculate the padding
        $this->padding_left = $this->convert($this->padding_left, $this->unit, "px");
        $this->padding_top = $this->convert($this->padding_top, $this->unit, "pt");

        // calculate left and top margins
        $this->page_margin_side = $this->convert($this->page_margin_side, $this->unit, "cm");
        $this->page_margin_top = $this->convert($this->page_margin_top, $this->unit, "cm");

        // calculate label width and label height
        $this->label_height = $this->convert($this->label_height, $this->unit, "pt");
        $this->label_width = $this->convert($this->label_width, $this->unit, "pt");

        // calculate paper width and height
        $this->page_height = $this->convert($this->page_height, $this->unit, "cm");
        $this->page_width = $this->convert($this->page_width, $this->unit, "cm");

        // calculate the spacing
        $this->pitch_horizontal = $this->convert($this->pitch_horizontal, $this->unit, "pt");
        $this->pitch_vertical = $this->convert($this->pitch_vertical, $this->unit, "pt");

        $CI =& get_instance();
        $CI->load->library("cezpdf", array($this->page_width, $this->page_height));

        $CI->cezpdf->selectFont(APPPATH . 'libraries/fonts/Helvetica.afm');
        $CI->cezpdf->ezSetCmMargins($this->page_margin_top, 0, $this->page_margin_side, $this->page_margin_side);

        // setup columns
        $col_names = array();
        $col_options = array();
        for ($i = 0; $i < $this->labels_across; $i++) {
            if ($this->pitch_horizontal - $this->label_width > 0 && $i > 0) {
                $col_names['padding' . $i] = '';
                $col_options['padding' . $i] = array('width' => $this->pitch_horizontal - $this->label_width);
            }
            $col_names['column' . $i] = '';
            $col_options['column' . $i] = array('width' => $this->label_width);
        }
        $table_options = array(
            'width' => 550,
            'showLines' => 0,
            'showHeadings' => 0,
            'shaded' => 0,
            'cols' => $col_options
        );

        $num_x = 0;
        $num_y = 0;
        $num_total = 0;
        $table_data = array();
        $row_table_data = array();
        foreach ($this->addresses as $address) {

            if ($num_total == $this->labels_total) {
                array_push($table_data, $row_table_data);
                $CI->cezpdf->ezTable($table_data, $col_names, '', $table_options);
                $CI->cezpdf->ezNewPage();
                $num_x = 0;
                $num_y = 0;
                $table_data = array();
                $row_table_data = array();
            }

            if ($num_x == $this->labels_across) {
                array_push($table_data, $row_table_data);
                $row_table_data = array();
                $num_y++;
                $num_x = 0;
            }

            if ($num_x < $this->labels_across) {

                // loop through and replace address elements
                $prespace = "";
                for ($i = 0; $i < $this->padding_left / 4; $i++) {
                    $prespace .= " ";
                }
                $search_array = array("<br />", "<br>", "<BR />", "<BR>");
                $replace_array = array("\n" . $prespace, "\n" . $prespace, "\n" . $prespace, "\n" . $prespace);
                foreach ($address as $address_key => $address_value) {
                    array_push($search_array, $address_key);
                    array_push($replace_array, $address[$address_key]);
                }
                if ($this->pitch_horizontal - $this->label_width > 0 && $num_x > 0) {
                    $row_table_data['padding' . $num_x] = '';
                }
                $row_table_data['column' . $num_x] = $prespace . str_replace(
                        $search_array,
                        $replace_array,
                        $this->layout
                    );

            }

            $num_x++;
            $num_total++;

        }
        array_push($table_data, $row_table_data);
        $CI->cezpdf->ezTable($table_data, $col_names, '', $table_options);
        $CI->cezpdf->ezStream();
    }

    function generate_labels_html()
    {
        // calculate the padding
        $this->padding_left = $this->convert($this->padding_left, $this->unit, "cm");
        $this->padding_top = $this->convert($this->padding_top, $this->unit, "cm");

        // calculate left and top margins
        $this->page_margin_side = $this->convert($this->page_margin_side, $this->unit, "cm");
        $this->page_margin_top = $this->convert($this->page_margin_top, $this->unit, "cm");

        // calculate label width and label height
        $this->label_height = $this->convert($this->label_height, $this->unit, "cm");
        $this->label_width = $this->convert($this->label_width, $this->unit, "cm");

        // calculate paper width and height
        $this->page_height = $this->convert($this->page_height, $this->unit, "cm");
        $this->page_width = $this->convert($this->page_width, $this->unit, "cm");

        // calculate the spacing
        $this->pitch_horizontal = $this->convert($this->pitch_horizontal, $this->unit, "cm");
        $this->pitch_vertical = $this->convert($this->pitch_vertical, $this->unit, "cm");
        $output = '<html>
			<head>

			</head>
			<body style="margin:0; font-family:' . $this->font_face . '; font-size:' . $this->font_size . 'pt; width:' . $this->page_width . 'cm">';

        // loop through addresses
        $num_x = 0;
        $num_y = 0;
        $num_total = 0;
        foreach ($this->addresses as $address) {

            // add page break / start and end table tag
            if ($num_total == $this->labels_total) {
                $output .= '</tr></table><br style="page-break-after:always" />';
                $num_total = 0;
                $num_x = 0;
                $num_y = 0;
            }
            if ($num_total == 0) {
                $output .= '<table width="100%" cellpadding="0" cellspacing="0">';
            }

            // start and end row tag
            if ($num_x == $this->labels_across) {
                $output .= '</tr>';
                $num_x = 0;
                $num_y++;
            }
            if ($num_x == 0) {
                if ($this->pitch_vertical - $this->label_height > 0 && $num_y > 0) { // if row required
                    $output .= '<tr>
									<td colspan="' . $this->labels_across . '" style="font-size:1px; height:' . ($this->pitch_vertical - $this->label_height) . 'cm">&nbsp;</td>
								</tr>';
                }
                $output .= '<tr>';
            }

            if ($this->pitch_horizontal - $this->label_width > 0 && $num_x > 0) { // if cell required
                $output .= '<td style="width:' . ($this->pitch_horizontal - $this->label_width) . 'cm; font-size:1pt">&nbsp;</td>';
            }

            $output .= '<td style="width:' . $this->label_width . 'cm; height:' . $this->label_height . 'cm; padding-left:' . $this->padding_left . 'cm; padding-top:' . $this->padding_top . 'cm;" align="' . $this->align_horizontal . '" valign="' . $this->align_vertical . '">';

            // loop through and replace address elements
            $search_array = array();
            $replace_array = array();
            foreach ($address as $address_key => $address_value) {
                array_push($search_array, $address_key);
                array_push($replace_array, $address[$address_key]);
            }
            $address_item = str_replace($search_array, $replace_array, $this->layout);

            $output .= $address_item;

            $output .= '</td>';

            $num_x++;
            $num_total++;
        }
        // Output any remaining cells from the last row
        for ($i = 0; $i < $this->labels_across - $num_x; $i++) {
            if ($this->pitch_horizontal - $this->label_width > 0) { // if cell required
                $output .= '<td style="font-size:1pt">&nbsp;</td>';
            }
            $output .= '<td></td>';
        }
        $output .= '</tr>
				</table>
			
			</body>
		</html>';

        echo $output;
    }

    function generate_labels_word()
    {
        // calculate the padding
        $this->padding_left = $this->convert($this->padding_left, $this->unit);
        $this->padding_top = $this->convert($this->padding_top, $this->unit);

        // calculate left and top margins
        $this->page_margin_side = $this->convert($this->page_margin_side, $this->unit);
        $this->page_margin_top = $this->convert($this->page_margin_top, $this->unit);

        // calculate label width and label height
        $this->label_height = $this->convert($this->label_height, $this->unit);
        $this->label_width = $this->convert($this->label_width, $this->unit);

        // calculate paper width and height
        $this->page_height = $this->convert($this->page_height, $this->unit);
        $this->page_width = $this->convert($this->page_width, $this->unit);

        // calculate the spacing
        $this->pitch_horizontal = $this->convert($this->pitch_horizontal, $this->unit);
        $this->pitch_vertical = $this->convert($this->pitch_vertical, $this->unit);

        $output = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
		<?mso-application progid="Word.Document"?>
		<w:wordDocument xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml">
			<w:docPr>
				<w:view w:val="print"/>
   				<w:zoom w:val="full-page" w:percent="100"/>
			</w:docPr>
			<w:body>';

        $table_definition = '	<w:tblPr>
								  	<w:tblW w:w="0" w:type="auto"/>
								</w:tblPr>
								<w:tblGrid>
								   <w:gridCol w:w="' . $this->label_width . '"/>
								   <w:gridCol w:w="' . $this->label_width . '"/>
								</w:tblGrid>';

        // loop through addresses
        $num_x = 0;
        $num_y = 0;
        $num_total = 0;
        foreach ($this->addresses as $address) {

            // add page break / start and end table tag
            if ($num_total == $this->labels_total) {
                $output .= '</w:tr></w:tbl><w:p><w:r><w:pageBreakBefore/></w:r></w:p>';
                $num_total = 0;
                $num_x = 0;
                $num_y = 0;
            }
            if ($num_total == 0) {
                $output .= '<w:tbl>' . $table_definition;
            }

            // start and end row tag
            if ($num_x == $this->labels_across) {
                $output .= '</w:tr>';
                $num_x = 0;
                $num_y++;
            }
            if ($num_x == 0) {
                if ($this->pitch_vertical - $this->label_height > 0 && $num_y > 0) { // if row required
                    $output .= '<w:tr><w:trPr><w:trHeight w:val="' . ($this->pitch_vertical - $this->label_height) . '"/></w:trPr>
									<w:tc><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>
									<w:tc><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>
									<w:tc><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>
								</w:tr>';
                }
                $output .= '<w:tr><w:trPr><w:trHeight w:val="' . $this->label_height . '"/></w:trPr>';
            }

            if ($this->pitch_horizontal - $this->label_width > 0 && $num_x > 0) { // if cell required
                $output .= '<w:tc><w:tcPr><w:tcW w:w="' . ($this->pitch_horizontal - $this->label_width) . '" w:type="dxa"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>';
            }

            $output .= '<w:tc>
							<w:tcPr>
								<w:tcW w:w="' . $this->label_width . '" w:type="dxa"/>
								<w:vAlign w:val="' . $this->align_vertical . '"/>
							</w:tcPr>
							<w:p>
								<w:pPr>
									<w:jc w:val="' . $this->align_horizontal . '"/>
									<w:spacing w:before="' . $this->padding_top . '"/>
									<w:ind w:left="' . $this->padding_left . '"/>
							  	</w:pPr>
								<w:r>
									<w:rPr>
										<w:rFonts w:ascii="' . $this->font_face . '" w:h-ansi="' . $this->font_face . '" w:cs="' . $this->font_face . '"/>
								   		<w:sz w:val="' . ($this->font_size * 2) . '"/>
										<w:sz-cs w:val="' . ($this->font_size * 2) . '"/>
									</w:rPr>
									<w:t>';

            // loop through and replace address elements
            $search_array = array();
            $replace_array = array();
            foreach ($address as $address_key => $address_value) {
                array_push($search_array, $address_key);
                array_push($replace_array, $address[$address_key]);
            }
            $address_item = str_replace($search_array, $replace_array, $this->layout);

            // replace html with WordML valid tags
            $address_item = str_replace(array("<br />", "<br>", "<BR />", "<BR>"), "<w:br/>", $address_item);

            $output .= $address_item;

            $output .= '</w:t>
								</w:r>
							</w:p>
						</w:tc>';

            $num_x++;
            $num_total++;
        }
        // Output any remaining cells from the last row
        for ($i = 0; $i < $this->labels_across - $num_x; $i++) {
            if ($this->pitch_horizontal - $this->label_width > 0) { // if cell required
                $output .= '<w:tc><w:tcPr><w:tcW w:w="' . ($this->pitch_horizontal - $this->label_width) . '" w:type="dxa"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>';
            }
            $output .= '<w:tc><w:tcPr><w:tcW w:w="' . $this->label_width . '" w:type="dxa"/></w:tcPr><w:p><w:r><w:t></w:t></w:r></w:p></w:tc>';
        }
        $output .= '</w:tr>
				</w:tbl>
				<w:sectPr>
					<w:pgSz w:w="' . $this->page_width . '" w:h="' . $this->page_height . '"/>
					<w:pgMar w:top="' . $this->page_margin_top . '" w:right="' . $this->page_margin_side . '" w:bottom="0" w:left="' . $this->page_margin_side . '" />
				</w:sectPr>
			</w:body>
		</w:wordDocument>';

        $this->output_file('labels.doc', $output);
    }

    function convert($input = 0, $unit_from = "cm", $unit_to = "dxa")
    {
        $output = 0;

        switch ($unit_from) {
            case "in": {
                switch ($unit_to) {
                    case "dxa": {
                        $output = ceil($input * 1440);
                        break;
                    }
                    case "px": {
                        $output = ceil(($input * 2.54) * 37.795275591);
                        break;
                    }
                    case "pt": {
                        $output = ceil(($input * 2.54) * 28.346456693);
                        break;
                    }
                    case "cm": {
                        $output = round(($input * 2.54), 2);
                        break;
                    }
                }
                break;
            }
            case "mm": {
                switch ($unit_to) {
                    case "dxa": {
                        $output = ceil((($input / 10) / 2.54) * 1440);
                        break;
                    }
                    case "px": {
                        $output = ceil(($input / 10) * 37.795275591);
                        break;
                    }
                    case "pt": {
                        $output = ceil(($input / 10) * 28.346456693);
                        break;
                    }
                    case "cm": {
                        $output = round(($input / 10), 2);
                        break;
                    }
                }
                break;
            }
            default: { // presume cm
                switch ($unit_to) {
                    case "dxa": {
                        $output = ceil(($input / 2.54) * 1440);
                        break;
                    }
                    case "px": {
                        $output = ceil($input * 37.795275591);
                        break;
                    }
                    case "pt": {
                        $output = ceil($input * 28.346456693);
                        break;
                    }
                    case "cm": {
                        $output = $input;
                        break;
                    }
                }
                break;
            }
        }

        return $output;
    }

    function output_file($filename = '', $output = '')
    {
        // Set headers
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $filename);
        header("Content-Type: application/vnd.ms-word.main+xml");
        header("Content-Transfer-Encoding: binary");
        echo $output;
    }

}

?>