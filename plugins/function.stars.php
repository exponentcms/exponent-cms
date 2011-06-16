<?php

##################################################
#
# Copyright (c) 2007-2008 OIC Group, Inc.
# Written and Designed by Adam Kessler
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

function smarty_function_stars($params,&$smarty) {

$rating = $params['rating'];
$star_percent = $params['starcount'] / 100;
$number_of_stars = $rating->rating * $star_percent;
$whole_stars = intval($number_of_stars);
$half_stars = ($number_of_stars - $whole_stars) >= .5 ? 1 : 0;

echo '<div class="stars">';
for($i=1; $i<=$params['starcount']; $i++) {
        $percentage = 100 / $params['starcount'] * ($i+1);
        if ($i <= $whole_stars) {
                $class="star active-star";
        } elseif ($i == $whole_stars + $half_stars) {
                $class = "star half-star";
        } else {
                $class = "star";
        }

        echo '<a class="'.$class.'" >&nbsp;</a>';
}
echo '</div>';
}

?>


?>

