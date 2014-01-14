var photopress = photopress || {'gallery' : {}, 'galleries': {} };

photopress.gallery.masonry = function( dom_selector, options ) {
	
	this.dom_selector = dom_selector || '';
	
	this.options = this.defaults;
	
	if ( options ) {
		
		for (var opt in options) {
			
			this.options[opt] = options[opt];
		}
	}	
};

photopress.gallery.masonry.prototype = {
	
	defaults : {

		//itemSelector: '.gallery-icon > a > img'
		itemSelector: '.gallery-item'
		
	},
	
	render : function() {
		
		var that = this;
		var container = jQuery( that.dom_selector );
		
		container.imagesLoaded(function(){
		  container.masonry({
		   itemSelector : that.options.itemSelector,
		   gutter: that.options.gutter || 10
		    //gutterWidth: '15px'
		    //columnWidth : 
		  });
		});
	}
};