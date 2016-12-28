<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <form method="post" style="margin-bottom: 2px;">
                <div class="row">
                    <div class="col-sm-9">
                        <striong>Статусы: </striong>
                        <xsl:for-each select="statuses/item">
                            <label class="btn btn-default" style="margin-right:10px;" onchange="filter_table()">
                                <input class="statuses" type="checkbox" aria-label="" value="{id}"/>
                                <xsl:text> </xsl:text>
                                <span style="vertical-align: top;"><xsl:value-of select="status"/></span>
                            </label>
                        </xsl:for-each>
                    </div>
                    <div class="col-sm-3">
                        <div class="input-group" style="float:right">
                            <input type="text" class="form-control" id="end_date" name="date_to" value="{@date_to}" style="text-align:center" />
                            <span class="input-group-btn">
                                <button class="btn btn-info">Обновить</button>
                            </span>
                        </div>
                        <script>
                            $(function () {
                                $('#end_date').datetimepicker({format: 'L', locale: 'ru'});
                            });
                        </script>
                    </div>
                </div>
            </form>
            <table class="table table-striped table-hover table-bordered new-logist-data-table">
                <thead>
                    <th>#</th>
                    <th>Заказ</th>
                    <th>Готовность</th>
                    <th>Время доставки</th>
                    <th>Адрес приема и контакт</th>
                    <th>Компания</th>
                    <th>Адрес доставки</th>
                    <th>Телефон</th>
                    <th>Получатель</th>
                    <th>Статус</th>
                    <th>Курьер и телефон</th>
                    <th>Стоимость</th>
                    <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                        <th>Инкассация</th>
                        <th>% инкассации</th>
                        <th>Заработок курьера</th>
                        <th>Заработок компании</th>
                    </xsl:if>
                    <th>Прим. заказ</th>
                    <th>Прим. адрес</th>
                </thead>
                <tbody>
                    <xsl:for-each select="orders/item/route/array">
                        <tr class="status_{status_id} order_route_{id_route}">
                            <td class="text text-muted"><xsl:value-of select="position()"/></td>
                            <td>
                                <xsl:value-of select="../../id"/>
                                <a href="/orders/order-{../../id}/" title="редактировать" class="btn btn-success btn-sm chg-status">
                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span> Изм.
                                </a>
                            </td>
                            <td><xsl:value-of select="../../ready"/></td>
                            <td><xsl:value-of select="to_time"/></td>
                            <td><b><xsl:value-of select="../../address"/></b>
                                <br/>
                                <i>
                                    <xsl:value-of select="../../addr_comment"/>
                                </i>
                            </td>
                            <td><xsl:value-of select="../../title"/></td>
                            <td><xsl:value-of select="to"/>, <xsl:value-of select="to_house"/>, <xsl:value-of select="to_corpus"/>, <xsl:value-of select="to_appart"/></td>
                            <td><nobr>
                                <b>
                                    <xsl:value-of select="to_phone"/>
                                </b>
                            </nobr></td>
                            <td><xsl:value-of select="to_fio"/></td>
                            <td class="order_info">
                                <xsl:attribute name="rel">{"order_id": "<xsl:value-of select="../../id"/>","from": "<xsl:value-of select="../../from"/>","ready": "<xsl:value-of select="../../ready"/>","to_addr": "<xsl:value-of select="to"/>, д.<xsl:value-of select="to_house"/>, корп.<xsl:value-of select="to_corpus"/>, кв.<xsl:value-of select="to_appart"/>","to_time": "<xsl:value-of select="to_time"/>","to_fio": "<xsl:value-of select="to_fio"/>","to_phone": "<xsl:value-of select="to_phone"/>","cost": "<xsl:value-of select="cost_route"/>"}</xsl:attribute>
                                <xsl:value-of select="status"/>
                                <div title="Изменить статус" class="btn btn-warning btn-sm chg-status" onclick="chg_status({id_route},{../../id})">
                                    <span class="glyphicon glyphicon-flag" aria-hidden="true"> </span> Стутус
                                </div>
                            </td>
                            <td><xsl:value-of select="../../car_fio"/>
                                <a href="#" title="Назначить курьера" class="btn btn-info btn-sm chg-status" onclick="chg_courier({../../id})">
                                    <i class="fa fa-car" aria-hidden="true"> </i> Курьер
                                </a>
                            </td>
                            <td><xsl:value-of select="cost_route"/></td>


                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                                <td>-</td>
                                <td>
                                    <xsl:value-of select="(number(cost_route) * number(../../inkass_proc)) div 100"/>
                                    (<xsl:value-of select="../../inkass_proc"/>%)
                                </td>
                                <td>-</td>
                                <td>-</td>
                            </xsl:if>
                            <td>
                                <xsl:value-of select="../../comment"/>
                            </td>
                            <td>
                                <xsl:value-of select="comment"/>
                            </td>
                        </tr>
                    </xsl:for-each>
                </tbody>
            </table>
        </xsl:if>
        <xsl:if test="//page/@isAjax = 1">
            Ajax!
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>