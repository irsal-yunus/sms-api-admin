<?php /* Smarty version Smarty-3.0.5, created on 2016-09-27 09:58:43
         compiled from "/var/www/html/sms-api-admin/src/templates/credit.viewTransaction.tpl" */ ?>
<?php /*%%SmartyHeaderCode:42242791657d8d62872c686-51656245%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'fe5b1864205209249d20f351b5f0deff790d5ef3' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/credit.viewTransaction.tpl',
      1 => 1474528347,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '42242791657d8d62872c686-51656245',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<form class="admin-xform" action="#">
	<fieldset class="float-centre">
		<dl class="admin-definitions">
			<dt>Reference Code</dt><dd><strong><?php echo $_smarty_tpl->getVariable('details')->value['transactionRef'];?>
</strong></dd>
			<dt>Requested By</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionRequester'];?>
</dd>
			<dt>Credit Mutation</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionCredit'];?>
</dd>
			<dt>Price</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionPrice'];?>
</dd>
			<dt>Currency</dt><dd><?php echo $_smarty_tpl->getVariable('currencyDesc')->value[$_smarty_tpl->getVariable('details')->value['transactionCurrency']];?>
 [<em><?php echo $_smarty_tpl->getVariable('details')->value['transactionCurrency'];?>
</em>]</dd>
			<dt>Payment Method</dt><dd><?php if (isset($_smarty_tpl->getVariable('paymentMethods',null,true,false)->value[$_smarty_tpl->getVariable('details',null,true,false)->value['paymentMethod']])){?><?php echo $_smarty_tpl->getVariable('paymentMethods')->value[$_smarty_tpl->getVariable('details')->value['paymentMethod']];?>
<?php }else{ ?><?php echo $_smarty_tpl->getVariable('undefinedMethodDesc')->value;?>
<?php }?></dd>
			<dt>Payment Status</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['paymentStatusName'];?>
</dd>
			<dt>Payment Date</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['paymentDate'];?>
</dd>
			<dt>Created Date</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionCreatedDate'];?>
</dd>
			<dt>Created By</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionCreatedByName'];?>
</dd>
			<dt>Updated Date</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionUpdatedDate'];?>
</dd>
			<dt>Updated By</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionUpdatedByName'];?>
</dd>
			<dt>Remark</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['transactionRemark'];?>
</dd>
		</dl>
	</fieldset>
</form>