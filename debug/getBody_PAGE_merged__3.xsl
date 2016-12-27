<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html" encoding="utf-8" indent="yes"/>
    <xsl:include href="head.main.page.xsl"/>
    <xsl:template match="/">
        <xsl:variable name="content">
            <xsl:value-of select="//page/body/@contentContainer"/>
        </xsl:variable>
        <xsl:text disable-output-escaping="yes">&lt;!DOCTYPE html&gt;</xsl:text>
        <html>
            <xsl:call-template name="main_head"/>
            <body>
                <xsl:attribute name="id">
                    <xsl:value-of select="//page/@name"/>
                </xsl:attribute>
                <div class="body-top">
                    <div class="main" style="width:90%;">
                        <xsl:call-template name="main_headWrap"/>
                        <div id="content">
                            <div class="wrapper2">
                                <xsl:choose>
                                    <xsl:when test="//page/body[@hasErrors = 0]">
                                        <xsl:apply-templates select="//page/body/module[@name = 'menu' and @name != '$content']"/>
                                        <xsl:apply-templates select="//page/body/module[@name = $content]"/>
                                    </xsl:when>
                                    <xsl:when test="//page/body[@hasErrors = 1]">
                                        <div id="errors">
                                            <h2>&#x41E;&#x448;&#x438;&#x431;&#x43A;&#x430;</h2>
                                            <xsl:apply-templates select="//page/body/module[@name = 'error']"/>
                                            <xsl:apply-templates select="//page/body/module[@name = $content]/container[@module = 'errors']"/>
                                            <p>
                                                <a href="/" title="&#x41D;&#x430; &#x433;&#x43B;&#x430;&#x432;&#x43D;&#x443;&#x44E;">&#x41D;&#x430; &#x433;&#x43B;&#x430;&#x432;&#x43D;&#x443;&#x44E;</a>
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
                        <div id="foot">
                            <p id="back-top" style="display: none;">
                                <a href="#top">
                                    <span/>
                                </a>
                            </p>
                            <div class="well wrapper">
                                <div class="moduletable">
                                    <ul class="bottom-menu navbar-nav">
                                        <li class="item-207">
                                            <a href="#">&#x41E; &#x43A;&#x43E;&#x43C;&#x43F;&#x430;&#x43D;&#x438;&#x438;</a>
                                        </li>
                                        <li class="item-471">
                                            <a href="#">&#x410;&#x43A;&#x446;&#x438;&#x438;</a>
                                        </li>
                                        <li class="item-470">
                                            <a href="#">&#x41A;&#x43E;&#x43D;&#x442;&#x430;&#x43A;&#x442;&#x44B;</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="footerText">
                                    <div class="footer1">Copyright &#xA9; <xsl:value-of select="//@year"/> &#x414;&#x43E;&#x441;&#x442;&#x430;&#x432;&#x43A;&#x430;
                                        &#x446;&#x432;&#x435;&#x442;&#x43E;&#x432;. &#x412;&#x441;&#x435; &#x43F;&#x440;&#x430;&#x432;&#x430; &#x437;&#x430;&#x449;&#x438;&#x449;&#x435;&#x43D;&#x44B;.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </body>
        </html>
    </xsl:template>
<xsl:include href="../layout/viewLoginBar.xsl"/><xsl:include href="../layout/orders/orders.view.xsl"/></xsl:stylesheet>
