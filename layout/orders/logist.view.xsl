<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <h2>Логист</h2>

            <hr/>
            <table class="table table-striped data-table">
                <thead>
                    <th>id</th>
                    <th>Компания</th>
                    <th>Адрес приема</th>
                    <th>Время готовности</th>
                    <th>Маршрут/Получатель/Время доставки/Стоимость</th>
                    <th>Статус</th>
                    <th>Курьер</th>
                    <th>Комментарий</th>
                </thead>
                <tbody>
                    <xsl:for-each select="orders/item">
                        <tr>
                            <td><xsl:value-of select="id"/></td>
                            <td>
                                <b><xsl:value-of select="title"/></b><br/>
                                <xsl:value-of select="name"/>
                            </td>
                            <td>
                                <xsl:value-of select="address"/><br/>
                                <i><xsl:value-of select="addr_comment"/></i>
                            </td>
                            <td><b><xsl:value-of select="ready"/></b></td>
                            <td style="font-size:85%">
                                <table class="table table-hover table-condensed">
                                    <tbody>
                                        <xsl:for-each select="route/array">
                                            <tr>
                                                <td>
                                                    <xsl:value-of select="to"/>, <xsl:value-of select="to_house"/>, <xsl:value-of select="to_corpus"/>,
                                                    <xsl:value-of select="to_appart"/>
                                                </td>
                                                <td>
                                                    <xsl:value-of select="to_fio"/>
                                                    <xsl:text> </xsl:text>
                                                    <nobr><xsl:value-of select="to_phone"/></nobr>
                                                </td>
                                                <td><xsl:value-of select="to_time"/></td>
                                                <td><xsl:value-of select="cost_route"/></td>
                                            </tr>
                                        </xsl:for-each>
                                    </tbody>
                                </table>
                            </td>
                            <td>
                                <xsl:value-of select="status"/>
                            </td>
                            <td>
                                <xsl:value-of select="car"/>
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