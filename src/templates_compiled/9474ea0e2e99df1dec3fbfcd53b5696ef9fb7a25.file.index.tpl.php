<?php /* Smarty version Smarty-3.0.5, created on 2017-06-05 09:25:49
         compiled from "/var/www/html/sms-api-admin/src/templates/index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13874777375935239d2ab261-44286505%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9474ea0e2e99df1dec3fbfcd53b5696ef9fb7a25' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/index.tpl',
      1 => 1496388265,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13874777375935239d2ab261-44286505',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<<?php ?>?xml version="1.0" encoding="UTF-8"?>
<!--
Copyright(c) 2010 1rstWAP. All rights reserved.
-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?php echo $_smarty_tpl->getVariable('siteTitle')->value;?>
</title>
                <link href="js/select2/css/select2.css" type="text/css" rel="stylesheet" />
		<link href="skin/style.css" type="text/css" rel="stylesheet" />
                <link href="js/datatable/jquery.dataTables.css" type="text/css" rel="stylesheet"/>
		<link href="skin/jquery.ui/jquery-ui.css" type="text/css" rel="stylesheet" />
                <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script> 
		<script type="text/javascript" src="js/ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ui/i18n/jquery.ui.datepicker-en-GB.min.js"></script>
		<script type="text/javascript" src="js/firstwap/firstwap.js"></script>
		<script type="text/javascript" src="js/ui.hourglass/jquery.ui.hourglass.min.js"></script>
		<script type="text/javascript" src="js/app/app.js"></script>
		<script type="text/javascript" src="skin/skin.js"></script>
                <script type="text/javascript" src="js/select2/js/select2.min.js"></script>
                <script type="text/javascript" src="js/jquery.form-validator.min.js"></script>
		<script type="text/javascript" src="js/jquery.fileDownload.js"></script>
        <script type="text/javascript" src="js/datatable/jquery.dataTables.min.js"></script>
            
		<script type="text/javascript">
		//<![CDATA[
                $(document).ready(function() {
                    $app.ready(function($app) {
                            
			<?php if ($_smarty_tpl->getVariable('isLogin')->value){?>
				$app.welcome();
			<?php }?>
            
			});
		});
		//]]>
		</script>
            
    </head>
    <body>
		<div id="container">
			<div id="pageHeader">
				<div id="siteTitle"><?php echo $_smarty_tpl->getVariable('siteTitle')->value;?>
</div>
			</div>
			<div id="navPanel">
				<div id="welcomeMessage" class="panel"></div>
				<div id="menuPanel" class="panel"></div>
			</div>
			<div id="titlePanel" class="panel"></div>
			<div id="contentPanel">
			<?php if (!$_smarty_tpl->getVariable('isLogin')->value){?>
				<?php $_template = new Smarty_Internal_Template("login.form.tpl", $_smarty_tpl->smarty, $_smarty_tpl, $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null);
 echo $_template->getRenderedTemplate();?><?php $_template->updateParentVariables(0);?><?php unset($_template);?>
			<?php }?>
			</div>
			<div id="pageFooter">
				<div id="siteCopyright">&copy; 2010 1rstWAP. All rights reserved.</div>
			</div>
		</div>
    </body>
</html>