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



    $('.data-table').dataTable();


    $('.thumbnail').click(function () {
        var src = $(this).attr("src");
        var img = '<img src="' + src + '" style="width: 100%;" />';
        bootbox.alert(img);
    });

    if ($('.camera_wrap').length)
        $('.camera_wrap').camera({
            height: '300px'
        });

    $("#back-top").hide();
    $(function () {
        $('#back-top a').click(function () {
            $('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });

    //ui_add();
    // сохранение улиц в браузере:
    $.getJSON('/orders/get_data-spbStreets', function(spb_street_data){
        $(".spb-streets").typeahead({ source: spb_street_data });
        //console.log(spb_street_data);
    },'json');
    //console.log(localStorage.getItem('spb_street_data'));

    $('.time-picker').datetimepicker({
        format: 'LT',
        locale: 'ru'
    });

    $('.date-picker').datetimepicker({
        format: 'L',
        locale: 'ru'
    });

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



});