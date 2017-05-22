{literal}
<script language="JavaScript">
    $( document ).ready(function(){
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
    
</script>
{/literal}

<form id="billing-view" class="admin-tabform" action="#" method="post" style="width: 100%;">
	<div id="billing-view-tabs" class="panel-tabs">
		<ul>
			<li><a href="#billing-profile-tab"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Billing Profile</span></a></li>
			<li><a href="#tiering-group-tab"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Tiering Group</span></a></li>
                        <li><a href="#report-group-tab"><img src="skin/images/icon-history.png" class="icon-image icon-size-small" alt="" /><span>Report Group</span></a></li>
                        
		</ul>
		<div id="billing-profile-tab">
                        <table id="apiuser-simpletable" class="admin-table">
                                <thead>
                                        <tr>
                                                <th class="type-nav" colspan="5">
                                                        <a href="#" class="form-button" onclick="$app.module('billing').newBillingProfile();">
                                                           <span class="form-button-text">New Billing Profile</span>
                                                        </a>
                                                </th>
                                        </tr>
                                        <tr>
                                                <th style="width: 20%;">Description</th>
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
                                    <tr>
                                        <td class="type-text">Billing Profile May</td>
                                        <td class="type-text">Tiering base</td>
                                        <td class="type-counter">2017-05-17</td>
                                        <td class="type-action">
                                                <a href="#" title="View Details" class="form-button" onclick="$app.module('billing').showBillingDetail()"><img src="skin/images/icon-view.png" class="icon-image" alt="" /></a>
                                                <a href="#" title="Edit" class="form-button" onclick="$app.module('billing').newBillingProfile()"><img src="skin/images/icon-edit.png" class="icon-image" alt="" /></a>
                                                <a href="#" title="Delete" class="form-button" onclick=""><img src="skin/images/icon-remove.png" class="icon-image" alt="" /></a>
                                        </td>
                                    </tr>
                                </tbody>
                        </table>
		</div><!-- END Billing profile tab-->
		
                <div id="tiering-group-tab">
                <div class="panel">
                    <div class="panel-header"><img src="skin/images/icon-history.png" class="icon-image" alt="" /><span>New Tiering Group</span></div>
                        <div class="panel-body">
                            <div class="panel-content">
                                    <fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                                            <label>Name</label>
                                            <input type="text" name="input-name" name="input-name">
                                            <span class="ui-helper-clearfix"></span>
                                            <label>Settings</label>
                                                    <select id="list-user" multiple style="height: 50%; min-width: 12%; overflow:auto; float:left;">
                                                        <option value="User A">User A</option>
                                                        <option value="User B">User B</option>
                                                        <option value="User C">User C</option>
                                                        <option value="User D">User D</option>
                                                    </select>
                                            <div>
                                                <a href="#" class="form-button" id="add_user" style="margin-left: 4px;margin-top: 9px;margin-right: 4px; float:left">></a>
                                                {*<span class="ui-helper-clearfix"></span>*}
                                                <a href="#" class="form-button" id="remove_user" style="margin-left: 4px;margin-top: 9px;margin-right: 4px; float:left"><</a>
                                            </div>    
                                            <select id="selected-user" style="height: 50%; min-width: 12%;" multiple>
                                            </select>      
                                    </fieldset>
                                    <fieldset class="form-fieldset-submission" style="width: 100%;">
                                            <a href="#" class="form-button" onclick="$app.module('billing').storeBillingProfile()" style="margin:5px;float:left;">
                                                    <img src="skin/images/icon-store.png" class="form-button-image" alt="" />
                                                    <span class="form-button-text">Save</span>
                                            </a>
                                            <a href="#" class="form-button" onclick="" style="margin:5px;float:left;">
                                                    <img src="skin/images/icon-cancel.png" class="form-button-image" alt="" />
                                                    <span class="form-button-text">Cancel</span>
                                            </a>

                                    </fieldset>
                            </div>
                        </div>
                    </div>
		</div><!-- END Tiering Group tab-->
                
                <div id="report-group-tab">
                    <div class="panel">
                        <div class="panel-header"><img src="skin/images/icon-history.png" class="icon-image" alt="" /><span>New Report Group</span></div>
                            <div class="panel-body">
                                <div class="panel-content">
                                        <fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                                                <label>Name</label>
                                                <input type="text" name="input-name" name="input-name">
                                                <span class="ui-helper-clearfix"></span>
                                                <label>Settings</label>
                                                        <select id="list-user" multiple style="height: 50%; min-width: 12%; overflow:auto; float:left;">
                                                            <option value="User A">User A</option>
                                                            <option value="User B">User B</option>
                                                            <option value="User C">User C</option>
                                                            <option value="User D">User D</option>
                                                        </select>
                                                <div>
                                                    <a href="#" class="form-button" id="add_user" style="margin-left: 4px;margin-top: 9px;margin-right: 4px; float:left">></a>
                                                    {*<span class="ui-helper-clearfix"></span>*}
                                                    <a href="#" class="form-button" id="remove_user" style="margin-left: 4px;margin-top: 9px;margin-right: 4px; float:left"><</a>
                                                </div>    
                                                <select id="selected-user" style="height: 50%; min-width: 12%;" multiple>
                                                </select>      
                                        </fieldset>
                                        <fieldset class="form-fieldset-submission" style="width: 100%;">
                                                <a href="#" class="form-button" onclick="$app.module('billing').storeBillingProfile()" style="margin:5px;float:left;">
                                                        <img src="skin/images/icon-store.png" class="form-button-image" alt="" />
                                                        <span class="form-button-text">Save</span>
                                                </a>
                                                <a href="#" class="form-button" onclick="" style="margin:5px;float:left;">
                                                        <img src="skin/images/icon-cancel.png" class="form-button-image" alt="" />
                                                        <span class="form-button-text">Cancel</span>
                                                </a>

                                        </fieldset>
                                </div>
                            </div>
                    </div>
		</div><!-- END Report Group tab-->
                
	</div><!-- END Tabs Container -->
</form>