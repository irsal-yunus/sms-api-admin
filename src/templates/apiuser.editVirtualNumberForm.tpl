<form action="apiuser.updateVirtualNumber" method="post" class="admin-xform">
<input value="{$details.virtualNumberID}" type="hidden" name="virtualNumberID"/>
<fieldset style="width:95%;" class="float-centre">
	<label class="form-flag-required">Destination</label><input name="virtualDestination" type="text" value="{$details.virtualDestination}" maxlength="16"/><span class="ui-helper-clearfix"></span>
	<label>Use Foward URL</label>
		<input name="virtualUrlActive" onclick="if($(this).is(':checked')) $('#apiuser-regvnum-virtualurl').removeAttr('readonly').focus();" value="1" type="radio" {if $details.virtualUrlActive}checked="checked"{/if}/>
		<label class="flexible-width">Yes</label>
		<input name="virtualUrlActive" onclick="if($(this).is(':checked')) $('#apiuser-regvnum-virtualurl').attr('readonly','readonly').val('');" value="0" type="radio" {if !$details.virtualUrlActive}checked="checked"{/if}/>
		<label class="flexible-width">No</label>
		<span class="ui-helper-clearfix"></span>
	<label>URL</label><input id="apiuser-regvnum-virtualurl" name="virtualUrl" type="text" value="{$details.virtualUrl}" {if !$details.virtualUrlActive}readonly="readonly"{/if}/><span class="ui-helper-clearfix" maxlength="255"></span>
</fieldset>
</form>