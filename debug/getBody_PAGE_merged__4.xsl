<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns="http://www.w3.org/TR/xhtml1/strict" version="1.0">
	<xsl:output method="html" encoding="utf-8" indent="yes"/>
	<xsl:include href="head.turs.page.xsl"/>
	<xsl:template match="/">
		<xsl:variable name="content">
			<xsl:value-of select="//page/body/@contentContainer"/>
		</xsl:variable>
		<xsl:text disable-output-escaping="yes">&lt;!DOCTYPE html&gt;</xsl:text>
		<html>
			<xsl:call-template name="turs_head"/>
			<body>
				<xsl:attribute name="id"><xsl:value-of select="//page/@name"/></xsl:attribute>
				<div class="body-top">
				<div style="position: fixed;left:-70px;bottom: 100px;"><input type="button" id="viewform2" class="btn btn-info btn-xs rotate" style="opacity: 1;" value="&#x41F;&#x43E;&#x434;&#x43F;&#x438;&#x441;&#x430;&#x442;&#x44C;&#x441;&#x44F; &#x43D;&#x430; &#x440;&#x430;&#x441;&#x441;&#x44B;&#x43B;&#x43A;&#x443;">
					<xsl:attribute name="onclick"><![CDATA[var data = $('#newsletter-signup').parent().html(); bootbox.dialog({ message: data, title: 'Подписаться'});]]></xsl:attribute>
				</input>
				</div>
				<div class="row" style="display:none;">
<form id="newsletter-signup" action="/turs/signup-1/" method="post" class="form-inline alert alert-info" role="form">
    <fieldset>
        <label>&#x41F;&#x43E;&#x434;&#x43F;&#x438;&#x448;&#x438;&#x442;&#x435;&#x441;&#x44C; &#x43D;&#x430; &#x440;&#x430;&#x441;&#x441;&#x44B;&#x43B;&#x43A;&#x443; &#x43F;&#x43E; e-mail &#x43D;&#x43E;&#x432;&#x43E;&#x441;&#x442;&#x435;&#x439;, &#x43F;&#x440;&#x435;&#x434;&#x43B;&#x43E;&#x436;&#x435;&#x43D;&#x438;&#x439; &#x438; &#x441;&#x43E;&#x431;&#x44B;&#x442;&#x438;&#x439;!</label><br/>
        <p id="signup-response" class="text-primary"/>
        <div class="row">
			<div class="col-xs-6 col-md-4">
				<input type="text" name="signup-name" placeholder="&#x412;&#x430;&#x448;&#x435; &#x438;&#x43C;&#x44F;"/>
			</div>
			<div class="col-xs-6 col-md-4">
				<input type="text" name="signup-email" id="signup-email" placeholder="Email"/>
			</div>
			<div class="col-xs-6 col-md-4">
				<input type="submit" id="signup-button" value="&#x41F;&#x43E;&#x434;&#x43F;&#x438;&#x441;&#x430;&#x442;&#x44C;&#x441;&#x44F;" class="btn btn-success btn-sm"/>
			</div>
		</div>
    </fieldset>
</form>
</div>
					<div class="main" style="width:90%;">
						<xsl:call-template name="turs_headWrap"/>
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
							<div class="wrapper">
								<div class="moduletable">
									<ul class="menu">
										<li class="item-207">
											<a href="/pages/view-28/">&#x41E; &#x43A;&#x43E;&#x43C;&#x43F;&#x430;&#x43D;&#x438;&#x438;</a>
										</li>
										<li class="item-471">
											<a href="/pages/view-49/">&#x410;&#x43A;&#x446;&#x438;&#x438;</a>
										</li>
										<li class="item-472">
											<a href="#">&#x422;&#x443;&#x440;&#x438;&#x441;&#x442;&#x443;</a>
										</li>
										<li class="item-470">
											<a href="/pages/view-29/">&#x41A;&#x43E;&#x43D;&#x442;&#x430;&#x43A;&#x442;&#x44B;</a>
										</li>
									</ul>
								</div>
							</div>
							<div class="space">
								<div class="wrapper">
									<div class="footerText">
										<div class="footer1">Copyright &#xA9; 2013-<xsl:value-of select="//@year"/> &#x411;&#x410;&#x41B;&#x422;&#x418;&#x41A; &#x41B;&#x410;&#x419;&#x41D;&#x421; &#x422;&#x423;&#x420;. &#x412;&#x441;&#x435; &#x43F;&#x440;&#x430;&#x432;&#x430; &#x437;&#x430;&#x449;&#x438;&#x449;&#x435;&#x43D;&#x44B;.</div>
									</div>
									<div style="float: right;margin-top: 17px;">
									<!-- Yandex.Metrika informer -->
<a href="https://metrika.yandex.ru/stat/?id=25076081&amp;from=informer" target="_blank" rel="nofollow"><img src="//bs.yandex.ru/informer/25076081/3_0_FFFFFFFF_FFFFFFFF_0_pageviews" style="width:88px; height:31px; border:0;" alt="&#x42F;&#x43D;&#x434;&#x435;&#x43A;&#x441;.&#x41C;&#x435;&#x442;&#x440;&#x438;&#x43A;&#x430;" title="&#x42F;&#x43D;&#x434;&#x435;&#x43A;&#x441;.&#x41C;&#x435;&#x442;&#x440;&#x438;&#x43A;&#x430;: &#x434;&#x430;&#x43D;&#x43D;&#x44B;&#x435; &#x437;&#x430; &#x441;&#x435;&#x433;&#x43E;&#x434;&#x43D;&#x44F; (&#x43F;&#x440;&#x43E;&#x441;&#x43C;&#x43E;&#x442;&#x440;&#x44B;, &#x432;&#x438;&#x437;&#x438;&#x442;&#x44B; &#x438; &#x443;&#x43D;&#x438;&#x43A;&#x430;&#x43B;&#x44C;&#x43D;&#x44B;&#x435; &#x43F;&#x43E;&#x441;&#x435;&#x442;&#x438;&#x442;&#x435;&#x43B;&#x438;)">
<xsl:attribute name="onclick"><![CDATA[try{Ya.Metrika.informer({i:this,id:25076081,lang:'ru'});return false}catch(e){}]]></xsl:attribute>
</img></a>
<!-- /Yandex.Metrika informer -->

<!-- Yandex.Metrika counter -->
<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter25076081 = new Ya.Metrika({id:25076081,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/25076081" style="position:absolute; left:-9999px;" alt=""/></div></noscript>
<!-- /Yandex.Metrika counter -->
</div>									
								</div>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>
	</xsl:template>
<xsl:include href="../layout/viewErrors.xsl"/><xsl:include href="../layout/news/news.viewlistadmin.xsl"/><xsl:include href="../layout/file.view.xsl"/></xsl:stylesheet>
