<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'order']">
        <xsl:variable name="no_edit">
            <xsl:if test="not(order/id) or (/page/body/module[@name='CurentUser']/container/group_id != 2
                                        and /page/body/module[@name='orders']/container/routes/item/status_id != 3
                                        and /page/body/module[@name='orders']/container/routes/item/status_id != 4
                                        and /page/body/module[@name='orders']/container/routes/item/status_id != 5)">0</xsl:if>
        </xsl:variable>
        <div class="row">
            <form class="order_edit" action="/orders/orderUpdate-{order/id}/without_menu-1/" method="post" name="main_form">
                <div class="col-sm-8">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <div class="row">
                                <div class="col-sm-5">
                                    <strong>Заказ №<xsl:value-of select="order/id"/>
                                    </strong>
                                    <br/>
                                    <span class="his_time_now"/>
                                    <input type="hidden" id="time_now" value="{@time_now}"/>
                                </div>
                                <div class="col-sm-7" style="text-align:right;">
                                    <input class="form-control" type="text" name="title" onkeyup="check_user(this)" value="{client/item/title}" size="30" readonly=""/>
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
                                        <input class="form-control store_address_new address" name="store_new" value="{order/address_new}" placeholder="ручной ввод"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2">
                                    <label>Дата:</label>
                                </div>
                                <div class="col-sm-4">
                                    <input class="form-control date-picker" type="text" name="date" onkeyup="check_user(this)" value="{order/date}" size="30" required="">
                                        <xsl:if test="not(order/date)">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="@today"/>
                                            </xsl:attribute>
                                        </xsl:if>
                                    </input>
                                </div>
                                <xsl:if test="order/id > 0">
                                    <div class="col-sm-2">
                                        <label>Курьер:</label>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-control">
                                            <i>
                                                <xsl:value-of select="order/fio_car"/>
                                            </i>
                                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 or /page/body/module[@name='CurentUser']/container/group_id = 3 or /page/body/module[@name='CurentUser']/container/group_id = 4">
                                                <span title="Назначить курьера" class="btn btn-info btn-xs chg-status" onclick="chg_courier({order/id})" style="float:right;">
                                                    <i class="fa fa-car" aria-hidden="true"/>
                                                </span>
                                            </xsl:if>
                                        </div>
                                    </div>
                                </xsl:if>
                                <!--<div class="col-sm-2">-->
                                    <!--<label>Готовность:</label>-->
                                <!--</div>-->
                                <!--<div class="col-sm-4">-->
                                    <!--<input class="form-control time-picker" type="text" name="ready" onkeyup="check_user(this)" value="{order/ready}" size="30" required=""/>-->
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
                            <xsl:if test="@is_single != 1 and $no_edit = 0">
                                <button type="button" class="btn-clone btn btn-xs btn-success" title="Добавить адрес" onclick="clone_div_row($('.routes-block').last())" style="float:right;">
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
                        <xsl:if test="$no_edit = 0">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div style="text-align: center">
                                        <span class="btn btn-info calc_route" onclick="calc_route()">Рассчитать маршрут</span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div style="text-align: center">
                                        <input class="btn btn-success btn-submit" type="submit" value="сохранить" name="submit" onclick="return test_time_all_routes()"/>
                                    </div>
                                </div>
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
            </div>
        </div>
        <script>
            $('FORM').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { e.preventDefault(); return false; }
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
            <div class="form-control" style="width: 55%;">
                <span class="order-add-title text-info">Улица</span>
                <input type="search" class="order-route-data spb-streets js-street_upper" name="to[]" title="Улица, проспект и т.д." value="{to}" onchange="" autocomplete="off" required=""/>
            </div>
            <div class="form-control" style="width: 15%;">
                <span class="order-add-title text-info">Дом</span>
                <input type="text" class="order-route-data to_house number" name="to_house[]" title="Дом" value="{to_house}" onchange="calc_route()" required=""/>
            </div>
            <div class="form-control" style="width: 15%;">
                <span class="order-add-title text-info">Корп.</span>
                <input type="text" class="order-route-data to_corpus number" name="to_corpus[]" title="Корпус" value="{to_corpus}" onchange="calc_route()" required=""/>
            </div>
            <div class="form-control" style="width: 15%;">
                <span class="order-add-title text-info">Кв.</span>
                <input type="text" class="order-route-data number" name="to_appart[]" title="Квартира" value="{to_appart}" required=""/>
            </div>


            <div class="form-control" style="width: 50%;">
                <span class="order-add-title text-warning">Кому</span>
                <input type="text" class="order-route-data" name="to_fio[]" title="Получатель" value="{to_fio}" required=""/>
            </div>
            <div class="form-control" style="width: 50%;">
                <span class="order-add-title text-warning">
                    <span class="glyphicon glyphicon-phone-alt" aria-hidden="true"/>
                </span>
                <input type="text" class="order-route-data" name="to_phone[]" title="Телефон получателя" value="{to_phone}" required=""/>
            </div>


            <div class="form-control" style="width: 34%;">
                <span class="order-add-title text-danger">
                    <span class="glyphicon glyphicon-time" aria-hidden="true"/>
                    готов
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_ready</xsl:with-param>
                    <xsl:with-param name="select_name">to_time_ready[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время готовности</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time_ready"/>
                </xsl:call-template>
                <!--<input type="text" class="order-route-data number time-picker to_time_ready" name="to_time_ready[]" title="Время готовности" value="{to_time_ready}" required=""/>-->
            </div>
            <div class="form-control" style="width: 33%;">
                <span class="order-add-title text-primary">
                    <span class="glyphicon glyphicon-time" aria-hidden="true"/>
                    с
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time</xsl:with-param>
                    <xsl:with-param name="select_name">to_time[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки с</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time"/>
                </xsl:call-template>
                <!--<input type="text" class="order-route-data number time-picker to_time start" name="to_time[]" title="Время доставки с" value="{to_time}" required=""/>-->
            </div>
            <div class="form-control" style="width: 33%;">
                <span class="order-add-title text-primary">
                    <span class="glyphicon glyphicon-time" aria-hidden="true"/>
                    по
                </span>
                <xsl:call-template name="time_selector">
                    <xsl:with-param name="select_class">order-route-data number to_time_end</xsl:with-param>
                    <xsl:with-param name="select_name">to_time_end[]</xsl:with-param>
                    <xsl:with-param name="select_title">Время доставки по</xsl:with-param>
                    <xsl:with-param name="select_value" select="to_time_end"/>
                </xsl:call-template>
                <!--<input type="text" class="order-route-data number time-picker to_time_end end" name="to_time_end[]" title="Время доставки по" value="{to_time_end}" required=""/>-->
            </div>


            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-success">
                    <span class="glyphicon glyphicon-usd" aria-hidden="true"/>
                    товар
                </span>
                <input type="text" class="order-route-data number cost_tovar" name="cost_tovar[]" title="Стоимость товара" value="{cost_tovar}" onkeyup="re_calc(this)" required=""/>
            </div>
            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-success">
                    <span class="glyphicon glyphicon-usd" aria-hidden="true"/>
                    дост.
                </span>
                <input type="text" class="order-route-data number cost_route" name="cost_route[]" title="Стоимость доставки" value="{cost_route}" onkeyup="re_calc(this)" required=""/>
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
                    <span class="glyphicon glyphicon-usd" aria-hidden="true"/>
                    оплата
                </span>
                <select class="order-route-data pay_type" name="pay_type[]" title="Тип оплаты курьеру" onchange="re_calc(this)">
                    <xsl:variable name="pay_type" select="pay_type"/>
                    <xsl:for-each select="../../pay_types/item">
                        <option value="{id}">
                            <xsl:if test="id = $pay_type">
                                <xsl:attribute name="selected">selected</xsl:attribute>
                            </xsl:if>
                            <xsl:value-of select="pay_type"/>
                        </option>
                    </xsl:for-each>
                </select>
            </div>
            <div class="form-control" style="width: 20%;">
                <span class="order-add-title text-success">
                    <span class="glyphicon glyphicon-usd" aria-hidden="true"/>
                    инк.
                </span>
                <input type="text" class="order-route-data number cost_all" title="Инкассация" value="{number(cost_route)+number(cost_tovar)}" disabled=""/>
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
                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 2">
                                <xsl:attribute name="disabled">disabled</xsl:attribute>
                            </xsl:if>
                            <xsl:variable name="status_id" select="status_id"/>
                            <xsl:for-each select="../../statuses/item">
                                <option value="{id}">
                                    <xsl:if test="id = $status_id">
                                        <xsl:attribute name="selected">selected</xsl:attribute>
                                    </xsl:if>
                                    <xsl:value-of select="status"/>
                                </option>
                            </xsl:for-each>
                        </select>
                </div>
            </xsl:if>

            <xsl:if test="../../@is_single != 1 and $no_edit = 0">
                <div class="add_buttons" style="vertical-align: top;">
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
        <select name="{$select_name}" class="{$select_class}" title="{$select_title}" required="">
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
