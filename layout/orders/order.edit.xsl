<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'order']">
        <div class="row">
            <div class="col-sm-8">
                <form action="/orders/orderUpdate-{order/id}/without_menu-1/" method="post" name="main_form">
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
                                    <select class="form-control store_address" name="store_id">
                                        <xsl:for-each select="stores/item">
                                            <option value="{id}">
                                                <xsl:if test="id = //order/id_address">
                                                    <xsl:attribute name="selected">selected
                                                    </xsl:attribute>
                                                </xsl:if>
                                                <xsl:value-of select="address"/>
                                            </option>
                                        </xsl:for-each>
                                    </select>
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
            <div class="input-group" rel="{position()}">
                <span class="input-group-addon">
                    <xsl:value-of select="position()"/>
                </span>
                <input type="search" class="form-control spb-streets" name="to[]" placeholder="Адрес" title="Адрес" value="{to}" style="width: 100%;" onchange=""/>
                <input type="text" class="form-control to_house" name="to_house[]" placeholder="Дом" title="Дом" value="{to_house}" style="width: 34%;" onchange="calc_route()"/>
                <input type="text" class="form-control to_corpus" name="to_corpus[]" placeholder="Корпус" title="Корпус" value="{to_corpus}" style="width: 33%;" onchange="calc_route()"/>
                <input type="text" class="form-control" name="to_appart[]" placeholder="Квартира" title="Квартира" value="{to_appart}" style="width: 33%;"/>
                <input type="text" class="form-control" name="to_fio[]" placeholder="Получатель" title="Получатель" value="{to_fio}" style="width: 50%;"/>
                <input type="text" class="form-control" name="to_phone[]" placeholder="Телефон получателя" title="Телефон получателя" value="{to_phone}" style="width: 50%;"/>
                <input type="text" class="form-control time-picker start" name="to_time[]" placeholder="Время доставки с" title="Время доставки с" value="{to_time}" style="width: 20%;"/>
                <input type="text" class="form-control time-picker end" name="to_time_end[]" placeholder="Время доставки по" title="Время доставки по" value="{to_time_end}" style="width: 20%;"/>
                <input type="text" class="form-control cost_tovar" name="cost_tovar[]" placeholder="Стоимость товара" title="Стоимость товара" value="{cost_tovar}" style="width: 20%;" onkeyup="re_calc(this)"/>
                <input type="text" class="form-control cost_route" name="cost_route[]" placeholder="Стоимость доставки" title="Стоимость доставки" value="{cost_route}" style="width: 20%;" onkeyup="re_calc(this)"/>
                <input type="text" class="form-control cost_all" placeholder="Инкассация" title="Инкассация" value="{number(cost_route)+number(cost_tovar)}" style="width: 20%;" disabled=""/>
                <br/>
                <textarea name="comment[]" class="form-control" placeholder="Комментарий" title="Комментарий" style="display:none">
                    <xsl:value-of select="comment"/>
                </textarea>
                <div class="input-group-btn add_buttons" style="vertical-align: top;">
                    <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row(this)">
                        <xsl:if test="position() != count(../../routes/item)">
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
        </xsl:for-each>
        <xsl:if test="count(routes/item) = 0">
            <div class="input-group" rel="1">
                <span class="input-group-addon">1</span>
                <input type="text" class="form-control spb-streets" name="to[]" placeholder="Адрес" value="" style="width: 100%;" onchange=""/>
                <input type="text" class="form-control to_house" name="to_house[]" placeholder="Дом" value="" style="width: 34%;" onchange="calc_route()"/>
                <input type="text" class="form-control to_corpus" name="to_corpus[]" placeholder="Корпус" value="" style="width: 33%;" onchange="calc_route()"/>
                <input type="text" class="form-control" name="to_appart[]" placeholder="Квартира" value="" style="width: 33%;"/>
                <input type="text" class="form-control" name="to_fio[]" placeholder="Получатель" value="" style="width: 50%;"/>
                <input type="text" class="form-control" name="to_phone[]" placeholder="Телефон получателя" value="" style="width: 50%;"/>
                <input type="text" class="form-control time-picker start" name="to_time[]" placeholder="Время доставки с" value="" style="width: 20%;"/>
                <input type="text" class="form-control time-picker end" name="to_time_end[]" placeholder="Время доставки по" value="" style="width: 20%;"/>
                <input type="text" class="form-control cost_tovar" name="cost_tovar[]" placeholder="Стоимость товара" title="Стоимость товара" value="" style="width: 20%;" onkeyup="re_calc(this)"/>
                <input type="text" class="form-control cost_route" name="cost_route[]" placeholder="Стоимость доставки" title="Стоимость доставки" value="" style="width: 20%;" onkeyup="re_calc(this)"/>
                <input type="text" class="form-control cost_all" placeholder="Инкассация" title="Инкассация" value="" style="width: 20%;" disabled=""/>
                <br/>
                <textarea name="comment[]" class="form-control"  placeholder="Комментарий" title="Комментарий" style="display:none">
                    <xsl:value-of select="comment"/>
                </textarea>
                <div class="input-group-btn add_buttons" style="vertical-align: top;">
                    <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row(this)">+</button>
                    <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)" disabled="">-</button>
                </div>
            </div>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
