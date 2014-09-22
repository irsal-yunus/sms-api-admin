/* 
 * Copyright(c) 2010 1rstWAP. All rights reserved.
 */

(function($,$app){
$app.ready(function($app){
	$app.config({
		imageUrl:"skin/images/"
	});
	var wheelImage = 'skin/images/wheel.gif';
	$.ui.hourglass.setDefaultOptions({imageUrl: wheelImage});
	$('#contentPanel').hourglass('option', 'imageUrl', wheelImage);
});
})(jQuery,$app);
