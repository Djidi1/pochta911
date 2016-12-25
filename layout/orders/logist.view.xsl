<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'list']">
        <xsl:if test="//page/@isAjax != 1">
            <h2>Логист</h2>

            <hr/>
            <form method="post">
                <div class="row">
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-3">
                        <div class="input-daterange input-group" id="datepicker">
                            <input type="text" class="input-sm form-control" id="start_date" name="from" value="{@from}"/>
                            <span class="input-group-addon">to</span>
                            <input type="text" class="input-sm form-control" id="end_date" name="to" value="{@to}"/>
                        </div>
                        <script type="text/javascript">
                            $(function () {
                            $('#start_date').datetimepicker({format: 'L', locale: 'ru'});
                            $('#end_date').datetimepicker({format: 'L', locale: 'ru',
                            useCurrent: false //Important! See issue #1075
                            });
                            $("#start_date").on("dp.change", function (e) {
                            $('#end_date').data("DateTimePicker").minDate(e.date);
                            });
                            $("#end_date").on("dp.change", function (e) {
                            $('#start_date').data("DateTimePicker").maxDate(e.date);
                            });
                            $("#start_date").on("dp.show", function (e) {
                            $('#start_date').data("DateTimePicker").maxDate(e.date);
                            });
                            $("#end_date").on("dp.show", function (e) {
                            $('#end_date').data("DateTimePicker").minDate(e.date);
                            });
                            });
                        </script>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-info btn-sm">Обновить</button>
                    </div>
                </div>
            </form>
            <table class="table table-striped logist-data-table">
                <thead>
                    <th>Заказ</th>
                    <th>Маршрут</th>
                    <th>Получатель</th>
                    <th>Время доставки</th>
                    <th>Стоимость</th>
                    <th>Статус</th>
                    <th>Курьер</th>
                </thead>
                <tbody>
                    <xsl:for-each select="orders/item/route/array">
                        <tr>
                            <td>
                                <div class="row">
                                    <div class="col-md-2">
                                        Готовность:<br/>
                                        <b>
                                            <xsl:value-of select="../../ready"/>
                                        </b>
                                    </div>
                                    <div class="col-md-3">
                                        Заказ №
                                        <xsl:value-of select="../../id"/>
                                        <br/>
                                        <b>
                                            <xsl:value-of select="../../title"/>
                                        </b>
                                        <br/>
                                        <i>
                                            <xsl:value-of select="../../name"/>
                                        </i>
                                    </div>
                                    <div class="col-md-3">
                                        Адрес магазина:<br/>
                                        <b><xsl:value-of select="../../address"/></b>
                                        <br/>
                                        <i>
                                            <xsl:value-of select="../../addr_comment"/>
                                        </i>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="alert alert-success">
                                            <xsl:value-of select="../../comment"/>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <xsl:value-of select="to"/>, <xsl:value-of select="to_house"/>, <xsl:value-of select="to_corpus"/>,
                                <xsl:value-of select="to_appart"/>
                            </td>
                            <td>
                                <xsl:value-of select="to_fio"/>
                                <xsl:text> </xsl:text>
                                <nobr>
                                    <b>
                                        <xsl:value-of select="to_phone"/>
                                    </b>
                                </nobr>
                            </td>
                            <td>
                                <b>
                                    <xsl:value-of select="to_time"/>
                                </b>
                            </td>
                            <td>
                                <xsl:value-of select="cost_route"/>
                            </td>
                            <td>
                                <xsl:value-of select="status"/>
                            </td>
                            <td>
                                <xsl:value-of select="fio"/>
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