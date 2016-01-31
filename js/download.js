// Set few parameters on opening #downloadModal
$('#downloadModal').on('show.bs.modal', function(e) {
	var formaturl = e.relatedTarget.dataset.formaturl;
	var filename = e.relatedTarget.dataset.filename;
	$("#downloadModal #download-link").attr("href", formaturl);
	$("#downloadModal input").val(filename);
});

// Select whole text of #downloadModal input on focus
$("#downloadModal input").on('focus', function(e) {
	$(this).select();
});