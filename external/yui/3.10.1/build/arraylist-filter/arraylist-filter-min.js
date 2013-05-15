/*
YUI 3.10.1 (build 8bc088e)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("arraylist-filter",function(e,t){e.mix(e.ArrayList.prototype,{filter:function(t){var n=[];return e.Array.each(this._items,function(e,r){e=this.item(r),t(e)&&n.push(e)},this),new this.constructor(n)}})},"3.10.1",{requires:["arraylist"]});
