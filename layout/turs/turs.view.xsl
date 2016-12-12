<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template match="container[@module = 'turlist']">
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
                <div class="col-md-6">
                    <!-- НОВОСТИ -->
                    <div class="comment-list">
                        <xsl:call-template name="newsListIndex"/>
                    </div>
                    <!-- КОНЕЦ НОВОСТЕЙ -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">ДОБРО ПОЖАЛОВАТЬ НА САЙТ ДОСТАВКИ ЦВЕТОВ!</h3>
                        </div>
                        <div id="viewListlang" class="panel-body">
                            <p>Очень надеемся, что этот сайт сделает более легким и удобным наше общение.
                                <br/>
                                <strong>на сайте можно:</strong>
                            </p>

                            <ul>
                                <li>быстро и легко заказать цветы</li>
                                <li>отслеживать состояние вашего заказа</li>
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
                <div class="col-md-6">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <a href="#" title="" class="btn btn-warning btn-xs"
                               style="color: #fff;width: 100px;float: right;">Перейти
                            </a>
                            <h3 class="panel-title">Калькулятор доставки</h3>
                        </div>
                        <div id="viewListlang" class="panel-body">

                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <br/>
        </xsl:if>
        <xsl:if test="//page/@isAjax = 1">
            Ajax!
        </xsl:if>
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