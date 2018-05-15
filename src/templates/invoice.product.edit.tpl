<form action="invoice.product.update" class="admin-xform" id="form-product">
    <fieldset class="float-centre">
        <input type="hidden" value="{$product.productId}" name="productId" />
        <input type="hidden" value="{$product.ownerType}" name="ownerType" />
        <input type="hidden" value="{$product.ownerId}" name="ownerId" />
        <legend>Invoice Profile</legend>
        <label class="form-flag-required">Product Name</label>
        <input name="productName" id="productName" value="{$product.productName}" type="text" maxlength="150" />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">User Report ?</label>
        <select name="useReport" id="useReport" size="1" class="flexible-width">
            {html_options options=['No','Yes'] selected=$product.useReport}
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="report-name form-flag-required"></label>
        <select name="reportName" id="reportName" class="hidden report-name">
            {html_options options=$reports selected=$product.reportName}
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Quantity</label>
        <input name="qty" class="toggle-report" data-mask="000,000,000,000,000" value="{$product.qty}" type="text" maxlength="11" />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Unit Price (IDR)</label>
        <input name="unitPrice" class="toggle-report" data-mask="000,000,000,000,000.00" value="{$product.unitPrice}" type="text" maxlength="11" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
