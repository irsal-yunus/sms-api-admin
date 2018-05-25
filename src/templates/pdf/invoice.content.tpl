<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice</title>
    <link rel="stylesheet" href="{$smarty.const.SMSAPIADMIN_BASE_URL}/skin/invoice.print.css" media="all" />
</head>
<body>
    <main>
        <div id="details" class="clearfix">
            <div id="client">
                <div class="to">BILL TO:</div>
                <h2 class="name">{$profile.companyName}</h2>
                <div class="address">{$profile.contactAddress}</div>
            </div>
            <div id="invoice">
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr id="customerId">
                        <td class="bold" style="width: 120px;">Customer ID</td>
                        <td class="bold">:</td>
                        <td class="bold">{$profile.customerId}</td>
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
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="5" align="center">
                        No Product
                    </td>
                </tr>
                {/foreach}
                {if count($invoice.products) gt 0}
                    {for $i=1 to (12 - count($invoice.products))}
                        <tr>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                            <td>&nbsp;</td>
                        </tr>
                    {/for}
                {/if}
            </tbody>
        </table>
        <table class="tableTotal">
            <tfoot>
                <tr class="sub-total">
                    <td><b>SUB TOTAL :</b></td>
                    <td style="width: 130px;">
                        {number_format($invoice->subTotal(), 2)}
                    </td>
                </tr>
                <tr class="vat">
                    <td><b>VAT :</b></td>
                    <td>
                        {number_format($invoice->vat(), 2)}
                    </td>
                </tr>
                <tr class="total-price">
                    <td><b>TOTAL :</b></td>
                    <td>
                        <b>{number_format($invoice->total(), 2)}</b>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div class="word-total">
            Amount In Words:
            <i><b>{$invoice->spellTotal()} Rupiah</b></i>
        </div>
        <!-- <br> -->
    </main>
    <footer class="">
        <div id="payment">
            <b><u>Payment Details:</u></b>
            <table border="0" cellspacing="0" cellpadding="2" id="bank-acount">
                <tr>
                    <td width="99">Bank Name</td>
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
        <div id="authorized" class="clearfix">
            <div class="signature-item" style="float: left; margin-left: 60px;">
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
            <div class="signature-item">
                <div class="title">
                    Approved by,
                </div>
                <div class="signature">
                </div>
                <div class="signature-name">
                    {$setting.approvedName} &nbsp;
                </div>
                <div class="signature-position">
                    {$setting.approvedPosition} &nbsp;
                </div>
            </div>
        </div>
        <div id="notices">
            <b>Notes:</b>
            <div class="notice">
                {nl2br($setting.noteMessage)}
            </div>
        </div>
    </footer>
</body>
