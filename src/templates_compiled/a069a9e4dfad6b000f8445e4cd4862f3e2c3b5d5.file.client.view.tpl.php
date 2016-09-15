<?php /* Smarty version Smarty-3.0.5, created on 2016-09-15 02:54:30
         compiled from "/var/www/html/sms-api-admin/src/templates/client.view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:191911318657da0d669ea218-54418070%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a069a9e4dfad6b000f8445e4cd4862f3e2c3b5d5' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/client.view.tpl',
      1 => 1473907559,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '191911318657da0d669ea218-54418070',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form id="clients-view-form" class="admin-tabform" action="#">
	<div class="panel">
		<div class="panel-header"><img src="skin/images/icon-client.png" class="icon-image" alt="" /><span>Client Details</span></div>
		<div class="panel-body">
			<div class="panel-content">
				<fieldset class="float-centre">
					<dl class="admin-definitions">
						<dt>Company Name</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['companyName'];?>
</dd>
						<dt>Company URL</dt><dd><a href="<?php echo $_smarty_tpl->getVariable('client')->value['companyUrl'];?>
"><?php echo $_smarty_tpl->getVariable('client')->value['companyUrl'];?>
</a></dd>
						<dt>Country</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['countryName'];?>
</dd>
						<dt>Contact Name</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['contactName'];?>
</dd>
						<dt>Contact Email</dt><dd><a href="mailto:<?php echo $_smarty_tpl->getVariable('clientcontactEmail')->value;?>
"><?php echo $_smarty_tpl->getVariable('client')->value['contactEmail'];?>
</a></dd>
						<dt>Contact Phone</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['contactPhone'];?>
</dd>
						<dt>Created By</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['createdByName'];?>
</dd>
						<dt>Created On</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['createdTimestamp'];?>
</dd>
						<dt>Updated By</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['updatedByName'];?>
</dd>
						<dt>Updated On</dt><dd><?php echo $_smarty_tpl->getVariable('client')->value['updatedTimestamp'];?>
</dd>
					</dl>
				</fieldset>
				<fieldset class="form-fieldset-submission float-centre">
					<a href="#" class="form-button" onclick="$app.module('client').manageUsers(<?php echo $_smarty_tpl->getVariable('client')->value['clientID'];?>
);">
						<img src="skin/images/icon-user.png" class="form-button-image" alt="" />
						<span class="form-button-text">API Accounts</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('client').editClient(<?php echo $_smarty_tpl->getVariable('client')->value['clientID'];?>
);">
						<img src="skin/images/icon-edit.png" class="form-button-image" alt="" />
						<span class="form-button-text">Edit</span>
					</a>
					<a href="#" class="form-button" onclick="$app.module('client').removeClient(<?php echo $_smarty_tpl->getVariable('client')->value['clientID'];?>
);">
						<img src="skin/images/icon-remove.png" class="form-button-image" alt="" />
						<span class="form-button-text">Delete</span>
					</a>
				</fieldset>
			</div>
		</div>
	</div>
</form>