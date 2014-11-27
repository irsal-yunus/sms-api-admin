/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

(function($, $1, $app){
$app.ready(function($app){
	
var MODULE_NAME='apiuser';
if($app.hasModule(MODULE_NAME))
	return;
var mod = {}; //implementation

function checkValidRecordID(id){
	if(!id || ((typeof id !='string') && (typeof id !='number'))){
		$app.tell('Invalid record ID');
		throw "Record ID="+id+", type="+typeof id;
	}
}
function title(text){
	try {
		var title = 'User Management';
		if($.trim(text) != '')
			title += ' > '+text;
		$app.title (title);
	} catch (ex) {
		$1.error("[mod:client#title] Error.",ex);
	}
}

mod.createUser = function(clientID){
	try {
		var regData, onlySpecifiedClient;
		if(typeof clientID != 'undefined'){
			onlySpecifiedClient=true;
			regData = {clientID:clientID};
		}else{
			onlySpecifiedClient=false;
			regData = null;
		}
		var title = 'User Registration';
		$app.form.openAutoDialog(('apiuser.new'), regData, title, {
			width: '40em',
			height: 316
		}, function(reply){
			if(typeof reply.attachment.userID != 'undefined'){
				$app.confirm("API User account has been created, do you want to edit the user details",
							title,
							function(){
								mod.editUser(reply.attachment.userID);
							},
							function(){
								var hilite = {
									highlight:reply.attachment.userID
								};
								if(onlySpecifiedClient){
									if(typeof reply.attachment.clientID != 'undefined'){
										$app.module('client').manageUsers(clientID, hilite);
										return;
									}
									$1.error('[mod:apiuser.createUser] No client ID in reply:',reply);

								}
								mod.showUserList(hilite);
							});
				
			}else{
				$1.error('[mod:apiuser.createUser] No user ID in reply:',reply);
			}
		});
	} catch (ex) {
		$1.error("[mod:apiuser.createUser] Error.",ex);
	}
};

mod.changePassword = function(userID){
		try {
		checkValidRecordID(userID);
		var title = 'Change User Password';
		$app.prompt('Enter new password:', '', function(password){
			try {
				var data = {
					userID: userID,
					userPassword: password
				};
				$app.call('apiuser.changePassword', data, function(reply){
					try {
						var success = $app.form.checkServiceReply(reply, false, title);
						if(success)
							mod.editUser(userID);
					} catch (ex) {
						$1.error("[mod:apiuser.changePassword@ajaxsuccess] Error.",ex);
					}
				});
			} catch (ex) {
				$1.error("[mod:apiuser.changePassword] Error.",ex);
			}
		}, title);
	} catch (ex) {
		$1.error("[mod:apiuser.changePassword] Error.",ex);
	}
};
/**
 * @param userID User ID
 * @param section The section name to show can be virtualNumber, sender, ip.
 *				  By default it is the account section
 *
 */
mod.editUser = function(userID, section){
	try {
		if(typeof userID == 'undefined'){
			$1.info('[mod:apiuser.editUser] Called without userID argument!');
		}
		checkValidRecordID(userID);
		$app.content('apiuser.edit', {userID:userID}, function(){
			title('User Edit');
			if(typeof section != 'undefined'){
				var tabIdx = 0;
				switch (section) {
					case 'replyBlacklist':
						tabIdx = 4;
						break;
					case 'virtualNumber':
						tabIdx = 3;
						break;
					case 'ip':
						tabIdx = 2;
						break;
					case 'sender':
						tabIdx = 1;
						break;
					case 'account':
					default:
						tabIdx = 0;
						break;
				}
				$('#apiuser-editform-tabs').tabs('select', tabIdx);
			}
		});
	} catch (ex) {
		$1.error("[mod:apiuser.editUser] Error.",ex);
	}
};
/**
 * Deactivate user account
 * @param userID
 * @param list Show user list if success
 */
mod.deactivateUser = function(userID, list){
	try {
		checkValidRecordID(userID);
		$app.call('apiuser.deactivate', {userID:userID}, function(reply){
			try {
				var success = $app.form.checkServiceReply(reply, false, 'Disable User Account');
				if(success){
					if((typeof list == 'object') || (list === true)){
						mod.showUserList(list);
					}else{
						mod.editUser(userID, 'account');
					}
				}
			} catch (ex) {
				$1.error("[mod:apiuser.deactivateUser@ajaxsuccess] Error.",ex);
			}
		});
	} catch (ex) {
		$1.error("[mod:apiuser.deactivateUser] Error.",ex);
	}
};
/**
 * Activate user account
 * @param userID
 * @param list Show user list if success
 */
mod.activateUser = function(userID, list){
	try {
		checkValidRecordID(userID);
		$app.call('apiuser.activate', {userID:userID}, function(reply){
			try {
				var success = $app.form.checkServiceReply(reply, false, 'Enable User Account');
				if(success){
					if((typeof list == 'object') || (list === true)){
						mod.showUserList(list);
					}else{
						mod.editUser(userID, 'account');
					}
				}
			} catch (ex) {
				$1.error("[mod:apiuser.activateSender@ajaxsuccess] Error.",ex);
			}
		});
	} catch (ex) {
		$1.error("[mod:apiuser.activateSender] Error.",ex);
	}
};
/**
 * Show user list
 * @param options Displaying options
 *                i.e., highlight
 *
 */
mod.showUserList = function(options){
	try {
		var args;
		if((typeof options != 'object') || $.isEmptyObject(options)){
			args = null;
		}else{
			args = options;
		}
		$app.content('apiuser.table', args, function(){
			title('User List');
		});
	} catch (ex) {
		$1.error("[mod:apiuser.showUserList] Error.",ex);
	}
};
mod.showUserDetails = function(userID){
	try {
		checkValidRecordID(userID);
		$app.content('apiuser.view',{userID:userID},function(){
			title('User Details');
		});
	} catch (ex) {
		$1.error("[mod:apiuser.showUserDetails] Error.",ex);
	}
};
mod.editAccountDetails = function(userID){
	try {
		checkValidRecordID(userID);
		$app.form.openAutoDialog(('apiuser.editAccountDetails'), {userID:userID}, 'Account Details Edit', {
			width: '45em',
			height: 300
		}, function(reply){
			mod.editUser(userID, 'account');
		});
	} catch (ex) {
		$1.error("[mod:apiuser.editAccountDetails] Error.",ex);
	}
};
mod.addSender = function(userID){
	try {
		checkValidRecordID(userID);
		$app.form.openAutoDialog(('apiuser.newSender'), {userID:userID}, 'Sender Identity Registration', {
			width: '40em',
			height: 254
		}, function(reply){
			mod.editUser(userID, 'sender');
		});
	} catch (ex) {
		$1.error("[mod:apiuser.addSender] Error.",ex);
	}
};

mod.selectCobranderID = function() {
	try {
                $app.form.showExtDialog(('apiuser.cobranderId'), {}, 'Select Cobrander ID',$('.containerDialog'));
            } catch (ex) {
                $1.error("[mod:apiuser.selectCobranderId] Error.", ex);
            }
};

 mod.clearCobranderID = function() {
            try {
                $app.form.clearCobranderID();
            } catch (ex){
                $1.error("[mod:apiuser.clearCobranderId] Error.",ex);
            }
};
                
mod.getValueCobrander = function (cobranderId) {
        try {
                $app.form.getValueCobrander(cobranderId);
            }catch (ex){
                $1.error("[mod:apiuser.getValueCobrander]",ex);
            }
        };

mod.editSender = function(senderID, userID) {
            try {
		checkValidRecordID(senderID);
		$app.form.openAutoDialog(('apiuser.editSingleSender'), {senderID:senderID}, 'Sender Identity Edit', {
                            width:'40em',
                            height: 250
		}, function(reply){
			mod.editUser(userID, 'sender');
		});
	} catch (ex) {
		$1.error("[mod:apiuser.editSender] Error.",ex);
	}
};
mod.disableSender = function(senderID, userID){
	try {
		checkValidRecordID(senderID);
		$app.call('apiuser.disableSender', {senderID:senderID}, function(reply){
			try {
				var success = $app.form.checkServiceReply(reply, false, 'Disable Sender');
				if(success)
					mod.editUser(userID, 'sender');
			} catch (ex) {
				$1.error("[mod:apiuser.disableSender@ajaxsuccess] Error.",ex);
			}
		});
	} catch (ex) {
		$1.error("[mod:apiuser.disableSender] Error.",ex);
	}
};
mod.enableSender = function(senderID, userID){
	try {
		checkValidRecordID(senderID);
		$app.call('apiuser.enableSender', {senderID:senderID}, function(reply){
			try {
				var success = $app.form.checkServiceReply(reply, false, 'Enable Sender');
				if(success)
					mod.editUser(userID, 'sender');
			} catch (ex) {
				$1.error("[mod:apiuser.enableSender@ajaxsuccess] Error.",ex);
			}
		});
	} catch (ex) {
		$1.error("[mod:apiuser.enableSender] Error.",ex);
	}
};
mod.allowIP = function(userID){
	try {
		checkValidRecordID(userID);
		var title = 'IP Permission';
		$app.prompt('Enter allowed IP address', '127.0.0.1', function(ipAddress){
			try {
				var data = {
					userID: userID,
					ipAddress: ipAddress
				};
				$app.call('apiuser.allowIP', data, function(reply){
					try {
						var success = $app.form.checkServiceReply(reply, false, title);
						if(success)
							mod.editUser(userID, 'ip');
					} catch (ex) {
						$1.error("[mod:apiuser.allowIP@ajaxsuccess] Error.",ex);
					}
				});
			} catch (ex) {
				$1.error("[mod:apiuser.allowIP] Error.",ex);
			}
		}, title);
	} catch (ex) {
		$1.error("[mod:apiuser.allowIP] Error.",ex);
	}
};
mod.disallowIP = function(userID, ipAddress){
	try {
		var title = 'IP Restriction';
		checkValidRecordID(userID);
		$app.confirm("Do you want to remove IP <"+ipAddress+"> from current user?", title,
			function(){
				$app.call('apiuser.disallowIP', {userID:userID, ipAddress:ipAddress}, function(reply){
					try {
						var success = $app.form.checkServiceReply(reply, false, title);
						if(success)
							mod.editUser(userID, 'ip');
					} catch (ex) {
						$1.error("[mod:apiuser.disallowIP@ajaxsuccess] Error.",ex);
					}
				});
			});
	} catch (ex) {
		$1.error("[mod:apiuser.disallowIP] Error.",ex);
	}
};
mod.addVirtualNumber = function(userID){
	try {
		checkValidRecordID(userID);
		$app.form.openAutoDialog(('apiuser.newVirtualNumber'), {userID:userID}, 'Virtual Number Registration', {
			height: 250
		}, function(reply){
			mod.editUser(userID, 'virtualNumber');
		});
	} catch (ex) {
		$1.error("[mod:apiuser.addVirtualNumber] Error.",ex);
	}
};
mod.editVirtualNumber = function(virtualNumberID, userID){
	try {
		checkValidRecordID(virtualNumberID);
		$app.form.openAutoDialog(('apiuser.editSingleVirtualNumber'), {virtualNumberID:virtualNumberID}, 'Virtual Number Edit', {
			height: 250
		}, function(reply){
			mod.editUser(userID, 'virtualNumber');
		});
	} catch (ex) {
		$1.error("[mod:apiuser.editVirtualNumber] Error.",ex);
	}
};
mod.removeVirtualNumber = function(virtualNumberID, userID){
	try {
		checkValidRecordID(virtualNumberID);
		var title = 'Virtual Number Removal';
		$app.confirm('Remove this virtual number?', title, function(){
			$app.call('apiuser.removeVirtualNumber', {virtualNumberID:virtualNumberID}, function(reply){
				try {
					var success = $app.form.checkServiceReply(reply, false, title);
					if(success)
						mod.editUser(userID, 'virtualNumber');
				} catch (ex) {
					$1.error("[mod:apiuser.removeVirtualNumber@ajaxsuccess] Error.",ex);
				}
			});
		});
	} catch (ex) {
		$1.error("[mod:apiuser.removeVirtualNumber] Error.",ex);
	}
};
mod.blacklistReplyNumber = function(userID){
	try {
		checkValidRecordID(userID);
		var title = 'Reply Blacklist';
		$app.prompt('Enter to-be-blacklisted phone number', '', function(msisdn){
			try {
				var data = {
					userID: userID,
					msisdn: msisdn
				};
				$app.call('apiuser.blacklistReplyNumber', data, function(reply){
					try {
						var success = $app.form.checkServiceReply(reply, false, title);
						if(success)
							mod.editUser(userID, 'replyBlacklist');
					} catch (ex) {
						$1.error("[mod:apiuser.blacklistReplyNumber@ajaxsuccess] Error.",ex);
					}
				});
			} catch (ex) {
				$1.error("[mod:apiuser.blacklistReplyNumber] Error.",ex);
			}
		}, title);
	} catch (ex) {
		$1.error("[mod:apiuser.blacklistReplyNumber] Error.",ex);
	}
};
mod.unblacklistReplyNumber = function(userID, msisdn){
	try {
		var title = 'Reply Blacklist';
		checkValidRecordID(userID);
		$app.confirm("Do you want to remove phone <"+msisdn+"> from reply blacklist?", title,
			function(){
				$app.call('apiuser.unblacklistReplyNumber', {userID:userID, msisdn:msisdn}, function(reply){
					try {
						var success = $app.form.checkServiceReply(reply, false, title);
						if(success)
							mod.editUser(userID, 'replyBlacklist');
					} catch (ex) {
						$1.error("[mod:apiuser.unblacklistReplyNumber@ajaxsuccess] Error.",ex);
					}
				});
			});
	} catch (ex) {
		$1.error("[mod:apiuser.unblacklistReplyNumber] Error.",ex);
	}
};



try{
	$app.registerModule(mod, MODULE_NAME);
}catch (ex){
	$1.log('[mod:apiuser] Failed registering module '+MODULE_NAME);
}

});
})($, $1, $app);
