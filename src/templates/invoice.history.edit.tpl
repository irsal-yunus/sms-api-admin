<form action="invoice.history.update" class="admin-xform">
    <fieldset class="float-centre">
        <legend>Invoice</legend>
        <input type="hidden" name="invoiceId" value="{$invoice.invoiceId}" />
        <label class="form-flag-required">Invoice Number</label>
        <div class="input-group">
            <div class="input-group-icon">{$setting.invoiceNumberPrefix}</div>
            <div class="input-group-input">
                <input name="invoiceNumber" id="invoiceNumber" value="{$invoice.invoiceNumber}" type="number" {($invoice->invoiceType!==$invoice::ORIGINAL)?'disabled':''} />
            </div>
        </div>
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Invoice Date</label>
        <input name="startDate" value="{$invoice.startDate}" type="text" class="datepicker" id="startDate" />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Term of Payment</label>
        <input name="paymentPeriod" id="paymentPeriod" value="{$invoice->paymentPeriod()}" type="number" min="1" />
        <span class="ui-helper-clearfix"></span>
        <label>Due Date</label>
        <input name="dueDate" id="dueDate" value="{$invoice.dueDate}" type="text" readonly />
        <span class="ui-helper-clearfix"></span>
        <label>Ref Number</label>
        <input name="refNumber" value="{$invoice.refNumber}" type="text" maxlength="50" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
