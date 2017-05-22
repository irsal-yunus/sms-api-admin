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

mod.newBillingProfile = function(options){
    try {
        var args;
        if((typeof options != 'object') || $.isEmptyObject(options)){
                args = null;
        }else{
                args = options;
        }
        $app.content('billing.new', args, function(){
                title('New Billing Profile');
        });
    } catch(ex) {
        $1.error("[mod:billing.newBillingProfile] Error.",ex);
    }
};

mod.storeBillingProfile = function(options){
    try {
        var args;
        if((typeof options != 'object') || $.isEmptyObject(options)){
                args = null;
        }else{
                args = options;
        }
        
        
    } catch(ex) {   
        
    }
};

try{
	$app.registerModule(mod, MODULE_NAME);
}catch (ex){
	$1.log('[mod:apiuser] Failed registering module '+MODULE_NAME);
}

});
})($, $1, $app);
