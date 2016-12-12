<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'messages']">
	<div class="alert alert-info" style="margin-bottom: 15px;">
			<xsl:for-each select="message">
				<p style="margin: 10px;">
					<span class="glyphicon glyphicon-info-sign" aria-hidden="true"> </span>
					<xsl:if test="@caption != ''">
						<xsl:value-of select="@caption"/>
						<xsl:text>: </xsl:text>
					</xsl:if>
					<xsl:text> </xsl:text>
					<xsl:value-of select="." disable-output-escaping="yes"/>
				</p>
			</xsl:for-each>
	</div>
		<xsl:if test="nolink = 1">
		</xsl:if>
		<xsl:if test="nolink = 2">
			<!--<p class="back">-->
				<!--<a href="javascript:history.go(-1)" title="назад">назад</a>-->
			<!--</p>-->
		</xsl:if>
	</xsl:template>
	<xsl:template match="container[@module = 'messageslink']">
		<div class="message-block">
			<xsl:for-each select="message">
				<p class="title">
					<xsl:if test="@caption != ''">
						<xsl:value-of select="@caption"/>
						<xsl:text> : </xsl:text>
					</xsl:if>
					<xsl:value-of select="."/>
				</p>
				<p>
					<a href="{@linkSrc}" title="На главную">
						<xsl:value-of select="@linkTitle"/>
					</a>
				</p>
			</xsl:for-each>
		</div>
	</xsl:template>
</xsl:stylesheet>
