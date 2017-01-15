<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:template name="main_head">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
            <base href="."/>
            <title>Доставка цветов</title>
            <link href="/images/favicon.png" rel="shortcut icon" type="image/vnd.microsoft.icon"/>
            <link rel="stylesheet" href="/css/camera.css"/>
            <link rel="stylesheet" href="/css/style.css"/>
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
            <script src="/js/ready.js?v1"/>
            <script src="/js/common.js?v1"/>
            <script src="/js/script.js?v1"/>
            <script src="//cdn.ckeditor.com/4.6.1/full/ckeditor.js"/>
            <script src="//cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"/>
            <script src="//cdn.datatables.net/1.10.13/js/dataTables.bootstrap.min.js"/>
            <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&amp;coordorder=longlat"/>
            <script src="/js/ymap.js?v1"/>
            <script>
            <!--var roxyFileman = '/fileman/index.html';-->
            <!--//1-->
            $(function(){
            if ($('#edit_content').length){CKEDITOR.replace( 'edit_content');}
            <!--if ($('#edit_content').length){CKEDITOR.replace( 'edit_content',{filebrowserBrowseUrl:roxyFileman,filebrowserUploadUrl:roxyFileman,filebrowserImageBrowseUrl:roxyFileman+'?type=image',filebrowserImageUploadUrl:roxyFileman+'?type=image'});}-->
            });
            </script>
            <!--[if IE 8]>
    <link href="./css/ie8only.css" rel="stylesheet" />
<![endif]-->
            <!--[if lt IE 8]>
    <div style=' clear: both; text-align:center; position: relative; z-index:9999;'>
        <a href="http://www.microsoft.com/windows/internet-explorer/default.aspx?ocid=ie6_countdown_bannercode"><img src="http://www.theie6countdown.com/images/upgrade.jpg" border="0"  alt="" /></a>
    </div>
<![endif]-->
            <!--[if lt IE 9]>
<script src="./js/html5.js"></script>
<![endif]-->
        </head>
    </xsl:template>
    <xsl:template name="main_headWrap">
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
                        <a class="navbar-brand" href="/title/" title="Доставка цветов">
                            <img src="./images/logo.png" alt="Logo"/>
                            <span class="header1" style="display:none;">Доставка цветов</span>
                        </a>
                    </div>

                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-flag" aria-hidden="true"> </i> Заказы</a>
                                <ul class="dropdown-menu">
                                    <li><a href="/orders/">Список заказов</a></li>
                                    <li><a href="#" onclick="open_bootbox_dialog('/orders/order-0/')">Новый заказ</a></li>
                                    <li><a href="#" onclick="popup_excel('orders/excel-1')">Выгрузить в Excel</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="/orders/LogistList-1"><i class="fa fa-bus" aria-hidden="true"> </i> Логист</a>
                            </li>
                            <li>
                                <a href="/admin/carsList-1/"><i class="fa fa-car" aria-hidden="true"> </i> Автоштат</a>
                            </li>
                            <li>
                                <a href="/admin/userList-1/"><span class="glyphicon glyphicon-user"> </span> Клиенты</a>
                            </li>
                        </ul>
                        <script>
                            var now_path = window.location.pathname;
                            $('ul li a[href="'+now_path+'"]').parent().addClass('active');
                        </script>

                        <!--<form class="navbar-form navbar-left">-->
                            <!--<div class="form-group">-->
                                <!--<input type="text" class="form-control" placeholder="Search"/>-->
                            <!--</div>-->
                            <!--<button type="submit" class="btn btn-default">Submit</button>-->
                        <!--</form>-->

                        <div class="moduletable_LoginForm navbar-right">
                            <xsl:apply-templates
                                    select="//page/body/module[@name = 'CurentUser']/container[@module = 'login' and position() = 1]"/>
                            <!--				<div xmlns="" class="form"><div class="poping_links"><a href="/admin/" style="padding-right: 0px;">Менеджерам</a></div></div>-->
                        </div>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        </div>
        <div id="loading2" style="display:none;">
            <div class="loading-block">
                <p class="title" style="text-align:center;">Пожалуйста, подождите...
                    <br/>
                    <img src="/images/anim_load.gif"/>
                </p>
            </div>
        </div>
    </xsl:template>
    <xsl:template name="bottom_block">
        <div id="foot">
            <p id="back-top" style="display: none;">
                <a href="#top">
                    <span/>
                </a>
            </p>
            <div class="well wrapper">
                <div class="moduletable">
                    <ul class="bottom-menu navbar-nav">
                        <li>
                            <a class="btn btn-default btn-xs" href="/pages/">
                                <span class="glyphicon glyphicon-file"> </span> Страницы
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-default btn-xs" href="/news/newsadmin-1/">
                                <span class="glyphicon glyphicon-bullhorn"> </span> Новости
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-default btn-xs" href="/admin/groupList-1/">
                                <i class="fa fa-users" aria-hidden="true"> </i> Группы
                            </a>
                        </li>
                        <li>
                            <a class="btn btn-default btn-xs" href="/admin/getTelegramUpdates-1/">
                                <i class="fa fa-telegram" aria-hidden="true"> </i> Телеграмм
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="footerText">
                    <div class="footer1">Copyright © <xsl:value-of select="//@year"/> Доставка
                        цветов. Все права защищены.
                    </div>
                </div>
            </div>
        </div>
    </xsl:template>
</xsl:stylesheet>
