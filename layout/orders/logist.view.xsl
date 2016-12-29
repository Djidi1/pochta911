<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <form method="post" style="margin-bottom: 2px;">
                <div class="row">
                    <div class="col-sm-9">
                        <striong>Статусы: </striong>
                        <xsl:for-each select="statuses/item">
                            <label class="btn btn-default btn-xs" style="margin-right:10px;" onchange="filter_table()">
                                <input class="statuses" type="checkbox" aria-label="" value="{id}"/>
                                <xsl:text> </xsl:text>
                                <span style="vertical-align: text-bottom;"><xsl:value-of select="status"/></span>
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
                        <!--<th>Инкассация</th>-->
                        <!--<th>% инкассации</th>-->
                        <!--<th>Заработок курьера</th>-->
                        <!--<th>Заработок компании</th>-->
                    </xsl:if>
                    <th>Прим. заказ</th>
                    <!--<th>Прим. адрес</th>-->
                    <th> </th>
                </thead>
                <tbody>
                    <xsl:for-each select="orders/item/route/array">
                        <tr class="status_{status_id} order_route_{id_route} order_{../../id}" rel="{id_route}">
                            <xsl:attribute name="class">
                                status_<xsl:value-of select="status_id"/> order_route_<xsl:value-of select="id_route"/> order_<xsl:value-of select="../../id"/>
                                <xsl:if test="../../car_accept != ''"> info</xsl:if>
                            </xsl:attribute>
                            <td class="text text-muted"><xsl:value-of select="position()"/></td>
                            <td>
                                <xsl:value-of select="../../id"/>
                            </td>
                            <td><xsl:value-of select="../../ready"/></td>
                            <td><nobr>
                                <xsl:value-of select="to_time"/>-<xsl:value-of select="to_time_end"/>
                            </nobr></td>
                            <td><b><xsl:value-of select="../../address"/></b>
                                <br/>
                                <i>
                                    <xsl:value-of select="../../addr_comment"/>
                                </i>
                            </td>
                            <td><xsl:value-of select="../../title"/></td>
                            <td><nobr><xsl:value-of select="to"/>, <xsl:value-of select="to_house"/>, <xsl:value-of select="to_corpus"/>, <xsl:value-of select="to_appart"/></nobr></td>
                            <td><nobr>
                                <b>
                                    <xsl:value-of select="to_phone"/>
                                </b>
                            </nobr></td>
                            <td><xsl:value-of select="to_fio"/></td>
                            <td class="order_info">
                                <xsl:attribute name="rel">{"order_id": "<xsl:value-of select="../../id"/>","from": "<xsl:value-of select="../../address"/>","ready": "<xsl:value-of select="../../ready"/>","to_addr": "<xsl:value-of select="to"/>, д.<xsl:value-of select="to_house"/>, корп.<xsl:value-of select="to_corpus"/>, кв.<xsl:value-of select="to_appart"/>","to_time": "<xsl:value-of select="to_time"/>","to_fio": "<xsl:value-of select="to_fio"/>","to_phone": "<xsl:value-of select="to_phone"/>","cost": "<xsl:value-of select="cost_route"/>"}</xsl:attribute>
                                <xsl:attribute name="class">
                                    order_info
                                    <xsl:if test="status_id = 3"> warning</xsl:if>
                                    <xsl:if test="status_id = 4"> success</xsl:if>
                                    <xsl:if test="status_id = 5"> danger</xsl:if>
                                </xsl:attribute>
                                <xsl:value-of select="status"/>
                            </td>
                            <td>
                                <xsl:value-of select="../../fio_car"/> (<xsl:value-of select="../../car_number"/>)
                            </td>
                            <td><xsl:value-of select="cost_tovar"/></td>


                            <xsl:if test="/page/body/module[@name='CurentUser']/container/group_id = 1">
                                <!--<td><xsl:value-of select="number(cost_route)+number(cost_tovar)"/></td>-->
                                <!--<td>-->
                                    <!--<xsl:value-of select="((number(cost_route)+number(cost_tovar)) * number(../../inkass_proc)) div 100"/>-->
                                    <!--(<xsl:value-of select="../../inkass_proc"/>%)-->
                                <!--</td>-->
                                <!--<td><xsl:value-of select="number(cost_route) * 0.75"/></td>-->
                                <!--<td><xsl:value-of select="number(cost_route) * 0.25"/></td>-->
                            </xsl:if>
                            <td>
                                <xsl:value-of select="../../comment"/>
                            </td>
                            <!--<td>-->
                                <!--<xsl:value-of select="comment"/>-->
                            <!--</td>-->
                            <td style="width:90px">
                                <div class="btn-group">
                                    <!--<div onclick="open_dialog('/orders/order-{../../id}/')" class="btn btn-success btn-xs chg-status" title="редактировать">-->
                                        <!--<span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>-->
                                    <!--</div>-->
                                    <a href="/orders/order-{../../id}/" class="btn btn-success btn-xs chg-status" title="редактировать" target="_blank">
                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>
                                    </a>
                                    <div title="Назначить курьера" class="btn btn-info btn-xs chg-status" onclick="chg_courier({../../id})">
                                        <i class="fa fa-car" aria-hidden="true"> </i>
                                    </div>
                                    <div title="Изменить статус" class="btn btn-warning btn-xs chg-status" onclick="chg_status({id_route},{../../id})">
                                        <span class="glyphicon glyphicon-flag" aria-hidden="true"> </span>
                                    </div>
                                </div>
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