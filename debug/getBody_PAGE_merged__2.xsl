<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
	<xsl:output method="html"/>
	<xsl:template match="/">
		<HTML>
			<head>
			</head>
			<xsl:variable name="content">
				<xsl:value-of select="//page/body/@contentContainer"/>
			</xsl:variable>
			<xsl:apply-templates select="//page/body/module[@name = $content]"/>
		</HTML>
	</xsl:template>
<xsl:include href="../layout/orders/logist.view.xsl"/></xsl:stylesheet>
