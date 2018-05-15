<form action="invoice.profile.store" class="admin-xform">
    <fieldset class="float-centre">
        <legend>Invoice Profile</legend>
        <label class="form-flag-required">Client</label>
        <select name="clientId" size="1" class="flexible-width">
            {html_options options=$clients}
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Payment detail</label>
        <select name="bankId" size="1" class="flexible-width">
            {html_options options=$banks}
        </select>
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
