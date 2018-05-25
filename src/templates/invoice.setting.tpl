<div class="panel-content text-right">
    <a href="#" class="form-button" onclick="$app.module('invoice').editSetting();">
        <img src="skin/images/icon-edit.png" class="form-button-image" alt="" />
        <span class="form-button-text">Edit</span>
    </a>
</div>
<dl class="admin-definitions">
    <dt>Term Of Payment</dt>
    <dd>{$setting.paymentPeriod} days</dd>
    <dt>Last Invoice Number</dt>
    <dd>{$setting.lastInvoiceNumber}</dd>
    <dt>Invoice Number Prefix</dt>
    <dd>{$setting.invoiceNumberPrefix}</dd>
    <dt>Authorized Name</dt>
    <dd>{$setting.authorizedName}</dd>
    <dt>Authorized Position</dt>
    <dd>{$setting.authorizedPosition}</dd>
    <dt>Approved Name</dt>
    <dd>{$setting.approvedName}</dd>
    <dt>Approved Position</dt>
    <dd>{$setting.approvedPosition}</dd>
    <dt>Note Message</dt>
    <dd>{$setting.noteMessage}</dd>
</dl>
<fieldset>
    <h3 class="type-action"> Bank Accounts </h3>
    <table class="admin-simpletable">
        <thead>
            <tr>
                <th class="zebra-odd">Bank Name</th>
                <th class="zebra-even">Account Name</th>
                <th class="zebra-odd">Account Number</th>
                <th class="zebra-even">Address</th>
                <th class="zebra-odd">
                    <a href="#" onclick="$app.module('invoice').createBank()" class="form-button">
                        <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        <span class="form-button-text">Add New</span>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            {section name=bank loop=$banks}
            <tr class="{cycle values=" zebra-odd,zebra-even "}">
                <td class="type-status">{$banks[bank].bankName}</td>
                <td class="type-status">{$banks[bank].accountName}</td>
                <td class="type-status">{$banks[bank].accountNumber}</td>
                <td class="type-status" style="max-width: 200px">{$banks[bank].address}</td>
                <td class="type-action">
                    <a href="#" onclick="$app.module('invoice').editBank({$banks[bank].bankId})" class="form-button"><img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" /></a>
                    <a href="#" onclick="$app.module('invoice').deleteBank({$banks[bank].bankId})" class="form-button"><img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" /></a>
                </td>
            </tr>
            {/section}
        </tbody>
    </table>
</fieldset>
