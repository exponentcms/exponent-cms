{*
 * Copyright (c) 2004-2013 OIC Group, Inc.
 *
 * This file is part of Exponent
 *
 * Exponent is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the
 * License, or (at your option) any later version.
 *
 * GPL: http://www.gnu.org/licenses/gpl.txt
 *
 *}
{*
    /**
     +------------------------------------------------------------------------+
     | Pixidou - an Open Source AJAX Image Editor                             |
     +------------------------------------------------------------------------+
     | index.php                                                              |
     +------------------------------------------------------------------------+
     | Copyright (c) Asvin Balloo 2008. All rights reserved.                  |
     | Version       0.1                                                      |
     | Last modified 29/10/2008                                               |
     | Email         asvin.balloo@gmail.com                                   |
     | Web           http://htmlblog.net                                      |
     +------------------------------------------------------------------------+
     | This program is free software; you can redistribute it and/or modify   |
     | it under the terms of the GNU General Public License version 2 as      |
     | published by the Free Software Foundation.                             |
     |                                                                        |
     | This program is distributed in the hope that it will be useful,        |
     | but WITHOUT ANY WARRANTY; without even the implied warranty of         |
     | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the          |
     | GNU General Public License for more details.                           |
     |                                                                        |
     | You should have received a copy of the GNU General Public License      |
     | along with this program; if not, write to the                          |
     |   Free Software Foundation, Inc., 59 Temple Place, Suite 330,          |
     |   Boston, MA 02111-1307 USA                                            |
     |                                                                        |
     +------------------------------------------------------------------------+
     */
*}

