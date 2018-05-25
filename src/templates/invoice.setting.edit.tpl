<form action="invoice.setting.update" class="admin-xform">
    <input type="hidden" value="{$setting.settingId}" name="settingId" />
    <fieldset class="float-centre">
        <legend>Invoice Settings</legend>
        <label class="form-flag-required">Term Of Payment</label>
        <div class="input-group">
            <div class="input-group-input">
                <input name="paymentPeriod" value="{$setting.paymentPeriod}" type="number" title="Term of payment in days" />
            </div>
            <div class="input-group-icon">days</div>
        </div>
        <span class="ui-helper-clearfix"></span>
        <label>Last Invoice Number</label>
        <input name="lastInvoiceNumber" value="{$setting.lastInvoiceNumber}" type="number" min="1" maxlength="10" />
        <span class="ui-helper-clearfix"></span>
        <label>Invoice Number Prefix</label>
        <input name="invoiceNumberPrefix" value="{$setting.invoiceNumberPrefix}" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
        <label>Authorized Name</label>
        <input name="authorizedName" value="{$setting.authorizedName}" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
        <label>Authorized Position</label>
        <input name="authorizedPosition" value="{$setting.authorizedPosition}" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
        <label>Approved Name</label>
        <input name="approvedName" value="{$setting.approvedName}" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
        <label>Approved Position</label>
        <input name="approvedPosition" value="{$setting.approvedPosition}" type="text" maxlength="45" />
        <span class="ui-helper-clearfix"></span>
        <label>Note Message</label>
        <textarea name="noteMessage" maxlength="2000" rows="5" cols="17" style="min-width: 111.234px">{$setting.noteMessage}</textarea>
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
