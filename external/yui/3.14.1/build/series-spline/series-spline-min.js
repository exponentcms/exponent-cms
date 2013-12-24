/*
YUI 3.14.1 (build 63049cb)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-spline",function(e,t){e.SplineSeries=e.Base.create("splineSeries",e.LineSeries,[e.CurveUtil,e.Lines],{drawSeries:function(){this.drawSpline()}},{ATTRS:{type:{value:"spline"}}})},"3.14.1",{requires:["series-line","series-curve-util"]});
