{literal}
<script language="JavaScript">
    var rowIndex            = 1,
        rowIndexOperator    = $('#operator-table tbody tr').length,
        rowIndexTiering     = $('#tiering-table tbody tr').length,
        dataOperator        = [];

    /**
     * Removes all handlers attached to the elements
     */
    $(document).unbind();
    
    $( document ).ready(function(){
        loadUserDetail();
        
        $.validate({
            errorMessagePosition : 'inline',
        });
        
        /**
         * 
         * Set masking number for tiering fied from, up to and price
         */
        $("#tiering-table :input:not([readonly])").mask('000.000.000.000.000', {reverse: true});

        /*check type*/
        var typeValue = $('#select-type').data('val');
        if(typeValue){
            $('#select-type').val(typeValue.toLowerCase()).trigger('change');
            showSettingContainer(typeValue);
        }
        $(document)        
            .on('change','#select-type',function(e){
                selectedType = this.value;
                rowIndex = 1;
                showSettingContainer(selectedType);
            })
            .on('click', '.btn-operator-remove', function(){
                var length = $('#operator-table').find('tbody tr').length;
                if (length > 1 && $(this).parent('th').parent('tr')){
                    $(this).parent('th').parent('tr').remove();
                }
            })
            .on('click', '.btn-tiering-remove', function(){
                var length = $('#tiering-table').find('tbody tr').length;
                if (length > 1 && $(this).parent('th').parent('tr') ){
                    $(this).parent('th').parent('tr').remove();
                    $('#tiering-table tr:last .tiering-to')
                            .val('MAX')
                            .attr('readonly', true);
                }
            })
            .on('change', 'input[name="isMax"]', function(){
                if ( $(this).is(':checked') ){
                    
                    $(this)
                       .parents('tr')
                       .find('input.tiering-to')
                       .val('MAX');
                       
                }else{
                    $(this)
                           .parents('tr')
                           .find('input.tiering-to')
                           .val('');
                }
            })
            .on('click', '.btn-tiering-add', function(){
                $('#tiering-table tr:last .tiering-to')
                        .val('')
                        .removeAttr('readonly');
                
                addTieringRow(this);
        
                $('#tiering-table tr:last .tiering-to')
                    .val('MAX')
                    .attr('readonly',true); 
        
            })
            .on('keyup', '.tiering-to', function(e){
                var uptoVal = parseInt($(this).cleanVal());
                
                /* fill the next tiering's from value based on current tiering's up to value */
                if(uptoVal > 0){
                    $(this).parents('tr').next().find('.tiering-from')
                           .val(uptoVal + 1)
                           .trigger('input');
                } else {
                    $(this).parents('tr').next().find('.tiering-from')
                           .val('');
                }
                
            })
            .on('keyup', '.tiering-from', function(e){
                var fromVal = parseInt($(this).cleanVal());
                
                /* fill the previous tiering's up to value based on current tiering's from value */
                if(fromVal > 0){
                    $(this).parents('tr').prev().find('.tiering-to')
                           .val(fromVal - 1)
                           .trigger('input');
                } else {
                    $(this).parents('tr').prev().find('.tiering-to')
                           .val('');
                }
                
            });
            

            $('#add-operator-field').click(function(){
                addOperatorRow();
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
            
            $('#btn-submit').on('click', function(e){
                e.preventDefault();
                $('#billingProfile-form').submit();
            });
            
            $('#billingProfile-form').on('submit', function(e){
                e.preventDefault();
                
                /* unmask all input in tiering tables to get the clean value without separator */
                $("#tiering-table :input:not([readonly])").unmask();
                $('#tiering-table .form-error').remove();
                
                var from = $('.tiering-from').serializeArray(),
                    to = $('.tiering-to').serializeArray(),
                    rowParent = $('#tiering-table').find('tr'),
                    rowT,
                    errorSpan;
                
                for(var i = 0; i < from.length-1; i++){
                    /* Check if there is a gap in tiering range before submit the form */
                    rowT = rowParent.eq(i+1);
                   
                    if(parseInt(from[i].value) > parseInt(to[i].value)){
                       
                        /* display the error notification for gap's value*/
                        errorSpan = '<span class="help-block form-error">Tiering \'up to\' must be greater than Tiering From</span>';
                        
                        $(errorSpan).insertAfter($(rowT).find('input:text.tiering-to'));
                        $(rowT)
                                .find('input:text.tiering-to')
                                .parent('th')
                                .addClass('has-error');
                        $("#tiering-table :input:not([readonly])").mask('000.000.000.000.000', {reverse: true});
                        
                        return;
                    }
                    
                    var diff = from[i+1].value - to[i].value;
                    if(diff > 1){
                        /* add additional empty row for gap range*/
                        addTieringRow(rowT);
                        
                        /* gap's range value*/
                        var rangeX = parseInt(to[i].value) + 1;
                        var rangeY = parseInt(from[i+1].value) - 1;
                        
                        /* display the error notification for gap's value*/
                        errorSpan = '<span class="help-block form-error">Range definition missing for value range '+rangeX+'-'+rangeY+'</span>';
                        
                        $(errorSpan).insertAfter($(rowT).next().find('input:text.tiering-from'));
                        $(rowT)
                                .next()
                                .find('input:text.tiering-from')
                                .parent('th')
                                .addClass('has-error');
                        
                        /* mask all input in tiering tables*/
                        $("#tiering-table :input:not([readonly])").mask('000.000.000.000.000', {reverse: true});
                        
                        return;
                    }  
                    
                    /**
                     * check if range is less than range before
                     */
                    if(parseInt(to[i].value) > parseInt(from[i+1].value)) {
                        errorSpan = '<span class="help-block form-error">This range should be greater than range before</span>';
                        $(errorSpan).insertAfter($(rowParent.eq(i+1)).next().find('input:text.tiering-from'));
                        $("#tiering-table :input:not([readonly])").mask('000.000.000.000.000', {reverse: true});
                        
                        return;
                    }
                }
                var data = $(this).serializeArray();
                $app.module('billing').storeBillingProfile(data);
            });

    });

    function addTieringRow(element){
        rowId       = rowIndexTiering;        
        newRow      =   '<tr>'
                            + '<th>'
                                    +'<img src="skin/images/icon-remove.png" class="form-button-image btn-tiering-remove" alt="Remove" width="13px" style="cursor:pointer;" />'
                            + '</th>'
                            + '<th><input type="text" class="tiering-from" name="tiering['+rowId+'][from]" data-validation="required"></th>'
                            + '<th><input type="text" class="tiering-to" name ="tiering['+rowId+'][to]" value="" id="tiering_upto" data-validation="required"></th>'
                            + '<th><input type="text" class="tiering-price" name ="tiering['+rowId+'][price]" data-validation="required"></th>'
                            + '<th>'
                                +'<img src="skin/images/icon-add.png" class="form-button-image btn-tiering-add" alt="Add New Field" width="13px" style="cursor:pointer;" />'
                                +'</th>'
                        + '</tr>';
        
        $(newRow).insertAfter($(element).closest('tr'));
        
        rowIndexTiering++;
    }
  
    function addOperatorRow(data){
        data = data || {OP_ID : "DEFAULT", PER_SMS_PRICE : ""};
        rowId       = rowIndexOperator;
        note        = rowId == 0 ? ' * This price will be implemented if destination prefix doesn\'t found on listed operator' : '';
        newRow      =   '<tr>'
                            + '<th>'
                                + '<img src="skin/images/icon-remove.png" class="form-button-image btn-operator-remove" alt="Remove" width="13px" style="cursor:pointer;"/>'
                            + '</th>'
                            + '<th>'
                            +   '<select class="operator_list" name="operatorID['+rowId+'][operator]"  data-validation="required" data-initvalue="'+data.OP_ID+'"></select>'
                            + '</th>'
                            + '<th><input type="text" name="operatorID['+rowId+'][price]" data-validation="number" value="'+data.PER_SMS_PRICE+'"><th>'
                            + '<td> <span style="font-size: 10px; color:red;">'+note+'</span></td>'
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
                    var html = '';
                    
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
                var html        = '';
                var defaultData = [];
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
    
    function showSettingContainer(type){
        if(type.toLowerCase() == 'operator'){
           $('#tiering-container').hide();
           $('#operator-container').show();
        }else{
           $('#operator-container').hide();
           $('#tiering-container').show();
        }
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
                                            <textarea id="text-description" name="description">{if isset($description['DESCRIPTION'])}{$description['DESCRIPTION']}{/if}</textarea>
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
                                                <span style="font-size:10px;color:red;margin-left:110px;display:block;">* Select users whose implement this billing profile</span>
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
                                                                    <th>Price</th>  
                                                                     <th></th> 
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                    {if !isset($tieringSettings)}
                                                                        <tr>
                                                                                <th>
                                                                                    <img src='skin/images/icon-remove.png' class='form-button-image btn-tiering-remove' alt='Remove' width='13px' style='cursor:pointer;' />
                                                                                </th>
                                                                                <th><input type='text' class='tiering-from' name='tiering[0][from]' value='0' data-validation='required' readonly></th>
                                                                                <th><input type='text' class='tiering-to' name ='tiering[0][to]' id='tiering_upto' value ='MAX' data-validation='required' readonly></th>
                                                                                <th><input type='text' class='tiering-price' name ='tiering[0][price]' value='' data-validation='required'></th>
                                                                                <th>
                                                                                    <img src='skin/images/icon-add.png' class='form-button-image btn-tiering-add' alt='Add New Field' width='13px' style='cursor:pointer;' />
                                                                                </th>
                                                                            </tr>
                                                                    {else}
                                                                        {foreach from=$tieringSettings key=key item=item}
                                                                            <tr>
                                                                                <th>
                                                                                    <img src='skin/images/icon-remove.png' class='form-button-image btn-tiering-remove' alt='Remove' width='13px' style='cursor:pointer;' />
                                                                                </th>
                                                                                <th><input type='text' class='tiering-from' name='tiering[{$key}][from]' value='{$item['SMS_COUNT_FROM']}' data-validation='required' {if $key == 0} readonly {/if}></th>
                                                                                <th><input type='text' class='tiering-to' name ='tiering[{$key}][to]' id='tiering_upto' value='{$item['SMS_COUNT_UP_TO']}' data-validation='required' {if $item['SMS_COUNT_UP_TO'] == 'MAX'} readonly {/if}></th>
                                                                                <th><input type='text' class='tiering-price' name ='tiering[{$key}][price]' value='{$item['PER_SMS_PRICE']}' data-validation='required'></th>
                                                                                <th>
                                                                                    <img src='skin/images/icon-add.png' class='form-button-image btn-tiering-add' alt='Add New Field' width='13px' style='cursor:pointer;' />
                                                                                </th>
                                                                            </tr>
                                                                        {/foreach}
                                                                    {/if}
                                                            </tbody>
                                                        </table>

                                                </div>
                                            </div>
				</fieldset>
                                
                                 <fieldset class="form-fieldset-submission" style="width: 100%;">
                                        <a href="#" id="btn-submit" class="form-button" style="margin:5px;float:left;">
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