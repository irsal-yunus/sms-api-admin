/* System           : SMS API Administration
 * Modules          : JS
 * Version          : 1.0
 * Filename         : firstwap.js
 * File version     : 1.001.000
 * Initial Creation : 2010
 * Purpose          : this file is for domain configuration
 * @author          : setia budi
 * 
 * ================================================
 * Initial Request  : 
 * ================================================
 * Change Log
 * Date         Author      Version     Request     Comment               
 * 2014-09-14   beni        1.000.000   #2299      removing some variable declarations that's no longer supported by the browser
 * Copyright 2014 PT. 1rstWAP confidential
 * This Document belongs to PT 1rstWAP. Propagation to others
 * then members of PT 1rstWAP is strictly forbidden  
 */

(function($){
	try {
		var noAction = function(){};
		var $1;
		if(typeof console === 'undefined') {
			console = {
				log : noAction,
				debug : noAction,
				info : noAction,
				warn : noAction,
				error : noAction
			};
		}
                
		window['$1'] =
		window['FirstWAP'] =console;
		window.$1.info("FirstWAP JS engine was loaded...");
	} catch (ex) {
		alert("Error initialising FirstWAP JS engine:\n"+ex);
	}
})($);
