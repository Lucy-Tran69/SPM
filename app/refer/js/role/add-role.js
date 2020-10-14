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

	 var formAddRole = $('#addRole');
    formAddRole.validate({
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
});

function redirectListRole() {
	window.location.href = "index.html";
}