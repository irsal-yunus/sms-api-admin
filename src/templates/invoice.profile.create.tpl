<form action="invoice.profile.store" class="admin-xform">
    <fieldset class="float-centre">
        <legend>Invoice Profile</legend>
        <label class="form-flag-required">Profile Name</label>
        <input name="profileName" value="" type="text" maxlength="100" required />
        <span class="ui-helper-clearfix"></span>
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
        <!--minimum commitment setting-->
        <label>Commitment Setting</label>
        <select name="useCommitment" id ="useCommitment" size="1" class="flexible-width commitmentSetting">
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select>
        <!--it will appear if commitment setting is yes, otherwise it'll dissapear-->
        <span class="ui-helper-clearfix"></span>
        <label class="commitmentSetting">Based On</label>
        <select name="bankId" size="1" class="flexible-width commitmentSetting">
            <option>Price</option>
            <option>Quantity</option>
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="commitmentSetting">Minimum Value</label>
        <input type="number" name="" class="commitmentSetting">
        <span class="ui-helper-clearfix"></span>
        <label class="commitmentSetting">Use Combine</label>
        <input name="" value="1" type="radio" checked="checked" class="commitmentSetting" />
        <label class="flexible-width commitmentSetting">Yes</label>
        <input class="commitmentSetting" name="" value="0" type="radio" />
        <label class="flexible-width commitmentSetting">No</label>
    </fieldset>
</form>
