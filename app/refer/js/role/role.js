function loadAjax(c) {
    var menu = $('#menu').val();
    var outSide = $('input[type="radio"]:checked').val();
    var status = $('#status').val();

    if ($('#status').is(':checked')) {
        status = 1;
    }

    if (c == false) {
          if (!$('#status').is(':checked')) {
            status = 0;
        }
    }

    $.ajax({
        type: 'POST',
        url: 'roles.php',
        dataType: 'html',
        data: {
            menu: menu,
            outSide: outSide,
            status: status,
        },
        success: function(res) {
            $('#roleList').html(res);

            if (c == false) {
                queryParams = '?menu=' + menu + '&outside=' + outSide + '&status=' + status;
                history.pushState(null, null, queryParams);
            }
        },
    });

}
$(function() {
    loadAjax(true);
    $('#searchRole').submit(function(e) {
        e.preventDefault();
        loadAjax(false);
        $('#flash-message').remove();
    });

    $('.sort').keypress(function(e) {
        if (isNaN(String.fromCharCode(e.which))) {
            e.preventDefault();
        }
    });

    $(document).on('click', '.change-sort-order', function(e) {
        e.preventDefault();
        var data = [];

        $('#topicsTable > tbody  > tr td:nth-child(3)')
            .not(':last')
            .each(function() {
                var $tr = $(this).closest('tr');
                data.push({
                    no: $($tr).attr('data-id'),
                    sortOrder: $(this).text().trim(),
                });
            });

        var duplicate = 0;
        for (var i = 0; i < data.length - 1; i++) {
            for (var j = i + 1; j < data.length; j++) {
                if (data[j]['sortOrder'] == data[i]['sortOrder']) {
                    var messages = '表示順は既に存在しています。もう一度お試しください。';
                    $('#duplicateSortOrderMessage').text(messages);
                    $('#duplicateSortOrder').modal('show');
                    return true;
                    duplicate = 1;
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
                success: function(res) {
                    $('#roleList').html(res);
                    location.reload();
                    $(window).scrollTop(0);
                },
            });
        }
    });

    $('#yes').on('click', function() {
        $('#duplicateSortOrder').modal('hide');
    });
});

function redirectAddRole() {
    window.location.href = 'add-role.html';
}