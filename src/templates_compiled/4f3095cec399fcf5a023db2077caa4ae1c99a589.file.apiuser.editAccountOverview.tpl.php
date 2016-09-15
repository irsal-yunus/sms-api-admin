<?php /* Smarty version Smarty-3.0.5, created on 2016-09-15 02:55:06
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.editAccountOverview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2810945257da0d8ad484b3-84464332%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f3095cec399fcf5a023db2077caa4ae1c99a589' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.editAccountOverview.tpl',
      1 => 1473907559,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2810945257da0d8ad484b3-84464332',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<fieldset> 
	<dl class="admin-definitions">
		<dt>Username</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['userName'];?>
</dd>
		<dt>Client Company</dt><dd><a href="#" onclick="$app.module('client').viewClient(<?php echo $_smarty_tpl->getVariable('details')->value['clientID'];?>
)"><?php echo $_smarty_tpl->getVariable('details')->value['clientCompanyName'];?>
</a></dd>
		<dt>Cobrander ID (User)</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['cobranderID'];?>
</dd>
		<dt>Status</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['statusName'];?>
</dd>
		<dt>Status Delivery</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['statusDeliveryStatusName'];?>
</dd>
		<dt>Delivery URL</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['statusDeliveryUrl'];?>
</dd>
		<dt>Delivery Failed</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['statusDeliveryUrlInvalidCount'];?>
 time(s)</dd>
		<dt>Delivery Retry</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['statusDeliveryUrlLastRetry'];?>
</dd>
		<dt>Reply Blacklist</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['replyBlacklistStatusName'];?>
</dd>
		<dt>Postpaid</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['isPostpaidStatusName'];?>
</dd>
		<dt>Last Access</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['lastAccess'];?>
</dd>
		<dt>Created By</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['createdByName'];?>
</dd>
		<dt>Created On</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['createdTimestamp'];?>
</dd>
		<dt>Updated By</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['updatedByName'];?>
</dd>
		<dt>Updated On</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['updatedTimestamp'];?>
</dd>

	</dl>
</fieldset>
<fieldset class="form-fieldset-submission">
	<a href="#" title="View Client" class="form-button" onclick="$app.module('client').viewClient(<?php echo $_smarty_tpl->getVariable('details')->value['clientID'];?>
);">
		<img src="skin/images/icon-client.png" class="form-button-image" alt="" />
		<span class="form-button-text">View Client</span>
	</a>
	<a href="#" title="Edit User" class="form-button" onclick="$app.module('apiuser').editAccountDetails(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
);">
		<img title="Register" src="skin/images/icon-edit.png" class="form-button-image" alt="" />
		<span class="form-button-text">Change Details</span>
	</a>
	<?php ob_start();?><?php echo $_smarty_tpl->getVariable('details')->value['active'];?>
<?php $_tmp1=ob_get_clean();?><?php if ($_tmp1){?>
	<a href="#" class="form-button" onclick="$app.module('apiuser').deactivateUser(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
);">
		<img title="Deactivate" src="skin/images/icon-disable.png" class="form-button-image" alt="" />
		<span class="form-button-text">Deactivate</span>
	</a>
	<?php }else{ ?>
	<a href="#" class="form-button" onclick="$app.module('apiuser').activateUser(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
);">
		<img title="Activate" src="skin/images/icon-enable.png" class="form-button-image" alt="" />
		<span class="form-button-text">Activate</span>
	</a>
	<?php }?>
	<a href="#" class="form-button" onclick="$app.module('apiuser').changePassword(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
);">
		<img title="Change Password" src="skin/images/icon-password.png" class="form-button-image" alt="" />
		<span class="form-button-text">Change Password</span>
	</a>
</fieldset>