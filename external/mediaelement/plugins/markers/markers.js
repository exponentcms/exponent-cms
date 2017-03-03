(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(_dereq_,module,exports){
'use strict';

/**
 * Markers plugin
 *
 * This feature allows you to add Visual Cues in the progress time rail.
 * This plugin also lets you register a custom callback function that will be called every time the play position reaches a marker.
 * Marker position and a reference to the MediaElement Player object is passed to the registered callback function for
 * any post processing. Marker color is configurable.
 */

// Feature configuration

Object.assign(mejs.MepDefaults, {
	/**
  * Default marker color
  * @type {String}
  */
	markerColor: '#E9BC3D',
	/**
  * @type {Number[]}
  */
	markers: [],
	/**
  * @type {Function}
  */
	markerCallback: function markerCallback() {}
});

Object.assign(MediaElementPlayer.prototype, {
	/**
  * Feature constructor.
  *
  * Always has to be prefixed with `build` and the name that will be used in MepDefaults.features list
  * @param {MediaElementPlayer} player
  * @param {$} controls
  * @param {$} layers
  * @param {HTMLElement} media
  */
	buildmarkers: function buildmarkers(player, controls, layers, media) {

		if (!player.options.markers.length) {
			return;
		}

		var t = this,
		    currentPos = -1,
		    currentMarker = -1,
		    lastPlayPos = -1,

		// Track backward seek
		lastMarkerCallBack = -1 // Prevents successive firing of callbacks
		;

		for (var i = 0, total = player.options.markers.length; i < total; ++i) {
			controls.find('.' + t.options.classPrefix + 'time-total').append('<span class="' + (t.options.classPrefix + 'time-marker"></span>'));
		}

		media.addEventListener('durationchange', function () {
			player.setmarkers(controls);
		});
		media.addEventListener('timeupdate', function () {
			currentPos = Math.floor(media.currentTime);
			if (lastPlayPos > currentPos) {
				if (lastMarkerCallBack > currentPos) {
					lastMarkerCallBack = -1;
				}
			} else {
				lastPlayPos = currentPos;
			}

			if (player.options.markers.length) {
				for (var _i = 0; _i < player.options.markers.length; ++_i) {
					currentMarker = Math.floor(player.options.markers[_i]);
					if (currentPos === currentMarker && currentMarker !== lastMarkerCallBack) {
						player.options.markerCallback(media, media.currentTime); //Fires the callback function
						lastMarkerCallBack = currentMarker;
					}
				}
			}
		}, false);
	},
	/**
  * Create markers in the progress bar
  *
  * @param {$} controls
  */
	setmarkers: function setmarkers(controls) {
		var t = this,
		    left = void 0;

		for (var i = 0, total = t.options.markers.length; i < total; ++i) {
			if (Math.floor(t.options.markers[i]) <= t.media.duration && Math.floor(t.options.markers[i]) >= 0) {
				left = 100 * Math.floor(t.options.markers[i]) / t.media.duration;
				$(controls.find('.' + t.options.classPrefix + 'time-marker')[i]).css({
					'width': '1px',
					'left': left + '%',
					'background': t.options.markerColor
				});
			}
		}
	}
});

},{}]},{},[1]);
