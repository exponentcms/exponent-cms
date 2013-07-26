/*
YUI 3.11.0 (build d549e5c)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-spline-stacked",function(e,t){e.StackedSplineSeries=e.Base.create("stackedSplineSeries",e.SplineSeries,[e.StackingUtil],{setAreaData:function(){e.StackedSplineSeries.superclass.setAreaData.apply(this),this._stackCoordinates.apply(this)}},{ATTRS:{type:{value:"stackedSpline"}}})},"3.11.0",{requires:["series-stacked","series-spline"]});
