/**
 * Created by Daniil on 11.12.2016.
 */
function delete_div_row(obj) {
    var row = $(obj).parent().parent();
    var panel = $(row).parent();
    $(row).remove();
    var i = 0;
    $(panel).find('.input-group').each(function(group){
        i++;
        $(this).attr('rel',i);
        $(this).find('.input-group-addon').text(i);

    });
    $(panel).find('.input-group:last-child .btn-clone').removeAttr('disabled');
}

function clone_div_row(obj) {
    // находим блок со строкой
    var row = $(obj).parent().parent();
    var class_id = $(row).attr("rel");
    var class_id_new = +class_id + 1;
    var new_el = $(row).clone().insertAfter($(row));
    $(new_el).attr("rel", class_id_new);
    $(new_el).find('.input-group-addon').text(class_id_new);
    // блокируем копирование у текущего
    $(row).find('.btn-clone').attr('disabled','');
    // даем возможность удалиться созданному
    $(new_el).find('.btn-delete').removeAttr('disabled');
    // автозаполение адреса
    $(new_el).remove('.typeahead');
    // $(new_el).find('.time-picker.start').datetimepicker({format: 'LT',locale: 'ru'});
    var start_time = $(new_el).find('.time-picker.start').get();
    var end_time = $(new_el).find('.time-picker.end').get();
    set_time_period(start_time,end_time,'LT');
    autoc_spb_streets();
    //$(new_el).find("input").attr('id', '');
}

function set_time_period (start, end, format) {
    $(start).datetimepicker({format: format, locale: 'ru'});
    $(end).datetimepicker({format: format, locale: 'ru',
        useCurrent: false //Important! See issue #1075
    });
    $(start).on("dp.change", function (e) {
        $(end).data("DateTimePicker").minDate(e.date);
        console.log('st_chg');
    });
    $(end).on("dp.change", function (e) {
        $(start).data("DateTimePicker").maxDate(e.date);
        console.log('end_chg');
    });
    $(start).on("dp.show", function (e) {
        $(end).data("DateTimePicker").minDate(e.date);
        console.log('st_show');
    });
    $(end).on("dp.show", function (e) {
        $(start).data("DateTimePicker").maxDate(e.date);
        console.log('end_show');
    });
}



var timer_check_user;
function check_user(obj){
    clearTimeout(timer_check_user);
    var elem_type = $(obj).attr('name');
    timer_check_user=setTimeout(function send_req_check_user(){
        var obj = $('input[name='+elem_type+']');
        var user_id = $('#user_id').val();
        var elem_val = $(obj).val();
        $.post("/admin/checkUser-1/", {user_id:user_id,elem_type:elem_type,value:elem_val},  function(data) {
            if (data == 1){
                $(obj).attr('style','border: 1px solid maroon');
                if ($('.alert-div-'+elem_type).length == 0) {
                    var alert_div = $('<div class="alert alert-danger alert-div alert-div-' + elem_type + '">Введите другое значение...</div>');
                    $(obj).parent().append(alert_div);
                }
                $('input[type=submit]').attr("disabled","disabled")
            }else{
                $(obj).attr('style','border: 1px solid darkgreen');
                $('.alert-div-'+elem_type+'').fadeOut().remove();
                if ($('.alert-div').length == 0) {
                    $('input[type=submit]').removeAttr("disabled");
                }
            }

        });
    },500,elem_type);
}

function open_bootbox_dialog(url) {
    bootbox.dialog({
        title: "Изменение заказа",
        message: '<iframe style="border:0;" src="'+url+'/without_menu-1/" height="500" width="100%" ></iframe>',
        className: "largeWidth",
        buttons: {
            'cancel': {
                label: 'Закрыть',
                className: 'btn-default pull-left'
            }
        }
    });
}

function popup_excel(url) {
    bootbox.dialog({
        title: "Экспорт в эксель",
        message: '<iframe style="border:0;" src="/'+url+'/without_menu-1/" height="500" width="100%" ></iframe>',
        className: "largeWidth",
        buttons: {
            'cancel': {
                label: 'Закрыть',
                className: 'btn-default pull-left'
            }
        }
    });
}

