/*
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

(function($, $1, $app){
$app.ready(function($app){

var MODULE_NAME='client';
if($app.hasModule(MODULE_NAME))
	return;
var mod = {}; //implementation
	try {
	} catch (ex) {
		$1.error("[mod:client.open] Error.",ex);
	}

function checkValidRecordID(id){
	if((typeof id !='string') && (typeof id !='number')){
		$app.tell('Invalid record ID');
		throw "Record ID="+id+", type="+typeof id;
	}
}

function title(text){
	try {
		var title = 'Client Management';
		if($.trim(text) != '')
			title += ' > '+text;
		$app.title (title);
	} catch (ex) {
		$1.error("[mod:client#title] Error.",ex);
	}
}

mod.viewClient = function(clientID){
	try {
		checkValidRecordID(clientID);
		$app.content('client.view',{clientID:clientID},function(){
			title('View Client Details');
		});
	} catch (ex) {
		$1.error("[mod:client.viewClient] Error.",ex);
	}
};
/**
 * Diplay users of a client
 * @param clientID The client ID
 * @param options displaying options
 */
mod.manageUsers = function(clientID, options){
	try {
		checkValidRecordID(clientID);
		var args = {
			clientID:clientID,
			onlySpecifiedClient:true
		};
		if(typeof options == 'object')
			args = $.extend({}, options, args);
		$app.module('apiuser').showUserList(args);
	} catch (ex) {
		$1.error("[mod:client.manageUsers] Error.",ex);
	}
};

mod.createNew = function(){
	try {
		var title  = 'Client Registration';
		$app.form.openAutoDialog('client.new', null, 'Client Registration', {
			height:350
		}, function(reply){
			if(typeof reply.attachment.clientID != 'undefined'){
				$app.confirm('Client has been registered, do you want to add accounts for this client?',
							title,
							function(){
								mod.manageUsers(reply.attachment.clientID);
							}, function(){
								mod.showClientList({highlight:reply.attachment.clientID});
							});
			}else{
				$1.error('[mod:client.createNew] No client ID in reply:',reply)
			}
		});
	} catch (ex) {
		$1.error("[mod:client.createUser] Error.",ex);
	}
};

mod.editClient = function(clientID){
	try {
		checkValidRecordID(clientID);
		$app.form.openAutoDialog('client.edit', {clientID:clientID}, 'Edit Client', {
			height:350
		}, function(reply){
			if(reply !== false){
				mod.viewClient(clientID);
			}
		});
	} catch (ex) {
		$1.error("[mod:client.editClient] Error.",ex);
	}
};
mod.removeClient = function(clientID){
	try {
		checkValidRecordID(clientID);
		var title = 'Client Removal';
		$app.confirm('Do you want to delete this client?', title, function(){
			$app.call('client.remove', {clientID:clientID}, function(reply){
				try {
					var success = $app.form.checkServiceReply(reply, false, title);
					if(success)
						mod.showClientList();
				} catch (ex) {
					$1.error("[mod:client.removeClient@ajaxsuccess] Error.",ex);
				}
			});
		});
	} catch (ex) {
		$1.error("[mod:client.removeClient] Error.",ex);
	}
};


mod.smsBilling = function(clientID){
	try {
		checkValidRecordID(clientID);
		$app.form.openAutoDialog('client.billing', {clientID:clientID}, 'Billing Options', {
			height:350
		}, function(reply){
			if(reply !== false){
				mod.viewClient(clientID);
			}
		});
	} catch (ex) {
		$1.error("[mod:client.editClient] Error.",ex);
	}
};

mod.showClientList= function(options){
	try {
		var args= (typeof options == 'object')? options : null;
		$app.content('client.table', args, function(){
			title('Client List');
		});
	} catch (ex) {
		$1.error("[mod:client.showClientList] Error.",ex);
	}
}
//FEATURES
try{
	$app.registerModule(mod, MODULE_NAME);
}catch (ex){
	$1.log('[mod:client] Failed registering module '+MODULE_NAME);
}


});
})($, $1, $app);
