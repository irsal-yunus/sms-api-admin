<fieldset class="content">
    <div class="action-container text-left" style="padding: 10px">
        <a href="#" class="form-button {(empty($type))?'btn-active':''}" title="Show All Invoices" onclick="$app.module('invoice').showInvoiceTable('');" >
            <img src="skin/images/icon-view.png" class="form-button-image" alt="" />
            <span class="form-button-text">All</span>
        </a>
        <a href="#" class="form-button {($type === 'unlocked')?'btn-active':''}" title="Only Show Unlocked Invoices" onclick="$app.module('invoice').showInvoiceTable('unlocked');" >
            <img src="skin/images/icon-unlock.png" class="form-button-image" alt="" />
            <span class="form-button-text">Show Unlocked
                {if $pending > 0}
                    <span class="badge alert">{$pending}</span>
                {/if}
            </span>
        </a>
        <a href="#" class="form-button {($type === 'locked')?'btn-active':''}" title="Only Show Locked Invoices" onclick="$app.module('invoice').showInvoiceTable('locked');" >
            <img src="skin/images/icon-lock.png" class="form-button-image" alt="" />
            <span class="form-button-text">Show Locked</span>
        </a>
        <a href="#" class="form-button float-right" title="Download Invoice" onclick="$app.module('invoice').showDownloadAll();" >
            <img src="skin/images/icon-download.png" class="form-button-image" alt="" />
            <span class="form-button-text">Download</span>
        </a>
        <a href="#" class="form-button float-right" title="Create Invoice" onclick="$app.module('invoice').addInvoice();" >
            <img src="skin/images/icon-add-file.png" class="form-button-image" alt="" />
            <span class="form-button-text">Create Invoice</span>
        </a>
    </div>
    {include 'invoice.history.table.tpl' invoices=$invoices}
</fieldset>
