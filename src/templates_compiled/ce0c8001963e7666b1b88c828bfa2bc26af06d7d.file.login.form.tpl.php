<?php /* Smarty version Smarty-3.0.5, created on 2016-10-10 13:46:34
         compiled from "/var/www/html/sms-api-admin-61/src/templates/login.form.tpl" */ ?>
<?php /*%%SmartyHeaderCode:158093427557fb9bbaf3efd2-69657305%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ce0c8001963e7666b1b88c828bfa2bc26af06d7d' => 
    array (
      0 => '/var/www/html/sms-api-admin-61/src/templates/login.form.tpl',
      1 => 1476098728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '158093427557fb9bbaf3efd2-69657305',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form action="redirect.php?url=index.php" class="admin-stdform" id="admin-login-form" method="post">
<div class="panel" style="width:27em;margin-top:2em;">
	<div class="panel-header"><img alt="" src="skin/images/icon-login.png" class="icon-image" /><span>Administrator Login</span></div>
	<div class="panel-body">
		<div class="panel-content">
			<fieldset class="float-centre" style="border:none;">
				<label class="form-flag-required flexible-width" style="width:6em;">Username</label><input type="text" maxlength="16" name="username"/><span class="ui-helper-clearfix">&nbsp;</span>
				<label class="form-flag-required flexible-width" style="width:6em;">Password</label><input type="password" name="password" /><span class="ui-helper-clearfix">&nbsp;</span>
			</fieldset>
			<fieldset class="form-fieldset-submission float-centre" style="padding-top:.5em;padding-bottom:.5em;">
				<a href="#" class="form-button"
						onclick="var userform=$('#admin-login-form')[0]; $app.login($('input[name=username]', userform).val(), $('input[name=password]', userform).val(), function(success){ if(success) { $(userform).submit(); return false; } }); return false;" >
					<span class="form-button-text">Login</span>
				</a>
			</fieldset>
		</div>
	</div>
</div>
</form>