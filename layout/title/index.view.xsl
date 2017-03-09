<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'index']">
        <xsl:if test="//page/@isAjax != 1">
            <div class="row">
                <div class="camera_wrap">
                    <div data-src="images/image_1.jpg"/>
                    <div data-src="images/image_2.jpg"/>
                    <div data-src="images/image_3.jpg"/>
                    <div data-src="images/image_4.jpg"/>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Добро пожаловать в Цветочное Такси!</h3>
                        </div>
                        <div id="viewListlang" class="panel-body">
                            <p>Очень надеемся, что этот сайт сделает более легким и удобным наше общение.
                                <br/>
                                <br/>
                                <strong>После регистрации вам будут доступны:</strong>
                            </p>

                            <ul>
                                <li>форма заказа доставки цветов</li>
                                <li>расчет стоимости доставки в режиме онлайн</li>
                                <li>отслеживание состояние ваших заказов</li>
                                <li>получение уведомлений об изменении статусов ваших заказов</li>
                                <li>неограниченный доступ к истории своих заказов</li>
                                <!--&lt;!&ndash; <li>оплатить тур не выходя из дома и распечатать договор, ваучер и путевку (<span style="color:#FF8C00"><strong>после 10.10.2014</strong></span>)</li> &ndash;&gt;-->
                                <!--<li>увидеть сколько мест осталось в продаже</li>-->
                                <!--<li>получить информацию по выезду просто наведя курсор на нужный тур</li>-->
                                <!--<li>по номеру заказа внести изменения и дополнения в свою заявку</li>-->
                                <!--<li>посмотреть на карте места остановок автобуса</li>-->
                                <!--<li>получить информацию важную и просто интересную</li>-->
                                <!--<li>купить "горящую путевку" с хорошей скидкой</li>-->
                                <!--<li>подобрать экскурсионную поездку или игру-квест для школьной группы</li>-->
                                <!--<li>подписаться на рассылку, заказать обратный звонок и многое другое</li>-->
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <span class="btn btn-info btn-xs calc_route right" onclick="calc_route()">Рассчитать маршрут</span>
                            <!--<a href="#" title="" class="btn btn-warning btn-xs"-->
                               <!--style="color: #fff;width: 100px;float: right;">Перейти-->
                            <!--</a>-->
                            <h3 class="panel-title">Калькулятор доставки</h3>
                        </div>
                        <div id="viewListlang" class="panel-body">
                            <xsl:call-template name="calcOnMain"/>
                            <div style="display:none">
                                <xsl:for-each select="prices/item">
                                    <input id="km_{id}" class="km_cost" type="hidden" value="{km_cost}" km_from="{km_from}" km_to="{km_to}"/>
                                </xsl:for-each>
                                <xsl:for-each select="add_prices/item">
                                    <input id="km_{type}" type="hidden" value="{cost_route}"/>
                                </xsl:for-each>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--<div class="col-md-6">-->
                <!--&lt;!&ndash; НОВОСТИ &ndash;&gt;-->
                <!--<div class="comment-list">-->
                    <!--<xsl:call-template name="newsListIndex"/>-->
                <!--</div>-->
                <!--&lt;!&ndash; КОНЕЦ НОВОСТЕЙ &ndash;&gt;-->
            <!--</div>-->
            <br/>
            <br/>
        </xsl:if>
        <xsl:if test="//page/@isAjax = 1">
            Ajax!
        </xsl:if>
    </xsl:template>
    <xsl:template name="calcOnMain">
        <div class="row">
            <div class="col-sm-2">
                <label>Откуда:</label>
            </div>
            <div class="col-sm-10">
                <div class="input-group routes-block" rel="{position()}">
                    <div class="form-control" style="width: 70%;">
                        <span class="order-add-title text-info">Адрес отправления</span>
                        <input type="search" class="order-route-data spb-streets js-street_upper" name="to[]" title="Улица, проспект и т.д." onchange="" autocomplete="off" required=""/>
                    </div>
                    <div class="form-control" style="width: 15%;">
                        <span class="order-add-title text-info">дом</span>
                        <input type="text" class="order-route-data to_house number" name="to_house[]" title="Дом" onchange="calc_route()" required=""/>
                    </div>
                    <div class="form-control" style="width: 15%;">
                        <span class="order-add-title text-info">корп/строение</span>
                        <input type="text" class="order-route-data to_corpus number" name="to_corpus[]" title="Корпус" onchange="calc_route()" required=""/>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <label>Куда:</label>
            </div>
            <div class="col-sm-10">
                <div class="input-group routes-block" rel="{position()}">
                    <div class="form-control" style="width: 70%;">
                        <span class="order-add-title text-info">Адрес доставки</span>
                        <input type="search" class="order-route-data spb-streets js-street_upper" name="to[]" title="Улица, проспект и т.д." onchange="" autocomplete="off" required=""/>
                    </div>
                    <div class="form-control" style="width: 15%;">
                        <span class="order-add-title text-info">дом</span>
                        <input type="text" class="order-route-data to_house number" name="to_house[]" title="Дом" onchange="calc_route()" required=""/>
                    </div>
                    <div class="form-control" style="width: 15%;">
                        <span class="order-add-title text-info">корп/строение</span>
                        <input type="text" class="order-route-data to_corpus number" name="to_corpus[]" title="Корпус" onchange="calc_route()" required=""/>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 map-form">
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
        </div>
    </xsl:template>
    <!-- НОВОСТИ НА ГЛАВНОЙ -->
    <xsl:template name="newsListIndex">
            <div class="panel panel-info arrow left">
                <div class="panel-heading">
                    <h3 class="panel-title">Новости</h3>
                </div>
                <div class="panel-body">
                  <xsl:for-each select="news/item">
                    <header class="text-left">
                        <span class="label label-info" style="float: right;">
                            <time class="comment-date" datetime="{time}">
                                <i class="fa fa-clock-o"/>
                                <xsl:text> </xsl:text><xsl:value-of select="time"/>
                            </time>
                        </span>
                        <div class="comment-user">
                            <i class="fa fa-user"/>
                            <xsl:text> </xsl:text><xsl:value-of select="title"/>
                        </div>
                    </header>
                    <div class="comment-post">
                        <xsl:value-of select="content" disable-output-escaping="yes"/>
                    </div>
                    <xsl:if test="subject != ''">
                        <p class="text-right" style="margin: 0;"><a href="/news/view-{id}/" class="btn btn-warning btn-xs"><i class="fa fa-reply"/><xsl:text> </xsl:text>Подробнее</a></p>
                    </xsl:if>
                      <hr/>
                 </xsl:for-each>
                </div>
            </div>
    </xsl:template>
</xsl:stylesheet>