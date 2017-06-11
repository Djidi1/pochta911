<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'order']">
        <xsl:variable name="no_edit">
            <xsl:if test="(order/id > 0) and (/page/body/module[@name='CurentUser']/container/group_id = 2
                                        and /page/body/module[@name='orders']/container/routes/item/status_id != 1)">1</xsl:if>
        </xsl:variable>

        <!--group_id<xsl:value-of select="/page/body/module[@name='CurentUser']/container/group_id"/><br/>-->
        <!--status_id<xsl:value-of select="/page/body/module[@name='orders']/container/routes/item/status_id"/><br/>-->
        <!--order/id<xsl:value-of select="(order/id)"/><br/>-->
        <!--$no_edit<xsl:value-of select="$no_edit"/>-->
        <div class="row">
            <input class="today-date" type="hidden" value="{@today}"/>
            <form id="order_edit" class="order_edit" action="/orders/orderUpdate-{order/id}/without_menu-1/" method="post" name="main_form">
                <div class="col-sm-8">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-5">
                                    <strong>Заказ № <xsl:value-of select="order/id"/>
                                    </strong>
                                    <br/>
                                    <span class="his_time_now"/>
                                    <input type="hidden" id="time_now" value="{@time_now}"/>
                                </div>
                                <div class="col-sm-7" style="text-align:right;">
                                    <xsl:if test="(/page/body/module[@name='CurentUser']/container/group_id = 2)">
                                        <input class="form-control" type="text" name="title" onkeyup="check_user(this)" value="{client/item/title}" size="30" readonly=""/>
                                    </xsl:if>
                                    <xsl:if test="(/page/body/module[@name='CurentUser']/container/group_id != 2)">
                                        <select class="form-control select2" name="new_user_id" onchange="updUserStores(this)">
                                            <xsl:for-each select="users/item">
                                                <option value="{id}">
                                                    <xsl:if test="../../order/id_user = id">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                    </xsl:if>
                                                    <xsl:value-of select="title"/>
                                                </option>
                                            </xsl:for-each>
                                        </select>
                                    </xsl:if>
                                </div>
                            </div>
                        </div>
                        <div class="panel-body">
                            <input id="order_id" type="hidden" name="order_id" value="{order/id}"/>
                            <input id="id_user" type="hidden" name="id_user" value="{order/id_user}"/>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>Откуда:</label>
                                </div>
                                <div class="col-sm-4">
                                    <xsl:if test="order/id_address > 0 or not(order/id_address)">
                                        <xsl:attribute name="class">col-sm-10</xsl:attribute>
                                    </xsl:if>
                                    <select class="form-control store_address js-store_address" name="store_id">
                                        <xsl:for-each select="stores/item">
                                            <option value="{id}">
                                                <xsl:if test="id = //order/id_address">
                                                    <xsl:attribute name="selected">selected
                                                    </xsl:attribute>
                                                </xsl:if>
                                                <xsl:value-of select="address"/>
                                            </option>
                                        </xsl:for-each>
                                        <option value="0" style="color:maroon;">
                                            <xsl:if test="order/id_address = 0">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>
                                            Ручной ввод
                                        </option>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <div class="hand_write">
                                        <xsl:if test="count(order/id_address) = 0 or order/id_address > 0">
                                            <xsl:attribute name="style">display:none</xsl:attribute>
                                        </xsl:if>
                                        <input class="form-control store_address_new address" name="store_new" value="{order/address_new}" placeholder="Введите адрес"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2 col-xs-4">
                                    <label>Дата:</label>
                                </div>
                                <div class="col-sm-4 col-xs-8">
                                    <input class="form-control date-picker" type="text" name="date" onkeyup="check_user(this)" value="{order/date}" size="30" required="">
                                        <xsl:if test="not(order/date)">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="@today"/>
                                            </xsl:attribute>
                                        </xsl:if>
                                    </input>
                                </div>
                                <xsl:if test="order/id > 0">
                                    <div class="col-sm-2 col-xs-4">
                                        <label>Курьер:</label>
                                    </div>
                                    <div class="col-sm-4 col-xs-8">
                                        <select class="form-control" name="car_courier" title="Курьер">
                                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
                                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                                            </xsl:if>
                                            <option value="0">Не назначен</option>
                                            <xsl:variable name="courier_id" select="order/id_car"/>
                                            <xsl:for-each select="couriers/item">
                                                <option value="{id}">
                                                    <xsl:if test="id = $courier_id">
                                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                                    </xsl:if>
                                                    <xsl:value-of select="fio"/> (<xsl:value-of select="car_number"/>)
                                                </option>
                                            </xsl:for-each>
                                        </select>
                                    </div>
                                </xsl:if>
                                <!--<div class="col-sm-2">-->
                                    <!--<label>Готовность:</label>-->
                                <!--</div>-->
                                <!--<div class="col-sm-4">-->
                                    <!--<input class="form-control time-picker" type="text" name="ready" onkeyup="check_user(this)" value="{order/ready}" size="30" required=""/>-->
                                <!--</div>-->
                                <!--<div class="col-sm-2">-->
                                <!--</div>-->
                                <!--<div class="col-sm-4 col-xs-12">-->
                                    <!--<label class="btn btn-default">-->
                                        <!--<input type="checkbox" id="target" name="target" value="1" onchange="calc_route(1); target_time_show()">-->
                                            <!--<xsl:if test="order/target = 1">-->
                                                <!--<xsl:attribute name="checked"/>-->
                                            <!--</xsl:if>-->
                                        <!--</input> доставка к точному времени</label>-->
                                <!--</div>-->
                            </div>
                            <!--<div class="row">-->
                                <!--<div class="col-sm-2">-->
                                    <!--<label>Статус:</label>-->
                                <!--</div>-->
                                <!--<div class="col-sm-4">-->
                                    <!--<div class="form-control">-->
                                        <!--<i>-->
                                            <!--<xsl:value-of select="routes/item[1]/status"/>-->
                                        <!--</i>-->
                                        <!--<xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 and order/id > 0">-->
                                            <!--<span title="Изменить статус" class="btn btn-warning btn-xs chg-status" onclick="chg_status({order/id})" style="float:right;">-->
                                                <!--<span class="glyphicon glyphicon-flag" aria-hidden="true"/>-->
                                            <!--</span>-->
                                        <!--</xsl:if>-->
                                    <!--</div>-->
                                <!--</div>-->

                            <!--</div>-->
                            <hr/>
                            <label>Адреса доставки:</label>
                            <xsl:if test="@is_single != 1 and $no_edit != 1">
                                <button type="button" class="btn-clone btn btn-xs btn-success" title="Добавить адрес" onclick="clone_div_row($('.routes-block').last())" style="float:right; display:none">
                                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                                        <xsl:attribute name="style">float:right;</xsl:attribute>
                                    </xsl:if>
                                    <xsl:if test="position() != count(../../routes/item) and count(../../routes/item) != 0">
                                        <xsl:attribute name="disabled"/>
                                    </xsl:if>
                                    Добавить адрес
                                </button>
                            </xsl:if>
                            <xsl:call-template name="adresses">
                                <xsl:with-param name="no_edit" select="$no_edit"/>
                            </xsl:call-template>
                            <!--<label>Дополнительная информация:</label>-->
                            <!--<textarea class="form-control" name="order_comment" placeholder="Комментарий к заказу" title="Комментарий к заказу">-->
                                <!--<xsl:value-of select="order/comment"/>-->
                            <!--</textarea>-->
                            <!--<font color="red">* Поля обязательны для заполнения.</font>-->
                        </div>
                        <xsl:if test="$no_edit != 1">
                            <div class="row">
                                <div class="col-xs-6">
                                    <div style="text-align: center">
                                        <span class="btn btn-info calc_route" onclick="calc_route(1)">Рассчитать маршрут</span>
                                    </div>
                                </div>
                                <div class="col-xs-6">
                                    <div style="text-align: center">
                                        <input class="btn btn-success btn-submit" type="button" value="сохранить" onclick="return test_time_all_routes()"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div style="text-align: center">
                                        <a href="/" class="btn btn-warning"><span class="glyphicon glyphicon-circle-arrow-left"/> Выйти без сохранения</a>
                                    </div>
                                </div>
                            </div>
                        </xsl:if>
                        <xsl:if test="$no_edit = 1">
                            <div class="alert alert-warning" style="margin: 0 15px">
                                Если вы хотетите отредактировать или отменить заказ свяжитесь, пожалуйста, с оператором по телефону: <b>407-24-52</b>
                            </div>
                        </xsl:if>
                        <br/>
                    </div>
                </div>
                <div class="col-sm-4 map-form">
                    <div class="map-container">
                        <div class="map-info">
                            <span id="ShortInfo"/>
                            <div class="map-full-info" id="viewContainer"/>
                        </div>
                        <div id="map" style="width: 100%; min-height: 500px"/>
                    </div>
                    <div class="alert alert-info">
                        <span class="delivery_sum"/>
                    </div>
                </div>
            </form>
            <div style="display:none">
                <xsl:for-each select="prices/item">
                    <input id="km_{id}" class="km_cost" type="hidden" value="{km_cost}" km_from="{km_from}" km_to="{km_to}"/>
                </xsl:for-each>
                <xsl:for-each select="add_prices/item">
                    <input id="km_{type}" type="hidden" value="{cost_route}"/>
                </xsl:for-each>
                <input id="user_fix_price" type="hidden" value="{//@user_fix_price}"/>
            </div>
        </div>
        <input id="order_edited" type="hidden" value="0"/>
        <input id="order_id" type="hidden" value="{order/id}"/>
        <script>
            $('FORM').on('keyup keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) { e.preventDefault(); return false; }
            });
            $('input, select').on('change', function() {
                $('#order_edited').val(1);
            });
        </script>
    </xsl:template>
    <xsl:template name="adresses">
        <xsl:param name="no_edit"/>
        <xsl:for-each select="routes/item">
            <xsl:call-template name="routes">
                <xsl:with-param name="no_edit" select="$no_edit"/>
            </xsl:call-template>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="routes">
        <xsl:param name="no_edit"/>
        <div class="input-group routes-block" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <div class="form-control" style="width: 60%;">
                <span class="order-add-title text-info">Адрес доставки</span>
                <input type="search" class="order-route-data spb-streets js-street_upper" name="to[]" title="Улица, проспект и т.д." value="{to}" onchange="" autocomplete="off" required="" region="{to_region}"/>
                <input type="hidden" class="to_region" name="to_region[]" value="{to_region}"/>
            </div>
            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-info">дом/корп/строение</span>
                <input type="text" class="order-route-data to_house number" name="to_house[]" title="Дом" value="{to_house}" onchange="calc_route(1)" autocomplete="off" required=""/>
            </div>
            <div class="form-control" style="width: 15%; display:none">
                <span class="order-add-title text-info">корп/строение</span>
                <input type="text" class="order-route-data to_corpus number" name="to_corpus[]" title="Корпус" value="{to_corpus}" onchange="calc_route(1)"/>
            </div>
            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-info">кв/офис/помещ</span>
                <input type="text" class="order-route-data number" name="to_appart[]" title="Квартира" value="{to_appart}" required=""/>
            </div>


            <div class="form-control" style="width: 40%;">
                <span class="order-add-title text-warning">Получатель ФИО</span>
                <input type="text" class="order-route-data" name="to_fio[]" title="Получатель" value="{to_fio}" required=""/>
            </div>
            <div class="form-control" style="width: 30%;">
                <span class="order-add-title text-warning">
                    Телефон получателя
                </span>
                <input type="text" class="order-route-data" name="to_phone[]" title="Телефон получателя" value="{to_phone}" required=""/>
            </div>
            <div class="form-control" style="width: 30%;">
                <span class="order-add-title text-warning">
                </span>
                <div class="funkyradio">
                    <div class="funkyradio-success">
                        <input type="checkbox" id="checkbox_{position()}" class="target" name="target" value="1" onchange="calc_route(1); target_time_show()" >
                            <xsl:if test="../../order/target = 1">
                                <xsl:attribute name="checked"/>
                            </xsl:if>
                        </input>
                        <label for="checkbox_{position()}"><span>К точному времени</span></label>
                    </div>
                </div>
                <label style="width:100%; text-align:center">
                    <input type="checkbox" class="order-route-data target" name="target" value="1" onchange="calc_route(1); target_time_show()" style="width:32px;margin:0">
                        <xsl:if test="../../order/target = 1">
                            <xsl:attribute name="checked"/>
                        </xsl:if>
                    </input>
                </label>
            </div>


            <div class="form-control" style="width: 34%;">
                <span class="order-add-title text-danger">
                    Можно забрать в
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_ready</xsl:with-param>
                    <xsl:with-param name="select_name">to_time_ready[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время готовности</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time_ready"/>
                    <xsl:with-param name="select_onchange">update_time_ready(this)</xsl:with-param>
                </xsl:call-template>
            </div>
            <div class="form-control target_select" style="width: 66%;">
                <xsl:if test="../../order/target != 1 or not(../../order/target)">
                    <xsl:attribute name="style">width: 66%; display:none;</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-primary">
                    Доставить К
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_target</xsl:with-param>
                    <xsl:with-param name="select_name">target_time[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time"/>
                    <xsl:with-param name="select_onchange">test_time_routes_add(); time_routes_set(this); $('.to_time').val($(this).val());$('.to_time_end').val($(this).val());</xsl:with-param>
                </xsl:call-template>
            </div>
            <div class="form-control period_select" style="width: 33%;">
                <xsl:if test="../../order/target = 1">
                    <xsl:attribute name="style">width: 33%; display:none;</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-primary">
                    Доставить С
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time</xsl:with-param>
                    <xsl:with-param name="select_name">to_time[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки с</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time"/>
                    <xsl:with-param name="select_onchange">test_time_routes_add(); $('.to_time_target').val($(this).val());</xsl:with-param>
                </xsl:call-template>
            </div>
            <div class="form-control period_select" style="width: 33%;">
                <xsl:if test="../../order/target = 1">
                    <xsl:attribute name="style">width: 33%; display:none;</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-primary">
                    Доставить По
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_end</xsl:with-param>
                    <xsl:with-param name="select_name">to_time_end[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки по</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time_end"/>
                    <xsl:with-param name="select_onchange">test_time_routes_add()</xsl:with-param>
                </xsl:call-template>
            </div>


            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-success">
                    Инкассация
                </span>
                <input type="text" class="order-route-data number cost_tovar" name="cost_tovar[]" title="Стоимость товара" value="{cost_tovar}" onkeyup="re_calc(this)" required=""/>
            </div>
            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-success">
                    Цена доставки
                </span>
                <input type="text" class="order-route-data number cost_route" name="cost_route[]" title="Стоимость доставки" value="{cost_route}" onkeyup="re_calc(this)" required="">
                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
                        <xsl:attribute name="readonly">readonly</xsl:attribute>
                    </xsl:if>
                </input>
            </div>
            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id != 2">
                <div class="form-control" style="width: 20%;">
                    <span class="order-add-title text-success">
                        <span class="glyphicon glyphicon-usd" aria-hidden="true"/>
                        курьер
                    </span>
                    <input type="text" class="order-route-data number cost_car" name="cost_car[]" title="Заработок курьера" value="{cost_car}" onkeyup="re_calc(this)"/>
                </div>
            </xsl:if>
            <div class="form-control" style="width: 20%;">
                <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
                    <xsl:attribute name="style">width:40%</xsl:attribute>
                </xsl:if>
                <span class="order-add-title text-success">
                    Оплата доставки
                </span>
                <xsl:if test="//@user_pay_type > 0">
                    <input type="hidden" name="pay_type[]" value="{//@user_pay_type}" />
                </xsl:if>
                <select class="order-route-data pay_type" name="pay_type[]" title="Тип оплаты курьеру" onchange="re_calc(this)" required="">
                    <xsl:if test="//@user_pay_type > 0">
                        <xsl:attribute name="disabled">disabled</xsl:attribute>
                    </xsl:if>
                    <xsl:variable name="pay_type" select="pay_type"/>
                    <xsl:variable name="user_pay_type" select="//@user_pay_type"/>
                    <option value=""> </option>
                    <xsl:for-each select="../../pay_types/item">
                        <option value="{id}">
                            <xsl:if test="id = $pay_type or (not($pay_type) and $user_pay_type = id)">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="pay_type"/>
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-success">
                    Общая сумма
                </span>
                <input type="text" class="order-route-data number cost_all" title="Инкассация" disabled="">
                <xsl:if test="string(number(cost_route)+number(cost_tovar)) != 'NaN'">
                    <xsl:attribute name="value">
                        <xsl:value-of select="(number(cost_route)+number(cost_tovar))"/>
                    </xsl:attribute>
                </xsl:if>
                </input>
            </div>
            <textarea name="comment[]" class="form-control" title="Комментарий" placeholder="Примечания к заказу"  style="width: 80%;">
                <xsl:value-of select="comment"/>
            </textarea>

            <xsl:if test="../../order/id > 0">
                <div class="form-control" style="width: 20%;">
                    <span class="order-add-title text-success">
                        Статус
                    </span>

                        <select class="order-route-data" name="status[]" title="Статус заказа">
                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2 and status_id != 1">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
                            <xsl:variable name="status_id" select="status_id"/>
                            <xsl:choose>
                                <xsl:when test="/page/body/module[@name='CurentUser']/container/group_id = 2 and status_id = 1">
                                    <xsl:for-each select="../../statuses/item">
                                        <!-- Либо Новый либо отмена -->
                                        <xsl:if test="id = 1 or id = 5">
                                            <option value="{id}">
                                                <xsl:if test="id = $status_id">
                                                    <xsl:attribute name="selected">selected</xsl:attribute>
                                                </xsl:if>
                                                <xsl:value-of select="status"/>
                                            </option>
                                        </xsl:if>
                                    </xsl:for-each>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:for-each select="../../statuses/item">
                                        <option value="{id}">
                                            <xsl:if test="id = $status_id">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>
                                            <xsl:value-of select="status"/>
                                        </option>
                                    </xsl:for-each>
                                </xsl:otherwise>
                            </xsl:choose>
                        </select>
                </div>
            </xsl:if>

            <xsl:if test="../../@is_single != 1 and $no_edit != 1">
                <div class="add_buttons" style="vertical-align: top; display:none">
                    <button type="button" class="btn-delete btn btn-sm btn-danger" title="Удалить" onclick="delete_div_row(this)">
                        <xsl:if test="position() = 1">
                            <xsl:attribute name="disabled"/>
                        </xsl:if>
                        <span class="glyphicon glyphicon-ban-circle" aria-hidden="true"/>
                    </button>
                </div>
            </xsl:if>
        </div>
    </xsl:template>
    <xsl:template name="time_selector">
        <xsl:param name="select_class"/>
        <xsl:param name="select_name"/>
        <xsl:param name="select_title"/>
        <xsl:param name="select_value"/>
        <xsl:param name="select_onchange"/>
        <select name="{$select_name}" class="{$select_class}" title="{$select_title}" required="">
            <xsl:if test="$select_onchange != ''">
                <xsl:attribute name="onchange">
                    <xsl:value-of select="$select_onchange"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:for-each select="../../timer/element">
                <option value="{.}">
                    <xsl:if test=". = $select_value">
                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>
                    <xsl:if test="not(../../order/id) and . = ../../@time_now_five">
                        <xsl:attribute name="selected">selected</xsl:attribute>
                    </xsl:if>
                    <xsl:value-of select="."/>
                </option>
            </xsl:for-each>
        </select>
    </xsl:template>
</xsl:stylesheet>
