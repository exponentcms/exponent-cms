/**
 * @name		jQuery Countdown Plugin
 * @author		Martin Angelov
 * @version 	1.0
 * @url			http://tutorialzine.com/2011/12/countdown-jquery/
 * @license		MIT License
 */

(function($){
	
	// Number of seconds in every time division
	var days	= 24*60*60,
		hours	= 60*60,
		minutes	= 60;
	
	// Creating the plugin
	$.fn.countdown = function(prop){
		
		var options = $.extend({
			callback	: function(){},
            finishedCallback: function (){},
			timestamp	: 0
		},prop);

        var timerId = 0;
		var left, d, h, m, s, positions;
        var manydays = false;

		// Initialize the plugin
		init(this, options);
		
		positions = this.find('.position');
		
		(function tick(){
			
			// Time left
			left = Math.floor((options.timestamp - (new Date())) / 1000);
            if(left < 0) { left = 0; }
			// Number of days left
			d = Math.floor(left / days);
            //Determine if we have more than 99 hours in the countdown;
            manydays = (d>99);

			if (manydays) {
				switchDigit(positions.eq(0),Math.floor(d/100)%100);
				updateDuo(1, 2, d);
			} else {
				updateDuo(0, 1, d);
			}
			left -= d*days;
			
			// Number of hours left
			h = Math.floor(left / hours);
			updateDuo(2+manydays, 3+manydays, h);
			left -= h*hours;
			
			// Number of minutes left
			m = Math.floor(left / minutes);
			updateDuo(4+manydays, 5+manydays, m);
			left -= m*minutes;
			
			// Number of seconds left
			s = left;
			updateDuo(6+manydays, 7+manydays, s);
			
            if (!timerId) {
                timerId = setInterval(tick, 1000);
            }

            // Calling an optional user supplied callback
            if (d > 0 || h > 0 || m > 0 || s > 0) {
                options.callback(d, h, m, s);
            }
            else {
                stopTimer();
                options.finishedCallback();
            }
		})();

        function stopTimer() {
            if (timerId) {
                clearInterval(timerId);
                timerId = 0;
            }
        }

		// This function updates two digit positions at once
		function updateDuo(minor,major,value){
			switchDigit(positions.eq(minor),Math.floor(value/10)%10);
			switchDigit(positions.eq(major),value%10);
		}
		
		return this;
	};


	function init(elem, options){
		elem.addClass('countdownHolder');

        // Time left
        left = Math.floor((options.timestamp - (new Date())) / 1000);
        if(left < 0) { left = 0; }
        // Number of days left
        d = Math.floor(left / days);
        //Determine if we have more than 99 hours in the countdown;
        manydays = (d>99);

		// Creating the markup inside the container
		$.each(['Days','Hours','Minutes','Seconds'],function(i){
			if(this=="Days" && manydays){
				$('<span class="count'+this+'">').html(
					'<span class="position">' +
					'	<span class="digit static">0</span>' +
					'</span>'
				).appendTo(elem);
			}
		
			$('<span class="count'+this+'"></span>').html(
				'<span class="position">' +
				'	<span class="digit static">0</span>' +
				'</span>' +
				'<span class="position">' +
				'	<span class="digit static">0</span>' +
			    '</span>'
			).appendTo(elem);
			
			if(this!="Seconds"){
				elem.append('<span class="countDiv countDiv'+i+'"></span>');
			}
		});

	}

	// Creates an animated transition between the two numbers
	function switchDigit(position,number){
		
		var digit = position.find('.digit')
		
		if(digit.is(':animated')){
			return false;
		}
		
		if(position.data('digit') == number){
			// We are already showing this number
			return false;
		}
		
		position.data('digit', number);
		
		var replacement = $('<span>',{
			'class':'digit',
			css:{
				top:'-2.1em',
				opacity:0
			},
			html:number
		});
		
		// The .static class is added when the animation
		// completes. This makes it run smoother.
		
		digit
			.before(replacement)
			.removeClass('static')
			.animate({top:'2.5em',opacity:0},'fast',function(){
				digit.remove();
			})

		replacement
			.delay(100)
			.animate({top:0,opacity:1},'fast',function(){
				replacement.addClass('static');
			});
	}
})(jQuery);