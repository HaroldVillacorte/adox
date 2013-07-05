$(document).ready(function() {

    /**
     * Ajax show busy.
     */
    function showBusy()
    {
        $('#ajax-content').html('<div><img id="load-gif" src="images/load.gif" /></div>');
    }

    /**
     * Ajax Update page.
     * @param {string} html Ajax content.
     */
    function updatePage(html) {
        $('#ajax-content').hide().html(html).fadeIn();
    }

    /**
     * Main ajax links.
     */
    /*$('.ajax-link').click(function(event) {
        event.preventDefault();
        var link = $(this).attr('href');
        $.ajax({
            url: link,
            type: 'GET',
            dataType: 'html',
            beforeSend: function()
            {
              showBusy();
            },
            success: function(html)
            {
              updatePage(html);
            }
        });
    });*/

});

$(document).on('submit', '#ajax-content form', function(event) {

    //event.preventDefault();

    //var values = $(this).serialize();
    //var link = $(this).attr('action');

    /**
     * Ajax show busy.
     */
    function showBusy()
    {
        $('#ajax-content').html('<h6>Please wait...</h6>');
    }

    /**
     * Ajax Update page.
     * @param {string} html Ajax content.
     */
    /*function updatePage(html) {
        $('#ajax-content').hide().html(html).fadeIn();
    }

    $.ajax({
        url: link,
        type: 'POST',
        data: values,
        dataType: 'html',
        beforeSend: function()
        {
          showBusy();
        },
        success: function(html)
        {
          updatePage(html);
        }

    });
    console.log(link);*/

});
