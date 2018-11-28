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
        <select name="useMinCommitment" id ="useCommitment" size="1" class="flexible-width ">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>
        <!--it will appear if commitment setting is yes, otherwise it'll dissapear-->
        <div class="commitmentSetting">
            <span class="ui-helper-clearfix"></span>
            <label>Based On</label>
            <select name="minCommitmentType" id="commitmentType" size="1" class="flexible-width" >
                <option value="PRICE">Price</option>
                <option value="QUANTITY">Quantity</option>
            </select>
            <span class="ui-helper-clearfix"></span>
            <label id="minimumLabel"></label>
            <input name="minCommitmentAmount" id="minAMount" data-mask="###,###,###,###,###,###" value="" type="text" maxlength="15" class="" />
            <div>
                <span class="ui-helper-clearfix"></span>
                <div class="combine">
                    <label >Use Combine</label>
                    <select name="combinedMinCommitment" id="useCombine" size="1" class="flexible-width ">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>
            <!--if qty-->
            <div class="quantity">
                <span class="ui-helper-clearfix"></span>
                <label> Minimum Charge</label>
                <input name="minCharge" data-mask="000,000,000,000,000.00" value="" type="text" maxlength="15" />
            </div>
            <!--end qty-->
        </div>
    </fieldset>
</form>
