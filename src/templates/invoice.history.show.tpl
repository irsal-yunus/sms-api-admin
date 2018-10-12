<link href="skin/invoice.css" type="text/css" rel="stylesheet" />
<div class="invoice-container">
    <div class="action-container text-right">
        <a href="#" class="form-button" onclick="$app.module('invoice').editInvoice({$invoice.invoiceId});" title="Edit Invoice">
            <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
        </a>
        <a href="#" onclick="$app.module('invoice').downloadInvoice({$invoice.invoiceId}, 0)" class="form-button" title="Preview Invoice">
            <img src="skin/images/icon-preview.png" class="icon-image icon-size-small" alt="" />
        </a>
        <a href="#" onclick="$app.module('invoice').lockInvoice({$invoice.invoiceId}, false)" class="form-button" title="Lock Invoice">
            <img src="skin/images/icon-lock.png" class="icon-image icon-size-small" alt="" />
        </a>
        <a href="#" onclick="$app.module('invoice').deleteInvoice({$profile.profileId}, {$invoice.invoiceId})" class="form-button" title="Remove Invoice">
            <img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" />
        </a>
    </div>
    <div id="title">
        INVOICE
        {if $invoice.invoiceType !== $invoice::ORIGINAL}
        <p class="invoice-type">
            ({ucfirst(strtolower($invoice.invoiceType))})
        </p>
        {/if}
    </div>
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">BILL TO:</div>
                <h2 class="name">{$profile.companyName}</h2>
                <div class="address">{$profile.contactAddress}</div>
            </div>
            <div id="invoice">
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>Customer ID</td>
                        <td>:</td>
                        <td>{$profile.customerId}</td>
                    </tr>
                    <tr>
                        <td>Invoice No</td>
                        <td>:</td>
                        <td>{$setting.invoiceNumberPrefix}{$invoice.invoiceNumber}</td>
                    </tr>
                    <tr>
                        <td>Invoice Date</td>
                        <td>:</td>
                        <td>{$invoice.startDate|date_format:"%d %B %Y"}</td>
                    </tr>
                    <tr>
                        <td>Due Date</td>
                        <td>:</td>
                        <td>{$invoice.dueDate|date_format:"%d %B %Y"}</td>
                    </tr>
                    <tr>
                        <td>Term of Payment</td>
                        <td>:</td>
                        <td>{$invoice->paymentPeriod()} days</td>
                    </tr>
                    <tr>
                        <td>Ref. No</td>
                        <td>:</td>
                        <td>{$invoice.refNumber}</td>
                    </tr>
                </table>
            </div>
        </div>
        <table class="item" border="0" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                    <th class="no">No.</th>
                    <th class="desc">DESCRIPTION</th>
                    <th class="qty">Quantity</th>
                    <th class="unit">Unit Price (IDR)</th>
                    <th class="total">Amount (IDR)</th>
                    <th class="unit">
                        <a href="#" onclick="$app.module('invoice').addInvoiceProduct({$invoice.invoiceId})" class="form-button" title="Add New Product">
                            <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        </a>
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach $invoice.products as $i => $product}
                <tr>
                    <td class="no">
                        {$i+1}
                    </td>
                    <td class="desc">
                        {$product.productName} period {$product->period|date_format:"1 - %e %B %Y"}
                        {if $product.userApiReport}
                            ({$product.userApiReport})
                        {/if}
                    </td>
                    <td class="qty">
                        {number_format($product.qty)}
                    </td>
                    <td class="unit">
                        {number_format($product.unitPrice, 2)}
                    </td>
                    <td class="total">
                        {number_format($product->amount(), 2)}
                    </td>
                    <td class="type-action">
                        <a href="#" onclick="$app.module('invoice').editInvoiceProduct({$product.productId}, {$invoice.invoiceId})" class="form-button" title="Edit Product">
                            <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
                        </a>
                        <a href="#" onclick="$app.module('invoice').deleteInvoiceProduct({$product.productId}, {$invoice.invoiceId})" class="form-button" title="Remove Product">
                            <img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" />
                        </a>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="5" align="center">
                        No Product
                    </td>
                </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3"></td>
                    <td>SUB TOTAL :</td>
                    <td>
                        {number_format($invoice->subTotal(), 2)}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>VAT :</td>
                    <td>
                        {number_format($invoice->vat(), 2)}
                    </td>
                </tr>
                <tr>
                    <td colspan="3"></td>
                    <td>TOTAL :</td>
                    <td>
                        {number_format($invoice->total(), 2)}
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="word-total">
            <p>Amount In Words:
                <i>{$invoice->spellTotal()} Rupiah</i>
            </p>
        </div>
        <div id="payment">
            <b><u>Payment Details:</u></b>
            <table border="0" cellspacing="0" cellpadding="2" id="bank-acount">
                <tr>
                    <td>Bank Name</td>
                    <td>:</td>
                    <td>{$profile.bankName}</td>
                </tr>
                <tr>
                    <td>Address</td>
                    <td>:</td>
                    <td>{$profile.address}</td>
                </tr>
                <tr>
                    <td>Account Name</td>
                    <td>:</td>
                    <td>{$profile.accountName}</td>
                </tr>
                <tr>
                    <td>Account Number</td>
                    <td>:</td>
                    <td>{$profile.accountNumber}</td>
                </tr>
            </table>
        </div>
        <div id="authorized" class="clearfix {if not $profile.approvedName}text-right{/if}">
            <div class="signature-item">
                <div class="title">
                    Authorized Signature,
                </div>
                <div class="signature">
                </div>
                <div class="signature-name">
                    {$setting.authorizedName} &nbsp;
                </div>
                <div class="signature-position">
                    {$setting.authorizedPosition} &nbsp;
                </div>
            </div>
            {if $profile.approvedName}
            <div class="signature-item">
                <div class="title">
                    Approved by,
                </div>
                <div class="signature">
                </div>
                <div class="signature-name">
                    {$profile.approvedName} &nbsp;
                </div>
                <div class="signature-position">
                    {$profile.approvedPosition} &nbsp;
                </div>
            </div>
            {/if}
        </div>
        <div id="notices">
            <b>Notes:</b>
            <div class="notice">
                {nl2br($setting.noteMessage)}
            </div>
        </div>
    </main>
</div>
