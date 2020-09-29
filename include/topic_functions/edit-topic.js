$(function () {
    let day = $('.openDay').val();
    var date = new Date();
    $('#openDay').datetimepicker({
        useCurrent: false,
        date: day,
        format: 'YYYY-MM-DD HH:mm:ss'
    });

    $('#closeDay').datetimepicker({
        useCurrent: false,
        minDate: date,
        format: 'YYYY-MM-DD HH:mm:ss'
    });

    $("#openDay").on("change.datetimepicker", function (e) {
        $('#closeDay').datetimepicker('minDate', e.date);
    });
    $("#closeDay").on("change.datetimepicker", function (e) {
        $('#openDay').datetimepicker('maxDate', e.date);
    });

    // /*add method validate date format*/
    // $.validator.addMethod('dateFormat', function (value, element) {
    //         let regEx = /^\d{4}-\d{2}-\d{2}$/;
    //         if (!value.match(regEx)) return false;

    //         let inputDate = new Date(value);
    //         let currentDate = new Date();

    //         if (!inputDate.getTime() && inputDate.getTime() !== 0) return false;

    //         return inputDate.toISOString().slice(0, 10) === value;
    //     }, 'Please enter a date in the format yyyy-mm-dd.');

    $('#editTopic').validate({
        rules: {
            title: {
                required: true,
            },
            body: {
                required: true,
            },
        },
        messages: {
            title: "タイトルを入力して下さい。",
            body: "本文を入力して下さい。",
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
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
});

