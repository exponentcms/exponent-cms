/*
YUI 3.10.3 (build 2fb5187)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-marker-stacked",function(e,t){e.StackedMarkerSeries=e.Base.create("stackedMarkerSeries",e.MarkerSeries,[e.StackingUtil],{setAreaData:function(){e.StackedMarkerSeries.superclass.setAreaData.apply(this),this._stackCoordinates.apply(this)}},{ATTRS:{type:{value:"stackedMarker"}}})},"true",{requires:["series-stacked","series-marker"]});
