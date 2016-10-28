<?php /* Smarty version Smarty-3.0.5, created on 2016-10-20 02:58:51
         compiled from "/var/www/html/sms-api-admin-61/src/templates/error.tpl" */ ?>
<?php /*%%SmartyHeaderCode:41404103557fc6840991e13-82174956%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cbf1583fb674e9de398530a0690c73786f356522' => 
    array (
      0 => '/var/www/html/sms-api-admin-61/src/templates/error.tpl',
      1 => 1476791134,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '41404103557fc6840991e13-82174956',
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