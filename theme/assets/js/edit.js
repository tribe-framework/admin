'use strict';

function activateTableButtons () {
	let editButtons = document.querySelectorAll(".edit_button:not([data-event-set='1'])");
	if (!editButtons) return;

	editButtons.forEach(btn => {
		if (typeof btn.onclick != 'loadEditForm') {
			btn.addEventListener('click', async e => await loadEditForm(e));
			btn.dataset.eventSet = 1;
		}
	})
}

async function loadEditForm (e) {
	e.preventDefault();

	let btn = e.target.closest('a.edit_button');
	if (!btn) return;

	if (btn.dataset.id) {
		document.querySelector('#editModal .modal-title').innerText = `#${btn.dataset.id}`;
		document.querySelector('.editModalClose').dataset.is_new = '';
	} else {
		document.querySelector('#editModal .modal-title').innerText = `New ${btn.dataset.type} ${btn.dataset.role}`;
		document.querySelector('.editModalClose').dataset.is_new = '1';
	}

	let modalBody = document.querySelector('#editModal .modal-body');
	modalBody.innerHTML = '<div class="d-flex justify-content-center py-4"><div class="spinner-grow spinner-border-lg text-primary-3" role="status"><span class="sr-only">Loading...</span></div></div>';

	modalBody.innerHTML = await loadEditFormContent(btn.dataset.href);

	refreshEditForm();
	enableEditFormButtons();
	initEditorJs();
	initTypeOut();
}

async function initEditorJs () {
	if (!document.querySelector('#editojs')) {
		return;
	}

	try {
		//EditorJS init
		let editor = new EditorJS({
			tools:{
				header:Header,
				delimiter: Delimiter,
				paragraph: {
					class: Paragraph,
					inlineToolbar: true,
				},
				embed: Embed,
				image: SimpleImage,
			}
		});
		await editor.isReady;
	} catch (reason) {
		console.error(`Editor.js initialization failed because of ${reason}`)
	}
}

function enableEditFormButtons () {
	enableCopyButton();

	let closeButton = document.querySelector('button.close_btn');

	if (closeButton) {
		closeButton.addEventListener('click', () => {
			document.querySelector('.editModalClose').click();
		});
	}

	let delButton = document.querySelector('button.delete_btn');
	if (delButton) {
		delButton.addEventListener('click', e => {
			formDelete(e);
		})
	}
}

async function loadEditFormContent (link) {
	let response = await fetch(link);
	response = await response.text();
	return response;
}

