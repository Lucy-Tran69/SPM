$(function () {
	$('#searchRole').submit(function (e){
		e.preventDefault();
		var menu = $('#menu').val();
		var outSide = $("input[type='radio']:checked").val();
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
		// debugger
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
				alert("Sort order is duplicate. Please input again!");
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
			}
		});
		}
	});

	// $('.sort').on("keyup", function(e) {
	// 	debugger
		
	// 	var row = $(this).closest('td');
	// 	var $tr = $(this).closest("tr");
	// 	var sortValue = $tr.find('.sort').text();

	// 	if (sortValue == '') {
	// 		alert("Please input sort order!");
	// 	}

	// 	var data = [];
	// 	var id = $($tr).attr("data-id");
	// 	$('#topicsTable > tbody  > tr td:nth-child(3)').not(row).each(function(){
	// 		data.push($(this).text().trim());
	// 	});


	// 	console.log(data);

	// });
});
