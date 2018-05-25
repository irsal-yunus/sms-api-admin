<fieldset class="content">
    <h2 class="type-action"> Invoice for {$profile.companyName} </h2><br>
    <dl class="admin-definitions">
        <dt>Customer ID</dt>
        <dd>{$profile.customerId}</dd>
        <dt>Client Name</dt>
        <dd><a href="#" onclick="$app.module('client').viewClient({$profile.clientId});">{$profile.companyName}</a></dd>
        <dt>API Users</dt>
        <dd>{$apiUsers}</dd>
        <dt>Contact Phone</dt>
        <dd>{$profile.contactPhone}</dd>
        <dt>Contact Email</dt>
        <dd>{$profile.contactEmail}</dd>
        <dt>Client Address</dt>
        <dd>{$profile.contactAddress}</dd>
    </dl>
    <span class="ui-helper-clearfix"></span><br>
    <table class="admin-simpletable invoice-table">
        <thead>
            <tr>
                <th class="zebra-odd">Invoice Number</th>
                <th class="zebra-even">Invoice Date</th>
                <th class="zebra-odd">Due Date</th>
                <th class="zebra-even">Status</th>
                <th class="zebra-odd">
                    <a href="#" onclick="$app.module('invoice').addInvoice({$profile.profileId})" class="form-button" title="Add New Invoice">
                        <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        <span class="form-button-text">Add New</span>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $invoices as $invoice}
            <tr class="{cycle values=" zebra-odd,zebra-even "}">
                <td class="type-status">{$invoice.invoiceNumber}</td>
                <td class="type-status">{$invoice.startDate}</td>
                <td class="type-status">{$invoice.dueDate}</td>
                <td class="type-status">{if $invoice->isLock()} Lock {else} Unlock {/if}</td>
                <td class="type-action">
                    <a href="#" onclick="$app.module('invoice').downloadInvoice({$invoice.invoiceId}, 0)" class="form-button" title="Preview Invoice">
                        <img src="skin/images/icon-preview.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    <a href="#" onclick="$app.module('invoice').downloadInvoice({$invoice.invoiceId}, 1)" class="form-button" title="Download Invoice">
                        <img src="skin/images/icon-download.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    {if  !$invoice->isLock()}
                    <a href="#" onclick="$app.module('invoice').showInvoice({$invoice.invoiceId}, {$profile.profileId})" class="form-button" title="Edit Invoice">
                        <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    <a href="#" onclick="$app.module('invoice').lockInvoice({$invoice.invoiceId}, {$profile.profileId})" class="form-button" title="Lock Invoice">
                        <img src="skin/images/icon-lock.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    <a href="#" onclick="$app.module('invoice').deleteInvoice({$profile.profileId}, {$invoice.invoiceId})" class="form-button" title="Remove Invoice">
                        <img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    {/if}
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5" align="center">
                    No Invoice
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</fieldset>
