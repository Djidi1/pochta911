<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template name="head">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
			<base href="."/>
			<title>Балтик Лайнс Тур</title>
			<link href="./images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
			<link rel="stylesheet" href="./css/facebox.css" type="text/css"/>
			<link rel="stylesheet" href="./css/camera.css" type="text/css"/>
			<link rel="stylesheet" href="./css/bootstrap.min.css?v2" type="text/css"/>
			<link rel="stylesheet" href="./css/jquery-ui.css" type="text/css"/>
			<link rel="stylesheet" href="./css/style.css?v3" type="text/css"/>
			<link rel="stylesheet" href="./css/custom.css" type="text/css"/>
			<link rel="stylesheet" href="./css/stylesheet.css" type="text/css"/>
			<link rel="stylesheet" href="./css/system.css" type="text/css"/>
			<link rel="stylesheet" href="./css/position.css" type="text/css" media="screen,projection"/>
			<link rel="stylesheet" href="./css/layout.css" type="text/css" media="screen,projection"/>
			<link rel="stylesheet" href="./css/print.css" type="text/css" media="Print"/>
			<link rel="stylesheet" href="./css/virtuemart.css?v2" type="text/css"/>
			<link rel="stylesheet" href="./css/products.css" type="text/css"/>
			<link rel="stylesheet" href="./css/personal.css" type="text/css"/>
			<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css" type="text/css"/>
			<!--<link rel="stylesheet" href="./css/flatui.css" type="text/css"/>-->
			<link rel="stylesheet" href="./css/colorPicker.css" type="text/css"/>
			<script src="./js/jquery.min.js" type="text/javascript"/>
			<script src="./js/jquery-ui.min.js" type="text/javascript"/>
			<script src="./js/bootstrap.min.js" type="text/javascript"/>
			<script src="./js/bootbox.min.js" type="text/javascript"/>
			<script src="./js/jquery.multiselect.min.js?v1" type="text/javascript"/>
			<script src="./js/jquery.maskinput.min.js" type="text/javascript"/>
			<script type="text/javascript" src="./js/ready.js?v1"/>
			<script type="text/javascript" src="./js/script.js?v3"/>
			<script type="text/javascript" src="./js/jquery.colorPicker.min.js"/>
            <script type="text/javascript" language="javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"/>
            <script type="text/javascript" language="javascript" src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"/>
			<script src="/ckeditor/ckeditor.js?v1"/>
<script> 
var roxyFileman = '/fileman/index.html';
    var config = {filebrowserBrowseUrl:roxyFileman,filebrowserUploadUrl:roxyFileman,filebrowserImageBrowseUrl:roxyFileman+'?type=image',filebrowserImageUploadUrl:roxyFileman+'?type=image'}
$(function(){
/*if ($('#edit_content').length){
   CKEDITOR.replace( 'edit_content',config);
}*/

    CKEDITOR.replaceAll( function('ckeditor',config){});

});
 </script>
<!--[if IE 8]>
	<link href="./css/ie8only.css" rel="stylesheet" type="text/css" />
<![endif]-->
<!--[if lt IE 8]>
    <div style=' clear: both; text-align:center; position: relative; z-index:9999;'>
        <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0"  alt="" /></a>
    </div>
<![endif]-->
<!--[if lt IE 9]>
<script type="text/javascript" src="./js/html5.js"></script>
<![endif]-->
		</head>
	</xsl:template>
	<xsl:template name="headWrap">
		<xsl:variable name="content">
			<xsl:value-of select="//page/body/@contentContainer"/>
		</xsl:variable>
		<div id="header">
			<div class="row-head">
				<div class="relative">
					<div id="topmenu">
						<div class="moduletable-nav">
							<nav class="navbar navbar-default">
								<div class="container-fluid">
									<!-- Collect the nav links, forms, and other content for toggling -->
									<div class="collapse navbar-collapse" id="navbar-collapse">
										<ul class="nav navbar-nav">
											<li><a href="/admin/">Главная М</a></li>
											<li><a href="/tc/search_order-1/">Поиск</a></li>
											<li><a href="/tc/">Туристы</a></li>
											<li><a href="/tc/viewTur-1/">Туры</a></li>
											<li><a href="/tc/viewStoryList-1/">Программы</a></li>
											<li><a href="/tc/viewTurList-1/">Списки заказов</a></li>
											<li><a href="/tc/dobList-1/">Дни рожденья</a></li>
										</ul>
										<script>
											var now_path = window.location.pathname;
											$('ul li a[href="'+now_path+'"]').parent().addClass('active');
										</script>
									</div>
								</div>
							</nav>
						</div>
					</div>
				</div>
			</div>
			<div class="logoheader">
				<h5 id="logo">
					<a href="/">
						<img src="./images/logo.png" alt="Logo"/>
					</a>
					<span class="header1">БАЛТИК ЛАЙНС ТУР </span>
				</h5>
			</div>
			<div class="moduletable_LoginForm">
				<xsl:apply-templates select="//page/body/module[@name = 'CurentUser']/container[@module = 'login']"/>
			</div>
		</div>
		<div id="loading2" style="display:none;"><div class="loading-block"><p class="title" style="text-align:center;">Пожалуйста, подождите...<br/><img src="/images/anim_load.gif" /></p></div></div>
	</xsl:template>
	<xsl:template name="archive">
		<xsl:call-template name="numbers">
			<xsl:with-param name="href" select="@module"/>
			<xsl:with-param name="count" select="@count"/>
			<xsl:with-param name="size" select="@size"/>
			<xsl:with-param name="current" select="@curPage"/>
		</xsl:call-template>
	</xsl:template>
	<xsl:template name="numbers">
		<xsl:param name="href"/>
		<xsl:param name="count"/>
		<xsl:param name="size"/>
		<xsl:param name="current"/>
		<div class="archive">Архив: 
      <xsl:call-template name="number">
				<xsl:with-param name="href" select="$href"/>
				<xsl:with-param name="max" select="ceiling($count div $size)"/>
				<xsl:with-param name="current" select="$current"/>
				<xsl:with-param name="number" select="1"/>
			</xsl:call-template>
		</div>
	</xsl:template>
	<xsl:template name="number">
		<xsl:param name="href"/>
		<xsl:param name="max"/>
		<xsl:param name="current"/>
		<xsl:param name="number"/>
		<xsl:choose>
			<xsl:when test="$current != $number">
				<a href="http://{//page/@host}/{$href}/page-{$number}">
					<xsl:value-of select="$number"/>
				</a>
			</xsl:when>
			<xsl:otherwise>
				<b>[<xsl:value-of select="$number"/>]</b>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="$number &lt; $max">
			<xsl:text disable-output-escaping="yes">&amp;nbsp;:&amp;nbsp;</xsl:text>
			<xsl:call-template name="number">
				<xsl:with-param name="href" select="$href"/>
				<xsl:with-param name="max" select="$max"/>
				<xsl:with-param name="current" select="$current"/>
				<xsl:with-param name="number" select="$number + 1"/>
			</xsl:call-template>
		</xsl:if>
	</xsl:template>
	<xsl:template name="userInfo">
		<xsl:if test="//page/logined = 1">
		test
		</xsl:if>
	</xsl:template>
	<xsl:template name="linkback">
		<p align="right">
			<a href="javascript:history.back(-1);">назад</a>
		</p>
	</xsl:template>
	<xsl:template name="confirmMsg">
		<xsl:param name="message"/>
		<script type="javascript" language="javascript">
			return confirm('<xsl:value-of select="$message"/>');
		</script>
	</xsl:template>
</xsl:stylesheet>
