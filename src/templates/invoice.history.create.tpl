<form action="invoice.history.store" class="admin-xform">
    <fieldset class="float-centre">
        <legend>Invoice</legend>
        <input type="hidden" name="profileId" value="{$profileId}" />
        <label class="form-flag-required">Invoice Number</label>
        <div class="input-group">
            <div class="input-group-icon">{$setting.invoiceNumberPrefix}</div>
            <div class="input-group-input">
                <input name="invoiceNumber" id="invoiceNumber" value="{$setting.lastInvoiceNumber+1}" type="number" />
            </div>
        </div>
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Invoice Date</label>
        <input name="startDate" value="" type="text" class="datepicker" id="startDate" />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Term of Payment</label>
        <input name="paymentPeriod" id="paymentPeriod" value="{$setting.paymentPeriod}" type="number" min="1" />
        <span class="ui-helper-clearfix"></span>
        <label>Due Date</label>
        <input name="dueDate" id="dueDate" value="" type="text" readonly />
        <span class="ui-helper-clearfix"></span>
        <label>Ref Number</label>
        <input name="refNumber" value="" type="text" maxlength="50" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
