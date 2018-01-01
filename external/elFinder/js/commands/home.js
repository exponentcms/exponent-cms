(elFinder.prototype.commands.home = function() {
	"use strict";
	this.title = 'Home';
	this.alwaysEnabled  = true;
	this.updateOnSelect = false;
	this.shortcuts = [{
		pattern     : 'ctrl+home ctrl+shift+up',
		description : 'Home'
	}];
	
	this.getstate = function() {
		var root = this.fm.root(),
			cwd  = this.fm.cwd().hash;
			
		return root && cwd && root != cwd ? 0: -1;
	};
	
	this.exec = function() {
		return this.fm.exec('open', this.fm.root());
	};
	

}).prototype = { forceLoad : true }; // this is required command
