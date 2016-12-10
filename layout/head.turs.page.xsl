<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/TR/xhtml1/strict">
	<xsl:template name="turs_head">
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
			<base href="."/>
			<title>Балтик Лайнс Тур</title>
			<link href="./images/favicon.ico" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
			<link rel="stylesheet" href="./css/facebox.css" type="text/css"/>
			<link rel="stylesheet" href="./css/camera.css" type="text/css"/>
			<link rel="stylesheet" href="./css/jquery-ui.css" type="text/css"/>
			<link rel="stylesheet" href="./css/style.css?v3" type="text/css"/>
			<link rel="stylesheet" href="./css/custom.css" type="text/css"/>
			<link rel="stylesheet" href="./css/camera.css" type="text/css"/>
			<link rel="stylesheet" href="./css/stylesheet.css" type="text/css"/>
			<link rel="stylesheet" href="./css/system.css" type="text/css"/>
			<link rel="stylesheet" href="./css/position.css" type="text/css" media="screen,projection"/>
			<link rel="stylesheet" href="./css/layout.css" type="text/css" media="screen,projection"/>
			<link rel="stylesheet" href="./css/print.css" type="text/css" media="Print"/>
			<link rel="stylesheet" href="./css/virtuemart.css?v2" type="text/css"/>
			<link rel="stylesheet" href="./css/products.css" type="text/css"/>
			<link rel="stylesheet" href="./css/personal.css" type="text/css"/>
			<link rel="stylesheet" href="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.css" type="text/css"/>
			<link rel="stylesheet" href="./css/bootstrap.min.css" type="text/css"/>
			<link rel="stylesheet" href="/css/bootstrap-slider.css" type="text/css"/>
			<!--<link rel="stylesheet" href="./css/flatui.css" type="text/css"/>-->
			<script src="./js/jquery.min.js" type="text/javascript"/>
			<script src="./js/jquery-ui.min.js" type="text/javascript"/>
			<script src="./js/bootstrap.min.js" type="text/javascript"/>
			<script src="./js/bootbox.min.js" type="text/javascript"/>
			<script src="./js/jquery.multiselect.min.js?v1" type="text/javascript"/>
			<script src="./js/jquery.maskinput.min.js" type="text/javascript"/>
			<script src="./js/camera.min.js" type="text/javascript"/>
			<script type="text/javascript" src="./js/ready.js?v1"/>
			<script type="text/javascript" src="./js/script.js?v1"/>
			<script type="text/javascript" src="/callme/js/callme.js"/>
			<script type="text/javascript" src="/js/bootstrap-slider.js"/>
		<script type="text/javascript" src="//cdn.datatables.net/1.10.4/js/jquery.dataTables.min.js"/>
		<script type="text/javascript" src="//cdn.datatables.net/plug-ins/3cfcc339e89/integration/bootstrap/3/dataTables.bootstrap.js"/>
			<script src="/ckeditor/ckeditor.js?v1"/>
			<script> 
				var roxyFileman = '/fileman/index.html';
                //1
				$(function(){
					if ($('#edit_content').length){CKEDITOR.replace( 'edit_content',{filebrowserBrowseUrl:roxyFileman,filebrowserUploadUrl:roxyFileman,filebrowserImageBrowseUrl:roxyFileman+'?type=image',filebrowserImageUploadUrl:roxyFileman+'?type=image'});}
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
	<xsl:template name="turs_headWrap">
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
											<li>
												<a href="/">Главная</a>
											</li>
											<li class="item-207">
												<a href="/pages/view-28/">О нас</a>
											</li>
											<li>
												<a href="/pages/view-49/">
												<xsl:if test="//page/@new_page = 1"><xsl:attribute name="class">new_site</xsl:attribute></xsl:if>Акции</a>
											</li>
											<li>
												<a href="/pages/view-52/">Услуги</a>
											</li>
											<li>
												<a href="/info/">
													<span class="glyphicon glyphicon-info-sign" aria-hidden="true" style="color:rgb(91,192,222);"/> Туристу</a>
											</li>
											<li>
													<a href="http://school.bltur.ru/" style="font-weight:bold;">Школьная страница</a>
											</li>
											<li>
												<a href="/pages/view-30/">Агентам</a>
											</li>
											<li>
												<a href="http://balticlines.ru/">Заказ автобусов</a>
											</li>
											<li>
												<a href="/pages/view-29/">Контакты</a>
											</li>
										</ul>
										<script>
											var now_path = window.location.pathname;
											$('ul li a[href="'+now_path+'"]').parent().addClass('active');
										</script>
										<div class="moduletable_LoginForm navbar-form navbar-right">
											<xsl:apply-templates select="//page/body/module[@name = 'CurentUser']/container[@module = 'login']"/>
											<!--				<div xmlns="" class="form"><div class="poping_links"><a href="/admin/" style="padding-right: 0px;">Менеджерам</a></div></div>-->
										</div>
										<!--<form id="search_order" class="navbar-form navbar-right" name="search_order" method="post" action="/turs/search_order-1/">
											<div class="row">
												<div class="col-xs-8 col-md-8">
													<input id="order_number" class="form-control" type="text" name="order_number" onchange="" size="15" placeholder="Номер заказа"/>
												</div>
												<div class="col-xs-4 col-md-4">
													<input class="btn  btn-primary" type="submit" value="Найти"/>
												</div>
											</div>
										</form>-->
									</div>
									<!-- /.navbar-collapse -->
								</div>
								<!-- /.container-fluid -->
							</nav>
						</div>
					</div>
				</div>
			</div>
			<div class="phoneheader">
				<span class="phone" style="">
					<ins/>8 812 715-06-11</span>
				<span class="phone" style="">
					<ins/>8 812 383-77-73</span>
				<span class="address" style="">
					<ins/>
					<a href="/pages/view-29/">м.Невский Проспект, <nobr>канал Грибоедова д.3, офис 321</nobr>
					</a>
				</span>
			</div>
			<div class="logoheader">
				<h5 id="logo">
					<a href="/">
						<img src="./images/logo.png" alt="Logo"/>
					</a>
					<span class="header1">БАЛТИК ЛАЙНС ТУР </span>
				</h5>
			</div>
		</div>
		<div id="loading2" style="display:none;">
			<div class="loading-block">
				<p class="title" style="text-align:center;">Пожалуйста, подождите...<br/>
					<img src="/images/anim_load.gif"/>
				</p>
			</div>
		</div>
	</xsl:template>
</xsl:stylesheet>
