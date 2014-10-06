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
                if(data.status == 'success'){
                    window.location.href = document.URL;
                } else {
                    var title = '';
                    if(data.status == 'validate'){
                        title = 'Ошибка валидации';
                    } else if(data.status == 'error'){
                        title = 'Важная ошибка';
                    }


                    $('#modal #title').text(title);
                    $('#modal #body').text(data.message);
                    $('#modal').modal();

                }
            }
        })
        return false;
    })
})(jQuery);