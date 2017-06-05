{literal}
<script language="JavaScript">
    var rowIndex = 1;
    $( document ).ready(function(){
        loadUserDetail();
        
        $.validate({
            errorMessagePosition : 'inline',
        });
        
        $ (document)
            .on('click', '#btn-submit', function(){
                $('#tieringGroup-form').submit();
            })
            .on('change', '#list-user', function(){
               if($("#list-user :selected").length == 0){
                   $('#list-user option').prop('disabled', false);
                   $('#list-user').select2();
               }else if($("#list-user :selected").length == 1){
                    $.ajax({
                         url        : 'services/billing.getUserBillingGroup.php',
                         type       : 'POST',
                         data       : {userID : $("#list-user :selected").val() },
                         dataType   : 'JSON',
                         success    : function (data) {
                            $.each(data, function(k,v){
                                    $('#list-user option[value="'+v.USER_ID+'"]').prop('disabled', 'disabled');
                            });
                            $('#list-user').select2();
                         },

                     });
                   
               }
            }); 
        
        $("#list-user").select2({
            placeholder: "Select a user"
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

<form id="tieringGroup-form" class="admin-tabform" action='services/billing.storeTieringGroup.php' method="post">
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
                                            <input type="text" name="name" value='{if isset($tieringDetail['NAME'])}{$tieringDetail['NAME']}{/if}' data-validation="required">
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                        <div>
                                            <label>Description</label>
                                            <input type="text" name="description" name="input-description" value="{if isset($tieringDetail['DESCRIPTION'])}{$tieringDetail['DESCRIPTION']}{/if}" data-validation="required">
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                        <div>
                                                <label>Users</label>
                                                <select id="list-user" name="user[]" multiple style="height: 100%; min-width: 15%; overflow:auto; float:left;" data-validation="required">
                                                    {if isset($user)}
                                                        {foreach from=$user item=item}
                                                            <option value='{$item['USER_ID']}' selected>{$item['USER_NAME']}</option>
                                                        {/foreach}
                                                    {/if}
                                                </select>
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