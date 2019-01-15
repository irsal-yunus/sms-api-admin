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
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        $("#backToTop").show();

    } else {
        $("#backToTop").hide();
    }
}

mod.topFunction = function () {
    $("html, body").animate({ scrollTop: 0 }, "slow");
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

/**
 * Show window to download billing report
 * @param  options
 */
mod.showBillingDetail = function(options){
    try {
            $app
                .form
                .openDownloadAllReportMesasge(
                    'apiuser.reportDownloadAll',
                    false,
                    'Download Billing Report',
                    {
                        height  : 230,
                        width   : 280
                    },
                    function() {}
                );
    } catch (ex) {
            $1.error("[mod:apiuser.reportBilling] Error.",ex);
    }
};

/**
 * Show create or update billing profile page
 * @param Int       billingProfileID
 * @param String    billingType
 * @param String    mode
 */
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

/**
 * Call storeBillingProfile services to process the billing profile data from view
 * @param Array data    [['mode','billingProfileID','name','description','price_based','user','operatorID','tiering']]
 */
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

/**
 * Show create or update tiering group page
 * @param Int       tieringID
 * @param String    mode        create a new tiering group or update the existing one
 */
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

/**
 * Call storeTieringGroup services to process the tiering group data from view
 * @param Array     data    [['mode','name','description','user']]
 */
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

/**
 * Show create or update report group page
 *
 * @param Int       reportID
 * @param String    mode        create a new report group or update the existing one
 */
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

/**
 * Call storeReportGroup services to process the report group data from view
 *
 * @param Array data    [['mode','name','description','user']]
 */
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

/**
 * Show window confirmation to delete billing profile, then call deleteBillingProfile services
 *
 * @param Int       billingProfileID
 * @param String    billingType         tiering or operator
 */
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

/**
 * Show window confirmation to delete tiering group, then call deleteTieringGroup services
 *
 * @param Int       tieringGroupID
 */
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

/**
 * Show window confirmation to delete tiering group, then call deleteTieringGroup services
 *
 * @param Int reportGroupID
 */
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

mod.viewMessageFilterPage = function(){
    try {
        $app.content(
                    'billing.messageFilterReport',
                    function(){
                        title('SMS Content Department Filter');
                    }
        );
    } catch(ex) {
        $1.error("[mod:billing.messageFilterReport] Error.",ex);
    }
};

/**
 * International Price Module
 */
var TAB_BILLING_INTL = 3;

var initMasking = function() {
    var replaceMent = function(val) {
        return val.replace(/(?!^)-/g, '').replace(/^,/, '').replace(/^-,/, '-');
    }
    $('input[data-mask]').each(function(i, input) {
        var mask = $(input).mask(input.dataset.mask, {
            reverse: true,
            translation: {
                '#': {
                    pattern: /-|\d/,
                    recursive: true
                }
            },
            onChange: function(value, e) {
                e.target.value = replaceMent(value);
            }
        });
        mask.val(replaceMent(mask.val()))
    });
}
var initIntlForm = function() {
    $("#countryCode").select2({
        placeholder: "Select a country",
        width: '200px',
    });
    initMasking();
}

mod.selectIntlPriceTab = function() {
    $('#billing-view-tabs').tabs('select', TAB_BILLING_INTL).tabs('load', TAB_BILLING_INTL);
}

mod.formIntlPrice = function(priceId) {
    var title = priceId ? "Edit International Price" : "Add International Price";
    var data = priceId ? { price_id: priceId } : null;

    try {
        $app.form.openAutoDialog('billing.intl.form', data, title, {
            width: '40em',
            height: 200
        }, function(reply) {
            if (reply.success) {
                mod.selectIntlPriceTab();
            }
        }, initIntlForm);
    } catch (ex) {
        $1.error("[mod:billing.intl.form (create) Error.", ex);
    }
};


mod.deleteIntlPrice = function(priceId) {
    try {
        var title = 'Deleted Internaltional Price';
        $app.confirm('Are you sure to remove the Internaltional Price data ?', title, function() {
            $app.call('billing.intl.delete', {
                price_id: priceId
            }, function(reply) {
                try {
                    var success = $app.form.checkServiceReply(reply, false, title);
                    if (success) {
                        if (reply && reply.summary) {
                            $app.tell(reply.summary || 'Success!', title);
                        }
                        mod.selectIntlPriceTab();
                    }
                } catch (ex) {
                    $1.error("[mod:invoice.history.delete@ajaxsuccess] Error.", ex);
                }
            });
        });
    } catch (ex) {
        $1.error("[mod:invoice.history.delete] Error.", ex);
    }
};


try{
	$app.registerModule(mod, MODULE_NAME);
}catch (ex){
	$1.log('[mod:apiuser] Failed registering module '+MODULE_NAME);
}

});
})($, $1, $app);
