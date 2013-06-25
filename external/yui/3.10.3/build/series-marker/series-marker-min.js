/*
YUI 3.10.3 (build 2fb5187)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("series-marker",function(e,t){e.MarkerSeries=e.Base.create("markerSeries",e.CartesianSeries,[e.Plots],{_setStyles:function(t){return t.marker||(t={marker:t}),t=this._parseMarkerStyles(t),e.MarkerSeries.superclass._mergeStyles.apply(this,[t,this._getDefaultStyles()])}},{ATTRS:{type:{value:"marker"}}})},"true",{requires:["series-cartesian","series-plot-util"]});
