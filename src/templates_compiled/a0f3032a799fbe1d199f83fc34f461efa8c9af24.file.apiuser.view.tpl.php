<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 05:28:50
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.view.tpl" */ ?>
<?php /*%%SmartyHeaderCode:209682264757d8e01240ce56-32374258%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a0f3032a799fbe1d199f83fc34f461efa8c9af24' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.view.tpl',
      1 => 1473742354,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '209682264757d8e01240ce56-32374258',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_function_cycle')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/function.cycle.php';
?><form id="apiuser-viewform" class="admin-tabform" action="#" method="post" style="width: 100%;">
	 <input type="hidden" value="<?php echo $_smarty_tpl->getVariable('details')->value['userID'];?>
" />
	<div id="apiuser-viewform-tabs" class="panel-tabs">
		<ul>
			<li><a href="#apiuser-viewform-tab-account"><img src="skin/images/icon-user.png" class="icon-image icon-size-small" alt="" /><span>Account</span></a></li>
			<li><a href="#apiuser-viewform-tab-senderid"><img src="skin/images/icon-senderid.png" class="icon-image icon-size-small" alt="" /><span>Sender ID</span></a></li>
			<li><a href="#apiuser-viewform-tab-ip"><img src="skin/images/icon-ip.png" class="icon-image icon-size-small" alt="" /><span>IP Restrictions</span></a></li>
			<li><a href="#apiuser-viewform-tab-reply"><img src="skin/images/icon-virtualnumber.png" class="icon-image icon-size-small" alt="" /><span>Virtual Numbers</span></a></li>
		</ul>
		<div id="apiuser-viewform-tab-account">
			<dl class="admin-definitions">
				<dt>Username</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['userName'];?>
</dd>
				<dt>Client Company</dt><dd><a href="#"><?php echo $_smarty_tpl->getVariable('details')->value['clientCompanyName'];?>
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
				<dt>Is Postpaid</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['isPostpaidStatusName'];?>
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
			<fieldset class="form-fieldset-submission">
				<a href="#" title="View Client" class="form-button" onclick="$app.module('client').viewClient(<?php echo $_smarty_tpl->getVariable('details')->value['clientID'];?>
);;">
					<img src="skin/images/icon-client.png" class="form-button-image" alt="" />
					<span class="form-button-text">View Client</span>
				</a>
				<a href="#" title="Edit Mode" class="form-button" onclick="$app.module('apiuser').editUser(<?php echo $_smarty_tpl->getVariable('details')->value['userID'];?>
);">
					<img src="skin/images/icon-edit.png" class="form-button-image" alt="" />
					<span class="form-button-text">Edit Mode</span>
				</a>
			</fieldset>
		</div><!-- END Account tab-->
		<div id="apiuser-viewform-tab-senderid">
			<table class="admin-simpletable">
				<thead>
					<tr>
						<th class="zebra-odd">Sender Name</th>
						<th class="zebra-even">Range Start</th>
						<th class="zebra-odd">Range End</th>
                                                <th class="zebra-even">CobranderId</th>
						<th class="zebra-odd">Status</th>
					</tr>
				</thead>
				<tbody>
					<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['name'] = 'sender';
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('senderID')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['sender']['total']);
?>
					<tr class="<?php echo smarty_function_cycle(array('values'=>"zebra-odd,zebra-even"),$_smarty_tpl);?>
">
						<td class="type-text"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderName'];?>
</td>
						<td class="type-phone"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['rangeStart'];?>
</td>
						<td class="type-phone"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['rangeEnd'];?>
</td>
                                                <td class="type-text"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['cobranderId'];?>
</td>
						<td class="type-status"><?php echo $_smarty_tpl->getVariable('senderID')->value[$_smarty_tpl->getVariable('smarty')->value['section']['sender']['index']]['senderStatusName'];?>
</td>
					</tr>
					<?php endfor; endif; ?>
				</tbody>
			</table>
		</div><!-- END SenderID tab-->
		<div id="apiuser-viewform-tab-ip">
			<table class="admin-simpletable">
				<thead>
					<tr>
						<th class="zebra-odd">Permitted IP</th>
					</tr>
				</thead>
				<tbody>
					<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['name'] = 'ip';
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('permittedIP')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['ip']['total']);
?>
					<tr class="<?php echo smarty_function_cycle(array('values'=>"zebra-odd,zebra-even"),$_smarty_tpl);?>
">
						<td class="type-ip"><?php echo $_smarty_tpl->getVariable('permittedIP')->value[$_smarty_tpl->getVariable('smarty')->value['section']['ip']['index']]['ipAddress'];?>
</td>
					</tr>
					<?php endfor; endif; ?>
				</tbody>
			</table>
		</div><!-- END IP tab-->
		<div id="apiuser-viewform-tab-reply">
			<table class="admin-simpletable">
				<thead>
					<tr>
						<th class="zebra-odd">Destination</th>
						<th class="zebra-even">URL Active</th>
						<th class="zebra-odd">Forward URL</th>
						<th class="zebra-even">Invalid Forward</th>
						<th class="zebra-odd">Last Forwaring  Retry</th>
					</tr>
				</thead>
				<tbody>
					<?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['name'] = 'vnumber';
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('virtualNumber')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['vnumber']['total']);
?>
					<tr class="<?php echo smarty_function_cycle(array('values'=>"zebra-odd,zebra-even"),$_smarty_tpl);?>
">
						<td class="type-text"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnumber']['index']]['virtualDestination'];?>
</td>
						<td class="type-url"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnumber']['index']]['virtualUrl'];?>
</td>
						<td class="type-status"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnumber']['index']]['virtualUrlStatusName'];?>
</td>
						<td class="type-counter"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnumber']['index']]['virtualUrlInvalidCount'];?>
</td>
						<td class="type-date"><?php echo $_smarty_tpl->getVariable('virtualNumber')->value[$_smarty_tpl->getVariable('smarty')->value['section']['vnumber']['index']]['virtualUrlLastRetry'];?>
</td>
					</tr>
					<?php endfor; endif; ?>
				</tbody>
			</table>
		</div><!-- END Reply tab-->

	</div><!-- END Tabs Container -->
</form>