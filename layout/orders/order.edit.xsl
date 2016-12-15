<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'order']">

        <!--<h2>Заказ:</h2>-->
        <form action="/orders/orderUpdate-{order/order_id}/" method="post" name="main_form">
            <div class="row">
                <div class="col-md-1"> </div>
                <div class="col-md-10">

                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <strong>Заказ</strong>
                            <div class="label label-info" style="float:right;">
                                <xsl:value-of select="status"/>
                                <xsl:if test="status = '' or not(status)">Новый</xsl:if>
                            </div>
                        </div>
                        <div class="panel-body">
                            <input id="user_id" type="hidden" name="user_id" value="{user/user_id}"/>
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td>Клиент:</td>
                                        <td>
                                            <input class="form-control" type="text" name="title" onkeyup="check_user(this)"
                                                   value="{order/title}" size="30" required=""/>
                                        </td>
                                        <td>Откуда:</td>
                                        <td>
                                            <select class="form-control" name="group_id">
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
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Дата:</td>
                                        <td>
                                            <input class="form-control" type="text" name="title" onkeyup="check_user(this)"
                                                   value="{order/date}" size="30" required=""/>
                                        </td>
                                        <td>Готовность:</td>
                                        <td>
                                            <input class="form-control" type="text" name="title" onkeyup="check_user(this)"
                                                   value="{order/ready}" size="30" required=""/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            Комментарий:
                            <textarea class="form-control" name="order_comment" placeholder="Комментарий к заказу">
                                <xsl:value-of select="order/comment"/>
                            </textarea>
                            <!--<font color="red">* Поля обязательны для заполнения.</font>-->
                        </div>
                    </div>

                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <strong>Адреса доставки</strong>
                        </div>
                        <div class="panel-body">
                            <xsl:for-each select="routes/item">
                                <div class="input-group" rel="{position()}">
                                    <span class="input-group-addon">
                                        <xsl:value-of select="position()"/>
                                    </span>
                                    <input type="text" class="form-control" name="to[]" placeholder="Адрес" title="Адрес" value="{to}" style="width: 100%;"/>
                                    <input type="text" class="form-control" name="to_house[]" placeholder="Дом" title="Дом" value="{to_house}" style="width: 34%;"/>
                                    <input type="text" class="form-control" name="to_corpus[]" placeholder="Корпус" title="Корпус" value="{to_corpus}" style="width: 33%;"/>
                                    <input type="text" class="form-control" name="to_appart[]" placeholder="Квартира" title="Квартира" value="{to_appart}" style="width: 33%;"/>
                                    <input type="text" class="form-control" name="to_fio[]" placeholder="Получатель" title="Получатель" value="{to_fio}" style="width: 50%;"/>
                                    <input type="text" class="form-control" name="to_phone[]" placeholder="Телефон получателя" title="Телефон получателя" value="{to_phone}" style="width: 50%;"/>
                                    <input type="text" class="form-control" name="to_time[]" placeholder="Время доставки" title="Время доставки" value="{time}" style="width: 50%;"/>
                                    <input type="text" class="form-control" name="cost_route[]" placeholder="Инкассация" title="Инкассация" value="{cost_route}" style="width: 50%;"/>
                                    <br/>
                                    <textarea name="comment[]" class="form-control">
                                        <xsl:value-of select="comment"/>
                                    </textarea>
                                    <div class="input-group-btn" style="vertical-align: top;">
                                        <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row(this)">
                                            <xsl:if test="position() != count(../../address/item)">
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
                                    <input type="text" class="form-control" name="to[]" placeholder="Адрес" value="" style="width: 100%;"/>
                                    <input type="text" class="form-control" name="to_house[]" placeholder="Дом" value="" style="width: 34%;"/>
                                    <input type="text" class="form-control" name="to_corpus[]" placeholder="Корпус" value="" style="width: 33%;"/>
                                    <input type="text" class="form-control" name="to_appart[]" placeholder="Квартира" value="" style="width: 33%;"/>
                                    <input type="text" class="form-control" name="to_fio[]" placeholder="Получатель" value="" style="width: 50%;"/>
                                    <input type="text" class="form-control" name="to_phone[]" placeholder="Телефон получателя" value="" style="width: 50%;"/>
                                    <input type="text" class="form-control" name="to_time[]" placeholder="Время доставки" value="" style="width: 50%;"/>
                                    <input type="text" class="form-control" name="cost_route[]" placeholder="Инкассация" value="" style="width: 50%;"/>
                                    <br/>
                                    <textarea name="сomment[]" class="form-control" placeholder="Комментарий">
                                        <xsl:value-of select="comment"/>
                                    </textarea>
                                    <div class="input-group-btn" style="vertical-align: top;">
                                        <button type="button" class="btn-clone btn btn-success" title="Добавить" onclick="clone_div_row(this)">+</button>
                                        <button type="button" class="btn-delete btn btn-danger" title="Удалить" onclick="delete_div_row(this)" disabled="">-</button>
                                    </div>
                                </div>
                            </xsl:if>
                        </div>
                    </div>

                </div>
            </div>
            <div style="text-align: center">
                <input class="btn btn-success" type="submit" value="сохранить" name="submit"/>
            </div>
        </form>
        <hr/>
    </xsl:template>
</xsl:stylesheet>
