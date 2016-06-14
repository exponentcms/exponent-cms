"use strict";
/**
 * @class  elFinder command "links"
 * Switch to Select Exponent Link dialog
 *
 * @author Dave Leffler
 **/
elFinder.prototype.commands.links = function() {
	this.updateOnSelect = false;

	this.getstate = function() {
        // Helper function to get parameters from the url
        var getUrlParam = function(paramName) {
            var pathArray = window.location.pathname.split( '/' );
            if (paramName == 'update' || paramName == 'filter') {
                if (paramName == 'update') {
                    var parmu = pathArray.indexOf('update');
                    if (parmu > 0)
                        return pathArray[parmu+1];
                } else if (paramName == 'filter') {
                    var parmf = pathArray.indexOf('filter');
                    if (parmf > 0)
                        return pathArray[parmf+1];
                }
            }
            if (EXPONENT.SEF_URLS && pathArray.indexOf(paramName) != -1) {
                var parm = pathArray.indexOf(paramName);
                if (parm > 0)
                    return pathArray[parm+1];
            } else {
                var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
                var match = window.location.search.match(reParam) ;
                return (match && match.length > 1) ? match[1] : '' ;
            }
        };
        var update = getUrlParam('update');
        var filter = getUrlParam('filter');
        if (update !== 'noupdate' && typeof top.tinymce !== 'undefined' && top.tinymce !== null) update = 'tiny';
        if ((update == 'ck' || update == 'tiny') && filter != 'image') {
            return 0;  // icon active
        } else {
            return -1;  // icon disabled
        }
	};
	
	this.exec = function() {
        var fm    = this.fm,
      		dfrd  = $.Deferred().fail(function(error) { error && fm.error(error); }),

            // Helper function to get parameters from the url
            getUrlParam = function(paramName) {
                var pathArray = window.location.pathname.split( '/' );
                if (paramName == 'update' || paramName == 'filter') {
                    if (paramName == 'update') {
                        var parmu = pathArray.indexOf('update');
                        if (parmu > 0)
                            return pathArray[parmu+1];
                    } else if (paramName == 'filter') {  //fixme we never get here?
                        var parmf = pathArray.indexOf('filter');
                        if (parmf > 0)
                            return pathArray[parmf+1];
                    }
                }
                if (EXPONENT.SEF_URLS && pathArray.indexOf(paramName) != -1) {
                    var parm = pathArray.indexOf(paramName);
                    if (parm > 0)
                        return pathArray[parm+1];
                } else {
                    var reParam = new RegExp('(?:[\?&]|&amp;)' + paramName + '=([^&]+)', 'i') ;
                    var match = window.location.search.match(reParam) ;
                    return (match && match.length > 1) ? match[1] : '' ;
                }
            },

            openPageSelector = function() {
                var update = getUrlParam('update');
                if (typeof top.tinymce !== 'undefined' && top.tinymce !== null) update = 'tiny';
                window.resizeTo(320, 600);
                if (update == 'ck') {
                    var funcNum = getUrlParam('CKEditorFuncNum');
                    var partNum = getUrlParam('CKEditor');
                    window.location.href=EXPONENT.PATH_RELATIVE+'framework/modules/file/connector/ckeditor_link.php?ajax_action=1&update=ck&CKEditor='+partNum+'&CKEditorFuncNum='+funcNum+'&langCode=en';
                } else if (update == 'tiny') {
                    window.location.href=EXPONENT.PATH_RELATIVE+'framework/modules/file/connector/ckeditor_link.php?ajax_action=1&update=tiny';
                } else {
                    return dfrd.reject('errLinks');
                }
            };

        openPageSelector();

        return dfrd.resolve();
	}

};
