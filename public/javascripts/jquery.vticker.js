(function($){
$.fn.vTicker = function(options) {
	var defaults = {
		speed: 700,
		pause: 5000,
		showItems: 1
	};

	var options = $.extend(defaults, options);

	moveUp = function(obj, height){
    	first = obj.children('ul').children('li:first').clone(true);
    	obj.children('ul')
    	.animate({top: '-=' + height + 'px'}, options.speed, function() {
        	$(this).children('li:first').remove();
        	$(this).css('top', '0px');
        });

    	first.appendTo(obj.children('ul'));
	};
	
	return this.each(function() {
		obj = $(this);
		maxHeight = 0;

		obj.css({overflow: 'hidden', position: 'relative'})
			.children('ul').css({position: 'absolute', margin: 0, padding: 0})
			.children('li').css({margin: 0, padding: 0});

		obj.children('ul').children('li').each(function(){
			if($(this).height() > maxHeight)
			{
				maxHeight = $(this).height();
			}
		});

		obj.children('ul').children('li').each(function(){
			$(this).height(maxHeight);
		});

		obj.height(maxHeight * options.showItems);
		
    	interval = setInterval('moveUp(obj, maxHeight)', options.pause);
	});
};
})(jQuery);