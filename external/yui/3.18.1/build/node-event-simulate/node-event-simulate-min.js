/*
YUI 3.18.1 (build f7e7bcb)
Copyright 2014 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("node-event-simulate",function(e,t){e.Node.prototype.simulate=function(t,n){e.Event.simulate(e.Node.getDOMNode(this),t,n)},e.Node.prototype.simulateGesture=function(t,n,r){e.Event.simulateGesture(this,t,n,r)}},"3.18.1",{requires:["node-base","event-simulate","gesture-simulate"]});
