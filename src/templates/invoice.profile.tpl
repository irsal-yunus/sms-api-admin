<fieldset>
    <table class="admin-simpletable">
        <thead>
            <tr>
                <th class="zebra-odd">Company Name</th>
                <th class="zebra-even">Bank Name</th>
                <th class="zebra-odd">
                    <a href="#" onclick="$app.module('invoice').createProfile()" class="form-button" title="Add New Profile">
                        <img title="Register" src="skin/images/icon-add.png" class="form-button-image" alt="" />
                        <span class="form-button-text">Add New</span>
                    </a>
                </th>
            </tr>
        </thead>
        <tbody>
            {section name=profile loop=$profiles}
            <tr class="{cycle values="zebra-odd,zebra-even"}">
                <td class="type-phone">{$profiles[profile].companyName}</td>
                <td class="type-url">{$profiles[profile].bankName}</td>
                <td class="type-action">
                    <a href="#" onclick="$app.module('invoice').showProfile({$profiles[profile].profileId})" class="form-button" title="Edit Profile">
                        <img src="skin/images/icon-edit.png" class="icon-image icon-size-small" alt="" />
                    </a>
                </td>
            </tr>
            {/section}
        </tbody>
    </table>
</fieldset>
