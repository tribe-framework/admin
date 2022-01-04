'use strict';

$(document).ready(() => {
	let datatableAjaxUrl = `/admin/${$('.datatable').data('jsonpath')}?type=${$('.datatable').data('type')}&role=${$('.datatable').data('role')}`;

	$('.datatable').DataTable({
		ajax: datatableAjaxUrl,
		deferRender: true,
		fixedHeader: true,
		language: {
			loadingRecords: '<div class="spinner-grow spinner-border-lg text-primary-3" role="status"><span class="sr-only">Loading...</span></div>'
		},
		dom: '<"#top.clearfix"ifl>rt<"#bottom"Bp>',
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

	$('#analysisTable').DataTable({
		dom: '<"#top.clearfix"fl>rt<"#bottom.clearfix"ip>',
		pageLength: 100
	});
});
