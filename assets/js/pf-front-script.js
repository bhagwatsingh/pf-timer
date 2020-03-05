/**
 * PFTimer front end script
 *
 * @category JS
 * @package PFTimer
 * @since 1.0.0
 * @subpackage PFTimer/assets/js
 * @author Team Profit-Funnels
 */

jQuery( document ).ready(
	function ($) {
		$( ".pf-timer-container" ).each(
			function (index) {
				var obj_pf_timer    = $( this );
				var pf_timestamp    = parseInt( obj_pf_timer.attr( "data-timestamp" ) );
				var pf_redirect_url = obj_pf_timer.attr( "data-redirect" );
				var seconds         = pf_timestamp;
				var pf_interval     = [];
				pf_interval[index]  = setInterval(
					function () {
						if (pf_timestamp <= 0) {
							clearInterval( pf_interval[index] );
							if (pf_redirect_url == "" || pf_redirect_url == "#") {
								obj_pf_timer.find( "ul" ).hide();
							} else {
								parent.location = pf_redirect_url;
							}
						} else {
							var pf_days    = Math.floor( pf_timestamp / (60 * 60 * 24) );
							var pf_hours   = Math.floor( (pf_timestamp % (60 * 60 * 24)) / ( 60 * 60) );
							var pf_minutes = Math.floor( (pf_timestamp % (60 * 60)) / (60) );
							var pf_seconds = Math.floor( pf_timestamp % ( 60) );

							obj_pf_timer.find( ".days" ).html( pf_days );
							obj_pf_timer.find( ".hours" ).html( pf_hours );
							obj_pf_timer.find( ".minutes" ).html( pf_minutes );
							obj_pf_timer.find( ".seconds" ).html( pf_seconds );
							pf_timestamp--;
						}
					},
					1000
				);
			}
		);
	}
);
