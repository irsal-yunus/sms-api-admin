<form action="invoice.bank.update" class="admin-xform">
    <fieldset class="float-centre">
        <input type="hidden" name="bankId" value="{$bank.bankId}">
        <legend>Bank Account</legend>
        <label class="form-flag-required">Bank Name</label>
        <input name="bankName" value="{$bank.bankName}" type="text" maxlength="150" title="Bank account name" />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Account Name</label>
        <input name="accountName" value="{$bank.accountName}" type="text" maxlength="150" />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Account Number</label>
        <input name="accountNumber" value="{$bank.accountNumber}" type="text" maxlength="30" />
        <span class="ui-helper-clearfix"></span>
        <label>Address</label>
        <textarea name="address" maxlength="250" rows="5" cols="17" style="min-width: 111.234px">{$bank.address}</textarea>
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
