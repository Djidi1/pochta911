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
                        <xsl:if test="/page/@without_menu != 1">
                            <xsl:call-template name="main_headWrap"/>
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
                        <xsl:if test="/page/@without_menu != 1">
                            <xsl:call-template name="bottom_block"/>
                        </xsl:if>
                    </div>
                </div>
            </body>
        </html>
    </xsl:template>
<xsl:include href="../layout/viewLoginBar.xsl"/><xsl:include href="../layout/orders/orders.excel.xsl"/></xsl:stylesheet>
