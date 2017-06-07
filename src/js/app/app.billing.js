/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

(function($, $1, $app){
$app.ready(function($app){
var MODULE_NAME='billing';
if($app.hasModule(MODULE_NAME))
	return;
var mod = {}; //implementation

function title(text){
	try {
		var title = 'Billing Management';
		if($.trim(text) != '')
			title += ' > '+text;
		$app.title (title);
	} catch (ex) {
		$1.error("[mod:client#title] Error.",ex);
	}
}
/**
 * Show Billing profile
 * @param options Displaying options
 *                i.e., highlight
 *
 */
mod.showBilling = function(options){
    try {
        var args;
        if((typeof options != 'object') || $.isEmptyObject(options)){
                args = null;
        }else{
                args = options;
        }
        $app.content('billing.view', args, function(){
                title('Billing Report');
        });
    } catch (ex) {
            $1.error("[mod:apiuser.showBilling] Error.",ex);
    }
};

mod.showBillingDetail = function(options){
    try {
            $app
                .form
                .openDownloadAllReportMesasge(
                    'apiuser.reportDownloadAll', 
                    false, 
                    'Download Billing Report', 
                    {
                        height  : 175,
                        width   : 260
                    }, 
                    function() {}
                );
    } catch (ex) {
            $1.error("[mod:apiuser.reportBilling] Error.",ex);
    }
};

mod.newBillingProfile = function(billingProfileID, billingType, mode){
    try {
        var titles = mode == 'update'? 'Update Biling Profile' : 'New Billing Profile';
        $app.content(
                    'billing.new', 
                    {
                        billingProfileID    : billingProfileID, 
                        billingType         : billingType, 
                        mode                : mode
                    }, function(){
                        title(titles);
        });
    } catch(ex) {
        $1.error("[mod:billing.newBillingProfile] Error.",ex);
    }
};

mod.storeBillingProfile = function(data){
    try {
        $app.content(
                    'billing.storeBillingProfile', 
                    data,
                    function(){
        });
        
    } catch(ex) {   
        
    }
};

mod.newTieringGroup = function(tieringID, mode){
    try {
        var titles = mode == 'update'? 'Update Tiering Group' : 'New Tiering Group';
        $app.content(
                    'billing.newTiering', 
                    {
                        tieringID    : tieringID, 
                        mode         : mode
                    }, function(){
                        title(titles);
        });
    } catch(ex) {
        $1.error("[mod:billing.newTiering] Error.",ex);
    }
};

mod.storeTieringGroup = function(data){
    try {
        $app.content(
                    'billing.storeTieringGroup', 
                    data,
                    function(){
        });
        
    } catch(ex) {   
        
    }
};

mod.newReportGroup = function(reportID, mode){
    try {
        var titles = mode == 'update'? 'Update Report Group' : 'New Report Group';
        $app.content(
                    'billing.newReport', 
                    {
                        reportID        : reportID, 
                        mode            : mode
                    }, function(){
                        title(titles);
        });
    } catch(ex) {
        $1.error("[mod:billing.newReport] Error.",ex);
    }
};

mod.storeReportGroup = function(data){
    try {
        $app.content(
                    'billing.storeReportGroup', 
                    data,
                    function(){
        });
        
    } catch(ex) {   
        
    }
};

mod.deleteBillingProfile = function(billingProfileID, billingType){
    try{
            var title = 'Delete Confirmation';
            $app
                .form
                .openConfirmationDialog(
                    'billing.deleteConfirmation', 
                    {
                        billingProfileID : billingProfileID,
                        billingType      : billingType
                    }, 
                    {
                        action          : 'billing.deleteBillingProfile',
                        title           : title,
                        message         : 'Are you sure want to delete this Billing Profile?'     
                    }, 
                    {
                        width: '25em',
                        height: 100
                    }
                );
    } catch(ex) {
      $1.error("[mod:billing.newReport] Error.",ex);
    }

    
};

mod.deleteTieringGroup = function(tieringGroupID){
    try{
            var title = 'Delete Confirmation';
            $app
                .form
                .openConfirmationDialog(
                    'billing.deleteConfirmation', 
                    {
                        tieringGroupID   : tieringGroupID,
                    }, 
                    {
                        action          : 'billing.deleteTieringGroup',
                        title           : title,
                        message         : 'Are you sure want to delete this Tiering Group?'     
                    },
                    {
                        width: '25em',
                        height: 100
                    }
                );
    } catch(ex) {
      $1.error("[mod:billing.newReport] Error.",ex);
    }

    
};

mod.deleteReportGroup = function(reportGroupID){
    try{
            var title = 'Delete Confirmation';
            $app
                .form
                .openConfirmationDialog(
                    'billing.deleteConfirmation', 
                    {
                        reportGroupID   : reportGroupID,
                    }, 
                    {
                        action          : 'billing.deleteReportGroup',
                        title           : title,
                        message         : 'Are you sure want to delete this Report Group?'     
                    },
                    {
                        width: '25em',
                        height: 100
                    }
                );
    } catch(ex) {
      $1.error("[mod:billing.newReport] Error.",ex);
    }

    
};

try{
	$app.registerModule(mod, MODULE_NAME);
}catch (ex){
	$1.log('[mod:apiuser] Failed registering module '+MODULE_NAME);
}

});
})($, $1, $app);
