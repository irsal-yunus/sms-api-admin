<?php /* Smarty version Smarty-3.0.5, created on 2016-10-11 02:34:16
         compiled from "/var/www/html/sms-api-admin-61/src/templates/credit.manager.tpl" */ ?>
<?php /*%%SmartyHeaderCode:193044085157fc4fa84e9733-49421649%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '4f2394d99f425b0f9a6d64a5457c569822e9a369' => 
    array (
      0 => '/var/www/html/sms-api-admin-61/src/templates/credit.manager.tpl',
      1 => 1476098728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '193044085157fc4fa84e9733-49421649',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form class="admin-tabform" action="#">
	<div class="panel">
		<div class="panel-header"></div>
		<div class="panel-body">
			<div class="panel-content">
				<fieldset class="float-centre">
					<dl class="admin-definitions">
						<dt>User Name</dt><dd><?php echo $_smarty_tpl->getVariable('user')->value['userName'];?>
</dd>
						<dt>Client Company</dt><dd><?php echo $_smarty_tpl->getVariable('user')->value['clientCompanyName'];?>
</dd>
						<dt>Cobrander ID</dt><dd><?php echo $_smarty_tpl->getVariable('user')->value['cobranderID'];?>
</dd>
						<dt>Balance</dt><dd><?php echo number_format($_smarty_tpl->getVariable('user')->value['userCredit']);?>
</dd>
					</dl>
				</fieldset>
				<fieldset class="form-fieldset-submission float-centre">
					<a href="#" class="form-button" onclick="$app.module('credit').manageUserCredit(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
);">
						<img src="skin/images/icon-refresh.png" class="form-button-image" alt="" />
						<span class="form-button-text">Refresh</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('credit').purchase(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
);">
						<img src="skin/images/icon-topup.png" class="form-button-image" alt="" />
						<span class="form-button-text">Top Up</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('credit').deduct(<?php echo $_smarty_tpl->getVariable('userID')->value;?>
);">
						<img src="skin/images/icon-deduct.png" class="form-button-image" alt="" />
						<span class="form-button-text">Deduct</span>
					</a>
				</fieldset>
			</div>
		</div>
	</div>
	<div id="apiuser-editform-tabs" class="panel-tabs">
		<ul>
			<li><a href="<?php echo @SMSAPIADMIN_SERVICE_URL;?>
credit.history.php?userID=<?php echo $_smarty_tpl->getVariable('userID')->value;?>
"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Credit History</span></a></li>
		</ul>
	</div>
</form>