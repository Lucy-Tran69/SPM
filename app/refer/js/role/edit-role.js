$(function () {
	if ($('#inCompany').is(":checked")) {
		$('#inSideCompany').show();
		$('#outSideCompany').hide();
	}

	if ($('#outCompany').is(":checked")) {
		 $('#inSideCompany').hide();
        $('#outSideCompany').show();
	}

	$(document).on('change', '.outSideCompany', function(e) {
		e.preventDefault();
		if($('#inCompany').is(":checked")) {
			$('#inSideCompany').show();
			$('#outSideCompany').hide();
		}

		if($('#outCompany').is(":checked")) {
			$('#inSideCompany').hide();
			$('#outSideCompany').show();
		}
	});

	// $.validator.addMethod('minSort', function (value, el, param) {
	// 	return value > param;
	// });

	 var formEditRole = $('#editRole');
    formEditRole.validate({
        rules: {
            roleName: {
                required: true,
            },
            // sortOrder: {
            // 	minSort: 0
            // }
        },
        messages: {
            roleName: "権限名を入力して下さい。",
            // sortOrder: "Please select number greater than 0"
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

    formEditRole.submit(function (event) {
        event.preventDefault();
        if (!formEditRole.valid()) {
            return false;
        }

        var outSide = '';
        var menu = [];

        if ($('#inCompany').is(":checked")) {
        	outSide = $('#inCompany').val();
        }

        if ($('#outCompany').is(":checked")) {
        	outSide = $('#outCompany').val();
        }

         if ($('#status').is(":checked")) {
        	var status = $('#status').val();
        }

         $("input[name=menu]:checked").each ( function() {
                menu.push($(this).val());
         });

        $.ajax({
            url: "editRole.php",
            type: "POST",
            data: {
                no : $('#no').val(),
            	roleName : $('#roleName').val(),
            	outSide : outSide,
            	sortOrderOld : $('#sortOrderOld').val(),
                sortOrderNew : $('#sortOrderNew').val(),
            	status : status,
            	menu : menu
            },
            dataType: "html",
            success: function (response) {
                //check response is blank if success 
                if (!$.trim(response)) {
                    window.location.href = "index.html";
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

