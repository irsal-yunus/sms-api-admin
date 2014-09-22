/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

(function($, $1, $app){
$app.ready(function($app){

var MODULE_NAME='credit';
if($app.hasModule(MODULE_NAME))
	return;
var mod = {}; //implementation


try {

} catch (ex) {
	$1.error("[mod:client.open] Error.",ex);
}

function checkValidRecordID(id){
	if(!id || ((typeof id !='string') && (typeof id !='number'))){
		$app.tell('Invalid record ID');
		throw "Record ID="+id+", type="+typeof id;
	}
}

function title(text){
	try {
		var title = 'Credit Management';
		if($.trim(text) != '')
			title += ' > '+text;
		$app.title (title);
	} catch (ex) {
		$1.error("[mod:credit#title] Error.",ex);
	}
}



mod.manageUserCredit = function(userID){
	try {
		$app.content('credit.manageUserCredit', {userID:userID}, function(){
			title('User Credit');
		});
	} catch (ex) {
		$1.error("[mod:credit.manageUserCredit] Error.",ex);
	}
};
mod.purchase = function(userID){
		try {
		checkValidRecordID(userID);
		$app.form.openAutoDialog(('credit.purchase'), {userID:userID}, 'Credit Top Up', {
			width: '50em',
			height: 300
		}, function(reply){
			if(reply !== false)
				mod.manageUserCredit(userID);
		});
	} catch (ex) {
		$1.error("[mod:credit.purchase] Error.",ex);
	}
};
mod.deduct = function(userID){
		try {
		checkValidRecordID(userID);
		$app.form.openAutoDialog(('credit.retur'), {userID:userID}, 'Credit Removal', {
			width: '50em',
			height: 220
		}, function(reply){
			if(reply !== false)
				mod.manageUserCredit(userID);
		});
	} catch (ex) {
		$1.error("[mod:credit.deduct] Error.",ex);
	}
};
mod.editTransaction = function(tranID, userID){
	try {
		var title = 'Edit Transaction Details';
		checkValidRecordID(tranID);
		$app.form.openAutoDialog(('credit.editTransaction'), {creditTransactionID:tranID}, title, {
			width: '50em',
			height: 300
		}, function(reply){
			if((reply !== false) && (typeof userID != 'undefined'))
				mod.manageUserCredit(userID);
		});
	} catch (ex) {
		$1.error("[mod:credit.editTransaction] Error.",ex);
	}
};
mod.viewTransaction = function(tranID, section){
	try {
		checkValidRecordID(tranID);
		$app.form.openDialog('credit.viewTransaction', {creditTransactionID:tranID}, function(){
			if(typeof section != 'undefined'){
				var tabIdx = 0;
				switch (section) {
					case 'payment':
						tabIdx = 1;
						break;
					case 'transaction':
						tabIdx = 0;
						break;
				}
				$('#credit-view-tabs').tabs('load', tabIdx);
			}
		}, {
			title: 'View Transaction Details',
			width: '50em',
			height: 530,
			buttons : {
				'Close': function(){
					$app.form.closeDialog();
				}
			}
		});
	} catch (ex) {
		$1.error("[mod:credit.viewTransaction] Error.",ex);
	}
};
mod.ackTransaction = function(tranID, userID){
	try {
		var title = 'Payment Acknowledgement';
		checkValidRecordID(tranID);
		$app.form.openAutoDialog(('credit.payment'), {creditTransactionID:tranID}, title, {
			width: '30em',
			height: 300
		}, function(reply){
			if((reply !== false) && (typeof userID != 'undefined'))
				mod.manageUserCredit(userID);
		});
	} catch (ex) {
		$1.error("[mod:credit.ackTransaction] Error.",ex);
	}
};
//FEATURES
try{
	$app.registerModule(mod, MODULE_NAME);
}catch (ex){
	$1.log('[mod:client] Failed registering module '+MODULE_NAME);
}


});
})($, $1, $app);
