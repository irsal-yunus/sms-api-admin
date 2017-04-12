{literal}
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
{/literal}
<form action="client.billing.update" method="post" class="admin-xform">    
	<input type="hidden" value="{$client.clientID}" name="clientID"/>
		<label class="form-flag-required">Company Name</label><input name="companyName" value="{$client.companyName}" type="text" maxlength="32"/><span class="ui-helper-clearfix"></span>
                <br>
                <label class="form-flag-required">Report Options*:</label><p>
                <input type="hidden" name="billsms[]" value ="Y" {if strpos($billing.billsms, 'Y') == false}checked="checked"{/if}><br>
                <input class="showBillNo" type="checkbox" name="billsms[]" value="N" {if strpos($billing.billsms, 'N') !== false}checked="checked"{/if}/>Non-billed SMS
                <br>  <input class="subBillNo" type="text" name="subIdBillNo" value="{$billing.subIdBillNo}" id="subIdBillNo"/><br>
                <input type="Checkbox" name= "errorCode" value ="E" {if strpos($billing.billsms, 'E') !== false}checked="checked"{/if}>Include error codes in the report
                <br>
                
         <input type="Checkbox" name="unknown" value ="1" {if !empty({$billing.unknown})}checked="checked"{/if}/>"Unknown" as "Delivered"<br>    
         <input type="Checkbox" name="pending" value ="1" {if !empty({$billing.pending})}checked="checked"{/if}>"Pending" as "Delivered"<br>
        {*<input type="checkbox" class="showDelivered" name="showDelivered" value="1" {if !empty({$billing.showDelivered})}checked="checked"{/if}/>DELIVERED<br>
        <table class="subDelivered">
                <tr>
                    <td align="right">
                        <font size="1" color="black">Include</font>
                    </td>
                </tr>
                <tr>
                        <td>
                            <input  type="text" name="deliveredDesc" value ="{$billing.deliveredDesc}" size="15"/><br>
                        </td>
                    </tr>
                </table><br> *}
            {literal}
      <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
      <script src="http://jqueryvalidation.org/files/dist/jquery.validate.min.js"></script>
      <script src="http://jqueryvalidation.org/files/dist/additional-methods.min.js"></script>
      
      <script>
      // avoids form submit
      
      </script>
      {/literal}
</form>