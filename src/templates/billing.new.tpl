{literal}
<script language="JavaScript">
    var rowIndex            = 1,
        rowIndexOperator    = $('#operator-table tbody tr').length,
        rowIndexTiering     = $('#tiering-table tbody tr').length
        dataOperator        = [];
    $( document ).ready(function(){
        loadUserDetail();
        
        $.validate({
            errorMessagePosition : 'inline',
        });
        

        /*check type*/
        var typeValue = $('#select-type').data('val');
        if(typeValue){
            $('#select-type').val(typeValue.toLowerCase()).trigger('change');
            showSettingContainer(typeValue);
        }
        $(document)        
            .on('click','#add-tiering-field',function(e){
                addTieringRow();
            })
            .on('click','#add-operator-field',function(e){
                addOperatorRow();
            })
            .on('change','#select-type',function(e){
                selectedType = this.value;
                rowIndex = 1;
                showSettingContainer(selectedType);
            })
            .on('click', '.btn-operator-remove', function(){
                length = $('#operator-table').find('tbody tr').length;
                if (length > 1 && $(this).parent('th').parent('tr')){
                    $(this).parent('th').parent('tr').remove();
                }
            })
            .on('click', '.btn-tiering-remove', function(){
                length = $('#tiering-table').find('tbody tr').length;
                if (length > 1 && $(this).parent('th').parent('tr')){
                    $(this).parent('th').parent('tr').remove();
                }
            })
            .on('change', 'input[name="isMax"]', function(){
                if ( $(this).is(':checked') ){
                    
                    $(this)
                       .parents('tr')
                       .find('input[id="tiering_upto"]')
                       .val('MAX');
                       
                }else{
                    $(this)
                           .parents('tr')
                           .find('input[id="tiering_upto"]')
                           .val('');
                }
            });
            
            var operatorValue = $('#operator-table tbody').data('operator') || [];
            loadOperator().done(function(){                
                if(operatorValue.length > 0){
                    for(i=0;i< operatorValue.length;i++){
                        addOperatorRow(operatorValue[i]);                    
                    }
                }else{
                    addOperatorRow();
                }
                $('select[name="operatorID[0][operator]"]').attr('disabled', 'disabled')
                                                            .parent()
                                                            .append(
                                                                '<input type="hidden" name="operatorID[0][operator]" value="DEFAULT">'
                                                            );
                $('select[name="operatorID[0][operator]"]').attr('disabled', 'disabled');
            });
            
            
            $("#list-user").select2({
                placeholder: "Select a user"
            });

    });

    function addTieringRow(){
        rowId       = rowIndexTiering;        
        newRow      =   '<tr>'
                            + '<th>'
                                    +'<img src="skin/images/icon-remove.png" class="form-button-image btn-tiering-remove" alt="Remove" width="13px" style="cursor:pointer;" />'
                            + '</th>'
                            + '<th><input type="text" name="tiering['+rowId+'][from]" data-validation="number"></th>'
                            + '<th><input type="text" name ="tiering['+rowId+'][to]" id="tiering_upto" data-validation="required"></th>'
                            + '<th width="5%"><input type="checkbox" name="isMax" style="margin-top: 0.5 em;"></th>'
                            + '<th width="5%" style="padding-top: 6px;">Max</th>'
                            + '<th><input type="text" name ="tiering['+rowId+'][price]" data-validation="number"></th>'
                        + '</tr>';
        $('#tiering-table tbody').append(newRow); 
        $('.scroll-container').scrollTop($('#operator-table').height());
    }
  
    function addOperatorRow(data){
        data = data || {OP_ID : "DEFAULT", PER_SMS_PRICE : ""};
        rowId       = rowIndexOperator;
        newRow      =   '<tr>'
                            + '<th>'
                                + '<img src="skin/images/icon-remove.png" class="form-button-image btn-operator-remove" alt="Remove" width="13px" style="cursor:pointer;"/>'
                            + '</th>'
                            + '<th>'
                            +   '<select class="operator_list" name="operatorID['+rowId+'][operator]"  data-validation="required" data-initvalue="'+data.OP_ID+'"></select>'
                            + '</th>'
                            + '<th><input type="text" name="operatorID['+rowId+'][price]" data-validation="number" value="'+data.PER_SMS_PRICE+'"><th>'
                        + '</tr>';
        $('#operator-table tbody').append(newRow); 
        $('select[name="operatorID['+rowId+'][operator]"]').select2({
              placeholder: "Select operator",
              data : dataOperator
        }).val(data.OP_ID).trigger('change');
        $('.scroll-container').scrollTop($('#operator-table').height());
        rowIndexOperator++;

    }
    
    function loadOperator(){
        return $.ajax({
                url: 'services/billing.getoperatorList.php',
                type: 'POST',
                dataType: 'JSON',
                success: function (data) {
                    html = '';

                    $.each(data, function(k,v){
                        dataOperator.push({id:v.OP_ID,text:v.OP_ID});
                        html += '<option value="'+v.OP_ID+'">'+v.OP_ID+'</option>';
                    });
                    $('#operator_list').append(html);
                },

            });
    }
    
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
    
    function showSettingContainer(type){
        if(type.toLowerCase() == 'operator'){
           $('#tiering-container').hide();
           $('#operator-container').show();
        }else{
           $('#operator-container').hide();
           $('#tiering-container').show();
        }
    }
    
    function storeBilling(){
        data = $('#billingProfile-form').serializeArray();
        $app.module('billing').storeBillingProfile(data);
    }
   
</script>
{/literal}

