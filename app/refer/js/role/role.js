$(function () {
    $('#searchRole').submit(function (e) {
        e.preventDefault();
        var menu = $('#menu').val();
        var outSide = $('input[type="radio"]:checked').val();
        var status = $('#status').is(':checked') ? $('#status').val() : 0;

        $('#flash-message').remove();

        $.ajax({
            type: 'POST',
            url: 'roles.php',
            dataType: 'html',
            data: {
                menu: menu,
                outSide: outSide,
                status: status,
            },
            success: function (res) {
                $('#roleList').html(res);
                queryParams = '?menu=' + menu + '&outside=' + outSide + '&status=' + status;
                history.pushState(null, null, queryParams);
            },
        });
    });

    $('.sort').keypress(function (e) {
        if (isNaN(String.fromCharCode(e.which))) {
            e.preventDefault();
        }
    });

    $(document).on('click', '.change-sort-order', function (e) {
        e.preventDefault();
        var data = [];

        $('#topicsTable > tbody  > tr td:nth-child(3)')
            .not(':last')
            .each(function () {
                var $tr = $(this).closest('tr');
                data.push({
                    no: $($tr).attr('data-id'),
                    sortOrder: $(this).text().trim(),
                });
            });

        var duplicate = 0;
        for (var i = 0; i < data.length - 1; i++) {
            for (var j = 0; j < data[i]['sortOrder']; j++) {
                if (data[j]['sortOrder'] == data[i]['sortOrder']) {
                    alert('表示順は既に存在しています。もう一度お試しください。');
                    duplicate = 1;
                    return true;
                }
            }
        }

        if (duplicate == 0) {
            $.ajax({
                type: 'POST',
                url: 'roles.php',
                dataType: 'html',
                data: {
                    sortOrder: data,
                },
                success: function (res) {
                    $('#roleList').html(res);
                    location.reload();
                    $(window).scrollTop(0);
                },
            });
        }
    });
});

function redirectAddRole() {
    window.location.href = 'add-role.html';
}
