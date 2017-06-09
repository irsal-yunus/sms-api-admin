{literal}
<script language="JavaScript">
    var rowIndex = 1;
    $( document ).ready(function(){
        loadUserDetail();
        
        $.validate({
            errorMessagePosition : 'inline',
        });
        
        $ (document)
            .on('change', '#list-user', function(){
               if($("#list-user :selected").length == 0){
                   $('#list-user option').prop('disabled', false);
                   $('#list-user').select2();
               }else if($("#list-user :selected").length == 1){
                    $('#list-user option').prop('disabled', 'disabled');
                    $('#list-user option').attr('title', 'This user is not on the same billing profile as current selected user');
                    $.ajax({
                         url        : 'services/billing.getUserBillingGroup.php',
                         type       : 'POST',
                         data       : {userID : $("#list-user :selected").val() },
                         dataType   : 'JSON',
                         success    : function (data) {
                            $.each(data, function(k,v){
                                    $('#list-user option[value="'+v.USER_ID+'"]').removeAttr('title');
                                    $('#list-user option[value="'+v.USER_ID+'"]').prop('disabled', false);
                            });
                            $('#list-user').select2();
                         },

                     });
                   
               }
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
    });
    
    function loadUserDetail(){
        $.ajax({
            url: 'services/billing.getUserDetail.php',
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                html        = '';
                defaultData = [];
                $('#selected-user option').each(
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