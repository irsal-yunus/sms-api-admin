<form action="client.update" class="admin-xform">
    <input type="hidden" value="{$client.clientID}" name="clientID" />
    <fieldset class="float-centre">
        <legend>Company</legend>
        <label class="form-flag-required">Customer ID</label>
        <input name="customerId" value="{$client.customerId}" type="text" maxlength="30" /><span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Company Name</label>
        <input name="companyName" value="{$client.companyName}" type="text" maxlength="32" /><span class="ui-helper-clearfix"></span>
        <label>Company URL</label>
        <input name="companyUrl" value="{$client.companyUrl}" type="text" maxlength="50" /><span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Country</label>
        <select name="countryCode" size="1" class="flexible-width">
            {html_options options=$countries selected=$client.countryCode}
        </select>
    </fieldset>
    <fieldset class="float-centre">
        <legend>Contact</legend>
        <label class="form-flag-required">Contact Name</label>
        <input name="contactName" value="{$client.contactName}" type="text" maxlength="32" /><span class="ui-helper-clearfix"></span>
        <label>Contact Email</label>
        <input name="contactEmail" value="{$client.contactEmail}" type="text" maxlength="32" /><span class="ui-helper-clearfix"></span>
        <label>Contact Phone</label>
        <input name="contactPhone" value="{$client.contactPhone}" type="text" maxlength="32" /><span class="ui-helper-clearfix"></span>
        <label>Contact Address</label>
        <textarea name="contactAddress" maxlength="200" rows="5" cols="17" style="min-width: 111.234px">{$client.contactAddress}</textarea><span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
