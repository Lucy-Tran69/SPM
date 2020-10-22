$(function () {
    if ($('#inCompany').is(":checked")) {
        $('#inSideCompany').show();
        $('#outSideCompany').hide();
    }

    if ($('#outCompany').is(":checked")) {
         $('#inSideCompany').hide();
        $('#outSideCompany').show();
    }

    $(document).on('change', '.outSideCompany', function() {
        if($('#inCompany').is(":checked")) {
            $('#inSideCompany').show();
            $('#outSideCompany').hide();
            $('.menuOut').removeClass('is-invalid');
            $('#menuOut\\[\\]-error').html('');
        }

        if($('#outCompany').is(":checked")) {
            $('#inSideCompany').hide();
            $('#outSideCompany').show();
            $('.menuIn').removeClass('is-invalid');
            $('#menuIn\\[\\]-error').html('');
        }
    });

    var formAddRole = $('#addRole');
    formAddRole.validate({
        rules: {
            roleName: {
                required: true,
            },
             'menuIn[]': {
                required: true,
                minlength: 1
            },
            'menuOut[]': {
                required: true,
                minlength: 1
            },
        },
        messages: {
            roleName: "権限名を入力して下さい。",
            'menuIn[]': "表示メニューを1つ以上選択してください。",
            'menuOut[]': "表示メニューを1つ以上選択してください。",
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
            if ($(element).attr("name") == "menuIn[]") {
                $('.menuIn').each(function () {
                    $(this).addClass('is-invalid');
                    
                });
            }
            if ($(element).attr("name") == "menuOut[]") {
                $('.menuOut').each(function () {
                    $(this).addClass('is-invalid');
                   
                });
            }
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
            if ($(element).attr("name") == "menuIn[]") {
                $('.menuIn').each(function () {
                    $(this).removeClass('is-invalid');
                });
            }
            if ($(element).attr("name") == "menuOut[]") {
                $('.menuOut').each(function () {
                    $(this).removeClass('is-invalid');
                });
            }
        }
    });

    formAddRole.submit(function (event) {
        event.preventDefault();
        if (!formAddRole.valid()) {
            return false;
        }

        var outSide = '';
        var menu = [];

        if ($('#inCompany').is(":checked")) {
            outSide = $('#inCompany').val();
            $(".menuIn:checked").each(function () {
                menu.push($(this).val());
            });
        }

        if ($('#outCompany').is(":checked")) {
            outSide = $('#outCompany').val();

            $(".menuOut:checked").each(function () {
                menu.push($(this).val());
            });
        }

        if ($('#status').is(":checked")) {
            var status = $('#status').val();
        }

        var menuIn = [];
        var menuOut = [];
        var checkMenu = 0;
        $(".menuIn:checked").each(function () {
            menuIn.push($(this).val());
         });
         $(".menuOut:checked").each(function () {
            menuOut.push($(this).val());
         });

         if ($('#inCompany').is(":checked") && menuOut.length > 0) {
            var messages = 'Menu ngoài công ty thì không được lưu!';
            $('#confirmMessageAdd').text(messages);

            $('#confirmMenuAdd').modal('show');
        }

        if ($('#outCompany').is(":checked") && menuIn.length > 0) {
            var messages = 'Menu trong công ty thì không được lưu!';
            $('#confirmMessageAdd').text(messages);

            $('#confirmMenuAdd').modal('show');
        }

        $('#agreeAdd').on('click', function () {
         $.ajax({
            url: "addRole.php",
            type: "POST",
            data: {
                roleName: $('#roleName').val(),
                outSide: outSide,
                sortOrder: $('#sortOrder').val(),
                status: status,
                menu: menu
            },
            dataType: "html",
            success: function (response) {
                //check response is blank if success 
                if (!$.trim(response)) {
                    window.history.back();
                    $(window).scrollTop(0);
                }
                // if error
                else {
                    $("#flash-message").html(response);
                    $(window).scrollTop(0);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(errorThrown);
            },
        });
     });
    });
});

