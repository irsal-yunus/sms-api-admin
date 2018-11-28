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

        <!--minimum commitment setting-->
        <label>Commitment Setting</label>
        <select name="useMinCommitment" id ="useCommitment" size="1" class="flexible-width ">
            {html_options options=['No','Yes'] selected=intval(in_array($profile.useMinCommitment, [1,2]))}
        </select>
        <!--it will appear if commitment setting is yes, otherwise it'll dissapear-->
        <div class="commitmentSetting">
            <span class="ui-helper-clearfix"></span>
            <label>Based On</label>
            <select name="minCommitmentType" id="commitmentType" size="1" class="flexible-width" >
                {if $profile.minCommitmentType == 'PRICE'}
                    <option value="PRICE" selected> Price </option>
                    <option value="QUANTITY"> Quantitiy </option>
                {else}
                    <option value="PRICE" > Price </option>
                    <option value="QUANTITY" selected> Quantitiy </option>
                {/if}
            </select>
            <span class="ui-helper-clearfix"></span>
            <label id="minimumLabel"></label>
            <input name="minCommitmentAmount"  data-mask="" value="{$profile.minCommitmentAmount}" type="text" maxlength="15" class="minAMount" />
            <div>
                <span class="ui-helper-clearfix"></span>
                <div class="combine">
                    <label >Use Combine</label>
                    <select name="combinedMinCommitment" id="useCombine" size="1" class="flexible-width ">
                        {html_options options=['No','Yes'] selected=intval(in_array($profile.combinedMinCommitment, [1,2]))}
                    </select>
                </div>
            </div>
            <!--if qty-->
            <div class="quantity">
                <span class="ui-helper-clearfix"></span>
                <label> Minimum Charge</label>
                <input name="minCharge" data-mask="000,000,000,000,000.00" value="{$profile.minCharge}" type="text" maxlength="15" />
            </div>
            <!--end qty-->
        </div>

    </fieldset>
</form>
