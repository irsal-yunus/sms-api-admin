<fieldset class="content">
    <div class="action-container text-left" style="padding: 10px;">
        {if $archived eq null}
            <a href="#" class="form-button" onclick="$app.module('invoice').showClient('archived');" >
                <span class="form-button-text">Include Archived Client</span>
            </a>
        {else}
            <a href="#" class="form-button" onclick="$app.module('invoice').showClient();" >
                <span class="form-button-text">Show only non Archived Client</span>
            </a>
        {/if}
        <a href="#" class="form-button" onclick="$app.module('invoice').showDownloadAll();" title="Download All" style="float:right">
            <img src="skin/images/icon-download.png" class="form-button-image" alt="" />
            <span class="form-button-text">Download All</span>
        </a>
    </div>
    <table class="admin-simpletable invoice-table border-inside">
        <thead>
            <tr>
                <th class="zebra-even">Customer ID</th>
                <th class="zebra-even">Company Name</th>
                <th class="zebra-even">Profile Name</th>
                <th class="zebra-even">Payment Detail</th>
                <th class="zebra-even">Auto Generate</th>
                <th class="zebra-even">
                    <a href="#" onclick="$app.module('invoice').createProfile()" class="form-button" title="Add New Profile">
                        <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        <span class="form-button-text">Add New</span>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            {foreach $profiles as $profile}
            <tr>
                {if isset($profile->print)}
                <td class="type-status" rowspan="{$rowspan[$profile.clientId]}">{$profile.customerId}</td>
                <td class="type-text" rowspan="{$rowspan[$profile.clientId]}">{$profile.companyName}</td>
                {/if}
                <td class="type-text">{$profile.profileName}</td>
                <td class="type-text">{$profile.bankName}</td>
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
