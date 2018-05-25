<fieldset class="content">
    <h2 class="type-action">Invoice Profile</h2>
    <div class="action-container text-right">
        <a href="#" onclick="$app.module('invoice').showHistory({$profile.profileId})" class="form-button" title="Invoice History">
            <img src="skin/images/icon-list.png" class="icon-image icon-size-small" alt="" />
        </a>
        <a href="#" class="form-button" onclick="$app.module('invoice').editProfile({$profile.profileId});" title="Edit Profile">
            <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
            <span class="form-button-text"></span>
        </a>
        <a href="#" onclick="$app.module('invoice').addInvoice({$profile.profileId})" class="form-button" title="Create Invoice">
            <img src="skin/images/icon-gear-add.png" class="icon-image icon-size-small" alt="" />
        </a>
    </div>
    <dl class="admin-definitions">
        <dt>Customer ID</dt>
        <dd>{$profile.customerId}</dd>
        <dt>Client Name</dt>
        <dd><a href="#" onclick="$app.module('client').viewClient({$profile.clientId});">{$profile.companyName}</a></dd>
        <dt>Client Address</dt>
        <dd>{$profile.contactAddress}</dd>
        <dt>API Users</dt>
        <dd>{$apiUsers}</dd>
        <dt>Bank Name</dt>
        <dd>{$profile.bankName}</dd>
        <dt>Account Name</dt>
        <dd>{$profile.accountName}</dd>
        <dt>Account Number</dt>
        <dd>{$profile.accountNumber}</dd>
        <dt>Bank address</dt>
        <dd>{$profile.address}</dd>
    </dl>
    <span class="ui-helper-clearfix"></span>
    <h3 class="action-container type-action">Products for Invoice </h3>
    <table class="admin-simpletable invoice-table">
        <thead>
            <tr>
                <th class="zebra-odd">Product Name</th>
                <th class="zebra-even">Base on Report</th>
                <th class="zebra-odd">Quantity</th>
                <th class="zebra-even">Unit Price (IDR)</th>
                <th class="zebra-odd">
                    <a href="#" onclick="$app.module('invoice').createProfileProduct({$profile.profileId})" class="form-button" title="Add New Product">
                        <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        <span class="form-button-text">Add New</span>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $profile.products as $product}
            <tr class="{cycle values=" zebra-odd,zebra-even "}">
                <td class="type-status">{$product.productName}</td>
                <td class="type-status">{if $product.useReport eq 1} Yes {else} No {/if}</td>
                <td class="type-status">{number_format($product.qty, 0)|default:'-'}</td>
                <td class="type-status">{number_format($product.unitPrice, 2)|default:'-'}</td>
                <td class="type-action">
                    <a href="#" onclick="$app.module('invoice').editProfileProduct({$profile.profileId}, {$product.productId})" class="form-button" title="Edit Product">
                        <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    <a href="#" onclick="$app.module('invoice').deleteProfileProduct({$profile.profileId}, {$product.productId})" class="form-button" title="Remove Product">
                        <img src="skin/images/icon-remove.png" class="icon-image icon-size-small" alt="" /
                        ></a>
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
    </table>
</fieldset>