<form id="billingProfile-form" class="admin-xform">
	<div class="panel">
		<div class="panel-header"><img src="skin/images/icon-history.png" class="icon-image" alt="" /><span>New Billing Profile</span></div>
		<div class="panel-body">
			<div class="panel-content">
				<fieldset class="float-centre" style="padding-bottom: 10px;padding-top: 10px;">
                                        <input type="hidden" name="mode" value="{if isset($mode)}{$mode}{else}new{/if}">
                                        <input type="hidden" name="billingProfileID" value="{if isset($billingProfileID)}{$billingProfileID}{/if}">
                                        <div>
                                            <label>Name</label>
                                            <input type="text" id="input-name" name="name" value="{if isset($description['NAME'])}{$description['NAME']}{/if}" data-validation="required">
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                        <div>
                                            <label>Description</label>
                                            <textarea rows="40" cols="20" id="text-description" name="description" value="{if isset($description['DESCRIPTION'])}{$description['DESCRIPTION']}{/if}" data-validation="required" ></textarea>
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                        <div>
                                            <label>Type</label>
                                            <select class="flexible-width" name="price_based" id="select-type" {if isset($mode) && $mode == 'edit'}disabled{/if} data-val='{if isset($description['BILLING_TYPE'])}{$description['BILLING_TYPE']}{/if}' data-validation="required" style="margin-left:5px;">
                                                    <option value="operator">Operator</option>
                                                    <option value="tiering">Tiering</option>
                                            </select> 
                                        </div>
                                        {if isset($mode) && $mode == 'edit'}
                                            <input type="hidden" id="price_based" name="price_based" value="{$description['BILLING_TYPE']}">
                                        {/if}
                                        <span class="ui-helper-clearfix"></span>
                                        <div>
                                            <label>Users</label>
                                            <select id="list-user" name ="user[]" multiple="multiple">
                                                <div style="max-height: 100px;overflow-y:scroll;overflow-x:hidden;">
                                                {if isset($user)}
                                                    {foreach from=$user item=item}
                                                        <option value='{$item['USER_ID']}' selected>{$item['USER_NAME']}</option>
                                                    {/foreach}
                                                {/if}
                                                </div>
                                            </select>
                                        </div>
                                        <span class="ui-helper-clearfix"></span>
                                        <label>Settings</label>
                                            <div  id="operator-container" style="display:block;">
                                                <div class="scroll-container" style="max-height: 150px; min-height:50px;margin-top:10px;overflow-x: auto;">
                                                        <table id="operator-table">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Operator name</th>
                                                                    <th>Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody data-operator="{if isset($operatorSettings)}{htmlspecialchars(json_encode($operatorSettings))}{/if}">
                                                            </tbody>
                                                        </table>
                                        
                                                </div>
                                                <a href="#" class="form-button" style="margin:5px;float:left;margin-left: 110px;" id="add-operator-field">
                                                    <span class="form-button-text">Add Field</span>
                                                </a>
                                            </div>
                                            <div id="tiering-container" style="display:none;">
                                                <div class="scroll-container" style="max-height: 150px; margin-top:10px;overflow-x: auto;">
                                                        <table id="tiering-table">
                                                            <thead>
                                                                <tr align="left">
                                                                    <th></th>
                                                                    <th>From</th>
                                                                    <th>Up to</th>
                                                                    <th></th>
                                                                    <th></th>
                                                                    <th>Price</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                    {if !isset($tieringSettings)}
                                                                        <tr>
                                                                                <th>
                                                                                    <img src='skin/images/icon-remove.png' class='form-button-image btn-tiering-remove' alt='Remove' width='13px' style='cursor:pointer;' />
                                                                                </th>
                                                                                <th><input type='text' name='tiering[0][from]' value='0' data-validation="number"></th>
                                                                                <th><input type="text" name ="tiering[0][to]" value='' data-validation="required"></th>
                                                                                <th width='5%'><input type='checkbox' name='isMax' style='margin-top: 0.5 em;'></th>
                                                                                <th width='5%' style="padding-top: 6px;">Max</th>
                                                                                <th><input type='text' name ='tiering[0][price]' value='' data-validation="number"></th>
                                                                            </tr>
                                                                    {else}
                                                                        {foreach from=$tieringSettings item=item}
                                                                            <tr>
                                                                                <th>
                                                                                    <img src='skin/images/icon-remove.png' class='form-button-image btn-tiering-remove' alt='Remove' width='13px' style='cursor:pointer;' />
                                                                                </th>
                                                                                <th><input type='text' name='tiering[0][from]' value='{$item['SMS_COUNT_FROM']}' data-validation="number"></th>
                                                                                <th><input type='text' name ='tiering[0][to]' value='{$item['SMS_COUNT_UP_TO']}' data-validation="required"></th>
                                                                                <th width='5%'><input type='checkbox' name='isMax' style='margin-top: 0.5 em;' {if $item['SMS_COUNT_UP_TO'] == 'MAX'} checked {/if}></th>
                                                                                <th width='5%' style="padding-top: 6px;">Max</th>
                                                                                <th><input type='text' name ='tiering[0][price]' value='{$item['PER_SMS_PRICE']}' data-validation="number"></th>
                                                                            </tr>
                                                                        {/foreach}
                                                                    {/if}
                                                            </tbody>
                                                        </table>

                                                </div>
                                                <a href="#" class="form-button" style="margin:5px;float:left;margin-left: 110px;" id="add-tiering-field">
                                                    <span class="form-button-text">Add Field</span>
                                                </a>
                                            </div>
				</fieldset>
                                
                                 <fieldset class="form-fieldset-submission" style="width: 100%;">
                                        <a href="#" id="btn-submit" class="form-button" onclick="storeBilling()" style="margin:5px;float:left;">
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