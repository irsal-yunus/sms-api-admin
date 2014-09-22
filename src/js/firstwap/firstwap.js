/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

(function($){
	try {
		var noAction = function(){};
		var $1;
		if(typeof console != 'undefined'){
			$1 = {
				debug: console.debug || noAction,
				info: console.info || noAction,
				warn: console.warn || noAction,
//				error: console.error || noAction,
				//work around to avoid this.trace is not afunction in $1.error
				error: console.warn || noAction
//				error: !console.error? noAction : function(){
//					console.error.apply("sdasdas");
//				}
			};
		}else{
			$1 = {
				debug : noAction,
				info : noAction,
				warn : noAction,
				error : noAction
			};
		}
		window['$1'] =
		window['FirstWAP'] = $1;
		window.$1.info("FirstWAP JS engine was loaded...");
	} catch (ex) {
		alert("Error initialising FirstWAP JS engine:\n"+ex);
	}
})($);
