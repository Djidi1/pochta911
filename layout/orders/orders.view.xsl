<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <h2>Ваши заказы</h2>
            <table class="table data-table">
                <thead>
                    <th>id</th>
                    <th>Заказчик</th>
                    <th>Маршрут</th>
                    <th>Комментарий</th>
                    <th>Дата заказа</th>
                    <th>Статус</th>
                </thead>
                <tbody>
                    <xsl:for-each select="orders/item">
                        <tr>
                            <td><xsl:value-of select="id"/></td>
                            <td><xsl:value-of select="name"/></td>
                            <td><xsl:value-of select="from"/></td>
                            <td><xsl:value-of select="comment"/></td>
                            <td><xsl:value-of select="dk"/></td>
                            <td><xsl:value-of select="status"/></td>
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