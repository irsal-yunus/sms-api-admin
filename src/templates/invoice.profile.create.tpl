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
        <label>Is Auto Generate?</label>
        <input name="autoGenerate" value="1" type="radio" checked="checked" />
        <label class="flexible-width">Yes</label>
        <input name="autoGenerate" value="0" type="radio" />
        <label class="flexible-width">No</label>
        <span class="ui-helper-clearfix"></span>
        <span class="ui-helper-clearfix"></span>
        <label>Approved Name</label>
        <input name="approvedName" value="" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
        <label>Approved Position</label>
        <input name="approvedPosition" value="" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
