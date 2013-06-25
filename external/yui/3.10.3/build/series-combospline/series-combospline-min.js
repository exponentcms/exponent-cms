/*
YUI 3.10.3 (build 2fb5187)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-combospline",function(e,t){e.ComboSplineSeries=e.Base.create("comboSplineSeries",e.ComboSeries,[e.CurveUtil],{drawSeries:function(){this.get("showAreaFill")&&this.drawAreaSpline(),this.get("showLines")&&this.drawSpline(),this.get("showMarkers")&&this.drawPlots()}},{ATTRS:{type:{value:"comboSpline"}}})},"true",{requires:["series-combo","series-curve-util"]});
