<fieldset class="content">
    <h2 class="type-action">Invoice Profile</h2>
    <div class="action-container text-right">
        <a href="#" onclick="$app.module('invoice').addInvoice({$profile.profileId})" class="form-button" title="Create Invoice">
            <img src="skin/images/icon-add-file.png" class="icon-image icon-size-small" alt="" />
        </a>
        <a href="#" onclick="$app.module('invoice').showHistory({$profile.profileId})" class="form-button" title="Invoice History">
            <img src="skin/images/icon-list.png" class="icon-image icon-size-small" alt="" />
        </a>
        <a href="#" class="form-button" onclick="$app.module('invoice').editProfile({$profile.profileId});" title="Edit Profile">
            <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
            <span class="form-button-text"></span>
        </a>
    </div>
    <dl class="admin-definitions">
        <dt>Profile Name</dt>
        <dd>{$profile.profileName}</dd>
        <dt>Customer ID</dt>
        <dd>{$profile.customerId}</dd>
        <dt>Client Name</dt>
        <dd><a href="#" onclick="$app.module('client').viewClient({$profile.clientId});">{$profile.companyName}</a></dd>
        <dt>Client Address</dt>
        <dd>{$profile.contactAddress}</dd>
        <dt>API Users</dt>
        <dd>
            {foreach $apiUsers as $id => $userName}
            <a href="#" title="View Details User" onclick="$app.module('apiuser').showUserDetails({$id});">
                {$userName}
            </a>
            {if not $userName@last},{/if}
            {/foreach}
        </dd>
        <dt>Bank Name</dt>
        <dd>{$profile.bankName}</dd>
        <dt>Account Name</dt>
        <dd>{$profile.accountName}</dd>
        <dt>Account Number</dt>
        <dd>{$profile.accountNumber}</dd>
        <dt>Auto Generate</dt>
        <dd>{($profile.autoGenerate)?"Yes":"No"}</dd>
        <dt>Approved Name</dt>
        <dd>{$profile.approvedName|default:'-'}</dd>
        <dt>Approved Position</dt>
        <dd>{$profile.approvedPosition|default:'-'}</dd>
        <dt>Use Minimum Commitment?</dt>
        <dd>{($profile.useMinCommitment)?"Yes":"No"}</dd>
        {if $profile.useMinCommitment eq 1}
            <dt>Minimum Commitment Type</dt>
            <dd>{$profile.minCommitmentType}</dd>
            <dt>Minimum Commitment Amout</dt>
            <dd>
                {if $profile.minCommitmentType eq 'PRICE'}
                    {{$profile.minCommitmentAmount}} (IDR)
                {else}
                    {{intval($profile.minCommitmentAmount)}}
                {/if}
            </dd>
            <dt>Use Combined Minimum Commitment? </dt>
            <dd>{($profile.combinedMinCommitment)?"Yes":"No"}</dd>
            {if $profile.minCommitmentType eq 'QUANTITY'}
                <dt>Minimum Charge </dt>
                <dd>{$profile.minCharge} (IDR) </dd>
            {/if}
        {/if}
    </dl>
    <span class="ui-helper-clearfix"></span>
    <h3 class="action-container type-action">Products for Invoice </h3>
    <table class="admin-simpletable invoice-table">
        <thead>
            <tr>
                <th class="zebra-odd">Product Name</th>
                <th class="zebra-even">Based on Billing Report</th>
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
                <td class="type-status">{if $product.useReport eq 1} Yes <br/> (<small>{$product.reportName}</small>) {else} No {/if}</td>
                <td class="type-status">{if $product.qty eq 0} - {else}{number_format($product.qty, 0)|default:'-'}{/if}</td>
                <td class="type-status">{if $product.unitPrice eq 0} - {else}{number_format($product.unitPrice, 2)|default:'-'}{/if}</td>
                <td class="type-action">
                    <a href="#" onclick="$app.module('invoice').editProfileProduct({$profile.profileId}, {$product.productId})" class="form-button" title="Edit Product">
                        <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
                    </a>
                    <a href="#" onclick="$app.module('invoice').deleteProfileProduct({$profile.profileId}, {$product.productId})" class="form-button" title="Remove Product">
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
    </table>
</fieldset>
