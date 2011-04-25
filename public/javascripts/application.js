Event.observe(window, 'load', function(e){
	$$('table td:nth-child(1)').each(function(td) {
		td.addClassName('first');
	});
	
	$$("ul li").each(function(elem) {

		elem.observe('mouseover', function() {
		  this.addClassName('hover');
		});
		elem.observe('mouseout', function() {
		  this.removeClassName('hover');
		});
	});
	
	$$('input[type=checkbox]').invoke('addClassName', 'checkbox');
	
});




