
(function($){
    

$.widget( "ui.zToggle", {
	version: "@VERSION",
	options: {
		text_off:   'Off',
                text_on:    'On',
                onToggle:   null
	},

	state:          false,
        textElement:    null,

	_create: function() {

		this.element.addClass( "z-toggle" );
                
                if (this.options.state != undefined){
                    this.state = this.options.state;
                } else {
                    this.state = this.element.hasClass('off') == false;
                }
                        
                var text = this._getText(this.options.state);
                this.textElement = $('<span class="text">'+text+'</span>');
                this.element.before(this.textElement);
                
                this._on(this.element, {
                    // _on won't call random when widget is disabled
                    click: "_toggle"
                });
	},

	_destroy: function() {
                this.element.removeClass( "z-toggle" );
                this.textElement.remove();
	},
        
        _getText: function(state) {
            if (state == undefined) state = this.state;
            if (state==true){
                return this.options.text_on;
            } else {
                return this.options.text_off;
            }
        },

	_toggle: function(e, doCallback) {
            if (doCallback == undefined) doCallback = true;
            
            if ($(this.element).attr('disabled')!=null) return false;
            
            // Toggle class off
            $(this.element).toggleClass("off");
            this.state = !this.state;
            $(this.textElement).text(this._getText());


            if (typeof(this.options.onToggle) == 'function' && doCallback == true){
                this.options.onToggle(this.state, this.element);
            }
            
            return false;
	},
        
        setState: function(state, doCallback){
          
            if ($(this.element).attr('disabled')!=null) return false;
            
            if (state != this.state) this._toggle(null, doCallback);
            
            return this.state;
          
        },

	_setOptions: function( options ) {

		this._super( options );
	},

	_setOption: function( key, value ) {

		this._super( key, value );
	}

});

})( jQuery );
