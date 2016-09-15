<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 04:14:55
         compiled from "/var/www/html/sms-api-admin/src/templates/error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:111581405457d8cebf738055-05521713%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b6edde88889ef240bed1fb916b39e0da00580fba' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/error.tpl',
      1 => 1473742353,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '111581405457d8cebf738055-05521713',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="ui-widget">
	<div style="padding: .5em 0em;min-height: 2em;" class="ui-state-error ui-corner-all">
		<div style="width: 95%;margin: auto;"><span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span>
		<strong>Alert:</strong> <?php echo $_smarty_tpl->getVariable('message')->value;?>
</div>
	</div>
</div>