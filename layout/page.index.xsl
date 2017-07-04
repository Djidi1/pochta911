<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" encoding="utf-8" indent="yes"/>
    <xsl:include href="head.main.page.xsl"/>
    <xsl:include href="head.page.xsl"/>
    <xsl:template match="/">
        <xsl:variable name="content">
            <xsl:value-of select="//page/body/@contentContainer"/>
        </xsl:variable>
        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html></xsl:text>
        <html xmlns="http://www.w3.org/1999/xhtml">
            <xsl:call-template name="head"/>
            <body>
                <xsl:attribute name="id">
                    <xsl:value-of select="//page/@name"/>
                </xsl:attribute>
                <div id="loadingDiv">
                    <div class="dumbBoxOverlay"/>
                    <div class="vertical-offset">
                        <div class="dumbBox"/>
                    </div>
                </div>
                <div class="body-top">
                    <div class="main">
                        <xsl:if test="/page/body/module[@name='CurentUser']/container/login = 1">
                            <xsl:call-template name="main_headWrap"/>
                        </xsl:if>
                        <xsl:if test="/page/body/module[@name='CurentUser']/container/login != 1">
                            <xsl:call-template name="headWrap"/>
                        </xsl:if>
                        <div id="content">
                            <div class="wrapper2">
                                <xsl:choose>
                                    <xsl:when test="//page/body[@hasErrors = 0]">
                                        <xsl:apply-templates select="//page/body/module[@name = 'menu' and @name != '$content']"/>
                                        <xsl:apply-templates select="//page/body/module[@name = $content]"/>
                                    </xsl:when>
                                    <xsl:when test="//page/body[@hasErrors = 1]">
                                        <div id="errors">
                                            <h2>Ошибка</h2>
                                            <xsl:apply-templates select="//page/body/module[@name = 'error']"/>
                                            <xsl:apply-templates select="//page/body/module[@name = $content]/container[@module = 'errors']"/>
                                            <p>
                                                <a href="/" title="На главную">На главную</a>
                                            </p>
                                        </div>
                                    </xsl:when>
                                    <xsl:when test="//page/body[@hasErrors = 2]">
                                        <xsl:apply-templates select="//page/body/module[@name = $content]"/>
                                    </xsl:when>
                                </xsl:choose>
                            </div>
                        </div>
                        <div class="clear"/>
                        <xsl:call-template name="bottom_block"/>
                    </div>
                </div>
                <!-- Yandex.Metrika counter -->
                <script type="text/javascript">
                    (function (d, w, c) {
                    (w[c] = w[c] || []).push(function() {
                    try {
                    w.yaCounter45182019 = new Ya.Metrika({
                    id:45182019,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                    });
                    } catch(e) { }
                    });

                    var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () { n.parentNode.insertBefore(s, n); };
                    s.type = "text/javascript";
                    s.async = true;
                    s.src = "https://mc.yandex.ru/metrika/watch.js";

                    if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                    } else { f(); }
                    })(document, window, "yandex_metrika_callbacks");
                </script>
                <noscript><div><img src="https://mc.yandex.ru/watch/45182019" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
                <!-- /Yandex.Metrika counter -->
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
