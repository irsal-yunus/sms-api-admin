/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

(function($, $1){
if(typeof window.$app != "undefined")
	return; //prevent double invocation
window.$app = {
	/**
	 * For internal use only, external components should not rely on it!!!
	 * @internal
	 */
	isPremature : true,
	queue : [],
	ready:function(fn){
		if(typeof fn =='function'){
			this.queue.push(fn);
		}
	}
};

$(document).ready(function(){
	var SERVICE_URL = 'services/',
		JS_URL = 'js/',
		SKIN_URL = 'skin/',
		EXIT_PAGE = 'logout.php',
		ENTRY_PAGE = 'welcome';
	var settings = {
			
		},
		modules = {},
		components = [],
		messenger = $('<div></div>').css({
			whiteSpace:'pre-wrap'
		});
	var panels = {
		container : $('#container'),
		contentPanel : $('#contentPanel'),
		menuPanel : $('#menuPanel'),
		greetingPanel : $('#welcomeMessage'),
		title : $('#titlePanel').first()		
	};
	var $app = {
	};

	
	//////////
	// CORE //
	//////////

	$app.ready = function(fn){
		try {
			if(typeof fn != 'function')
				throw 'Invalid callback';
			fn($app);
		} catch (ex) {
			$1.error("[$app.ready] Error.",ex);
		}
	};
	/**
	 * load once a js or css
	 * @param url url of the contents
	 * @param type css or js. Default is script
	 */
	function loadComponent(url, type){
		try {
			if((typeof url != "string") || url=='')
				throw "Invalid Url";
			if(components.indexOf(url) != -1)
				return;//has loaded
			switch (type) {
				case 'css':
					$('<link type="text/css" href="'+url+'" rel="stylesheet"/>').appendTo('head');
					break;
				case 'js':
				default:
					$('<script type="text/javascript" src="'+url+'"></script>').appendTo('head');;
					break;
			}
			components.push(url);
		} catch (ex) {
			$1.error("[$app#loadComponent] Error.",ex);
		}
	}
	$app.loadSkinComponent = function(url, type){
		loadComponent(SKIN_URL+url, type);
	};
	$app.loadJSComponent = function(url, type){
		loadComponent(JS_URL+url, type);
	};

	/**
	 * Get acess to a module
	 */
	$app.module = function(modName){
		try {
			if(typeof modName !="string")
				throw "Invalid module name '"+modName+"'";
			if(!(modName in modules))
				throw "Module '"+modName+"' is not yet registered";
			return modules[modName];
		} catch (ex) {
			$1.error("[$app.module] Error.",ex);
			return {};
		}
	};
	$app.hasModule = function(modName){
		try {
			if(typeof modName !="string")
				throw "Invalid module name '"+modName+"'";
			return (modName in modules);
		} catch (ex) {
			$1.error("[$app.hasModule] Error.",ex);
			return false;
		}
	};
	
	/**
	 * Register a module
	 * @param mod Module object
	 * @param name Module name
	 * @param complete Callback which is executed after module registration
	 * @param error Callback which is executed after module registration
	 */
	$app.registerModule = function(mod, name, complete, error){
		try {
			if(typeof mod != "object")
				throw "Invalid module definition for module: "+name;
			if(name in modules)
				throw "Module '"+name+"' has been registered before!";
			modules[name] = mod;
			if(typeof complete === "function")
				complete();
		} catch (ex) {
			$1.error("[$app.registerModule] Error.",ex);
			if(typeof error == "function")
				error(ex);
		}
	};

	/**
	 * Configure application
	 * @param cfg name of config or properties object
	 * @param value [optional] config value
	 */
	$app.config = function(cfg, value){
		try {
			if(!cfg)
				throw "Invalid config name";
			if(typeof name=="string"){
				if(typeof value=='undefined')
					return settings[cfg];
				changeConfig(cfg, value);
			}else if(typeof cfg == "object"){
				for(var name in cfg){
					changeConfig(name, cfg[name]);
				}
				return this;
			}else{
				throw "Invalid parameter";
			}
		} catch (ex) {
			$1.error("[$app.config] Error.",ex);
			return null;
		}
	};
	$app.checkDOMContext = function(selector, defaultScope){
		try {
			if(typeof defaultScope=='undefined')
				defaultScope = document;
			if((typeof selector!='string') && (typeof selector!='object'))
				return defaultScope;
			var context = $(selector);
			return context.length? context[0] : defaultScope;
		} catch (ex) {
			$1.error("[$app.checkDOMContext] Error.",ex);
		}
	}
	/**
	 * Apply single config change
	 */
	function changeConfig(name, value){
		try {
			switch (name) {
				default:
					settings[name] =  value;
					break;
			}
		} catch (ex) {
			$1.error("[$app#changeConfig] Error.",ex);
		}
	}

	//////////////
	// SECURITY //
	//////////////
	/**
	 * Read data from cookie
	 * @param name The cookie's name
	 */
	function readCookie(name){
		try {
			//$.noop();
			var allCookies = document.cookie.split(';');
			for(var i in allCookies){
				var cookiePiece = allCookies[i].split('=',2);
				if(cookiePiece[0]===name){
					return unescape(cookiePiece[1]);
				}
			}
			return null;
		} catch (ex) {
			$1.error("[$app.example] Error.",ex);
			return null;
		}
	}
//	/**
//	 * Check if currenctly logged in
//	 */
//	function isCurrentlyLoggedIn(){
//		return false;
//	}
	/**
	 * Show login dialog
	 * @param username the user name
	 * @param password login password
	 * @param fn A callback which will be called after the server 
	 *           reply the login request. The function will accept
	 *           single boolean parameter, which is true when login success
	 *           and false otherwise
	 * @example
	 * $app.login('foo', 'woo', function(success){
	 *     if(success){
	 *          //You've been logged in now
	 *     }else{
	 *         //Sorry, login failed
	 *     }
	 * });
	 */
	$app.login = function(username, password, fn){
		try {
			if(typeof username!='string' ||
				typeof password != 'string')
				throw 'Invalid username/password data type';
			if(username === '' || password === '')
				throw 'Username/password can not be empty';
			var onresult = (fn && (typeof fn == 'function'))? fn : null;
			var credentials = {
				username:username,
				password:password
			};
			$app.call('login', credentials, function(reply, status){
				try {
					var msg='';
					if((typeof reply != 'object') || $.isEmptyObject(reply)){
						throw {'message': 'Invalid service reply', 'reply':reply};
					}
					var normalForm = {
						success:false,
						summary:'',
						attachment:{}
					};
					var normalisedReply = $.extend(normalForm, reply);
					var doPageUpdate;
					if(normalisedReply.success){						
						if(onresult){
							doPageUpdate = onresult(true);
						}
						//update page content and navigation by default
						//won't update when onresult return == FALSE
						if((typeof doPageUpdate == 'undefined') || doPageUpdate){
							$app.updateNavigationPanel()
								.content(ENTRY_PAGE);
						}
					}else if((typeof normalisedReply.summary == 'string') && (normalisedReply.summary !='')){
						$app.tell(normalisedReply.summary, 'Login');
						if(onresult){
							onresult(false);
						}
					}
					
				} catch (ex) {
					$1.error('[$app.login@post] Error processing form.',ex);
					alert("ERROR: "+ex);
				}
			});
		} catch (ex) {
			$1.error("[$app.login] Error.",ex);
			var emsg = (typeof ex == 'object' && ('message' in ex))? ex.message : ex;
			$app.tell('ERROR: '+emsg,'Login Error');

		}
	};

	/**
	 * Show logout confirmation dialog
	 */
	$app.logout = function(){
		try {
			$app.confirm("Logout from application?" ,"Confirm logout", function(){
				$.post(resolveServiceUrl('logout'), null, function(){
					try {
						$app.updateNavigationPanel();
						$app.redirect(EXIT_PAGE);
					} catch (ex) {
						$1.error("[$app.logout@post] Error.",ex);
					}
				})
			});
		} catch (ex) {
			$1.error("[$app.login] Error.",ex);
		}
	};

	////////////////
	// NAVIGATION //
	////////////////
	/**
	 * Resolve service url
	 */
	function resolveServiceUrl(serviceName){
		try {
			if(typeof serviceName != 'string' || serviceName =='')
				throw "Invalid service name: "+serviceName;
			return SERVICE_URL+serviceName+'.php';
		} catch (ex) {
			$1.error("[$app#resolveServiceUrl] Error.",ex);
			return null;
		}
	}
	/**
	 * Call a service
	 * 
	 * @param serviceName service name
	 * @param data data object passed data
	 * @param success [optional] function to execute when success
	 * @param ajaxOptions [optional] Ajax Options
	 */
	$app.call = function(serviceName, data, success, ajaxOptions){
		try {
			var url = resolveServiceUrl(serviceName);
			if(url == null)
				throw "Can not resolve service url for service: "+serviceName;
			if((typeof ajaxOptions == 'undefined') || (ajaxOptions === null)){
				ajaxOptions = {};
			}else if(typeof ajaxOptions != 'object'){
				throw "Invalid type for ajaxOptions";
			}
			var callArguments = { //reserved arguments
				type:'POST',
				url:url,
				data:data,
				dataType:'json'
			};
			if(typeof success == 'function')
				callArguments.success = success;
			$.extend(callArguments, ajaxOptions);
			callArguments.error = function(xhr, textStatus, errorThrown){
				$1.error('[$app.call@error] Ajax request error.',arguments);
				if(typeof ajaxOptions.error == 'function')
					ajaxOptions.error(textStatus, errorThrown);
			};
			$.ajax(callArguments);
			return $app;
		} catch (ex) {
			$1.error("[$app.call] Error.",ex);
			return $app;
		}
	};
	function activateWidgets(contextDOM){
		try {
			var context = ((typeof contextDOM == 'string') || (typeof contextDOM=='object'))?
								$(contextDOM)[0] : panels.contentPanel[0];
			$('.form-button', context).button();
			$('.form-datepicker', context).datepicker({
				changeYear: true,
				changeMonth: true,
				dateFormat:'yy-mm-dd'
//				showOn: 'both',
//				buttonImage: 'skin/images/icon-date.png',
//				buttonImageOnly: true
			});

			$('.panel-tabs', context).tabs({
				ajaxOptions :{
					error: function(xhr, status, index, anchor) {
						$(anchor.hash).html("Request failed!");
					}
				},
				load: function(event, ui) {
				}
			}).bind("tabsload", function(event, ui) {
				if((typeof ui=='object') && (typeof ui.panel=='object'))
					activateWidgets(ui.panel)
			});
		} catch (ex) {
			$1.error("[$app#activateWidgets] Error.",ex);
		}
	}
	/**
	 * Load content of the page from other url
	 * @param serviceName page service
	 * @param data data object
	 * @param complete onComplete handler
	 * @param container parent dom where the content should be placed in
	 */
	$app.content = function(serviceName, data, complete, container){
		try {
			var $container;
			if(container && ((typeof container == 'string') || (typeof container == 'object'))){
				$container = $(container); //load it shomewher
//				if(!$container.length) //failed
//					$container = panels.contentPanel;
			}else{
				$container = panels.contentPanel;//load in content panel
			}
			if(serviceName===false){
				return $container.html('');
			}else if((typeof serviceName != 'string') || (serviceName=='')){
				throw "Invalid page: "+serviceName;
			}
			$container
				.hourglass()
				.hourglass('show');
			return $container.load(resolveServiceUrl(serviceName), data, function(){
				activateWidgets($container);
				$container.hourglass('hide');
				if(typeof complete=="function")
					complete();
			});
//			return $container;
		} catch (ex) {
			$1.error("[$app.content] Error.",ex);
			return $container;
		}
	};

	$app.panelContent = function(panel, serviceName, data, complete){
		try {
			var $container = $(panel).children('.panel-body').children('.panel-content');
			if(!$container.length)
				throw "Invalid panel structure";
			var $title = $(panel).children('.panel-header'),
				originalTitle = $title.html();
			$title.html('<em>Loading...</em>');

			$app.content(serviceName, data, function(){
				$title.html(originalTitle);
				if(typeof complete=='function'){
					complete();
				}
			}, $container[0])
		} catch (ex) {
			$1.error('[$app.panelContent] Error.',ex);
		}
	};
	
	$app.welcome = function(){
		$app.content(ENTRY_PAGE, null, function(){
			$app.title('Home');
		});
	};
	$app.title = function(text){
		try {
			panels.title.text(text);
		} catch (ex) {
			$1.error("[$app.title] Error.",ex);
		}
	};
	/**
	 * Redirect page
	 */
	$app.redirect = function(url){
		try {
			panels.container
				.hourglass('text','Redirecting...')
				.hourglass('show');
			window.location = url;
		} catch (ex) {
			$1.error("[$app.redirect] Error.",ex);
			window.location = url;
		}
	}
	//////////
	// MENU //
	//////////
	$app.updateMenu = function(){
		try {
			panels.menuPanel.load(resolveServiceUrl('menu'));
			return this;
		} catch (ex) {
			$1.error("[$app.menu] Error.",ex);
			return this;
		}
	};
	$app.updateNavigationPanel = function(){
		try {
			panels.greetingPanel.load(resolveServiceUrl('greeting'));
			this.updateMenu();
			return this;
		} catch (ex) {
			$1.error("[$app.menu] Error.",ex);
			return this;
		}
	};

	/////////////
	// MESSAGE //
	/////////////
	function resetMessenger(){
		try {
//			$1.debug('[$app#resetMessenger] Running:', arguments);
			messenger
				.empty()
				.dialog('option', {
					title : '',
					buttons : {}
				});
		} catch (ex) {
			$1.error("[$app#resetMessenger] Error.",ex);
		}
	}
	/**
	 * Show alert box
	 */
	$app.tell = function(content, title){
		try {
			var dialogOptions = {
				title : (title==undefined)? "" : title,
				modal: true,
				buttons: {
					'OK' : function(){
						resetMessenger();
						$(this).dialog('close');
					}
				}
			};
			resetMessenger();
			messenger
				.html(content)
				.dialog('option', dialogOptions)
				.dialog('open');
		} catch (ex) {
			alert(content); //do natively when fancy way was failed
			$1.error("[$app.tell] Error.",ex);
		}
	};
	$app.alert = function(content){
		$app.tell(content, 'Error');
	};
	/**
	 * Show cionfirmation dialog
	 */
	$app.confirm = function(content, title, onYes, onNo, data){
		try {
			var dialogOptions = {
				title : (title==undefined)? "Confirmation" : title,
				closeOnEscape : false,
				modal: true,
				buttons:{
					'Yes' : function(){
						try {
							$(this).dialog('close');
							resetMessenger();
							if(typeof onYes =="function"){
								try {
									onYes(data);
								} catch (ex) {
									$1.error("[$app.confirm@yes] Error.",ex);
								}
							}
						} catch (ex) {}
					},
					'No' : function(){
						try {
							$(this).dialog('close');
							resetMessenger();
							if(typeof onNo =="function"){
								try {
									onNo(data);
								} catch (ex) {
									$1.error("[$app.confirm@no] Error.",ex);
								}
							}
						} catch (ex) {}							
					}
				}
			};
			resetMessenger();
			messenger
				.html(content)
				.dialog('option', dialogOptions)
				.dialog('open');
		} catch (ex) {
			//do natively when fancy way was failed
			if(confirm(content)){
				if(typeof onYes =="function"){
					try {
						onYes(data);
					} catch (ex) {}
				}
			}else{
				if(typeof onNo =="function"){
					try {
						onNo(data);
					} catch (ex) {}
				}
			}
			$1.error("[$app.confirm] Error.",ex);
		}
	};
	/**
	 * Show cionfirmation dialog
	 */
	$app.prompt = function(question, defaultValue, onOK, title){
		try {
			var $question = $('<div class="ui-helper-reset" style="margin:.5em 0em;"></div>');
			var $input = $('<input class="ui-helper-reset" type="text" value="" style="display:block;clear:both;float:none;margin:.2em 0;width:100%;height:1.5em;padding:.2em 0;text-indent:.2em;border:none;outline:1px #aaa solid;"/>');
			
			var dialogOptions = {
				title : (title==undefined)? "" : title,
				closeOnEscape : false,
				modal: true,
				buttons:{
					'Cancel' : function(){
						try {
							$(this).dialog('close');
							resetMessenger();							
						} catch (ex) {
							$1.error("[$app.prompt@click(Cancel)] Error.",ex);
						}
					},
					'OK' : function(){
						try {
							$(this).dialog('close');
							resetMessenger();
							if(typeof onOK =="function"){
								try {
									onOK($input.val());
								} catch (ex) {
									$1.error("[$app.prompt@click(OK)] Error.",ex);
								}
							}
						} catch (ex) {
							$1.error("[$app.prompt@click(OK)] Error.",ex);
						}
					}
				}
			};
			if(typeof value!='undefined')
				$input.val(defaultValue);
			resetMessenger();
			$question
				.html(question)
				.appendTo(messenger);
			var form = $('<form action="#" style="margin:0;padding:0;"></form>');
			messenger
				.append(form.append($input))
				.dialog('option', dialogOptions)
				.dialog('open');
		} catch (ex) {
			//do natively when fancy way was failed
			var answer=window.prompt(question, defaultValue);
			if(typeof onOK =="function"){
				try {
					onOK(answer);
				} catch (ex) {}
			}
			$1.error("[$app.prompt] Error.",ex);
		}
	};
	
	//////////
	// INIT //
	//////////
	(function($app,$1){
		try {
			panels.contentPanel.hourglass();
			messenger.dialog({
				autoOpen:false
			});
			$app.loadJSComponent('app/app.form.js');
			$app.loadJSComponent('app/app.apiuser.js');
			$app.loadJSComponent('app/app.credit.js');
			$app.loadJSComponent('app/app.client.js');
			var prematureApp;
			if(window.$app && window.$app.isPremature){//$app is still premature, initialise
				prematureApp = window.$app;
				if((typeof prematureApp.queue=='object') && prematureApp.queue){
					window.$app = $app;
					delete prematureApp;
					var registeredInitFunctions = prematureApp.queue;
					activateWidgets(panels.contentPanel);
					var fn;
					while(fn = registeredInitFunctions.shift()){
						if(typeof fn =='function')
							fn($app);
					}
					$app.updateNavigationPanel();
				}else{
					window.$app = $app;
					delete prematureApp;
					activateWidgets(panels.contentPanel);
					$app.updateNavigationPanel();
				}
				
			}			
			panels.contentPanel.hourglass('hide');
		} catch (ex) {
			$1.error("[$app@init] Error.",ex);
			alert('Error while initialising application');
		}
	})($app, $1);
});
})($, $1);
