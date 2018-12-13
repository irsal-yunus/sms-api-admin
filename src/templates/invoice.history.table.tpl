    <table class="admin-simpletable invoice-table">
    <thead>
        <tr>
            <th class="zebra-odd">Invoice Number</th>
            {if !isset($profile)}
            <th class="zebra-even">Company Name</th>
            <th class="zebra-odd">Profile Name</th>
            {/if}
            <th class="zebra-even">Invoice Date</th>
            <th class="zebra-odd">Due Date</th>
            <th class="zebra-even">Status</th>
            <th class="zebra-even">Type</th>
            <th class="zebra-odd">
                {if isset($profile) && $profile.archivedDate eq null}
                <a href="#" onclick="$app.module('invoice').addInvoice({$profile.profileId})" class="form-button" title="Add New Invoice">
                        <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        <span class="form-button-text">Add New</span>
                </a>
                {else}
                Actions
                {/if}
            </th>
        </tr>
    </thead>
    <tbody>
        {foreach $invoices as $invoice}
        <tr class="{cycle values=" zebra-odd,zebra-even "}">
            <td class="type-status">{$invoice.invoiceNumber}</td>
            {if !isset($profile)}
            <td class="type-text">
                {$invoice.companyName}
            </td>
            <td class="type-text">
                <a href="#" onclick="$app.module('invoice').showHistory({$invoice.profileId})" title="Show Profile">{$invoice.profileName}
                </a>
            </td>
            {/if}
            <td class="type-status">{$invoice.startDate}</td>
            <td class="type-status">{$invoice.dueDate}</td>
            <td class="type-status">{if $invoice->isLock()} Locked {else} Unlocked {/if}</td>
            {assign var="invoiceUsage" value=" ( {$invoice.invoiceUsage} )"}
            <td class="type-status">{$invoice.invoiceType}{($invoice.invoiceType=='ORIGINAL')?'':$invoiceUsage}</td>
            <td class="type-action">
                <a href="#" onclick="$app.module('invoice').downloadInvoice({$invoice.invoiceId}, 0)" class="form-button" title="View Invoice File">
                    <img src="skin/images/icon-preview.png" class="icon-image icon-size-small" alt="" />
                </a>
                {if $invoice->isLock()}
                <a href="#" onclick="$app.module('invoice').downloadInvoice({$invoice.invoiceId}, 1)" class="form-button" title="Download Invoice File">
                    <img src="skin/images/icon-download.png" class="icon-image icon-size-small" alt="" />
                </a>
                    {if !isset($profile) or $profile.archivedDate eq null}
                        <a href="#" onclick="$app.module('invoice').copyInvoice({$invoice.invoiceId}, {(!isset($profile))?'true':'false'})" class="form-button" title="Copy Invoice">
                            <img src="skin/images/icon-copy.png" class="icon-image icon-size-small" alt="" />
                        </a>
                        <a href="#" onclick="$app.module('invoice').reviseInvoice({$invoice.invoiceId}, {(!isset($profile))?'true':'false'})" class="form-button" title="Revise Invoice">
                            <img src="skin/images/icon-revise.png" class="icon-image icon-size-small" alt="" />
                        </a>
                    {/if}
                {else}
                {if !isset($profile) or $profile.archivedDate eq null}
                    <a href="#" onclick="$app.module('invoice').showInvoice({$invoice.invoiceId}, {$invoice.profileId})" class="form-button" title="Edit Invoice">
                        <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    <a href="#" onclick="$app.module('invoice').lockInvoice({$invoice.invoiceId}, {(!isset($profile))?'true':'false'})" class="form-button" title="Lock Invoice">
                        <img src="skin/images/icon-lock.png" class="icon-image icon-size-small" alt="" />
                    </a>
                {/if}
                <a href="#" onclick="$app.module('invoice').deleteInvoice({(!isset($profile))?'null':$invoice.profileId}, {$invoice.invoiceId})" class="form-button" title="Remove Invoice">
                    <img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" />
                </a>
                {/if}
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="{if !isset($profile)} 8 {else} 5 {/if}" align="center">
                No Invoice
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>
<br>
{if isset($pageCount)}
<div class="pagination" style="float:left">
    <a href="#" class="paging first" onclick="$app.module('invoice').showInvoiceTable('{$type}', 1)"> << </a>
    {if $page-1 >= 1}
        <a href="#" class="paging"onclick="$app.module('invoice').showInvoiceTable('{$type}', {$page-1})"> < </a>
    {else}
        <a href="#" class="paging" onclick="$app.module('invoice').showInvoiceTable('{$type}',1)"> < </a>
    {/if}
    <input type="number" id="targetPage" class="input-page" value="{$page}" min="1" max="{$pageCount}" onchange="$app.module('invoice').getInputPage(0,'{$type}',{$pageCount})">
    {if $page+1 <= $pageCount}
        <a href="#" class="paging" onclick="$app.module('invoice').showInvoiceTable('{$type}', {$page+1})"> > </a>
    {else}
        <a href="#" class="paging" onclick="$app.module('invoice').showInvoiceTable('{$type}', {$pageCount})"> > </a>
    {/if}
    <a href="#" class="paging end" onclick="$app.module('invoice').showInvoiceTable('{$type}', {$pageCount})"> >> </a>
    <span style="margin:auto">&nbsp; Showing {$numberFiles['firstNumber']}-{$numberFiles['endNumber']} of {$totalData}</span>&nbsp;
</div>
{/if}
<br>
