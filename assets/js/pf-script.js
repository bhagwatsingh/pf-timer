jQuery( document ).ready(function($) {

   // $('.wplc-color-box').wpColorPicker();

    $('.pf_timer_expiry_date').datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
        minDate: 0,
        changeMonth: true,
        changeYear: true,
    });

/*    $(".wpcdt-circle-slider").slider({
        min: 0.0033333333333333335,
        max: 0.13333333333333333,
        step: 0.003333333,
        slide: function (event, ui) {
            $(this).parent().find(".wpcdt-number").val(ui.value);
        },
        create: function(event, ui){
            $(this).slider('value',$(this).parent().find(".wpcdt-number").val());
        }
    });

    $(".wpcdt-background-slider").slider({
        min: 0.1,
        max: 3,
        step: 0.1,
        slide: function (event, ui) {
            $(this).parent().find(".wpcdt-number").val(ui.value);
        },
        create: function(event, ui){
            $(this).slider('value',$(this).parent().find(".wpcdt-number").val());
        }
    });*/
});