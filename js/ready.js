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

    add_data_table($('.new-logist-data-table'));


    $('.logist-data-table').dataTable({
        "columnDefs": [
            { "visible": false, "targets": 0 }
        ],
        "order": [[ 0, 'asc' ]],
        "displayLength": 25,
        "drawCallback": function ( ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
            api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td class="order-group" colspan="6">'+group+'</td></tr>'
                    );
                    last = group;
                }
            } );
        },
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Russian.json"
        }
    } );

    $('.orders-data-table').dataTable({
        "columnDefs": [
            { "visible": false, "targets": 0 }
        ],
        "order": [[ 0, 'asc' ]],
        "displayLength": 25,
        "drawCallback": function ( ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;
            api.column(0, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class="group"><td class="order-group" colspan="6">'+group+'</td></tr>'
                    );
                    last = group;
                }
            } );
        },
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Russian.json"
        }
    } );

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
        if ($('.navbar').length > 0) {
            if (isVisisble($('.navbar'))) {
                $("#back-top").show();
            } else {
                $("#back-top").hide();
            }
        }
    });

    //ui_add();
    // автозаполнение улиц
    autoc_spb_streets();

    // Установка дата/время пикеров
    $('.time-picker').datetimepicker({format: 'LT',locale: 'ru'});

    $('.date-picker').datetimepicker({format: 'L', locale: 'ru'});

    var start_time = $('.time-picker.start').get();
    var end_time = $('.time-picker.end').get();
    set_time_period(start_time,end_time,'LT');


    $('.js-store_address').change(function() {
        if ($(this).val() == 0){
            $(this).parent().find('.hand_write').show();
        }else{
            $(this).parent().find('.hand_write').hide();
        }
    });

});

function isVisisble(elem){
    //return $(elem).offset().top - $(window).scrollTop() < $(elem).height() ;
    return $(elem).offset().top - $(window).scrollTop() < -1 * $(elem).height() ;
}