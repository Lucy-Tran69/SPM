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
            //$('.menuOut').prop('checked', false);
        }

        if($('#outCompany').is(":checked")) {
            $('#inSideCompany').hide();
            $('#outSideCompany').show();
            //$('.menuIn').prop('checked', false);
        }
    });

    var formAddRole = $('#addRole');
    formAddRole.validate({
        rules: {
            roleName: {
                required: true,
            },
        },
        messages: {
            roleName: "権限名を入力して下さい。",
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
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

        if (menu == '') {
            var messages = '表示メニューを1つ以上選択してください。';
            $('#confirmMessageAdd').text(messages);

            $('#confirmMenuAdd').modal('show');

            $('#agreeAdd').on('click', function () {
                $('#confirmMenuAdd').modal('hide');
            });
         }

         if (menu != '') {
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
       }
    });
});

