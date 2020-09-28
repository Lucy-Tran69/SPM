$(function() {
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

    $("#openDay").on("change.datetimepicker", function(e) {
        $('#closeDay').datetimepicker('minDate', e.date);
    });
    $("#closeDay").on("change.datetimepicker", function(e) {
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

    // $.validator.addMethod('compareDate', function(value, element) {
    //     var openDay = $('.openDay').val();
    //     return Date.parse(openDay) <= Date.parse(value) || value == "";
    // }, 'Please select close day greater than or equal to open day.');

    // $.validator.addMethod('emptyFile', function (value, element) {
    //        var imgFile = $('.file').attr('value');
    //         return imgFile == "";
    //     }, '画像を入力して下さい。');

    $('#editTopic').validate({
        rules: {
            title: {
                required: true,
            },
            body: {
                required: true,
            },
            // imgFile: {
            //     emptyFile: {
            //         required: true,
            //     },
            // },
            // closeDay: {
            //     compareDate: {
            //         required: true,
            //     },
            // },
        },
        messages: {
            title: "タイトルを入力して下さい。",
            body: "本文を入力して下さい。",
            // imgFile: {
            //     emptyFile : "画像を入力して下さい。",
            // },
            // closeDay: {
  //     compareDate: "Please select close day greater than or equal to open day",
  // }
        },
        errorElement: 'span',
        errorPlacement: function(error, element) {
            error.addClass('invalid-feedback');
            element.closest('.form-group').append(error);
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass('is-invalid');
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
});

function redirectAddOrEditTopic() {
    window.location.href = "../../app/topic/add-topic.html";
}