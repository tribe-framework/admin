key('⌘+s, ctrl+s', function(e){$('.save_btn').trigger('click'); e.preventDefault();});
key('⌘+b, ctrl+b', function(e){$('.typeout-bold').trigger('click'); e.preventDefault();});
key('⌘+i, ctrl+i', function(e){$('.typeout-italic').trigger('click'); e.preventDefault();});

// jquery functions
$( document ).ready(function() {
	if ($('.multi_drop_select_table').length) {
		$('.multi_drop_select_table').DataTable({
			"dom": '<"top"f>rt<"bottom">',
			"pageLength":50,
			"order": [[ 0, "desc" ]]
		});
	}

	$('.typeout-content').each(function() {update_textarea($(this).data('input-slug'));});

	$(document).on('keyup', '.typeout-content', function() {update_textarea($(this).data('input-slug'));});
	$(document).on('blur', '.typeout-content', function() {update_textarea($(this).data('input-slug'));});

	$(document).on('click', '.multi_add_btn', function(e) {
		$(this).closest('#'+$(this).data('group-class')+'-'+$(this).data('input-slug')+' .input-group').first().clone().appendTo('#'+$(this).data('group-class')+'-'+$(this).data('input-slug'));
		$('#'+$(this).data('group-class')+'-'+$(this).data('input-slug')+' .input-group:last input').val('');
	});

	$(document).on('click', '.edit_button', function(e) {
		if ($(this).data('id')) {
			$('#editModal .modal-title').text('#'+$(this).data('id'));
			$('.editModalClose').attr('data-is_new', '');
		}
		else {
			$('#editModal .modal-title').text('New '+$(this).data('type')+' '+$(this).data('role'));
			$('.editModalClose').attr('data-is_new', '1');
		}

		$('#editModal .modal-body').html('<div class="spinner-grow spinner-border-lg text-primary-3" role="status"><span class="sr-only">Loading...</span></div>').load($(this).data('href'), {}, function() {refreshEditForm()});
	});

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

	var drake = dragula({
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

});

function refreshEditForm() {

	// code to handle file uploads
	var sli=0;
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
			slvl='';

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
					<div class="modal fade" id="${$(this).attr('id')}_descriptor_${sli}" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title">add file descriptor</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="close"
										><span aria-hidden="true">&times;</span>
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
									<button
										type="button"
										class="btn btn-sm btn-outline-primary text-capitalize"
										data-dismiss="modal"
										>save
									</button>
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
		      .append(`<div class="btn-group"><span class="delete_btn btn btn-sm btn-outline-danger px-3"><span class="fas fa-trash-alt"></span></span><input type="hidden" name="${$(this).attr('id')}[]" value="${data.result.files[0].url}"><span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize" data-clipboard-text="${data.result.files[0].url}"><span class="fas fa-copy mr-1"></span>&nbsp;copy URL</span><span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize" data-clipboard-text="[[${data.result.files[0].url}]]"><span class="fas fa-copy mr-1"></span>&nbsp;copy shortcode</span><a style="display: inline-block;" class="btn btn-sm btn-outline-primary px-3 text-capitalize" href="${data.result.files[0].url}" target="new"><span class="fas fa-external-link-alt mr-1"></span>&nbsp;view</a></div>${slvl}`)
		      .addClass("done");

		}
    });

    // submit form over ajax - used in edit.php
    (() => {
        let editForm = document.querySelector('.edit_form');
        if (!editForm) return;

        editForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            $('.save_btn').html('<div class="spinner-border spinner-border-sm mb-1" role="status"><span class="sr-only">Loading...</span></div>&nbsp;Save');
            $('.save_btn').prop('disabled', true);

            let response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then((response) => {
                return response.json();
            })
            .then((data) => {
                $('.save_btn').html('<span class="fa fa-save"></span>&nbsp;Save');
                $('.save_btn').prop('disabled', false);
                $('#save-success').toast('show');
                $('input[name=id]').val(data.last_data[0].id);
                $('input[name=slug]').val(data.last_data[0].slug);
                $('.object_slug').text(data.last_data[0].slug);
                $('.editModalClose').attr('data-id', data.last_data[0].id);
                $('#editModal .modal-title').text('#'+data.last_data[0].id);
                $('#slug_update').prop('checked', false);
                $('#slug_update_div').removeClass('d-none');
            });

        });
    })()


	var clipboard = new ClipboardJS('.copy_btn');
	clipboard.destroy();
}

function dropMultiFormField(e) {
	e.preventDefault();

	e.target.closest('.dragula').remove();
}
