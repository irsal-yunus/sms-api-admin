<?php /* Smarty version Smarty-3.0.5, created on 2016-08-24 08:36:09
         compiled from "/www/web/sms-api-admin/src/templates/menu.forUser.tpl" */ ?>
<?php /*%%SmartyHeaderCode:164485988157bd5c792c21c7-58812809%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6be2a231da83ffdb78e96a2a2fff5d860c58ec4b' => 
    array (
      0 => '/www/web/sms-api-admin/src/templates/menu.forUser.tpl',
      1 => 1472027728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '164485988157bd5c792c21c7-58812809',
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