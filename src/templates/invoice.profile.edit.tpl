<form action="invoice.profile.update" class="admin-xform">
    <fieldset class="float-centre">
        <input type="hidden" name="profileId" value="{$profile.profileId}">
        <legend>Invoice Profile</legend>
        <label class="form-flag-required">Client</label>
        <select name="clientId" size="1" class="flexible-width" disabled>
            {html_options options=$clients selected=$profile.clientId}}
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Payment detail</label>
        <select name="bankId" size="1" class="flexible-width">
            {html_options options=$banks selected=$profile.bankId}
        </select>
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
