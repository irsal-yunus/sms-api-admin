{literal}<?xml version="1.0" encoding="UTF-8"?>{/literal}
<!--
Copyright(c) 2010 1rstWAP. All rights reserved.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>{$siteTitle}</title>
		<link href="skin/style.css" type="text/css" rel="stylesheet" />
		<link href="skin/jquery.ui/jquery-ui.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript" src="js/jquery.min.js"></script>
		<script type="text/javascript" src="js/ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ui/i18n/jquery.ui.datepicker-en-GB.min.js"></script>
		<script type="text/javascript" src="js/firstwap/firstwap.js"></script>
		<script type="text/javascript" src="js/ui.hourglass/jquery.ui.hourglass.min.js"></script>
		<script type="text/javascript" src="js/app/app.js"></script>
		<script type="text/javascript" src="skin/skin.js"></script>
		<script type="text/javascript">
		//<![CDATA[
		$(document).ready(function(){
			$app.ready(function($app){
			{if $isLogin}
				$app.welcome();
			{/if}
			});
		});
		//]]>
		</script>
    </head>
    <body>
		<div id="container">
			<div id="pageHeader">
				<div id="siteTitle">{$siteTitle}</div>
			</div>
			<div id="navPanel">
				<div id="welcomeMessage" class="panel"></div>
				<div id="menuPanel" class="panel"></div>
			</div>
			<div id="titlePanel" class="panel"></div>
			<div id="contentPanel">
			{if !$isLogin}
				{include "login.form.tpl"}
			{/if}
			</div>
			<div id="pageFooter">
				<div id="siteCopyright">&copy; 2010 1rstWAP. All rights reserved.</div>
			</div>
		</div>
    </body>
</html>