<?php

//global $SITE;

$SITE = array();

$SITE['noaazone'] = 'INZ090';  // change this line to your NOAA warning zone.
$SITE['fcsturlNWS'] = "http://forecast.weather.gov/MapClick.php?CityName=Louisville&state=KY&site=LMK&textField1=38.2567&textField2=-86.0004&e=1&TextType=2";
$SITE['tz'] = 'America/Kentucky/Louisville'; // default timezone (new V5.00)
$SITE['fcsticonsdir'] = THEME_ABSOLUTE . 'forecast/forecast/images/';
$SITE['fcsticonsurl'] = URL_BASE . THEME_RELATIVE . 'forecast/forecast/images/';
$SITE['fcsticonsheight'] = 48;  // default height of conditions icon (saratoga-icons.zip)
$SITE['fcsticonswidth'] = 48;  // default width of conditions icon  (saratoga-icons.zip)

$doIncludeNWS = true;
$doPrintNWS = true;

?>