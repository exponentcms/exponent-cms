"use strict";
/**
 * @class elfinderworkzone - elFinder container for nav and current directory
 * @author Dmitry (dio) Levashov
 **/
$.fn.elfinderworkzone = function(fm) {
	var cl = 'elfinder-workzone';
	
	this.not('.'+cl).each(function() {
		var wz     = $(this).addClass(cl),
			wdelta = wz.outerHeight(true) - wz.height(),
			prevH  = Math.round(wz.height()),
			parent = wz.parent(),
			fitsize = function() {
				var height = parent.height() - wdelta,
					style  = parent.attr('style'),
					curH   = Math.round(wz.height());
	
				parent.css('overflow', 'hidden')
					.children(':visible:not(.'+cl+')').each(function() {
						var ch = $(this);
		
						if (ch.css('position') != 'absolute' && ch.css('position') != 'fixed') {
							height -= ch.outerHeight(true);
						}
					});
				parent.attr('style', style || '');
				
				height = Math.max(0, Math.round(height));
				if (prevH !== height || curH !== height) {
					prevH  = Math.round(wz.height());
					wz.height(height);
					fm.trigger('wzresize');
				}
			};
			
		parent.add(window).on('resize.' + fm.namespace, fitsize);
		fm.one('cssloaded', function() {
			var old = wdelta;
			wdelta = wz.outerHeight(true) - wz.height();
			if (old !== wdelta) {
				fm.trigger('uiresize');
			}
		}).bind('uiresize', fitsize);
	});
	return this;
};
