<?php /* Smarty version Smarty-3.0.5, created on 2016-10-18 11:56:29
         compiled from "/var/www/html/sms-api-admin-61/src/templates/apiuser.edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:70654592558060ded0ab642-42693052%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dde2c596284d2f51979f0bca34cfe3775cb9a7c8' => 
    array (
      0 => '/var/www/html/sms-api-admin-61/src/templates/apiuser.edit.tpl',
      1 => 1476791134,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '70654592558060ded0ab642-42693052',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form id="apiuser-editform" class="admin-tabform" action="#" method="post" style="width: 100%;">
	<input type="hidden" value="<?php echo $_smarty_tpl->getVariable('userID')->value;?>
" />
	<div id="apiuser-editform-tabs" class="panel-tabs">
		<ul>
			<li><a href="<?php echo @SMSAPIADMIN_SERVICE_URL;?>
apiuser.editAccountOverview.php?userID=<?php echo $_smarty_tpl->getVariable('userID')->value;?>
"><img src="skin/images/icon-user.png" class="icon-image icon-size-small" alt="" /><span>Account</span></a></li>
			<li><a href="<?php echo @SMSAPIADMIN_SERVICE_URL;?>
apiuser.editSenders.php?userID=<?php echo $_smarty_tpl->getVariable('userID')->value;?>
"><img src="skin/images/icon-senderid.png" class="icon-image icon-size-small" alt="" /><span>Sender ID</span></a></li>
			<li><a href="<?php echo @SMSAPIADMIN_SERVICE_URL;?>
apiuser.editIP.php?userID=<?php echo $_smarty_tpl->getVariable('userID')->value;?>
"><img src="skin/images/icon-ip.png" class="icon-image icon-size-small" alt="" /><span>IP Restrictions</span></a></li>
			<li><a href="<?php echo @SMSAPIADMIN_SERVICE_URL;?>
apiuser.editVirtualNumbers.php?userID=<?php echo $_smarty_tpl->getVariable('userID')->value;?>
"><img src="skin/images/icon-virtualnumber.png" class="icon-image icon-size-small" alt="" /><span>Virtual Numbers</span></a></li>
		</ul>		
	</div><!-- END Tabs Container -->
</form>