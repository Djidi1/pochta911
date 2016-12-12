<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:template match="container[@module = 'adminmain']">
		<div id="cpanel">
			<table class="adminform" align="center" style="margin: 0 auto;">
				<tbody>
					<tr>
						<td valign="top">
							<h2>Контент</h2>
									<a class="btn btn-default" href="http://{//page/@host}/pages/">
										<span class="glyphicon glyphicon-file"> </span>
										<span> Страницы</span>
									</a>
									<a class="btn btn-default" href="/news/newsadmin-1/">
										<span class="glyphicon glyphicon-bullhorn"> </span>
										<span> Новости</span>
									</a>
							<!--<div style="float: left;">-->
								<!--<div class="icon">-->
									<!--<a href="http://{//page/@host}/email/">-->
										<!--<img src="/images/icon-48-mail.png" alt="Email рассылка"/>-->
										<!--<span>Рассылка</span>-->
									<!--</a>-->
								<!--</div>-->
							<!--</div>-->
							<!--<div style="float: left;">-->
								<!--<div class="icon">-->
									<!--<a href="http://{//page/@host}/email/viewlist-1">-->
										<!--<img src="/images/icon-48-mail.png" alt="Email подписчики"/>-->
										<!--<span>Подписчики</span>-->
									<!--</a>-->
								<!--</div>-->
							<!--</div>-->
						</td>
					</tr>
					<tr>
						<td valign="top">
							<h2>Настройки</h2>
									<a class="btn btn-default" href="http://{//page/@host}/admin/userList-1/">
										<span class="glyphicon glyphicon-user"> </span>
										<span> Пользователи</span>
									</a>
									<a class="btn btn-default" href="http://{//page/@host}/admin/groupList-1/">
										<i class="fa fa-users" aria-hidden="true"> </i>
										<span> Группы</span>
									</a>
						</td>
					</tr>
				</tbody>
			</table>
			<!--<xsl:call-template name="linkback"/>-->
		</div>
	</xsl:template>
</xsl:stylesheet>
