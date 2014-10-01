/**
 * Created by karmis on 01.10.14.
 */

!(function ($) {
    'use strict';
    var form = $(".form-ajax");

    // Enable validation
    form.bootstrapValidator();

    form.unbind('submit').submit(function(){

        var data = new FormData(this);
        $.ajax({
            type:'POST',
            data:data,
            url: 'index.php',
            cache : false,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data){
                if(data.code == 200){
                    window.location.href = document.URL;
                } else {
                    debugger;
                   form.find('button').button('reset');
                }
            }
        })
        return false;
    })
})(jQuery);