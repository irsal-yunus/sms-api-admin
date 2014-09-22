/* 
 * @author setia.budi
 * @created 12 Sept 2010 GMT+0700
 * @require jQuery
 */
(function( $ ) {
var defaultOptions = {
	'autostart': true,
	'imageUrl': '',
	'imageHeight': 19,
	'imageWidth': ''
};
$.widget( "ui.hourglass", {
	options: {},
	_image : null,
	_caption : null,
	_layer : null,
	isShowing : false,
	_create: function() {
		this.options = defaultOptions;
		var settings = this.options;
		this._caption = $('<div style="position:absolute;bottom:0.2em;width:100%;height:40px;line-height:40px;color:white;font-family:sans-serif;font-size:16px;font-weight:bold;text-align:center;">Processing..</div>');
		this._image = $('<img src="" style="height:19px;margin:0 auto;"/>')
			.css({
					width:this._parseDimension(settings.imageWidth),
					height:this._parseDimension(settings.imageHeight)
				})
			.attr('src', settings.imageUrl);
		this._layer = $('<div style="width:auto;height:auto;position:absolute;top:0;bottom:0;left:0;right:0;text-align:center;vertical-align:middle;background-color:rgba(0,0,0,.35)"></div>');
		$('<div style="vertical-align: top;position:absolute;top:30%;bottom:0;display:block;width:100%;height:auto;overflow:hidden;"></div>')
			.append(this._image)
			.appendTo(this._layer);
		$('<div style="vertical-align: bottom;position:absolute;top:0%;bottom:70%;isplay:block;width:100%;height:auto;overflow:hidden;"></div>')
			.append(this._caption)
			.appendTo(this._layer);
		this._layer.hide();
		this.text(this.options.text);
		if(settings.autostart)
			this.show();
	},
	destroy: function() {
		this._layer.empty().remove();
		$.Widget.prototype.destroy.apply( this, arguments );
	},
	show: function() {
		if(this.isShowing)
			return;
		this.isShowing = true;		
		this._layer.appendTo(this.element).show();
	},
	hide: function() {
		if(!this.isShowing)
			return;
		this._layer
			.hide()
			.detach();
		this.isShowing = false;
	},
	toggle: function(show) {
		if(show){
			this.begin()
		}else{
			this.end();
		}
	},
	text: function(show) {
		this._caption.text()
	},
	_setOption: function( key, value ) {
		switch ( key ) {
			case "imageUrl":
				this.options.imageUrl = value;
				this._image.attr('src', this.options.imageUrl);
				break;
			case "imageWidth":
				this.options.imageWidth = this._parseDimension(value);
				this._image.css('width',this.options.imageWidth);
				break;
			case "imageHeight":
				this.options.imageHeight = this._parseDimension(value);
				this._image.css('height',this.options.imageHeight);
				break;
		}
	},
	_parseDimension: function(value){
		if(typeof value=='number')
			return value+'px';
		
		if(typeof value=='string')
			return value;
		return '';
	}
});

$.extend( $.ui.hourglass, {
	version: "1.0",
	setDefaultOptions : function(options){
		if(typeof options != 'object'){
			return;
		}
		if($.isEmptyObject(options))
			return;
		for(var name in options){
			if(name in defaultOptions){
				defaultOptions[name] = options[name];
			}else{
			}
		}
	}
});

})( jQuery );