

$.fn.fancigrid = function(e){

	$.extend({}, e);

	var cols = new Array();

	// Pasando los headers de la tabla a la tabla 2
	var fgrid = $(this);
	var thID = fgrid.attr("id");
	var h = $('<table id="'+ thID +'-th" class="fancigrid fanci-thead"></table>')
					.html(this.find('thead'))
					//.addClass(cfg.gridClass)
					//.addClass(cfg.headerClass)
					//.height(cfg.headerHeight)
					.extend({
						cols : cols
					});

	fgrid.parent().prepend(h);

    /*$("tbody tr:last td", grid).each(function(index){
        var tdWidth = $(this).outerWidth();
        $("thead th", grid).eq(index).width(tdWidth);
        $(this).width(tdWidth);
    });*/
};