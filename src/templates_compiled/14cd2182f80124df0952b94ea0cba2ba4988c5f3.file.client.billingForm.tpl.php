<?php /* Smarty version Smarty-3.0.5, created on 2016-09-14 04:45:07
         compiled from "/www/web/sms-api-admin/src/templates/client.billingForm.tpl" */ ?>
<?php /*%%SmartyHeaderCode:45842882157d8d5d399a8c6-32727804%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '14cd2182f80124df0952b94ea0cba2ba4988c5f3' => 
    array (
      0 => '/www/web/sms-api-admin/src/templates/client.billingForm.tpl',
      1 => 1472027728,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '45842882157d8d5d399a8c6-32727804',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
)); /*/%%SmartyHeaderCode%%*/?>

<script language="JavaScript">
    
    $("#submit").on("click",function(){
          if (($("input[name*='billsms']:checked").length)<=0) {
              alert("You must check at least 1 of Billed SMS");
              return false;
          }else{
          return true;
      }
      });

$(".subBillNo").hide();
$(".showBillNo").click(function() {
    if($(this).is(":checked")) {
        $(".subBillNo").show();
    } else {
        $(".subBillNo").hide();
    }
});

$(".subDelivered").hide();
$(".showDelivered").click(function() {
    if($(this).is(":checked")) {
        $(".subDelivered").show();
    } else {
        $(".subDelivered").hide();
    }
});

$(".subProvider").hide();
$(".showProvider").click(function() {
    if($(this).is(":checked")) {
        $(".subProvider").show();
    } else {
        $(".subProvider").hide();
    }
});

function ShowProvider(id_provider) {
    if(document.getElementById('idProvider').value=='Hide Layer'){
        document.getElementById('idProvider').value = 'Show Layer';
        document.getElementById(id_provider).style.display = 'none';
    }else{
        document.getElementById('idProvider').value = 'Hide Layer';
        document.getElementById(id_provider).style.display = 'inline';
    }
}



</script>

<form action="client.billing.update" method="post" class="admin-xform">    
	<input type="hidden" value="<?php echo $_smarty_tpl->getVariable('client')->value['clientID'];?>
" name="clientID"/>
		<label class="form-flag-required">Company Name</label><input name="companyName" value="<?php echo $_smarty_tpl->getVariable('client')->value['companyName'];?>
" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
                <br>
                <label class="form-flag-required">Report Options*:</label><p>
                <input type="hidden" name="billsms[]" value ="Y" <?php if (strpos($_smarty_tpl->getVariable('billing')->value['billsms'],'Y')==false){?>checked="checked"<?php }?>><br>
                <input class="showBillNo" type="checkbox" name="billsms[]" value="N" <?php if (strpos($_smarty_tpl->getVariable('billing')->value['billsms'],'N')!==false){?>checked="checked"<?php }?>/>Non-billed SMS
                <br>  <input class="subBillNo" type="text" name="subIdBillNo" value="<?php echo $_smarty_tpl->getVariable('billing')->value['subIdBillNo'];?>
" id="subIdBillNo"/><br>
                <input type="Checkbox" name= "errorCode" value ="E" <?php if (strpos($_smarty_tpl->getVariable('billing')->value['billsms'],'E')!==false){?>checked="checked"<?php }?>>Include error codes in the report
                <br>
                
         <input type="Checkbox" name="unknown" value ="1" <?php ob_start();?><?php echo $_smarty_tpl->getVariable('billing')->value['unknown'];?>
<?php $_tmp1=ob_get_clean();?><?php if (!empty($_tmp1)){?>checked="checked"<?php }?>/>"Unknown" as "Delivered"<br>    
         <input type="Checkbox" name="pending" value ="1" <?php ob_start();?><?php echo $_smarty_tpl->getVariable('billing')->value['pending'];?>
<?php $_tmp2=ob_get_clean();?><?php if (!empty($_tmp2)){?>checked="checked"<?php }?>>"Pending" as "Delivered"<br>
            
      <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
      <script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
      <script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
      
      <script>
      // avoids form submit
      
      </script>
      
</form>