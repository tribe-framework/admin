'use strict';

$(document).ready(() => {

	let isProcessing = false;
	let isServerSide = false;

	if ($('.datatable').data('lazyload')) {
		isProcessing = true;
		isServerSide = true;
	}

	let datatableAjaxUrl = `/admin/${$('.datatable').data('jsonpath')}?type=${$('.datatable').data('type')}&role=${$('.datatable').data('role')}`;

	let datatableOptions = {
        processing: isProcessing,
        serverSide: isServerSide,
		ajax: datatableAjaxUrl,
		drawCallback: function () {
			popoverActivate();
			rigDataTableRows();
		},
		rowId: [0],
		deferRender: true,
		fixedHeader: true,
		dom: '<"#top.d-flex"iflp>rt<"#bottom1.d-flex"iflp><"#bottom2"B>',
		lengthMenu: [ [10, 25, 50, 100, 250, 500, 1000, 2500, 10000, 25000], [10, 25, 50, 100, 250, 500, 1000, 2500, 10000, 25000] ],
		pageLength: 50,
		columnDefs: [{
			targets: 0,
		}],
		select: {
			style: 'multi',
			selector: 'td:first-child'
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
		}],
		"language": {
			"info": "_START_ to _END_ of _TOTAL_",
			"emptyTable": "No records found",
    		"infoEmpty":      "0 to 0 of 0",
    		"infoFiltered":   "(from _MAX_)",
    		"infoPostFix":    "",
    		"thousands":      ",",
    		"lengthMenu":     "_MENU_ / page",
    		"loadingRecords": '<div class="spinner-grow spinner-border-lg text-primary-3" role="status"><span class="sr-only">Loading...</span></div>',
    		"processing":     '<div class="spinner-grow spinner-border-lg text-primary-3" role="status"><span class="sr-only">Loading...</span></div>',
    		"search":         "_INPUT_",
    		"searchPlaceholder": "Search...",
    		"zeroRecords":    "No records found",
		    "paginate": {
		        "first":      "&#8656;",
		        "last":       "&#8658;",
		        "next":       "&gt;",
		        "previous":   "&lt;"
		    }
		}
	};

	let listDatatable = $('.datatable').DataTable(datatableOptions);

	$(document).on('click', '.editModalClose', function(e) {
		let id = $(this).attr('data-id');
		let is_new = $(this).attr('data-is_new');

		if (id) {
			if (is_new)
				location.reload();
			else {
		        $.post('/admin/single-datatable-json', {"id": id}, function(data) {
		        	//REPLACE RECORD AT ROW NUMBER
					listDatatable.row('#'+id).data(data);
		        }, 'json');
		    }
		}
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

	let selectedListCount = document.querySelectorAll('.selectedListCount')
	selectedListCount.forEach(sel => sel.innerText = selectedTableRows.length ?? 0);


	if (selectedTableRows.length) {
		let actionBtnGroup = document.querySelector('div#edit-btn-group.d-none');
		if (!actionBtnGroup) return;

		actionBtnGroup.classList.remove('d-none');
	} else {
		let actionBtnGroup = document.querySelector('div#edit-btn-group:not(.d-none)');
		if (!actionBtnGroup) return;

		actionBtnGroup.classList.add('d-none');
	}
}

function popoverActivate() {

	//Popover to show linked data
	$('[data-toggle="popover"]').popover();
	$('.popover-dismiss').popover({
	  trigger: 'focus'
	});
	$('[data-toggle="popover"]').on('inserted.bs.popover', function () {
		$('.popover-body').html('<div class="spinner-grow spinner-border-sm text-dark" role="status"><span class="sr-only">Loading...</span></div>');
        $.post('/admin/linked-data-json', {"type": $(this).data('linked_type'), "slug": $(this).data('linked_slug'), "field": $(this).data('linked_display_module')}, function(data) {
            $('.popover-body').html(data.html);
        }, 'json');
	});

}

(() => {
	let delSelectedButton = document.getElementById('deleteSelected');
	let duplicateSelectedBtn = document.querySelector("#duplicateConfirm button[type='submit']");
	if (!delSelectedButton && !duplicateSelectedBtn) return;

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

	duplicateSelectedBtn.addEventListener('click', e => {
		e.preventDefault();

		document.querySelector('#duplicateConfirm button[data-dismiss]').click();

		let selectedRows = document.querySelectorAll('tr.selected > td:first-of-type');
		if (!selectedRows) return;

		let ids = [];
		selectedRows.forEach(td => ids.push(td.innerText));

		axios.post('/admin/copy-dt-records', {
			ids
		}).then(() => {
			$('#toast-success').toast('show');
		}).catch(err => {
			console.log(err);
		})
	})
})();
