$( document ).ready(function() {
	$('.datatable').DataTable({
		fixedHeader: true,
		dom: '<"top"ifl>rt<"bottom"Bp>',
		pageLength:50,
		order: [[ 0, "desc" ]],
        buttons: [{
            extend: 'collection',
            text: '<i class="fas fa-file-export mr-1"></i> Export data',
            buttons: [
				{
					"extend": 'excel',
					"text": '<i class="fas fa-file-excel mr-1"></i> .xlsx',
					"title": 'data'
				},
				{
					"extend": 'pdf',
					"text": '<i class="fas fa-file-pdf mr-1"></i> .pdf',
					"title": 'data'
				}
        	]
        }]
	});
});
