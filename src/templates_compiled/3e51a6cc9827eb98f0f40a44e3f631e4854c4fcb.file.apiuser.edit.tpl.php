<?php /* Smarty version Smarty-3.0.5, created on 2016-08-26 09:49:43
         compiled from "/www/web/sms-api-admin/src/templates/apiuser.edit.tpl" */ ?>
<?php /*%%SmartyHeaderCode:137303545557c010b780c5c3-61727834%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3e51a6cc9827eb98f0f40a44e3f631e4854c4fcb' => 
    array (
      0 => '/www/web/sms-api-admin/src/templates/apiuser.edit.tpl',
      1 => 1472027728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '137303545557c010b780c5c3-61727834',
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