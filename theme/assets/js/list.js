'use strict';

$(document).ready(() => {
	let datatableAjaxUrl = `/admin/${$('.datatable').data('jsonpath')}?type=${$('.datatable').data('type')}&role=${$('.datatable').data('role')}`;

	$('.datatable').DataTable({
		ajax: datatableAjaxUrl,
		fnInitComplete: function () {
			rigDataTableRows();
		},
		deferRender: true,
		fixedHeader: true,
		language: {
			loadingRecords: '<div class="spinner-grow spinner-border-lg text-primary-3" role="status"><span class="sr-only">Loading...</span></div>'
		},
		dom: '<"#top.clearfix"ifl>rt<"#bottom"Bp>',
		pageLength: 50,
		columnDefs: [
			{
				targets: 0,
				checkboxes: {
					selectRow: true
				}
			}
		],
		select: {
			style: 'multi',
		},
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

// multi-select delete action for lists
function rigDataTableRows() {
	let tableRows = document.querySelectorAll('#dtList tbody tr');
	if (!tableRows) return;

	tableRows.forEach(tr => {
		tr.addEventListener('click', () => {
			setTimeout(() => {
				toggleMultiDeleteButton();
			}, 0);
		});
	});
}

function toggleMultiDeleteButton(e) {
	let selectedTableRows = document.querySelectorAll('tr.selected');

	document.getElementById('deleteTally').innerText = selectedTableRows.length ?? 0;


	if (selectedTableRows.length) {
		let hiddenDelButton = document.querySelector('button[data-attr="delete-multi"].d-none');
		if (!hiddenDelButton) return;

		hiddenDelButton.classList.remove('d-none');
	} else {
		let visibleDelButton = document.querySelector('button[data-attr="delete-multi"]:not(.d-none)');
		if (!visibleDelButton) return;

		visibleDelButton.classList.add('d-none');
	}
}

(() => {
	let delSelectedButton = document.getElementById('deleteSelected');
	if (!delSelectedButton) return;

	delSelectedButton.addEventListener('click', e => {
		e.preventDefault();
		let selectedRows = document.querySelectorAll('tr.selected > td:first-of-type');
		if (!selectedRows) return;

		let listForm = document.getElementById('dtList');

		let ids = [];
		selectedRows.forEach(td => ids.push(td.innerText));
		ids = JSON.stringify(ids);

		let hiddenInputIds = document.querySelector("input[name='ids'][type='hidden']");
		hiddenInputIds.value = ids;

		listForm.submit();
	})
})();
