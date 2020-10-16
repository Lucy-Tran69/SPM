$(function () {
	$('#searchRole').submit(function (e){
		e.preventDefault();
		$('#flash-message').remove();
		var menu = $('#menu').val();
		var outSide = $('input[type="radio"]:checked').val();
		var status = $('input[name="status"]:checked').val();
		$.ajax({
			type : "POST",
			url : "roles.php",
			dataType: "html",                 
			data : 
			{
				menu: menu,
				outSide: outSide,
				status: status
			},
			success : function(res) {
				$('#roleList').html(res);
			}
		});
	});

	$('.sort').keypress(function(e) {
		if (isNaN(String.fromCharCode(e.which))) {
			e.preventDefault();
		}
	});

	$(document).on("click",".change-sort-order", function(e) {
		e.preventDefault();
		var data = [];

		$('#topicsTable > tbody  > tr td:nth-child(3)').not(':last').each(function(){
			var $tr = $(this).closest("tr");
			data.push({
				no : $($tr).attr("data-id"),
				sortOrder : $(this).text().trim()
			});
		});

		var duplicate = 0;
		data = data.sort();
		for (var i = 0; i < data.length -1 ; i++) {
			if (data[i+1]['sortOrder'] == data[i]['sortOrder']) {
				alert("表示順は既に存在しています。もう一度お試しください。");
				duplicate = 1;
				return true;
			}
		}

		if (duplicate == 0) {
			$.ajax({
			type : "POST",
			url : "roles.php",
			dataType: "html",                 
			data : 
			{
				sortOrder : data
			},
			success : function(res) {
				$('#roleList').html(res);
				location.reload();
				$(window).scrollTop(0);
			}
		});
		}
	});
});

function redirectAddRole() {
	window.location.href = "add-role.html";
}
