<?php /* Smarty version Smarty-3.0.5, created on 2016-08-25 03:45:48
         compiled from "/www/web/sms-api-admin/src/templates/error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:185214268257be69ec6fbab3-22238595%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ee06a278e2e6fbcba799ea8b14ed8413b01f8ec5' => 
    array (
      0 => '/www/web/sms-api-admin/src/templates/error.tpl',
      1 => 1472027728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '185214268257be69ec6fbab3-22238595',
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