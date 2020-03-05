/**
 * PFTimer admin script
 *
 * @category JS
 * @package PFTimer
 * @since 1.0.0
 * @subpackage PFTimer/assets/js
 * @author Team Profit-Funnels
 */

jQuery( document ).ready(
	function ($) {

		$( '.pf_timer_expiry_date' ).datetimepicker(
			{
				dateFormat: 'yy-mm-dd',
				timeFormat: 'HH:mm:ss',
				minDate: 0,
				changeMonth: true,
				changeYear: true,
			}
		);

		$( '.pf_timer_bg_color,.pf_timer_font_color' ).wpColorPicker();

		var pf_timer_font_size     = document.getElementById( "pf_timer_font_size" );
		var pf_timer_border_radius = document.getElementById( "pf_timer_border_radius" );

		pf_timer_font_size.oninput     = function () {
			document.getElementById( "pf_timer_font_size_span" ).innerHTML = this.value + "px";
		}
		pf_timer_border_radius.oninput = function () {
			document.getElementById( "pf_timer_border_radius_span" ).innerHTML = this.value + "px";
		}
	}
);
