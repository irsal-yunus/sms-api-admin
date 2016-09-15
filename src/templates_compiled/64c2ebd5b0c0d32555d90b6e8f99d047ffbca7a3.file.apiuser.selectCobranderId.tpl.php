<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 04:31:37
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.selectCobranderId.tpl" */ ?>
<?php /*%%SmartyHeaderCode:5789764657d8d2a9859073-01154981%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '64c2ebd5b0c0d32555d90b6e8f99d047ffbca7a3' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.selectCobranderId.tpl',
      1 => 1473742353,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '5789764657d8d2a9859073-01154981',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

    <script type="text/javascript">
        function selectCobranderID(data){
                $('#cobranderId').val(data);
                $('.containerDialog').remove();
            };
        $(document).ready(function() {
            $('#tableCobrander').dataTable({
                "sPaginationType": "full_numbers",
                "bLengthChange": false,
                "bInfo": false
            });
        });
    </script> 
    

<table id="tableCobrander">
    <thead>
        <tr>
            <th>Cobrander Id</th>
            <th>Country</th>
            <th>Company</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php unset($_smarty_tpl->tpl_vars['smarty']->value['section']['data']);
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['name'] = 'data';
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['loop'] = is_array($_loop=$_smarty_tpl->getVariable('datas')->value) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['show'] = true;
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['max'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['loop'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['step'] = 1;
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['start'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['step'] > 0 ? 0 : $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['loop']-1;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['data']['show']) {
    $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['total'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['loop'];
    if ($_smarty_tpl->tpl_vars['smarty']->value['section']['data']['total'] == 0)
        $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['show'] = false;
} else
    $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['total'] = 0;
if ($_smarty_tpl->tpl_vars['smarty']->value['section']['data']['show']):

            for ($_smarty_tpl->tpl_vars['smarty']->value['section']['data']['index'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['start'], $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['iteration'] = 1;
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['iteration'] <= $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['total'];
                 $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['index'] += $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['step'], $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['iteration']++):
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['rownum'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['iteration'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['index_prev'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['index'] - $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['index_next'] = $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['index'] + $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['step'];
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['first']      = ($_smarty_tpl->tpl_vars['smarty']->value['section']['data']['iteration'] == 1);
$_smarty_tpl->tpl_vars['smarty']->value['section']['data']['last']       = ($_smarty_tpl->tpl_vars['smarty']->value['section']['data']['iteration'] == $_smarty_tpl->tpl_vars['smarty']->value['section']['data']['total']);
?>
            <tr>
                <td><?php echo $_smarty_tpl->getVariable('datas')->value[$_smarty_tpl->getVariable('smarty')->value['section']['data']['index']]['cobranderId'];?>
</td>
                <td><?php echo $_smarty_tpl->getVariable('datas')->value[$_smarty_tpl->getVariable('smarty')->value['section']['data']['index']]['cobranderCountry'];?>
</td>
                <td><?php echo $_smarty_tpl->getVariable('datas')->value[$_smarty_tpl->getVariable('smarty')->value['section']['data']['index']]['companyName'];?>
</td>
                <td><button onclick="selectCobranderID('<?php echo $_smarty_tpl->getVariable('datas')->value[$_smarty_tpl->getVariable('smarty')->value['section']['data']['index']]['cobranderId'];?>
')" style="height: auto;">Select</button></td>
            </tr>
        <?php endfor; endif; ?>
    </tbody>
</table>