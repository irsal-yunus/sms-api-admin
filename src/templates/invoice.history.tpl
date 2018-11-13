<fieldset class="content">
    <h2 class="type-action">Invoice History</h2><br>
    <div class="action-container text-right">
        {if $profile.archivedDate eq null}
        <a href="#" class="form-button" onclick="$app.module('invoice').showProfile({$profile.profileId});" title="Profile Details">
            <img src="skin/images/icon-profile.png" class="icon-image icon-size-small" alt="" />
            <span class="form-button-text"></span>
        </a>
        {/if}
    </div>
    <dl class="admin-definitions">
        <dt>Customer ID</dt>
        <dd>{$profile.customerId}</dd>
        <dt>Client Name</dt>
        <dd><a href="#" onclick="$app.module('client').viewClient({$profile.clientId});">{$profile.companyName}</a></dd>
        <dt>API Users</dt>
        <dd>
            {foreach $apiUsers as $id => $userName}
            <a href="#" title="View Details User" onclick="$app.module('apiuser').showUserDetails({$id});">
                {$userName}
            </a>
            {if not $userName@last},{/if}
            {/foreach}
        </dd>
        <dt>Contact Phone</dt>
        <dd>{$profile.contactPhone}</dd>
        <dt>Contact Email</dt>
        <dd>{$profile.contactEmail}</dd>
        <dt>Client Address</dt>
        <dd>{$profile.contactAddress}</dd>
    </dl>
    <span class="ui-helper-clearfix"></span><br>
    {include 'invoice.history.table.tpl' invoices=$invoices profile=$profile}

</fieldset>
