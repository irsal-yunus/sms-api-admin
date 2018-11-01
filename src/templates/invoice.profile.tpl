<fieldset class="content">
    <div class="action-container text-right" style="padding: 10px ;">
        {if $archived eq null}
            <a href="#" class="form-button" onclick="$app.module('invoice').showClient('archived');" style="float:left" >
                <span class="form-button-text">Include Archived Client</span>
            </a>
        {else}
            <a href="#" class="form-button" onclick="$app.module('invoice').showClient();" style="float:left" >
                <span class="form-button-text">Show only non Archived Client</span>
            </a>
        {/if}
        <a href="#" class="form-button" onclick="$app.module('invoice').showDownloadAll();" title="Download All" style="float:right">
            <img src="skin/images/icon-download.png" class="form-button-image" alt="" />
            <span class="form-button-text">Download All</span>
        </a>
    </div>
    <table class="admin-simpletable invoice-table">
        <thead>
            <tr>
                <th class="zebra-odd">Customer ID</th>
                <th class="zebra-odd">Company Name</th>
                <th class="zebra-even">Payment Detail</th>
                <th class="zebra-even">Auto Generate</th>
                <th class="zebra-odd">
                    <a href="#" onclick="$app.module('invoice').createProfile()" class="form-button" title="Add New Profile">
                        <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        <span class="form-button-text">Add New</span>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $profiles as $profile}
            <tr class="{cycle values="zebra-odd,zebra-even"}">
                <td class="type-status">{$profile.customerId}</td>
                <td class="type-status">{$profile.companyName}</td>
                <td class="type-status">{$profile.bankName}</td>
                <td class="type-status">{($profile.autoGenerate)?'Yes':'No'}</td>
                <td class="type-action">
                    {if $profile.archivedDate eq null}
                        <a href="#" onclick="$app.module('invoice').addInvoice({$profile.profileId})" class="form-button" title="Create Invoice">
                            <img src="skin/images/icon-add-file.png" class="icon-image icon-size-small" alt="" />
                        </a>
                    {/if}
                        <a href="#" onclick="$app.module('invoice').showHistory({$profile.profileId})" class="form-button" title="Invoice History">
                            <img src="skin/images/icon-list.png" class="icon-image icon-size-small" alt="" />
                        </a>
                    {if $profile.archivedDate eq null}
                        <a href="#" onclick="$app.module('invoice').showProfile({$profile.profileId})" class="form-button" title="Edit Profile">
                            <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
                        </a>
                    {/if}
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="5" align="center">
                    No Profile
                </td>
            </tr>
            {/foreach}
        </tbody>
    </table>
</fieldset>
