<?php /* Smarty version Smarty-3.0.5, created on 2016-10-10 07:19:24
         compiled from "/var/www/html/sms-api-admin/src/templates/apiuser.report.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17006395357fb40fc8e0cb6-81174445%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '376284847fa355f31307f390838fb765587f5341' => 
    array (
      0 => '/var/www/html/sms-api-admin/src/templates/apiuser.report.tpl',
      1 => 1476083952,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17006395357fb40fc8e0cb6-81174445',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>
<?php if (!is_callable('smarty_block_php')) include '/var/www/html/sms-api-admin/src/lib/com/smarty/plugins/block.php.php';
?><form id="apiuser-editform" class="report-tabform" action="services/generateReportUser.php" method="post" style="width: 100%;">
    <input type="hidden" name="userID" value="<?php echo $_smarty_tpl->getVariable('details')->value['userID'];?>
" />
    <input type="hidden" name="userName" value="<?php echo $_smarty_tpl->getVariable('details')->value['userName'];?>
" />
    <fieldset class="float-centre">

        <dl class="admin-definitions">
            <input type="hidden" name="clientID" value="<?php echo $_smarty_tpl->getVariable('details')->value['clientID'];?>
" />
            <dt>Client Company</dt><dd><a href="#" onclick="$app.module('client').viewClient(<?php echo $_smarty_tpl->getVariable('details')->value['clientID'];?>
)"><?php echo $_smarty_tpl->getVariable('details')->value['clientCompanyName'];?>
</a></dd>	
            <dt>Username</dt><dd><?php echo $_smarty_tpl->getVariable('details')->value['userName'];?>
</dd>

        </dl><br>

        <span class="ui-helper-clearfix"></span><br>
        <div>
            <label class="form-flag-required">
                Month 
                <select name="month" style="float:none;display: inline">
                    <!--<option value="">- Select -</option>-->
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                </select>
            </label>
            
            <label class="form-flag-required">Year  
                 <select name="year" style="float:none;display: inline">
                    <!--<option value="">- Select -</option>-->
                    <option value=<?php $_smarty_tpl->smarty->_tag_stack[] = array('php', array()); $_block_repeat=true; smarty_block_php(array(), null, $_smarty_tpl, $_block_repeat);while ($_block_repeat) { ob_start();?>

                                    for($year = 2010 ; $year <= date('Y'); $year++){
                                      echo "<option>$year</option>";
                                   }
                                    <?php $_block_content = ob_get_clean(); $_block_repeat=false; echo smarty_block_php(array(), $_block_content, $_smarty_tpl, $_block_repeat);  } array_pop($_smarty_tpl->smarty->_tag_stack);?>
></option>
                </select>
            </label>
        </div>
        <span class="ui-helper-clearfix"></span><br>
        <div>
            <input style="float: none;display: inline;margin:0; margin-left: 5px" type="checkbox" name="sms_dr" /> Include SMS awaiting DR
        </div>
        <span class="ui-helper-clearfix"></span><br>
        <br>
    </fieldset>

</form>
