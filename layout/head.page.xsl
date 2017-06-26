<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<!-- Это гостевой заголовок - ТИТУЛЬНАЯ СТРАНИЦА -->
	<xsl:template name="head">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
			<xsl:if test="//@fullscreen != 1">
				<meta name="viewport" content="width=device-width, initial-scale=1"/>
			</xsl:if>
			<base href="."/>
			<title>Доставка цветов</title>
			<link href="/images/favicon.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
			<link rel="stylesheet" href="/css/camera.css?v1.0"/>
			<link rel="stylesheet" href="/css/select2.css?v1.1"/>
			<link rel="stylesheet" href="/css/style.css?v2.11"/>
			<link rel="stylesheet" href="/css/font-awesome.min.css"/>
			<link rel="stylesheet" href="/css/print.css" media="Print"/>
			<link rel="stylesheet" href="/css/bootstrap.min.css"/>
			<link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css"/>
			<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css"/>
			<script src="/js/jquery.min.js"/>
			<script src="/js/jquery-ui.min.js"/>
			<script src="/js/bootstrap.min.js"/>
			<script src="/js/bootbox.min.js"/>
			<script src="/js/jquery.multiselect.min.js?v1"/>
			<script src="/js/jquery.maskinput.min.js"/>
			<script src="/js/moment.min.js"/>
			<script src="/js/moment.ru.js"/>
			<script src="/js/bootstrap-datetimepicker.js"/>
			<script src="/js/bootstrap-typeahead.min.js"/>
			<script src="/js/camera.min.js"/>
			<script src="/js/ready.js?v2.1"/>
            <script src="/js/common.js?v3.23"/>
			<script src="/js/script.js?v2.5"/>
            <script src="//cdn.ckeditor.com/4.6.1/full/ckeditor.js"/>
			<script src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"/>
			<script src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"/>
			<xsl:text disable-output-escaping="yes">
                <![CDATA[
            <script src="//maps.googleapis.com/maps/api/js?key=AIzaSyAnDrB-qO4i5uCua-4krGQsloWYJBRtgNU&libraries=places"></script>
                ]]>
            </xsl:text>
			<script src="/js/gmap.js?v2.21"/>
			<script type="text/javascript" src="/callme/js/callme.js"/>
		</head>
	</xsl:template>
	<xsl:template name="headWrap">
		<xsl:variable name="content">
			<xsl:value-of select="//page/body/@contentContainer"/>
		</xsl:variable>
		<div id="header">
			<nav class="navbar navbar-default">
				<div class="container-fluid">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
								data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"/>
							<span class="icon-bar"/>
							<span class="icon-bar"/>
						</button>
						<a class="navbar-brand" href="/" title="Доставка цветов">
							<img src="./images/logo.png?v2" alt="Logo"/>
							<span class="header1" style="display:none;">Доставка цветов</span>
						</a>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li>
								<a class="callme_viewform" href="#">Зарегистрироваться</a>
							</li>
							<li>
								<a href="/pages/view-49/">Условия сотрудничества</a>
							</li>
						</ul>
						<script>
							var now_path = window.location.pathname;
							$('ul li a[href="'+now_path+'"]').parent().addClass('active');
						</script>


						<div class="moduletable_LoginForm navbar-right">
							<xsl:apply-templates select="//page/body/module[@name = 'CurentUser']/container[@module = 'login']"/>
						</div>
						<div class="phone-in-header phone">
							<span class="city-code">(812)</span> 407-24-52
						</div>
					</div><!-- /.navbar-collapse -->
				</div><!-- /.container-fluid -->
			</nav>
			<div class="mobile-sub-menu">
				<div class="slogan">Логистика для цветочных магазинов</div>
				<div class="moduletable_LoginForm login-mobile">
					<xsl:apply-templates select="//page/body/module[@name = 'CurentUser']/container[@module = 'login']"/>
				</div>
				<div class="phone-in-header phone-mobile">
					<span class="city-code">(812)</span> 407-24-52
				</div>
			</div>
		</div>
		<div id="loading2" style="display:none;"><div class="loading-block"><p class="title" style="text-align:center;">Пожалуйста, подождите...<br/><img src="/images/anim_load.gif" /></p></div></div>
	</xsl:template>
</xsl:stylesheet>
