{literal}
<script language="JavaScript">
    var rowIndex = 1;
    $( document ).ready(function(){
        addTieringRow();
        addOperatorRow();
        
        $(document)        
            .on('click','#add-tiering-field',function(e){
                addTieringRow();
            })
            .on('click','#add-operator-field',function(e){
                addOperatorRow();
            })
            .on('change','#input-type',function(e){
                selectedType = this.value;
                rowIndex = 1;
                if(selectedType == 'operator'){
                    $('#tiering-container').hide();
                    $('#operator-container').show();
                }else{
                    $('#operator-container').hide();
                    $('#tiering-container').show();
                }
            })
            .on('click', '.btn-operator-remove', function(){
                length = $('#operator-table').find('tbody tr').length;
                if (length > 1){
                    $(this).parent('th').parent('tr').remove();
                }
            })
            .on('click', '.btn-tiering-remove', function(){
                length = $('#tiering-table').find('tbody tr').length;
                if (length > 1){
                    $(this).parent('th').parent('tr').remove();
                }
            })
            
    });

    function addTieringRow(){
        rowId       = rowIndex;        
        newRow      =   '<tr>'
                            + '<th>'
                                    +'<img src="skin/images/icon-remove.png" class="form-button-image btn-tiering-remove" alt="Remove" width="13px" style="cursor:pointer;" />'
                            + '<th><input type="text" name="tiering_from"></th>'
                            + '<th><input type="text" name ="tiering_upto"></th>'
                            + '<th width="5%"><input type="checkbox" name="isMax" id="isMax" style="margin-top: 0.5 em;"></th>'
                            + '<th width="5%">Max</th>'
                            + '<th><input type="text" name ="price"></th>'
                        + '</tr>';
        $('#tiering-table tbody').append(newRow); 
        rowIndex++;
    }
  
    function addOperatorRow(){
        rowId       = rowIndex;
        newRow      =   '<tr>'
                            + '<th>'
                                    +'<img src="skin/images/icon-remove.png" class="form-button-image btn-operator-remove" alt="Remove" width="13px" style="cursor:pointer;" />'
                            + '</th>'
                            + '<th><input type="text" name="operator_name" list="operator_list" placeholder="Type keyword to search"></th>'
                            + '<th><input type="text"></th>'
                        + '</tr>';
        $('#operator-table tbody').append(newRow); 
        rowIndex++;
    }
</script>
{/literal}

<form id="clients-view-form" class="admin-tabform" action="#">
	<div class="panel">
		<div class="panel-header"><img src="skin/images/icon-history.png" class="icon-image" alt="" /><span>New Billing Profile</span></div>
		<div class="panel-body">
			<div class="panel-content">
				<fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                                        <label>Description</label>
                                            <input type="text" name="input-description" name="input-description">
                                        <span class="ui-helper-clearfix"></span>
                                        <label>Type</label>
                                            <select class="flexible-width" name="input-type" id="input-type">
                                                <option value="operator">Operator</option>
                                                <option value="tiering">Tiering</option>
                                               
                                            </select>
                                        <span class="ui-helper-clearfix"></span>
                                         <label>Settings</label>
                                            <div  id="operator-container" style="display:block;">
                                                <div style="max-height: 150px; margin-top:10px;overflow-x: auto;">
                                                        <table id="operator-table">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Operator name</th>
                                                                    <th>Price</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                                            </tbody>
                                                        </table>
                                                        <datalist id="operator_list">
                                                          <option value="Telkomsel">
                                                          <option value="Indosat">
                                                          <option value="XL">
                                                          <option value="Tri">
                                                        </datalist>
                                                        
                                                </div>
                                                <a href="#" class="form-button" style="margin:5px;float:left;margin-left: 110px;" id="add-operator-field">
                                                    <span class="form-button-text">Add Field</span>
                                                </a>
                                            </div>
                                            <div id="tiering-container" style="display:none;">
                                                <div style="max-height: 150px; margin-top:10px;overflow-x: auto;">
                                                        <table id="tiering-table">
                                                            <thead>
                                                                <tr align="left">
                                                                    <th>
                                                                    <th>From</th>
                                                                    <th>Up to</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th>Price</th>
                                                                    <th></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>

                                                </div>
                                                <a href="#" class="form-button" style="margin:5px;float:left;margin-left: 110px;" id="add-tiering-field">
                                                    <span class="form-button-text">Add Field</span>
                                                </a>
                                            </div>
				</fieldset>
                                
                                 <fieldset class="form-fieldset-submission" style="width: 100%;">
                                        <a href="#" class="form-button" onclick="$app.module('billing').storeBillingProfile()" style="margin:5px;float:left;">
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