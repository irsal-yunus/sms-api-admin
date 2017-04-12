<form action="apiuser.billing.print" method="post" class="admin-xform"> 
<fieldset> 
	<dl class="admin-definitions">
		<dt>Client Company</dt><dd><a href="#" onclick="$app.module('client').viewClient({$details.clientID})">{$details.clientCompanyName}</a></dd>	
		<dt>Username</dt><dd>{$details.userName}</dd>

	</dl>
</fieldset>
                <br>               
                    
<label class="form-flag-required">From </label>
<div><input type="text" name="fromDate" value="{$details.paymentDate}" class="form-datepicker" maxlength="10"/><span class="ui-helper-clearfix"></span></div><br>
<label class="form-flag-required"> To  </label>
<div><input type="text" name="endDate" value="{$details.paymentDate}" class="form-datepicker" maxlength="10"/><span class="ui-helper-clearfix"></span></div>
                
           
</form>  