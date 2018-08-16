<script>
    var billingData        = {$billingList|@json_encode nofilter};
    var userTiering        = {$userTiering|@json_encode nofilter};
    var usersEdit          = {(isset($user))?(json_encode($user)):'[]'};
</script>

{literal}
<script language="JavaScript">
    var rowIndex = 1;
    $( document ).ready(function(){
        if (usersEdit.length > 0) {
            $('#select-billing').val(usersEdit[0].BILLING_PROFILE_ID);
        }

        $.validate({
            errorMessagePosition : 'inline',
        });

        $("#list-user").select2({
            placeholder: "Select a user"
        });

        $('#btn-submit').on('click', function(e){
            e.preventDefault();
            $('#tieringGroup-form').submit();
        });

        $('#tieringGroup-form').on('submit', function(e){
            e.preventDefault();
            data = $(this).serializeArray();
            $app.module('billing').storeTieringGroup(data);
        });

        $('#select-billing').on('change', function(e){
            e.preventDefault();
            var selectedBillingID  = $(this).val();
            var usersById = userTiering[selectedBillingID];
            $("#list-user").html('').select2().select2({
                placeholder: "Select a user",
                data: usersById,
            });
        });

        //To filter user
        function find_in_object(my_object, my_criteria){
              return my_object.filter(function(obj) {
                return Object.keys(my_criteria).every(function(c) {
                  return obj[c] == my_criteria[c];
                });
              });
        }
    });

    function loadUserDetail(){
        $.ajax({
            url: 'services/billing.getUserDetail.php',
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                html        = '';
                defaultData = [];
                $('#list-user option').each(
                       function(index,el){
                           defaultData.push($(this).attr('value'));
                        }
                );
                $.each(data, function(k,v){
                    if($.inArray(v.USER_ID, defaultData ) == -1)
                        html += '<option value="'+v.USER_ID+'">'+v.USER_NAME+'</option>';
                });
                $('#list-user').append(html);
            },

        });
    }

</script>
{/literal}

<form id="tieringGroup-form" class="admin-tabform">
    <div class="panel">
                    <div class="panel-header"><img src="skin/images/icon-history.png" class="icon-image" alt="" /><span>New Tiering Group</span></div>
                        <div class="panel-body">
                            <div class="panel-content">
                                <fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                                    {if !$billingList}
                                        <div class='notification-container'>
                                            <label style="color:red;"><i>Please create a billing report first, before you create tiering group.</i></label>
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                    {/if}
                                        <input type="hidden" name="mode" value="{if isset($mode)}{$mode}{else}new{/if}">
                                        <input type="hidden" name="tieringGroupID" value="{if isset($tieringGroupID)}{$tieringGroupID}{/if}">
                                        <div>
                                            <label>Name</label>
                                            <input type="text" id="input-name" name="name" value='{if isset($tieringDetail['NAME'])}{$tieringDetail['NAME']}{/if}' data-validation="required">
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                         <div>
                                            <label>Biling Profile</label>
                                            <select class="flexible-width" id="select-billing" data-validation="required" style="margin-left:5px;">
                                                <option value="">-- Select Tiering --</option>
                                                {section name=list loop=$billingList}
                                                    <option value="{$billingList[list].BILLING_PROFILE_ID}">{$billingList[list].NAME}</option>
                                                {/section}
                                            </select>
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                        <div>
                                            <label>Description</label>
                                            <textarea rows="3" cols="20" name="description" id="text-description" name="input-description">{if isset($tieringDetail['DESCRIPTION'])}{$tieringDetail['DESCRIPTION']}{/if}</textarea>
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                        <div>
                                                <label>Users</label>
                                                <select id="list-user" name="user[]" multiple data-validation="required">
                                                    {if isset($user)}
                                                        {foreach from=$user item=item}
                                                            <option value='{$item['USER_ID']}' selected>{$item['USER_NAME']}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>
                                                <span style="font-size:10px;color:red;margin-left:110px;display:block;">* Select users which accumulate the same tiering</span>
                                                <span style="font-size:10px;color:red;margin-left:110px;display:block;">** You can only select users whose implement the same billing profile</span>
                                        </div>
                                </fieldset>
                                <fieldset class="form-fieldset-submission" style="width: 100%;">
                                        <a href="#" class="form-button" id="btn-submit" style="margin:5px;float:left;"  {if !$billingList} disabled {/if}>
                                                <img src="skin/images/icon-store.png" class="form-button-image" alt="" />
                                                <span class="form-button-text">Save</span>
                                        </a>
                                        <a href="#" class="form-button" onclick="$app.module('billing').showBilling()" style="margin:5px;float:left;">
						<img src="skin/images/icon-cancel.png" class="form-button-image" alt="" />
						<span class="form-button-text">Cancel</span>
					</a>
                                </fieldset>
                            </div>
                        </div>
                    </div>

</form>