<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
        <title>Pixidou - {'Open Source AJAX Image Editor'|gettext}</title>
	    <link rel="stylesheet" type="text/css" href="{$smarty.const.YUI2_RELATIVE}yui2-reset-fonts/yui2-reset-fonts.css" >
	    <link rel="stylesheet" type="text/css" href="{$smarty.const.YUI2_RELATIVE}assets/skins/sam/skin.css" >
        <link rel="stylesheet" type="text/css" href="{$smarty.const.PATH_RELATIVE}framework/modules/pixidou/assets/css/pixidou.css" >
    </head>
    <body class="yui-skin-sam">
        <div id="doc3" class="yui-t7 hide">
            <div id="hd">
                <!-- Top menu -->
                <div id="nav-menu" class="yuimenubar yuimenubarnav">
                    <div class="bd">
                        <ul class="first-of-type">
                            <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="#file">{'File'|gettext}</a>
                                <div id="file" class="yuimenu">
                                    <div class="bd">
                                        <ul>
                                            {if $saveog==1}
                                                <li class="yuimenuitem yuimenuitemlabel" id="saveAsIs">{'Save over existing'|gettext}</li>
                                            {/if}
                                            <li class="yuimenuitem yuimenuitemlabel" id="saveAsCopy">{'Save as a copy'|gettext}</li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="quit">{'Quit without saving'|gettext}</li>
                                            <!--li class="yuimenuitem yuimenuitemlabel" id="openFile">Open <em class="helptext">Shift + O</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="saveJpgTool">Save as JPG <em class="helptext">Shift + J</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="savePngTool">Save as PNG <em class="helptext">Shift + P</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="saveGifTool">Save as GIF <em class="helptext">Shift + G</em></li-->
                                        </ul>
                                    </div>
                                </div>      
                            </li>
                            <li class="yuimenubaritem first-of-type"><a class="yuimenubaritemlabel" href="#edit">{'Edit'|gettext}</a>
                                <div id="edit" class="yuimenu">
                                    <div class="bd">
                                        <ul>
                                            <li class="yuimenuitem yuimenuitemlabel" id="undoTool">{'Undo'|gettext} <em class="helptext">{'Shift'|gettext} + Z</em></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#image">{'Image'|gettext}</a>
                                <div id="image" class="yuimenu">
                                    <div class="bd">
                                        <ul>
                                            <li class="yuimenuitem yuimenuitemlabel" id="brightnessTool">{'Brightness'|gettext} <em class="helptext">{'Shift'|gettext} + B</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="contrastTool">{'Contrast'|gettext} <em class="helptext">{'Shift'|gettext} + C</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="cropTool">{'Crop'|gettext} <em class="helptext">{'Shift'|gettext} + V</em></li>
                                            <li class="yuimenuitem">
                                                <a class="yuimenuitemlabel" href="#flipTool">{'Flip'|gettext}</a>
                                                <div id="flipTool" class="yuimenu">
                                                    <div class="bd">
                                                        <ul class="first-of-type">
                                                            <li class="yuimenuitem yuimenuitemlabel" id="flipToolH">{'Horizontal'|gettext} <em class="helptext">{'Shift'|gettext} + H</em></li>
                                                            <li class="yuimenuitem yuimenuitemlabel" id="flipToolV">{'Vertical'|gettext} <em class="helptext">{'Shift'|gettext} + Y</em></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="negativeTool">{'Negative'|gettext} <em class="helptext">{'Shift'|gettext} + N</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="resizeTool">{'Resize'|gettext} <em class="helptext">{'Shift'|gettext} + X</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="rotateTool">{'Rotate'|gettext} <em class="helptext">{'Shift'|gettext} + R</em></li>
                                            <li class="yuimenuitem yuimenuitemlabel" id="tintTool">{'Tint'|gettext} <em class="helptext">{'Shift'|gettext} + T</em></li>
                                        </ul>
                                    </div>                   
                                </div>
                            </li>
                            <li class="yuimenubaritem"><a class="yuimenubaritemlabel" href="#help">{'Help'|gettext}</a>
                                <div id="help" class="yuimenu">
                                    <div class="bd">
                                        <ul>
                                            <li class="yuimenuitem yuimenuitemlabel" id="aboutTool">{'About'|gettext} <em class="helptext">{'Shift'|gettext} + A</em></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                <!-- /Top menu -->
            </div>
            
            <div id="bd">
                <!-- Image container, to be filled via inner HTML -->
                <div id="imageContainer">{$image->id}</div>
                <!-- /Image container, to be filled via inner HTML -->
                
                <!-- Upload form -->
                <div id="uploadPanel">
                    <div class="hd">{'Choose your file to upload'|gettext}</div>
                    <div class="bd">
                        <form action="upload.php" enctype="multipart/form-data" method="post" name="uploadForm" id="uploadForm">
                            Image : <input type="file" name="uploadImage" id="uploadImage" />
                        </form>
                    </div>
                </div>
                <!-- /Upload form -->
                
                <!-- Tint panel -->
                <div id="tintPickerPanel" class="yui-picker-panel">
                    <div class="hd">{'Please choose a color'|gettext}:</div>
                    <div class="bd">
                        <div class="yui-picker" id="tintPicker"></div>
                    </div>
                </div>
                <!-- /Tint panel -->
                
                <!-- Contrast panel -->
                <div id="contrastPanel">
                    <div class="hd">{'Adjust contrast'|gettext}</div>
                    <div class="bd">
                        <div id="contrastSliderBg" class="yui-h-slider" title="Slider">
                            <div id="contrastSliderThumb" class="yui-slider-thumb"><img src="{$smarty.const.PATH_RELATIVE|cat:'framework/modules/pixidou/assets/images/thumb-n.gif'}" alt="" /></div>
                        </div>
                        <p>Value : <span id="contrastSliderValue">0</span></p>
                    </div>
                </div>
                <!-- /Contrast panel -->
                
                <!-- Brightness panel -->
                <div id="brightnessPanel">
                    <div class="hd">{'Adjust brightness'|gettext}</div>
                    <div class="bd">
                        <div id="brightnessSliderBg" class="yui-h-slider" title="Slider">
                            <div id="brightnessSliderThumb" class="yui-slider-thumb"><img src="{$smarty.const.PATH_RELATIVE|cat:'framework/modules/pixidou/assets/images/thumb-n.gif'}" alt="" /></div>
                        </div>
                        <p>Value : <span id="brightnessSliderValue">0</span></p>
                    </div>
                </div>
                <!-- /Brightness panel -->
                
                <!-- Help About panel -->
                <div id="aboutPanel">
                    <div class="hd">{'About'|gettext} Pixidou</div>
                    <div class="bd">
                        <div id="aboutTab" class="yui-navset exp-skin-tabview">
                            <ul class="yui-nav">
                                <li class="selected"><a href="#aboutAbout"><em>{'About'|gettext}</em></a></li>
                                <li><a href="#aboutJoin"><em>Join Pixidou</em></a></li>
                                <li><a href="#aboutLicense"><em>License Agreement</em></a></li>
                            </ul>           
                            <div class="yui-content">
                                <div id="aboutAbout">
                                    <h1>Pixidou 0.1-exp - an open source AJAX image editor</h1>
                                    <br/>
                                    <p>(c) 2008 - Asvin Balloo</p>
                                    <p>(c) 2013 - Extensively enhanced to work within Exponent CMS</p>
                                </div>
                                <div id="aboutJoin">
                                    <p>I'm currently looking for some collaborators to take Pixidou a level higher. </p>
                                    <br/>
                                    <p>If you're interested drop me an email at : asvin.balloo [@] gmail.com</p>
                                    <br/>
                                    <p>I'll happily add you to the list of collaborators on <a href="http://github.com/asvinb/pixidou/tree/master" target="_blank">github</a>.</p>
                                </div>
                                <div id="aboutLicense">
                                    Pixidou is free software; you can redistribute it and/or modify it under the terms of the <a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU General Public License version 2</a>, as published by the Free Software Foundation.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Help About panel -->
            </div>
            
            <div id="ft">
                <!-- Info panel -->
                <div id="infoPanel">
                    <div class="infoZoom">
                        <button type="button" id="zoomInTool" name="zoomInTool" value="Zoom In">{'Zoom'|gettext} +</button>
                        <button type="button" id="zoomOutTool" name="zoomOutTool" value="Zoom Out">{'Zoom'|gettext} -</button>
                        <span id="zoomValue">100</span>%
                    </div>
                </div>
                <!-- /Info panel -->
                
                <!-- Actions panel -->
                <div id="actionsPanel">
                    <button type="button" id="applyTool" name="applyTool" value="Apply">{'Apply'|gettext}</button>
                    <button type="button" id="cancelTool" name="cancelTool" value="Cancel">{'Cancel'|gettext}</button>
                </div>
                <!-- /Actions panel -->
            </div>
        </div>
        
        <!-- Combo-handled YUI JS files: -->
        <script type="text/javascript" src="http://yui.yahooapis.com/combo?2.9.0/build/utilities/utilities.js&2.9.0/build/container/container-min.js&2.9.0/build/menu/menu-min.js&2.9.0/build/button/button-min.js&2.9.0/build/slider/slider-min.js&2.9.0/build/colorpicker/colorpicker-min.js&2.9.0/build/resize/resize-min.js&2.9.0/build/imagecropper/imagecropper-min.js&2.9.0/build/json/json-min.js&2.9.0/build/selector/selector-min.js&2.9.0/build/layout/layout-min.js&2.9.0/build/tabview/tabview-min.js"></script>
        <!-- Using Local YUI JS files: -->
{*        <script type="text/javascript" src="{$smarty.const.YUI3_RELATIVE}yui/yui-min.js"></script>
		<script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-utilities/yui2-utilities-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-container/yui2-container-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-menu/yui2-menu-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-button/yui2-button-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-slider/yui2-slider-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-colorpicker/yui2-colorpicker-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-resize/yui2-resize-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-imagecropper/yui2-imagecropper-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-json/yui2-json-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-selector/yui2-selector-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-layout/yui2-layout-min.js"></script>
        <script type="text/javascript" src="{$smarty.const.YUI2_RELATIVE}yui2-tabview/yui2-tabview-min.js"></script> *}

        <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}exponent.js2.php"></script>

        <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}framework/modules/pixidou/assets/js/ui.js"></script>
        <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}framework/modules/pixidou/assets/js/layout.js"></script>
        <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}framework/modules/pixidou/assets/js/uploader.js"></script>
        <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}framework/modules/pixidou/assets/js/pixidou.js"></script>
        <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}framework/modules/pixidou/assets/js/keys.js"></script>
        <script type="text/javascript" src="{$smarty.const.PATH_RELATIVE}framework/modules/pixidou/assets/js/app.js"></script>
        
        <script type="text/javascript" charset="utf-8">
            YAHOO.util.Event.onDOMReady(function(){ldelim}
                pixidou.updateImage('{$image->filename}',{$image->image_width},{$image->image_height});
            {rdelim});
        </script>
        
        <input type=hidden id="update" value="{$update}">
        <input type=hidden id="fid" value="{$image->id}">
        {*<input type=hidden id="fromCK" value="{$smarty.get.ck}">*}
    </body>
</html>