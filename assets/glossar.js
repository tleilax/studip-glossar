$(function() {
  var collapsables, load_url;
  $('a[href*="entries/display"]').live('click', function(event) {
    var href, row, title;
    event.preventDefault();
    title = $(this).text();
    href = $(this).attr('href');
    row = $(this).closest('tr');
    return $('<div/>').load(href, function() {
      return $(this).dialog({
        title: title,
        modal: true,
        width: $(window).width() * 3 / 4,
        height: $(window).height() * 3 / 4,
        buttons: [
          {
            text: 'Bearbeiten'.toLocaleString(),
            click: function() {
              return location.href = $('a[href*=edit]', row).attr('href');
            }
          }, {
            text: 'Löschen'.toLocaleString(),
            click: function() {
              return location.href = $('a[href*=delete]', row).attr('href');
            }
          }, {
            text: 'Abbrechen'.toLocaleString(),
            click: function() {
              return $(this).dialog('close');
            }
          }
        ],
        open: function() {
          return $(this).parent().find('.ui-dialog-buttonpane button').first().focus();
        }
      });
    });
  });
  collapsables = $('fieldset.collapsable');
  $('legend', collapsables).wrapInner('<a/>').click(function() {
    var fieldset;
    fieldset = $(this).closest('fieldset');
    return fieldset.toggleClass('collapsed');
  });
  $('a.cancel', collapsables).click(function(event) {
    var fieldset;
    fieldset = $(this).closest('fieldset');
    fieldset.addClass('collapsed');
    $('input,textarea', fieldset).each(function() {
      return this.value = this.defaultValue;
    });
    return event.preventDefault();
  });
  collapsables = $('dl.collapsable > dt');
  collapsables.live('click', function() {
    return $(this).toggleClass('collapsed');
  });
  collapsables.click();
  load_url = function(href) {
    return $.get(href, function(result) {
      $('.paginated').replaceWith($('.paginated', result));
      return $('.pagination').replaceWith($('.pagination', result));
    });
  };
  $('.pagination a').live('click', function(event) {
    var href;
    href = $(this).attr('href');
    load_url(href);
    if (history.pushState) {
      history.pushState({
        url: href
      }, '', href);
    }
    return event.preventDefault();
  });
  return window.onpopstate = function(event) {
    if (event.state && event.state.url) return load_url(event.state.url);
  };
});