// adds functionality to form buttons once they're loaded
function refreshEditForm() {
	// assign key shortcuts
	key('⌘+s, ctrl+s', function(e){$('.save_btn').trigger('click'); e.preventDefault();});
	key('⌘+b, ctrl+b', function(e){$('.typeout-bold').trigger('click'); e.preventDefault();});
	key('⌘+i, ctrl+i', function(e){$('.typeout-italic').trigger('click'); e.preventDefault();});

	if ($('.multi_drop_select_table').length) {
		$('.multi_drop_select_table').DataTable({
			"dom": '<"top"f>rt<"bottom">',
			"pageLength":50,
			"order": [[ 0, "desc" ]]
		});
	}

	$('.typeout-content').each(function() {
		update_textarea($(this).data('input-slug'));
	});

	$(document).on('keyup', '.typeout-content', function() {
		update_textarea($(this).data('input-slug'));
	});

	$(document).on('blur', '.typeout-content', function() {
		update_textarea($(this).data('input-slug'));
	});

	// multi add button
	(() => {
		let multiAddBtn = document.querySelectorAll('.multi_add_btn');
		if (!multiAddBtn) return;

		multiAddBtn.forEach(mab => {
			mab.addEventListener('click', function (e) {
				addNewTextArea(e);
			})
		})
	})();


	let multiAddBtn = document.querySelectorAll('.btn.multi_add');

	if (multiAddBtn) {
		const listIndexes = {};

		multiAddBtn.forEach(btn => {
			btn.addEventListener('click', function (e) {
				e.preventDefault();

				let buttonParentWrapper = e.target
					.closest('button')
					.closest('.multi_add_btn_parent');

				let listId = buttonParentWrapper.previousElementSibling.id;

				let inputForm = buttonParentWrapper
					.previousElementSibling
					.querySelector('div.dragula:last-of-type')
					.cloneNode(true);

				window.iform = inputForm;

				if (!listIndexes[listId]) {
					listIndexes[listId] = Number(inputForm.querySelector('.input-group-prepend > span').innerText);

					buttonParentWrapper.querySelector('p.d-none').classList.remove('d-none');
				}

				listIndexes[listId] = listIndexes[listId] + 1;

				inputForm.querySelector('.input-group-prepend > span').innerText = listIndexes[listId];
				inputForm.querySelector('input').value = "";
				inputForm.querySelector('.btn.delete-multi-form')
					.addEventListener('click', e => dropMultiFormField(e));

				buttonParentWrapper.querySelector('.input-append').appendChild(inputForm);
			});
		})
	}

	let deleteFormBtn = document.querySelectorAll('.btn.delete-multi-form');
	if (deleteFormBtn) {
		deleteFormBtn.forEach(btn => {
			btn.addEventListener('click', e => dropMultiFormField(e));
		});
	}

	$(document).on('click', '.remove_multi_drop_option', function(e) {
		e.preventDefault();
		$(this).closest('.grid-item').remove();
	});

	dragula({
	  isContainer: function (el) {
		return el.classList.contains('dragula-container');
	  },
	  direction: 'vertical'
	});

	$(document).on('click', '.select_multi_drop_option', function(e) {
		e.preventDefault();
		$('#'+$(this).data('multi_drop_filled_table')+' .grid').append('<div class="bg-light grid-item w-100 p-3">'+$('#'+$(this).data('multi_drop_option_text')).text()+' <a href="#" class="float-right remove_multi_drop_option"><span class="fas fa-minus-circle"></span></a><input type="hidden" name="'+$(this).parent().data('name')+'" value="'+$(this).parent().data('value')+'"></div>');

		var $grid = $('.grid').packery({
		  itemSelector: '.grid-item'
		});
		$grid.find('.grid-item').each( function( i, gridItem ) {
		  var draggie = new Draggabilly( gridItem );
		  $grid.packery( 'bindDraggabillyEvents', draggie );
		});
	});

	$(document).on('click', '.delete_btn', function(e) {
		$(this).closest('div.file').remove();
	});

	// code to handle file uploads
	let sli=0;
    $('.edit_form input[type=file]').fileupload({
		dataType: 'json',

		// this callback gets invoked as soon as a file is added to upload request queue
		add: function(e, data) {
			$('#progress').parent().removeClass('d-none');
		    data.context = $('<div class="mt-2 mb-0 pb-2 file dragula d-flex justify-content-between align-items-center">')
				.append($('<span class="flex-grow-1">').text(data.files[0].name))
				.appendTo('#'+$(this).attr('id')+'_fileuploads');
		    data.submit();
		},

		// callback for upload progress events
		progress: function(e, data) {
		    var progress = parseInt((data.loaded / data.total) * 100, 10);
		    $('#progress .bar').css('width', progress + '%');
		},

		// callback for successful upload requests
		done: function(e, data) {
			sli++;
			let slvl='';

			if ($(this).data('bunching')) {
				slvl+='&nbsp;&nbsp;<select class="btn btn-sm btn-outline-primary" name="'+$(this).attr('id')+'_bunching[]'+'">';
				slvl+='<option value="">file option</option>';
				$.each($(this).data('bunching'), function(i, item) {
					slvl+='<option value="'+item.slug+'">'+item.title+'</option>';
				});
				slvl+='</select>';
			}

			if ($(this).data('descriptor')) {
				slvl += `<button
						type="button"
						class="btn btn-sm btn-outline-primary m-1 text-capitalize"
						data-toggle="modal"
						data-target="#${$(this).attr('id')}_descriptor_${sli}"
						><i class="fas fa-align-left mr-1"></i>descriptor
					</button>
					<div class="modal fade" id="${$(this).attr('id')}_descriptor_${sli}" data-keyboard="false" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered">
							<div class="modal-content shadow-lg">
								<div class="modal-header">
									<h5 class="modal-title">add file descriptor</h5>
									<button type="button" onclick="handleDescriptorClose(event)" class="close" data-target="#${$(this).attr('id')}_descriptor_${sli}" aria-label="close">
										<span aria-hidden="true">&times;</span>
									</button>
								</div>
								<div class="modal-body">
									<textarea
										name="${$(this).attr('id')}_descriptor[]"
										class="form-control"
										placeholder="enter file descriptor"
									></textarea>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-sm btn-primary" data-target="#${$(this).attr('id')}_descriptor_${sli}" onclick="handleDescriptorClose(event)">save</button>
								</div>
							</div>
						</div>
					</div>`;
			}

			document.querySelector('#progress .bar').classList.add('d-none');

			window.upData = data;

			if (!data.result.files[0].url) {
				data.context.addClass('alert-danger px-2 border').removeClass('file pb-2');
				data.context.append(`<span>${data.result.files[0].error}</span>`);
				return;
			}

			const isImage = data.result.files[0].type.includes('image');
			if (isImage) {
				data.context
					.prepend(`
						<span class="fas fa-check-circle text-success mr-2">
						<img class="thumb-preview" src="${data.result.files[0].url}">
					`);
			} else {
				data.context
					.prepend('<span class="fas fa-check-circle text-success mr-2">');
			}

		    data.context
		      .append(`<div class="btn-group">
					<span class="delete_btn btn btn-sm btn-outline-danger px-3"><i class="fas fa-trash-alt"></i></span>
					<input type="hidden" name="${$(this).attr('id')}[]" value="${data.result.files[0].url}">
					<span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize" data-clipboard-text="${data.result.files[0].url}"><i class="fas fa-copy mr-1"></i>&nbsp;copy URL</span>
					<span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize" data-clipboard-text="[[${data.result.files[0].url}]]"><i class="fas fa-copy mr-1"></i>&nbsp;copy shortcode</span>
					<a style="display: inline-block;" class="btn btn-sm btn-outline-primary px-3 text-capitalize" href="${data.result.files[0].url}" target="new"><i class="fas fa-external-link-alt mr-1"></i>&nbsp;view</a>
					</div>${slvl}`)
		      .addClass("done");

		}
    });

    // submit form over ajax - used in edit.php
    (() => {
        let editForm = document.querySelector('.edit_form');
		if (!editForm || typeof FORM_IS_PAGE != 'undefined') return;

        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            $('.save_btn').html('<div class="spinner-border spinner-border-sm mb-1" role="status"><span class="sr-only">Loading...</span></div>&nbsp;Save');
            $('.save_btn').prop('disabled', true);

            let res = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })

			res = await res.json()

			if (res) {
				$('.save_btn').html('<span class="fa fa-save"></span>&nbsp;Save');
				$('.save_btn').prop('disabled', false);
				$('.view_btn').removeClass('disabled').attr('href', res.last_data[0].url);
				$('#save-success').toast('show');
				$('input[name=id]').val(res.last_data[0].id);
				$('input[name=slug]').val(res.last_data[0].slug);
				$('.object_slug').text(res.last_data[0].slug);
				$('.editModalClose').attr('data-id', res.last_data[0].id);
				$('#editModal .modal-title').text('#'+res.last_data[0].id);
				$('#slug_update').prop('checked', false);
				$('#slug_update_div').removeClass('d-none');
			}
        });
    })()
}

