<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/TR/xhtml1/strict" version="1.0">
	<xsl:output method="html" omit-xml-declaration="yes" doctype-public="-//W3C//DTD XHTML 1.0 Strict//EN" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd" indent="yes"/>
	<!--<xsl:include href="head.page.xsl"/>-->
	<xsl:template match="/">
		<HTML>
			<head>
				<!--<title>Info</title>
			<link rel="stylesheet" href="css/style.css" type="text/css" media="screen"/>
			<link rel="stylesheet" href="css/pda.css" type="text/css" media="handheld" />
			<link type="text/css" href="css/jqueryui.css" rel="stylesheet"/>
			<link rel="stylesheet" type="text/css" media="print" href="css/print.css"/>
			--></head>
			<xsl:variable name="content">
				<xsl:value-of select="//page/body/@contentContainer"/>
			</xsl:variable>
			<xsl:apply-templates select="//page/body/module[@name = $content]"/>
		</HTML>
	</xsl:template>
	<!--<xsl:template name="linkback"/>-->
<xsl:include href="../layout/tc/tc.turlist.xsl"/><xsl:include href="../layout/head.page.xsl"/></xsl:stylesheet>
