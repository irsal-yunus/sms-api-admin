<form action="invoice.profile.update" class="admin-xform">
    <fieldset class="float-centre">
        <input type="hidden" name="profileId" value="{$profile.profileId}">
        <legend>Invoice Profile</legend>
        <label class="form-flag-required">Profile Name</label>
        <input name="profileName" value="{$profile.profileName}" type="text" maxlength="100" />
        <span class="ui-helper-clearfix"></span>
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
        <label>Is Auto Generate?</label>
        <input name="autoGenerate" value="1" type="radio" {if $profile.autoGenerate}checked="checked"{/if}/>
        <label class="flexible-width">Yes</label>
        <input name="autoGenerate" value="0" type="radio" {if $profile.autoGenerate==0}checked="checked"{/if}/>
        <label class="flexible-width">No</label>
        <span class="ui-helper-clearfix"></span>
        <span class="ui-helper-clearfix"></span>
        <label>Approved Name</label>
        <input name="approvedName" value="{$profile.approvedName}" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
        <label>Approved Position</label>
        <input name="approvedPosition" value="{$profile.approvedPosition}" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
