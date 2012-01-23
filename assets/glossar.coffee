$ ->

    # Display description in dialog
    $('a[href*="entries/display"]').live 'click', (event) ->
        event.preventDefault()
        title = $(@).text()
        href = $(@).attr 'href'
        row = $(@).closest 'tr'
        $('<div/>').load href, ->
            $(@).dialog
                title  : title
                modal  : true
                width  : $(window).width() * 3 / 4,
                height : $(window).height() * 3 / 4,
                buttons: [
                    text : 'Bearbeiten'.toLocaleString()
                    click: ->
                        location.href = $('a[href*=edit]', row).attr 'href'
                ,
                    text: 'Löschen'.toLocaleString()
                    click: ->
                        location.href = $('a[href*=delete]', row).attr 'href'
                ,
                    text: 'Abbrechen'.toLocaleString()
                    click: ->
                        $(@).dialog 'close'
                ]
                open   : ->
                    $(@).parent().find('.ui-dialog-buttonpane button').first().focus()

    # Collapsable forms
    collapsables = $('fieldset.collapsable')
    $('legend', collapsables)
        .wrapInner('<a/>')
        .click ->
            fieldset = $(@).closest 'fieldset'
            fieldset.toggleClass 'collapsed'

    $('a.cancel', collapsables).click (event) -> 
        fieldset = $(@).closest 'fieldset'
        fieldset.addClass 'collapsed'
        $('input,textarea', fieldset).each ->
            @value = @defaultValue
        event.preventDefault()

    # Collapsable definition lists
    collapsables = $('dl.collapsable > dt')
    collapsables.live 'click', ->
        $(@).toggleClass 'collapsed'
    collapsables.click()

    # Pagination via ajax
    load_url = (href) ->
        $.get href, (result) ->
            $('.paginated').replaceWith $('.paginated', result)
            $('.pagination').replaceWith $('.pagination', result)

    $('.pagination a').live 'click', (event) ->
        href = $(@).attr('href');
        load_url(href);
        history.pushState url: href, '', href if history.pushState
        event.preventDefault()

    window.onpopstate = (event) ->
        load_url event.state.url if event.state and event.state.url
