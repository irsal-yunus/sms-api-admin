<?php /* Smarty version Smarty-3.0.5, created on 2016-09-15 02:53:26
         compiled from "/var/www/html/sms-api-admin/src/templates/menu.forUser.tpl" */ ?>
<?php /*%%SmartyHeaderCode:25715766857da0d26ade195-49222450%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f58c6218d8dce921cbc3e8291f8f1988f24e845' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/menu.forUser.tpl',
      1 => 1473907559,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '25715766857da0d26ade195-49222450',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<div class="panel-body">
	<div class="panel-content">
		<ul class="menu menu-set">
			<li class="menu-item"><a href="#" onclick="$app.welcome();"><img class="icon-image" alt="" src="skin/images/icon-home.png" />Home</a></li>
			<li class="menu-item"><a href="#" onclick="$app.module('client').showClientList();"><img class="icon-image" alt="" src="skin/images/icon-client.png" />Client Management</a></li>
			<li class="menu-item"><a href="#" onclick="$app.module('apiuser').showUserList();"><img class="icon-image" alt="" src="skin/images/icon-user.png" />User Management</a></li>
			<li class="menu-item"><a href="#" onclick="$app.logout();"><img class="icon-image" alt="" src="skin/images/icon-logout.png" />Logout</a></li>
		</ul>
	</div>
</div>