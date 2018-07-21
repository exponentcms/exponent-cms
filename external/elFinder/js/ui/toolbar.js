/**
 * @class  elFinder toolbar
 *
 * @author Dmitry (dio) Levashov
 **/
$.fn.elfindertoolbar = function(fm, opts) {
	"use strict";
	this.not('.elfinder-toolbar').each(function() {
		var commands = fm._commands,
			self     = $(this).addClass('ui-helper-clearfix ui-widget-header ui-corner-top elfinder-toolbar'),
			options  = {
				// default options
				displayTextLabel: false,
				labelExcludeUA: ['Mobile'],
				autoHideUA: ['Mobile'],
				showPreferenceButton: 'none'
			},
			filter   = function(opts) {
				return $.grep(opts, function(v) {
					if ($.isPlainObject(v)) {
						options = Object.assign(options, v);
						return false;
					}
					return true;
				});
			},
			render = function(disabled){
				var name,cmdPref;
				
				$.each(buttons, function(i, b) { b.detach(); });
				self.empty();
				l = panels.length;
				while (l--) {
					if (panels[l]) {
						panel = $('<div class="ui-widget-content ui-corner-all elfinder-buttonset"/>');
						i = panels[l].length;
						while (i--) {
							name = panels[l][i];
							if ((!disabled || !disabled[name]) && (cmd = commands[name])) {
								button = 'elfinder'+cmd.options.ui;
								if (! buttons[name] && $.fn[button]) {
									buttons[name] = $('<div/>')[button](cmd);
								}
								if (buttons[name]) {
									buttons[name].children('.elfinder-button-text')[textLabel? 'show' : 'hide']();
									panel.prepend(buttons[name]);
								}
							}
						}
						
						panel.children().length && self.prepend(panel);
						panel.children(':gt(0)').before('<span class="ui-widget-content elfinder-toolbar-button-separator"/>');

					}
				}
				
				if (cmdPref = commands['preference']) {
					//cmdPref.state = !self.children().length? 0 : -1;
					if (options.showPreferenceButton === 'always' || (!self.children().length && options.showPreferenceButton === 'auto')) {
						//cmdPref.state = 0;
						panel = $('<div class="ui-widget-content ui-corner-all elfinder-buttonset"/>');
						name = 'preference';
						button = 'elfinder'+cmd.options.ui;
						buttons[name] = $('<div/>')[button](cmdPref);
						buttons[name].children('.elfinder-button-text')[textLabel? 'show' : 'hide']();
						panel.prepend(buttons[name]);
						self.append(panel);
					}
				}
				
				(! self.data('swipeClose') && self.children().length)? self.show() : self.hide();
				prevHeight = self[0].clientHeight;
				fm.trigger('toolbarload').trigger('uiresize');
			},
			buttons = {},
			panels   = filter(opts || []),
			dispre   = null,
			uiCmdMapPrev = '',
			prevHeight = 0,
			l, i, cmd, panel, button, swipeHandle, autoHide, textLabel, resizeTm;
		
		// normalize options
		options.showPreferenceButton = options.showPreferenceButton.toLowerCase();
		
		// correction of options.displayTextLabel
		textLabel = fm.storage('toolbarTextLabel');
		if (textLabel === null) {
			textLabel = (options.displayTextLabel && (! options.labelExcludeUA || ! options.labelExcludeUA.length || ! $.grep(options.labelExcludeUA, function(v){ return fm.UA[v]? true : false; }).length));
		} else {
			textLabel = (textLabel == 1);
		}
		
		// add contextmenu
		self.on('contextmenu', function(e) {
				e.stopPropagation();
				e.preventDefault();
				fm.trigger('contextmenu', {
					raw: [{
						label    : fm.i18n('textLabel'),
						icon     : 'accept',
						callback : function() {
							textLabel = ! textLabel;
							self.css('height', '').find('.elfinder-button-text')[textLabel? 'show':'hide']();
							fm.trigger('uiresize').storage('toolbarTextLabel', textLabel? '1' : '0');
						},
					},{
						label    : fm.i18n('toolbarPref'),
						icon     : 'preference',
						callback : function() {
							fm.exec('preference', void(0), {tab: 'toolbar'});
						}
					}],
					x: e.pageX,
					y: e.pageY
				});
			}).on('touchstart', function(e) {
				if (e.originalEvent.touches.length > 1) {
					return;
				}
				self.data('tmlongtap') && clearTimeout(self.data('tmlongtap'));
				self.removeData('longtap')
					.data('longtap', {x: e.originalEvent.touches[0].pageX, y: e.originalEvent.touches[0].pageY})
					.data('tmlongtap', setTimeout(function() {
						self.removeData('longtapTm')
							.trigger({
								type: 'contextmenu',
								pageX: self.data('longtap').x,
								pageY: self.data('longtap').y
							})
							.data('longtap', {longtap: true});
					}, 500));
			}).on('touchmove touchend', function(e) {
				if (self.data('tmlongtap')) {
					if (e.type === 'touchend' ||
							( Math.abs(self.data('longtap').x - e.originalEvent.touches[0].pageX)
							+ Math.abs(self.data('longtap').y - e.originalEvent.touches[0].pageY)) > 4)
					clearTimeout(self.data('tmlongtap'));
					self.removeData('longtapTm');
				}
			}).on('click', function(e) {
				if (self.data('longtap') && self.data('longtap').longtap) {
					e.stopImmediatePropagation();
					e.preventDefault();
				}
			}).on('touchend click', '.elfinder-button', function(e) {
				if (self.data('longtap') && self.data('longtap').longtap) {
					e.stopImmediatePropagation();
					e.preventDefault();
				}
			});

		self.prev().length && self.parent().prepend(this);
		
		render();
		
		fm.bind('open sync select toolbarpref', function() {
			var disabled = Object.assign({}, fm.option('disabledFlip')),
				userHides = fm.storage('toolbarhides'),
				doRender, sel, disabledKeys;
			
			if (! userHides && Array.isArray(options.defaultHides)) {
				userHides = {};
				$.each(options.defaultHides, function() {
					userHides[this] = true;
				});
				fm.storage('toolbarhides', userHides);
			}
			if (this.type === 'select') {
				if (fm.searchStatus.state < 2) {
					return;
				}
				sel = fm.selected();
				if (sel.length) {
					disabled = fm.getDisabledCmds(sel, true);
				}
			}
			
			$.each(userHides, function(n) {
				if (!disabled[n]) {
					disabled[n] = true;
				}
			});
			
			if (Object.keys(fm.commandMap).length) {
				$.each(fm.commandMap, function(from, to){
					if (to === 'hidden') {
						disabled[from] = true;
					}
				});
			}
			
			disabledKeys = Object.keys(disabled);
			if (!dispre || dispre.toString() !== disabledKeys.sort().toString()) {
				render(disabledKeys.length? disabled : null);
				doRender = true;
			}
			dispre = disabledKeys.sort();

			if (doRender || uiCmdMapPrev !== JSON.stringify(fm.commandMap)) {
				uiCmdMapPrev = JSON.stringify(fm.commandMap);
				if (! doRender) {
					// reset toolbar
					$.each($('div.elfinder-button'), function(){
						var origin = $(this).data('origin');
						if (origin) {
							$(this).after(origin).detach();
						}
					});
				}
				if (Object.keys(fm.commandMap).length) {
					$.each(fm.commandMap, function(from, to){
						var cmd = fm._commands[to],
							button = cmd? 'elfinder'+cmd.options.ui : null,
							btn;
						if (button && $.fn[button]) {
							btn = buttons[from];
							if (btn) {
								if (! buttons[to] && $.fn[button]) {
									buttons[to] = $('<div/>')[button](cmd);
									if (buttons[to]) {
										buttons[to].children('.elfinder-button-text')[textLabel? 'show' : 'hide']();
										if (cmd.extendsCmd) {
											buttons[to].children('span.elfinder-button-icon').addClass('elfinder-button-icon-' + cmd.extendsCmd);
										}
									}
								}
								if (buttons[to]) {
									btn.after(buttons[to]);
									buttons[to].data('origin', btn.detach());
								}
							}
						}
					});
				}
			}
		}).bind('resize', function(e) {
			resizeTm && cancelAnimationFrame(resizeTm);
			resizeTm = requestAnimationFrame(function() {
				var h = self[0].clientHeight;
				if (prevHeight !== h) {
					prevHeight = h;
					fm.trigger('uiresize');
				}
			});
		});
		
		if (fm.UA.Touch) {
			autoHide = fm.storage('autoHide') || {};
			if (typeof autoHide.toolbar === 'undefined') {
				autoHide.toolbar = (options.autoHideUA && options.autoHideUA.length > 0 && $.grep(options.autoHideUA, function(v){ return fm.UA[v]? true : false; }).length);
				fm.storage('autoHide', autoHide);
			}
			
			if (autoHide.toolbar) {
				fm.one('init', function() {
					fm.uiAutoHide.push(function(){ self.stop(true, true).trigger('toggle', { duration: 500, init: true }); });
				});
			}
			
			fm.bind('load', function() {
				swipeHandle = $('<div class="elfinder-toolbar-swipe-handle"/>').hide().appendTo(fm.getUI());
				if (swipeHandle.css('pointer-events') !== 'none') {
					swipeHandle.remove();
					swipeHandle = null;
				}
			});
			
			self.on('toggle', function(e, data) {
				var wz    = fm.getUI('workzone'),
					toshow= self.is(':hidden'),
					wzh   = wz.height(),
					h     = self.height(),
					tbh   = self.outerHeight(true),
					delta = tbh - h,
					opt   = Object.assign({
						step: function(now) {
							wz.height(wzh + (toshow? (now + delta) * -1 : h - now));
							fm.trigger('resize');
						},
						always: function() {
							requestAnimationFrame(function() {
								self.css('height', '');
								fm.trigger('uiresize');
								if (swipeHandle) {
									if (toshow) {
										swipeHandle.stop(true, true).hide();
									} else {
										swipeHandle.height(data.handleH? data.handleH : '');
										fm.resources.blink(swipeHandle, 'slowonce');
									}
								}
								toshow && self.scrollTop('0px');
								data.init && fm.trigger('uiautohide');
							});
						}
					}, data);
				self.data('swipeClose', ! toshow).stop(true, true).animate({height : 'toggle'}, opt);
				autoHide.toolbar = !toshow;
				fm.storage('autoHide', Object.assign(fm.storage('autoHide'), {toolbar: autoHide.toolbar}));
			}).on('touchstart', function(e) {
				if (self.scrollBottom() > 5) {
					e.originalEvent._preventSwipeY = true;
				}
			});
		}
	});
	
	return this;
};
