<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'order']">
        <div class="row">
            <div class="col-sm-8">
                <form class="order_edit" action="/orders/orderUpdate-{order/id}/without_menu-1/" method="post" name="main_form">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <strong>Заказ №<xsl:value-of select="order/id"/></strong>
                        </div>
                        <div class="panel-body">
                            <input id="order_id" type="hidden" name="order_id" value="{order/id}"/>
                            <input id="id_user" type="hidden" name="id_user" value="{order/id_user}"/>
                            <div class="row">
                                <div class="col-sm-2"><label>Клиент:</label></div>
                                <div class="col-sm-4">
                                    <input class="form-control" type="text" name="title" onkeyup="check_user(this)" value="{client/item/title}" size="30" readonly=""/>
                                </div>
                                <div class="col-sm-2"><label>Откуда:</label></div>
                                <div class="col-sm-4">
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
                                        <option value="0">
                                            <xsl:if test="order/id_address = 0">
                                                <xsl:attribute name="selected">selected</xsl:attribute>
                                            </xsl:if>Ручной ввод</option>
                                    </select>
                                    <div class="hand_write">
                                        <xsl:if test="count(order/id_address) = 0 or order/id_address > 0">
                                            <xsl:attribute name="style">display:none</xsl:attribute>
                                        </xsl:if>
                                        <input class="form-control store_address_new" name="store_new" value="{order/address_new}" placeholder="ручной ввод"/>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-2"><label>Дата:</label></div>
                                <div class="col-sm-4">
                                    <input class="form-control date-picker" type="text" name="date" onkeyup="check_user(this)" value="{order/date}" size="30" required="">
                                        <xsl:if test="not(order/date)">
                                            <xsl:attribute name="value">
                                                <xsl:value-of select="@today"/>
                                            </xsl:attribute>
                                        </xsl:if>
                                    </input>
                                </div>
                                <div class="col-sm-2"><label>Готовность:</label></div>
                                <div class="col-sm-4">
                                    <input class="form-control time-picker" type="text" name="ready" onkeyup="check_user(this)" value="{order/ready}" size="30" required=""/>
                                </div>
                                <div class="col-sm-2"><label>Статус:</label></div>
                                <div class="col-sm-4">
                                    <div class="form-control">
                                        <i>
                                            <xsl:value-of select="routes/item[1]/status"/>
                                        </i>
                                        <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 and order/id > 0">
                                            <div title="Изменить статус" class="btn btn-warning btn-xs chg-status" onclick="chg_status({order/id})" style="float:right;">
                                                <span class="glyphicon glyphicon-flag" aria-hidden="true"> </span>
                                            </div>
                                        </xsl:if>
                                    </div>
                                </div>
                                <div class="col-sm-2"><label>Курьер:</label></div>
                                <div class="col-sm-4">
                                    <div class="form-control">
                                        <i>
                                            <xsl:value-of select="order/fio_car"/>
                                        </i>
                                        <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1 and order/id > 0">
                                            <div title="Назначить курьера" class="btn btn-info btn-xs chg-status" onclick="chg_courier({order/id})" style="float:right;">
                                                <i class="fa fa-car" aria-hidden="true"> </i>
                                            </div>
                                        </xsl:if>
                                    </div>
                                </div>
                            </div>
                            <label>Адреса доставки:</label>
                            <xsl:call-template name="adresses"/>

                            <label>Комментарий:</label>
                            <textarea class="form-control" name="order_comment" placeholder="Комментарий к заказу" title="Комментарий к заказу">
                                <xsl:value-of select="order/comment"/>
                            </textarea>
                            <!--<font color="red">* Поля обязательны для заполнения.</font>-->
                        </div>
                    </div>

                    <!--<div class="panel panel-info">-->
                        <!--<div class="panel-heading">-->
                            <!--<strong>Адреса доставки</strong>-->
                        <!--</div>-->
                        <!--<div class="panel-body">-->
                        <!--</div>-->
                    <!--</div>-->


                    <div class="row">
                        <div class="col-sm-6">
                            <div style="text-align: center">
                                <span class="btn btn-info calc_route" onclick="calc_route()">Рассчитать маршрут</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div style="text-align: center">
                                <input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-sm-4">
                <div id="map" style="width: 100%; min-height: 500px"/>
                <div id="viewContainer"/>
            </div>
            <div style="display:none">
                <xsl:for-each select="prices/item">
                    <input id="km_{id}" class="km_cost" type="hidden" value="{km_cost}" km_from="{km_from}" km_to="{km_to}" />
                </xsl:for-each>
                <xsl:for-each select="add_prices/item">
                    <input id="km_{type}" type="hidden" value="{cost_route}" />
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
        <xsl:for-each select="routes/item">
            <xsl:call-template name="routes"/>
        </xsl:for-each>
        <xsl:if test="count(routes/item) = 0">
            <xsl:call-template name="routes"/>
        </xsl:if>
    </xsl:template>

    <xsl:template name="routes">
        <div class="input-group" rel="{position()}">
            <span class="input-group-addon">
                <xsl:value-of select="position()"/>
            </span>
            <div class="form-control" style="width: 100%;">
                <span class="order-add-title">Улица</span>
                <input type="search" class="order-route-data spb-streets" name="to[]" title="Улица, проспект и т.д." value="{to}" onchange="" autocomplete="off"/>
            </div><div class="form-control" style="width: 20%;">
            <span class="order-add-title">Дом</span>
            <input type="text" class="order-route-data to_house number" name="to_house[]" title="Дом" value="{to_house}"  onchange="calc_route()"/>
        </div><div class="form-control" style="width: 20%;">
            <span class="order-add-title">Корп.</span>
            <input type="text" class="order-route-data to_corpus number" name="to_corpus[]" title="Корпус" value="{to_corpus}" onchange="calc_route()"/>
        </div><div class="form-control" style="width: 20%;">
            <span class="order-add-title">Кв.</span>
            <input type="text" class="order-route-data number" name="to_appart[]" title="Квартира" value="{to_appart}"/>
        </div><div class="form-control" style="width: 20%;">
            <span class="order-add-title"><span class="glyphicon glyphicon-time" aria-hidden="true"/> с</span>
            <input type="text" class="order-route-data number time-picker start" name="to_time[]" title="Время доставки с" value="{to_time}"/>
        </div><div class="form-control" style="width: 20%;">
            <span class="order-add-title"><span class="glyphicon glyphicon-time" aria-hidden="true"/> по</span>
            <input type="text" class="order-route-data number time-picker end" name="to_time_end[]" title="Время доставки по" value="{to_time_end}"/>
        </div>

            <div class="form-control" style="width: 50%;">
                <span class="order-add-title">Кому</span>
                <input type="text" class="order-route-data" name="to_fio[]" title="Получатель" value="{to_fio}"/>
            </div>
            <div class="form-control" style="width: 50%;">
                <span class="order-add-title"><span class="glyphicon glyphicon-phone-alt" aria-hidden="true"/></span>
                <input type="text" class="order-route-data" name="to_phone[]" title="Телефон получателя" value="{to_phone}"/>
            </div>


            <div class="form-control" style="width: 25%;">
                <span class="order-add-title"><span class="glyphicon glyphicon-usd" aria-hidden="true"/> товар</span>
                <input type="text" class="order-route-data number cost_tovar" name="cost_tovar[]" title="Стоимость товара" value="{cost_tovar}" onkeyup="re_calc(this)"/>
            </div><div class="form-control" style="width: 25%;">
            <span class="order-add-title"><span class="glyphicon glyphicon-usd" aria-hidden="true"/> дост.</span>
            <input type="text" class="order-route-data number cost_route" name="cost_route[]" title="Стоимость доставки" value="{cost_route}" onkeyup="re_calc(this)"/>
        </div><div class="form-control" style="width: 25%;">
            <span class="order-add-title"><span class="glyphicon glyphicon-usd" aria-hidden="true"/> курьер</span>
            <input type="text" class="order-route-data number cost_car" name="cost_car[]" title="Заработок курьера" value="{cost_car}" onkeyup="re_calc(this)"/>
        </div><div class="form-control" style="width: 25%;">
            <span class="order-add-title"><span class="glyphicon glyphicon-usd" aria-hidden="true"/> инк.</span>
            <input type="text" class="order-route-data number cost_all" title="Инкассация" value="{number(cost_route)+number(cost_tovar)}" disabled=""/>
        </div>
            <br/>
            <textarea name="comment[]" class="form-control" title="Комментарий" style="display:none">
                <xsl:value-of select="comment"/>
            </textarea>
            <div class="input-group-btn add_buttons" style="vertical-align: top;">
                <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row(this)">
                    <xsl:if test="position() != count(../../routes/item) and count(../../routes/item) != 0">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    +
                </button>
                <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)">
                    <xsl:if test="position() = 1">
                        <xsl:attribute name="disabled"> </xsl:attribute>
                    </xsl:if>
                    -
                </button>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
