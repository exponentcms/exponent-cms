/*
YUI 3.10.3 (build 2fb5187)
Copyright 2013 Yahoo! Inc. All rights reserved.
Licensed under the BSD License.
http://yuilibrary.com/license/
*/

YUI.add("datatype-date-parse",function(e,t){e.mix(e.namespace("Date"),{parse:function(t){var n=new Date(+t||t);return e.Lang.isDate(n)?n:null}}),e.namespace("Parsers").date=e.Date.parse,e.namespace("DataType"),e.DataType.Date=e.Date},"true");
