<form action="invoice.product.update" class="admin-xform" id="form-product">
    <fieldset class="float-centre">
        <input type="hidden" value="{$product.productId}" name="productId" />
        <input type="hidden" value="{$product.ownerType}" name="ownerType" />
        <input type="hidden" value="{$product.ownerId}" name="ownerId" />
        <legend>Invoice Profile</legend>
        <label class="form-flag-required">Product Name</label>
        <input name="productName" id="productName" value="{$product.productName}" type="text" maxlength="150" />
        <span class="ui-helper-clearfix"></span>
        <label class="">Use Period?</label>
        <select name="isPeriod" id="isPeriod" size="1" class="flexible-width">
            {html_options options=['No','Yes'] selected=intval(in_array($product.isPeriod, [1,2]))}
        </select>
        <span class="ui-helper-clearfix"></span>
        {if $product.ownerType eq 'HISTORY'}
        <label class="form-flag-required" id="label-period"></label>
        <select name="period" id="period" size="1" class="flexible-width">
            {html_options options=$dateRange selected=$selectedRange}
        </select>
        <input type="date" id="date" name="date" value="{$realDate}">
        <span class="ui-helper-clearfix"></span>
        {/if}
        <label class="" id="label-useReport">User Report ?</label>
        <select name="useReport" id="useReport" size="1" class="flexible-width">
            {html_options options=['No','Yes'] selected=intval(in_array($product.useReport, [1,2]))}
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="report-name">User/Group Name</label>
        <select name="reportName" id="reportName" class="hidden report-name">
            {html_options options=$reports selected=$product.reportName}
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="report-name hidden">Input Manual ?</label>
        <input type="checkbox" name="manualInput" id="manualInput" value="1" class="report-name hidden checkbox-normal" {if $product.useReport eq 2} checked {/if} />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Quantity</label>
        <input name="qty" class="toggle-report" data-mask="###,###,###,###,###,###" value="{number_format($product.qty)}" type="text" maxlength="14" />
        <span class="ui-helper-clearfix"></span>
        <label class="form-flag-required">Unit Price (IDR)</label>
        <input name="unitPrice" class="toggle-report" data-mask="000,000,000,000,000.00" value="{$product.unitPrice}" type="text" maxlength="14" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
