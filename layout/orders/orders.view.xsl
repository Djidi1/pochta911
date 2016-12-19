<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <h2>Ваши заказы</h2>
            <div>
                <a class="btn btn-success" href="/orders/order-0/" title="Добавить заказ">
                    <span class="glyphicon glyphicon-flag"> </span>
                    <span>Новый заказ</span>
                </a>
            </div>
            <hr/>
            <table class="table data-table">
                <thead>
                    <th>id</th>
                    <th>Заказчик</th>
                    <th>Откуда</th>
                    <th>Маршрут</th>
                    <th>Комментарий</th>
                    <th>Дата заказа</th>
                    <th>Статус</th>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                </thead>
                <tbody>
                    <xsl:for-each select="orders/item">
                        <tr>
                            <td>
                                <xsl:value-of select="id"/>
                            </td>
                            <td>
                                <xsl:value-of select="name"/>
                            </td>
                            <td>
                                <xsl:value-of select="from"/>
                            </td>
                            <td style="font-size:85%">
                                <xsl:for-each select="route/array">
                                    <xsl:value-of select="position()"/>)
                                    <xsl:value-of select="to"/>, <xsl:value-of select="to_house"/>, <xsl:value-of select="to_corpus"/>,
                                    <xsl:value-of select="to_appart"/>
                                    <br/>
                                </xsl:for-each>
                            </td>
                            <td>
                                <xsl:value-of select="comment"/>
                            </td>
                            <td>
                                <xsl:value-of select="dk"/>
                            </td>
                            <td>
                                <xsl:value-of select="status"/>
                            </td>
                            <td width="40px" align="center">
                                <a href="#" title="Изменить статус" class="btn btn-warning btn-xs" onclick="chg_status({id})">
                                    <span class="glyphicon glyphicon-flag" aria-hidden="true"> </span>
                                </a>
                            </td>
                            <td width="40px" align="center">
                                <a href="/orders/order-{id}/" title="редактировать" class="btn btn-success btn-xs">
                                    <span class="glyphicon glyphicon-pencil" aria-hidden="true"> </span>
                                </a>
                            </td>
                            <td width="40px" align="center">
                                <a href="/orders/orderBan-{id}/" title="удалить" class="btn btn-danger btn-xs">
                                    <xsl:attribute name="onClick">return confirm('Вы действительно хотите удалить заказ <xsl:value-of select="id"/>?');
                                    </xsl:attribute>
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"> </span>
                                </a>
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