// used with div.multi_add_btn
function addNewTextArea (e) {
	e.preventDefault();
	let button = e.target.closest('button');

	let cloned = button.closest(`#${button.dataset.groupClass}-${button.dataset.inputSlug} .input-group`)
		.cloneNode(true) ?? null;

	let parentGroup = document.querySelector(`#${button.dataset.groupClass}-${button.dataset.inputSlug}`)
	if (!parentGroup) return;

	// empty value and event listener on cloned item
	cloned.querySelector('textarea').value = '';
	cloned.querySelector('.multi_add_btn').addEventListener('click', function(e) {
		addNewTextArea(e);
	});

	parentGroup.appendChild(cloned);
}

window.clipboard = null;
function enableCopyButton() {
	// destroy old clipboard
	if (window.clipboard) {
		window.clipboard.destroy();
	}

	// create new clipboard
	window.clipboard = new ClipboardJS('.copy_btn', {
		container: document.getElementById('editModal')
	});
	window.clipboard.on('success', function (e) {
		$('#copy-success').toast('show');
	})
}

// close file descriptor and add modal-open to body so that form modal keeps scroll
function handleDescriptorClose(e) {
	e.preventDefault();
	$(e.target.closest('button').dataset.target).modal('hide');
	setTimeout(() => {
		document.querySelector('body').classList.add('modal-open')
	}, 1000);
}

function dropMultiFormField(e) {
	e.preventDefault();
	e.target.closest('.dragula').remove();
}

async function formDelete(e) {
	e.preventDefault();
	let button = e.target.closest('button');

	let result = await Swal.fire({
		title: 'Are you sure?',
		text: "Your action will delete this record!",
		icon: 'warning',
		showCancelButton: true,
		confirmButtonColor: 'var(--danger)',
		cancelButtonColor: 'var(--primary)',
		confirmButtonText: 'Yes, delete it!'
	})

	if (result.isConfirmed) {
		let res = await fetch(`/admin/delete-record?id=${button.dataset.id}`, {
			method: 'delete'
		});
		res = await res.json();

		if (res.status === 'ok') {
			if (typeof FORM_IS_PAGE === 'undefined') {
				dtable.row(`#${button.dataset.id}`).remove().draw(false);
				$('#editModal').modal('hide');
			}

			Swal.fire({
				title: 'Deleted!',
				text: 'Record has been deleted.',
				icon: 'success',
				confirmButtonColor: 'var(--primary)'
			}).then(() => {
				if (typeof FORM_IS_PAGE === 'undefined') return;

				let searchParam = new URLSearchParams(document.location.search);
				let type = searchParam.get('type');

				history.replaceState(null, null, `/admin/list?type=${type}`);
				window.location.reload(true);
			});
		}
	}
}

// add js events to nodes if form is loaded as standalone page
(() => {
	if (!FORM_IS_PAGE) {
		return;
	}

	refreshEditForm();
	enableEditFormButtons();
	initEditorJs();
	initTypeOut();
})();