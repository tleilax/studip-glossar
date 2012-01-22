jQuery(function ($) {

	// Display description in dialog
	$('a[href*="entries/display"]').live('click', function () {
		var title = $(this).text(),
			row = $(this).closest('tr'),
			href = $(this).attr('href');
		$('<div/>').load(href, function () {
			$(this).dialog({
				title: title,
				modal: true,
				width: $(window).width() * 3 / 4,
				height: $(window).height() * 3 / 4,
				buttons: [{
						text: 'Bearbeiten'.toLocaleString(),
						click: function () { location.href = $('a[href*=edit]', row).attr('href'); }
					}, {
						text: 'Löschen'.toLocaleString(),
						click: function () { location.href = $('a[href*=delete]', row).attr('href'); }
					}, {
						text: 'Abbrechen'.toLocaleString(),
						click: function () { $(this).dialog('close'); }
					}],
				open: function () {
					$(this).parent().find('.ui-dialog-buttonpane button').first().focus();
				}
			})
		});
		return false;
	});

	// Collapsable forms
	$('fieldset.collapsable legend').click(function () {
		$(this).closest('fieldset').toggleClass('collapsed');
	}).wrapInner('<a/>');
	$('fieldset.collapsable a.cancel').click(function () {
		$(this).closest('fieldset').addClass('collapsed')
			.find('input,textarea').each(function () {
				this.value = this.defaultValue;
			});
		return false;
	});
	
	// Collapsable definition lists
	$('dl.collapsable > dt').live('click', function () {
		$(this).toggleClass('collapsed');
	}).click();

	// Pagination via ajax
	function load_url(href) {
		$.get(href, function (result) {
			var table = $('.paginated', result),
				pagination = $('.pagination', result);
			$('.paginated').replaceWith(table);
			$('.pagination').replaceWith(pagination);
		});
	}
	$('.pagination a').live('click', function () {
		var href = $(this).attr('href');
		load_url(href);
		if (history.pushState) {
			history.pushState({url: href}, '', href);
		}
		return false;
	});
	window.onpopstate = function (event) {
		load_url(event.state.url);
	};
	
});