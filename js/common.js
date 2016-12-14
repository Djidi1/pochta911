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
    //$(new_el).find("input").attr('id', '');
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