function chg_courier(order_id){
    var order_route_id = $('tr.order_'+order_id).first().attr('rel');
    var order_info = $('.order_route_'+order_route_id+' .order_info').attr('rel');
    $.post("/orders/chg_courier-1/", {order_id:order_id,order_info:order_info},  function(data) {
        bootbox.confirm({
            title: "Изменение курьера в заказе № "+order_id,
            message: data,
            callback: function(result){ if(result){send_new_courier()} }
        });
        //bootbox.alert(data,send_new_status(this));
    });
}
function send_new_courier(){
    var order_id = $('.bootbox-body').find('input[name=order_id]').val();
    var order_route_id = $('.bootbox-body').find('input[name=order_route_id]').val();
    // var new_courier = $('.bootbox-body').find('select[name=new_courier]').val();
    var new_courier = $('.bootbox-body').find('input[name=new_courier]').val();
    var new_car_courier = $('.bootbox-body').find('select[name=new_car_courier]').val();
    var courier_comment = $('.bootbox-body').find('textarea[name=courier_comment]').val();
    var order_info_message = $('.bootbox-body').find('input[name=order_info_message]').val();

    $.post("/orders/chg_courier-1/", {order_id:order_id,order_route_id:order_route_id,new_courier:new_courier,new_car_courier:new_car_courier,courier_comment:courier_comment,order_info_message:order_info_message},  function(data) {
          bootbox.alert(data,location.reload());
        // bootbox.alert(data);
    });

}
function chg_status(order_route_id, order_id){
    var order_info = $('.order_route_'+order_route_id+' .order_info').attr('rel');
    $.post("/orders/chg_status-1/", {order_route_id:order_route_id,order_info:order_info},  function(data) {
        bootbox.confirm({
            title: "Изменение статуса доставки в заказе № "+order_id,
            message: data,
            callback: function(result){ if(result){send_new_status()} }
        });
        // bootbox.alert(data,send_new_status(this));
    });
}

function send_new_status(){
    var order_route_id = $('.bootbox-body').find('input[name=order_route_id]').val();
    var new_status = $('.bootbox-body').find('select[name=new_status]').val();
    var stat_comment = $('.bootbox-body').find('textarea[name=comment_status]').val();
    var order_info_message = $('.bootbox-body').find('input[name=order_info_message]').val();

    $.post("/orders/chg_status-1/", {order_route_id:order_route_id,new_status:new_status,stat_comment:stat_comment,order_info_message:order_info_message},  function(data) {
        bootbox.alert(data,location.reload());
        // bootbox.alert(data);
    });

}

function autoc_spb_streets(){
    // $(".spb-streets").typeahead({ ajax: '', hint: true });
    $(".spb-streets").typeahead({ source: function(query, process) {
        // var textVal=$("#field1").val();
        $.ajax({
            url: '/service/kladr.php',
            type: 'POST',
            data: 'type=street&street=' + query + '&city=',
            dataType: 'JSON',
            async: true,
            timeout: 5000,
            success: function(data) {
                process(data);
                // console.log(textVal);
            }
        });
    },
        minLength: 4,
//		scrollHeight: 200,
		items: 'all'
    });
    /*
    var saved_data = localStorage.getItem('spb_street_data');
    if (typeof saved_data == 'undefined' || saved_data == null || saved_data == '' ) {
        $.getJSON('/orders/get_data-spbStreets', function(spb_street_data){
            localStorage.setItem('spb_street_data', JSON.stringify(spb_street_data));
            $(".spb-streets").typeahead({ source: spb_street_data, hint: true });
        },'json');
    }else{
        var localData = JSON.parse(localStorage.getItem('spb_street_data'));
        $(".spb-streets").typeahead({ source: localData, hint: true });
    }
    */
}



function filter_table(){
    $('TABLE.new-logist-data-table TBODY tr').hide();
    var i = 0;
    $('input.statuses').each(function() {
        if ( $(this).is(':visible') && $(this).prop('checked') ) {
            var chk_val = $(this).val();
            $('TABLE.new-logist-data-table TBODY tr.status_'+chk_val).show();
            // Если статус выбран, но таких строк нет, то показывать все.
            // if ($('.new-logist-data-table tr.status_'+chk_val).length){
                i++;
            // }
        }
    });
    if (i == 0){
        $('.new-logist-data-table TBODY tr').show();
    }
}

function re_calc(obj){
    var cost_tovar = $(obj).parent().find('.cost_tovar').val();
    var cost_route = $(obj).parent().find('.cost_route').val();
    $(obj).parent().find('.cost_all').val(Number(cost_tovar)+Number(cost_route));
}

function add_data_table(obj){
    // Setup - add a text input to each footer cell
    $(obj).find('tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );
    // DataTable
    var table = $(obj).DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.13/i18n/Russian.json"
        }
        , "bLengthChange": false
        , "bPaginate": false
        , "bFilter": false
        ,initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );

                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );

                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        }
    });
}