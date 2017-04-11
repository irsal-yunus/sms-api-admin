<?php /* Smarty version Smarty-3.0.5, created on 2016-10-18 14:31:58
         compiled from "/var/www/html/sms-api-admin-61/src/templates/menu.forUser.tpl" */ ?>
<?php /*%%SmartyHeaderCode:191190570957fb9c476901a8-00924546%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2e70df7a2fec7efe4e2b218d04e2031ac33cffd5' => 
    array (
      0 => '/var/www/html/sms-api-admin-61/src/templates/menu.forUser.tpl',
      1 => 1476791134,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '191190570957fb9c476901a8-00924546',
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