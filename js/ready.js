jQuery(function ($) {

    // Please wait...
    var $loading = $('#loadingDiv').hide();
    $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });



    $('.data-table').dataTable({"language": {
        "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Russian.json"
    }});



    $('.thumbnail').click(function () {
        var src = $(this).attr("src");
        var img = '<img src="' + src + '" style="width: 100%;" />';
        bootbox.alert(img);
    });

    if ($('.camera_wrap').length)
        $('.camera_wrap').camera({
            height: '300px'
        });


    $('#back-top').hide().find('a').click(function () {
        $('body,html').animate({
            scrollTop: 0
        }, 800);
        return false;
    });


    $(window).scroll(function(){
        if (isVisisble( $('.navbar') )) {
            $("#back-top").show();
        } else {
            $("#back-top").hide();
        }
    });

    //ui_add();
    // автозаполнение улиц
    autoc_spb_streets();

    $('.time-picker').datetimepicker({format: 'LT',locale: 'ru'});

    $('.date-picker').datetimepicker({format: 'L', locale: 'ru'});

    /*
    $.datepicker.regional['ru'] = {
        changeMonth: true,
        changeYear: true,
        closeText: 'Закрыть',
        prevText: '&#x3c;Пред',
        nextText: 'След&#x3e;',
        currentText: 'Сегодня',
        monthNames: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь',
            'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
        monthNamesShort: ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн',
            'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
        dayNames: ['воскресенье', 'понедельник', 'вторник', 'среда', 'четверг', 'пятница', 'суббота'],
        dayNamesShort: ['вск', 'пнд', 'втр', 'срд', 'чтв', 'птн', 'сбт'],
        dayNamesMin: ['Вс', 'Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
        yearRange: "1930:2015",
        dateFormat: 'dd.mm.yy', firstDay: 1,
        isRTL: false
    };
    $.datepicker.setDefaults($.datepicker.regional['ru']);
*/


});

function isVisisble(elem){
    //return $(elem).offset().top - $(window).scrollTop() < $(elem).height() ;
    return $(elem).offset().top - $(window).scrollTop() < -1 * $(elem).height() ;
}