<form action="invoice.product.store" class="admin-xform" id="form-product">
    <fieldset class="float-centre">
        <input type="hidden" value="{$owner['ownerType']}" name="ownerType">
        <input type="hidden" value="{$owner['ownerId']}" name="ownerId">
        <legend>Invoice Profile</legend>
        <label class="form-flag-required">Product Name</label>
        <input name="productName" id="productName" value="" type="text" maxlength="150" />
        <span class="ui-helper-clearfix"></span>
        {if $owner['ownerType'] eq 'HISTORY'}
        <label class="form-flag-required">Period</label>
        <select name="period" size="1" class="flexible-width">
            {html_options options=$dateRange selected=$selectedRange}
        </select>
        <span class="ui-helper-clearfix"></span>
        {/if}
        <label class="">User Report ?</label>
        <select name="useReport" id="useReport" size="1" class="flexible-width">
            {html_options options=['No','Yes']}
        </select>
        <span class="ui-helper-clearfix"></span>
        <label class="report-name">User/Group Name</label>
        <select name="reportName" id="reportName" class="hidden report-name">
            {html_options options=$reports}
        </select>
        <span class="ui-helper-clearfix"></span>
        {if $owner['ownerType'] eq 'HISTORY'}
            <label class="report-name hidden">Input Manual ?</label>
            <input type="checkbox" name="manualInput" id="manualInput" value="1" class="report-name hidden checkbox-normal" />
            <span class="ui-helper-clearfix"></span>
        {/if}
        <label class="">Quantity</label>
        <input name="qty" class="toggle-report" data-mask="###,###,###,###,###,###" value="" type="text" maxlength="14" />
        <span class="ui-helper-clearfix"></span>
        <label class="">Unit Price (IDR)</label>
        <input name="unitPrice" class="toggle-report" data-mask="000,000,000,000,000.00" value="" type="text" maxlength="15" />
        <span class="ui-helper-clearfix"></span>
    </fieldset>
</form>
