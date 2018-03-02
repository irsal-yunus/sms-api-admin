{literal}
<script language="JavaScript">
    $( document ).ready(function(){
        loadUserDetail();
        $('#add_user').click(function(){
            selectedUser    = $('#list-user').val();
            var html        = "";
            
            if(selectedUser){
                $.each(selectedUser,function(key,val){
                    $("#list-user option[value='"+val+"']").remove();
                    html        += '<option value="'+val+'">'+val+'</option>';
                });
                $('#selected-user').append(html);
            }
        });
        
        $('#remove_user').click(function(){
            selectedUser    = $('#selected-user').val();
            var html        = "";
            
            if(selectedUser){
                $.each(selectedUser,function(key,val){
                    $("#selected-user option[value='"+val+"']").remove();
                    html        += '<option value="'+val+'">'+val+'</option>';
                });
                $('#list-user').append(html);
            }
        });
    });
    
    function loadUserDetail(){
        $.ajax({
            url: 'services/billing.getUserDetail.php',
            type: 'POST',
            dataType: 'JSON',
            success: function (data) {
                html = '';
                $.each(data, function(k,v){
                    html += '<option value="'+v.USER_NAME+'">'+v.USER_NAME+'</option>';
                });
                $('#list-user').append(html);
            },
            
        });
    }
</script>
{/literal}
<div style="padding: 3px;">
    <a href="#" onclick="$app.module('billing').viewMessageFilterPage();">SMS Content Department Filter (Adira)</a>
</div>
<form id="billing-view" class="admin-tabform" action="#" method="post" style="width: 100%;">
	<div id="billing-view-tabs" class="panel-tabs">
		<ul>
			<li class="{if $tab == "billing"}ui-tabs-selected{/if}"><a href="#billing-profile-tab"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Billing Profile</span></a></li>
			<li class="{if $tab == "tiering"}ui-tabs-selected{/if}"><a href="#tiering-group-tab"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Tiering Group</span></a></li>
                        <li class="{if $tab == "report"}ui-tabs-selected{/if}"><a href="#report-group-tab"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Report Group</span></a></li>
                        
		</ul>
		<div id="billing-profile-tab">
                        <table id="billing-profile-table" class="admin-table">
                                <thead>
                                        <tr>
                                                <th class="type-nav" colspan="5">
                                                        <a href="#" class="form-button" onclick="$app.module('billing').newBillingProfile();">
                                                           <span class="form-button-text">New Billing Profile</span>
                                                        </a>
                                                </th>
                                        </tr>
                                        <tr>
                                                <th style="width: 20%;">Name</th>
                                                <th style="width: 20%;">Price Base</th>
                                                 <th style="width: 20%;">Created At</th>
                                                <th style="width: 40%;">Action(s)</th>
                                        </tr>
                                </thead>
                                <tfoot>
                                        <tr>
                                                <th colspan="9">
                                                        &nbsp;
                                                </th>
                                        </tr>
                                </tfoot>
                                <tbody>
                                    {section name=list loop=$billingList}
                                    <tr>
                                        <td class="type-text">{$billingList[list].NAME}</td>
                                        <td class="type-text">{$billingList[list].BILLING_TYPE}</td>
                                        <td class="type-text">{$billingList[list].CREATED_AT}</td>
                                        <td class="type-action">
                                                <a href="#" title="Edit" class="form-button" onclick="$app.module('billing').newBillingProfile({$billingList[list].BILLING_PROFILE_ID},'{$billingList[list].BILLING_TYPE}','edit')"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
                                                <a href="#" title="Delete" class="form-button" onclick="$app.module('billing').deleteBillingProfile('{$billingList[list].BILLING_PROFILE_ID}','{$billingList[list].BILLING_TYPE}')"><img src="skin/images/icon-remove.png" class="icon-image" alt="" /></a>
                                        </td>
                                    </tr>
                                    {/section}  
                                </tbody>
                        </table>
		</div><!-- END Billing profile tab-->
		
                <div id="tiering-group-tab">
                    <table id="tiering-group-table" class="admin-table">
                            <thead>
                                    <tr>
                                            <th class="type-nav" colspan="5">
                                                    <a href="#" class="form-button" onclick="$app.module('billing').newTieringGroup();">
                                                       <span class="form-button-text">New Tiering Group</span>
                                                    </a>
                                            </th>
                                    </tr>
                                    <tr>
                                            <th style="width: 20%;">Name</th>
                                            <th style="width: 20%;">Description</th>
                                             <th style="width: 20%;">Created At</th>
                                            <th style="width: 40%;">Action(s)</th>
                                    </tr>
                            </thead>
                            <tfoot>
                                    <tr>
                                            <th colspan="9">
                                                    &nbsp;
                                            </th>
                                    </tr>
                            </tfoot>
                            <tbody>
                                {section name=list loop=$tieringGroupList}
                                <tr>
                                    <td class="type-text">{$tieringGroupList[list].NAME}</td>
                                    <td class="type-text">{$tieringGroupList[list].DESCRIPTION}</td>
                                    <td class="type-text">{$tieringGroupList[list].CREATED_AT}</td>
                                    <td class="type-action">
                                            <a href="#" title="Edit" class="form-button" onclick="$app.module('billing').newTieringGroup({$tieringGroupList[list].BILLING_TIERING_GROUP_ID},'edit')"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
                                            <a href="#" title="Delete" class="form-button" onclick="$app.module('billing').deleteTieringGroup({$tieringGroupList[list].BILLING_TIERING_GROUP_ID})"><img src="skin/images/icon-remove.png" class="icon-image" alt="" /></a>
                                    </td>
                                </tr>
                                {/section}  
                            </tbody>
                    </table>
		</div><!-- END Tiering Group tab-->
                
                <div id="report-group-tab">
                    <table id="report-group-table" class="admin-table">
                            <thead>
                                    <tr>
                                            <th class="type-nav" colspan="5">
                                                    <a href="#" class="form-button" onclick="$app.module('billing').newReportGroup();">
                                                       <span class="form-button-text">New Report Group</span>
                                                    </a>
                                            </th>
                                    </tr>
                                    <tr>
                                            <th style="width: 20%;">Name</th>
                                            <th style="width: 20%;">Description</th>
                                             <th style="width: 20%;">Created At</th>
                                            <th style="width: 40%;">Action(s)</th>
                                    </tr>
                            </thead>
                            <tfoot>
                                    <tr>
                                            <th colspan="9">
                                                    &nbsp;
                                            </th>
                                    </tr>
                            </tfoot>
                            <tbody>
                                {section name=list loop=$reportGroupList}
                                <tr>
                                    <td class="type-text">{$reportGroupList[list].NAME}</td>
                                    <td class="type-text">{$reportGroupList[list].DESCRIPTION}</td>
                                    <td class="type-text">{$reportGroupList[list].CREATED_AT}</td>
                                    <td class="type-action">
                                            <a href="#" title="Edit" class="form-button" onclick="$app.module('billing').newReportGroup({$reportGroupList[list].BILLING_REPORT_GROUP_ID},'edit')"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
                                            <a href="#" title="Delete" class="form-button" onclick="$app.module('billing').deleteReportGroup({$reportGroupList[list].BILLING_REPORT_GROUP_ID})"><img src="skin/images/icon-remove.png" class="icon-image" alt="" /></a>
                                    </td>
                                </tr>
                                {/section}  
                            </tbody>
                    </table>   
		</div><!-- END Report Group tab-->
                
	</div><!-- END Tabs Container -->
</form>