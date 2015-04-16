/*
 * HTML5 Forms Fallback for older and unsupporting browsers
 * Using jQuery, jQuery UI, Modernizr, Webforms2, and other jQuery Plugins
 * 
 * 2010 Cristian I. Colceriu
 *
 * www.ghinda.net
 * contact@ghinda.net
 *
 */
 
/* Slide
 * input[type=range] fallback
 *
 * using jQuery UI Slider
 */
var initSlider = function() {			
	$('input[type=range]').each(function() {
		var input = $(this);
		var slider = $('<div id="' + input.attr('id') + '" class="' + input.attr('class') + '"></div>');
        var min = parseInt(input.attr('min'));
        if (isNaN(min) || typeof min == 'undefined') {
            min = 0;
        }
        var max = parseInt(input.attr('max'));
        if (isNaN(max) || typeof max == 'undefined') {
            max = 100;
        }
        var step = parseInt(input.attr('step'));
        if (isNaN(step) || typeof step == 'undefined') {
            step = 1;
        }
        var value = parseInt(input.attr('value'));
        if (isNaN(value) || typeof value == 'undefined') {
            value = 0;
        }

		input.after(slider).hide();

		slider.slider({
			min:   min,
			max:   max,
			step:  step,
            value: value,
			change: function(e, ui) {
                $(this).prev().val(ui.value);  // set original input value
			}
		});
	});
};

if(!Modernizr.inputtypes.range){
	$(document).ready(initSlider);
};

/* Numeric Spinner
 * input[type=number] fallback
 * 
 * using jQuery UI Spinner
 */
var initSpinner = function() {			
	$('input[type=number]').each(function() {
		var $input = $(this);
		$input.spinner({
			min: $input.attr('min'),
			max: $input.attr('max'),
			step: $input.attr('step')
		});
	});
};

if(!Modernizr.inputtypes.number){		
	$(document).ready(initSpinner);
};

/* Datepicker
 * input[type=date] fallback
 *
 * using jQuery UI Datepicker
 */
var initDatepicker = function() {
	$('input[type=date]').each(function() {
		var $input = $(this);
		$input.datepicker({
			minDate: $input.attr('min'),
			maxDate: $input.attr('max'),
			dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
		});
	});
};

if(!Modernizr.inputtypes.date){
	$(document).ready(initDatepicker);
};

/* ColorPicker
 * input[type=color] fallback
 *
 * using jQuery ColorPicker plugin by Stefan Petre (http://www.eyecon.ro/)
 * http://www.eyecon.ro/colorpicker/
 *
 * now using jQuery Spectrum which auto-initiates
 * http://bgrins.github.io/spectrum
 */
//var initColorpicker = function() {
//	$('input[type=color]').each(function() {
//		var $input = $(this);
//		$input.ColorPicker({
//			onSubmit: function(hsb, hex, rgb, el) {
//				$(el).val(hex);
//				$(el).ColorPickerHide();
//			}
//		});
//	});
//};

//if(!Modernizr.inputtypes.color){
//	$(document).ready(initColorpicker);
//};

/* Placeholder
 * placeholder attribute fallback
 *
 * using jQuery Placeholder plugin
 * http://andrew-jones.com/jquery-placeholder-plugin
 */
var initPlaceholder = function() {
//	$('input[placeholder]').placehold({
//		placeholderClassName: 'placeholder'
//	});
    $(":input[placeholder]").placeholder();
};

if(!Modernizr.input.placeholder){
	$(document).ready(initPlaceholder);
};