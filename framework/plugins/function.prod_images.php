<?php
##################################################
#
# Copyright (c) 2004-2021 OIC Group, Inc.
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
 * Smarty {prod_images} function plugin
 *
 * Type:     function<br>
 * Name:     prod_images<br>
 * Purpose:  display product images
 *
 * @param         $params
 * @param \Smarty $smarty
 *
 * @package Smarty-Plugins
 * @subpackage Function
 */
function smarty_function_prod_images($params,&$smarty) {
    //load the {img} plugin
    foreach ($smarty->smarty->plugins_dir as $value) {
        $filepath = $value ."/function.img.php";
        if (file_exists($filepath)) {
            require_once $filepath;
            break;
        }
    }

    $rec = $params['record'];

    if ($rec->main_image_functionality == 'iws') {
        $images = $rec->expFile['imagesforswatches'];
    } else {
        $images = $rec->expFile['mainimage'];
    }
    //ref for additional images so we can play with the array
    $additionalImages = !empty($rec->expFile['images']) ? $rec->expFile['images'] : array();

    $mainImages = !empty($additionalImages) ? array_merge($images, $additionalImages) : $images;

//    $mainthmb = !empty($rec->expFile['mainthumbnail'][0]) ? $rec->expFile['mainthumbnail'][0] : $mainImages[0] ;
//    $addImgs = array_merge(array($mainthmb),$additionalImages);
    $addImgs = $additionalImages;

    //pulling in store configs. This is a placeholder for now, so we'll manually set them til we get that worked in.
    $config = $smarty->getTemplateVars('config');

    // $config = array(
    //     "listing-width"=>148,
    //     "listing-height"=>148,
    //     "disp-width"=>200,
    //     "disp-height"=>250,
    //     "thmb-box"=>40,
    //     "swatch-box"=>30,
    //     "swatch-pop"=>100
    //     );

    switch ($params['display']) {
        case 'single':
        case 'thumbnail':
        case 'featured':
        default;
            $class = "ecom-image";
            $class .= !empty($params['class']) ? ' ' . $params['class'] : '' ;
            if ($rec->parent_id) {
                $link = makelink(array("controller"=>"store","action"=>"show","id"=>$rec->parent_id));
            } else {
                $link = makelink(array("controller"=>"store","action"=>"show","title"=>$rec->sef_url));
            }
            $html = '<a class="prod-img" href="'.$link.'" title="' . gt('View') . ' ' . $rec->title.'">';
                $width = !empty($params['width']) ? $params['width'] : (!empty($config["listingwidth"]) ? $config["listingwidth"] : 100) ;
                $imgparams = array("constraint"=>1,
                                   "file_id"=>$images[0]->id,
                                   "w"=>$width,
                                   "h"=>$config["listingheight"],
                                   "return"=>1,
                                   "class"=>$class
                                   );

            if (!$images[0]->id) {
                unset($imgparams['file_id']);
                $imgparams['src'] = PATH_RELATIVE . 'framework/modules/ecommerce/assets/images/no-image.jpg';
                $imgparams['alt'] = gt('No image found for').' '.$rec->title;
            }
            $img = smarty_function_img($imgparams,$smarty);

            $html .= $img;
            $html .= '</a>';
        break;
        case 'main':
            // if we have only 1 image to display, left do a little math to figure out how tall to make our display box
            if (count($addImgs)<=1) {
                $config['displayheight'] = ceil(($mainImages[0]->image_height * $config['displaywidth'])/$mainImages[0]->image_width);
            }

            if (count($addImgs)>1) {
                $adi = '<ul class="thumbnails">';
                for ($i = 0, $iMax = count($addImgs); $i < $iMax; $i++) {
                    $thumbparams = array("h"=>$config['addthmbw'],"w"=>$config['addthmbh'],"zc"=>1,"file_id"=>$addImgs[$i]->id,"return"=>1,"class"=>"thumnail");
                    $thmb = '<li>'.smarty_function_img($thumbparams,$smarty).'</li>';
                }
                $adi .= $thmb;
                $adi .= '</ul>';
            }

            // shrink shrink the display window to fit the selected image if no height is set
            if ($config['displayheight']==0) {
                $config['displayheight'] = (($config['displaywidth'] * $mainImages[0]->image_height) / $mainImages[0]->image_width);
            }
            $html = '<div class="ecom-images loading-images" style="width:'.$config['displaywidth'].'px;">';

            // if configured, the additional thumb images will display at the bottom
            $html .= ($config['thumbsattop']==1) ? $adi : '';

            $html .= '<ul class="enlarged" style="height:'.$config['displayheight'].'px;width:'.$config['displaywidth'].'px;">';

            for ($i = 0, $iMax = count($mainImages); $i < $iMax; $i++) {
                $imgparams = array("w"=>$config['displaywidth'],"file_id"=>$mainImages[$i]->id,"return"=>1,"class"=>"large-img");
                $img = '<li>'.smarty_function_img($imgparams,$smarty).'</li>';
            }
            $html .= $img;
            $html .= '</ul>';

            // if configured, the additional thumb images will display at the bottom
            $html .= ($config['thumbsattop']!=1) ? $adi : '';

            $html .= '</div>';

            // javascripting
            $js = "
                YUI(EXPONENT.YUI3_CONFIG).use('*', function(Y) {
                    // set up the images with correct z-indexes to put the first image on top
                    var imgs = Y.all('.ecom-images img.large-img');
                    var thumbs = Y.all('.thumbnails img');
                    var swatches = Y.all('.swatches .swatch');

                    //remove loading
                    Y.one('.loading-images').removeClass('loading-images');

                    var resetZ = function(n,y){
                        n.setStyles({'zIndex':0,'display':'none'});
                        n.set('id','exp-ecom-msi-'+y);
                    }

                    imgs.each(resetZ);
                    imgs.item(0).setStyles({'zIndex':'1','display':'block'});

                    swatches.each(function(n,y){
                        n.set('id','exp-ecom-ms-'+y)
                    });

                    swatches.on('click',function(e){
                        imgs.each(resetZ);
                        var curImg = imgs.item(swatches.indexOf(e.target));
                        var imgWin = curImg.ancestor('ul.enlarged');
                        imgWin.setStyle('height',curImg.get('height')+'px');
                        //animImgWin(imgWin,curImg.get('height'));
                        curImg.setStyles({'zIndex':'1','display':'block'});
                    });

                    thumbs.on('click',function(e){
                        imgs.each(resetZ);

                        if (swatches.size()!=0) {
                            var processedIndex = thumbs.indexOf(e.target)==0 ? 0 : swatches.size()+thumbs.indexOf(e.target)-1;
                        } else {
                            var processedIndex = thumbs.indexOf(e.target);
                        }
                        var curImg = imgs.item(processedIndex);
                        curImg.ancestor('ul.enlarged').setStyle('height',curImg.get('height')+'px');
                        curImg.setStyles({'zIndex':'1','display':'block'});
                    });

                    // animation...  too much for now, but we'll leave the code
                    var animImgWin = function (node,h) {
                        var hAnim = new Y.Anim({
                                node: node,
                                to: {height: h},
                                easing:Y.Easing.easeOut,
                                duration:0.5
                        });
                        hAnim.run();
                    }

                });
            ";
            expJavascript::pushToFoot(array(
                "unique"=>'imgswatches',
                "yui3mods"=>"node,anim",
                "content"=>$js,
                "src"=>""
             ));
        break;
        case 'swatches':
            $html = '<ul class="swatches">';
            $swatches = $rec->expFile['swatchimages'];
            for ($i = 0, $iMax = count($swatches); $i < $iMax; $i++) {
                $small = array("h"=>$config['swatchsmh'],"w"=>$config['swatchsmw'],"zc"=>1,"file_id"=>$swatches[$i]->id,"return"=>1,"class"=>'swatch');
                $med = array("h"=>$config['swatchpoph'],"w"=>$config['swatchpopw'],"zc"=>1,"file_id"=>$swatches[$i]->id,"return"=>1);
                $swtch = '<li>'.smarty_function_img($small,$smarty);
                $swtch .= '<div>'.smarty_function_img($med,$smarty).'<strong>'.$swatches[$i]->title.'</strong></div>';
                $swtch .= '</li>';
            }
            $html .= $swtch;
            $html .= '</ul>';
        break;
    }

    echo $html;
}

?>

