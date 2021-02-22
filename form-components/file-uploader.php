<div class="input-group mt-5">
    <div class="input-group-prepend">
        <span
            class="input-group-text border-top-0 border-left-0 border-right-0 rounded-0"
            id="inputGroupFileAddon01"
        >
            <span class="fas fa-upload"></span>
        </span>
    </div>

    <div class="custom-file border-top-0 border-left-0 border-right-0 rounded-0">
        <input
            type="file"
            class="custom-file-input border-top-0 border-left-0 border-right-0 rounded-0"
            type="file"
            id="<?=$module_input_slug_lang?>"
            data-bunching="<?=json_encode($module['input_bunching'])?>"
            data-descriptor="<?=$module['input_descriptor'] ? '1' : ''?>"
            data-url="/admin/uploader"
            multiple
        >
        <label
            class="custom-file-label border-top-0 border-left-0 border-right-0 rounded-0"
            for="fileupload"
        >Choose file</label>
    </div>

    <?php if ($module_input_placeholder): ?>
    <div class="col-12 row text-muted small m-0">
        <span class="ml-auto mr-0"><?=$module_input_placeholder?></span>
    </div>
    <?php endif?>
</div>

<div class="col-12 p-0 mb-4 d-none" id="<?=$module_input_slug_lang?>_fileuploads">
    <div id="progress">
        <div style="width: 0%;" class="bar"></div>
    </div>
</div>

<div class="col-12 p-0 mb-4 d-block">
    <?php
$i = 0;
if (isset($post)):
?>
    <button
        class="btn btn-light w-100 ex-area position-sticky mb-1"
        style="top: 0; z-index: 999;"
        type="button"
        data-toggle="collapse"
        data-target="#file<?=$module['input_slug']?>"
        aria-expanded="false"
        aria-controls="file<?=$module['input_slug']?>"
    >
        <span class="d-flex justify-content-between align-items-center">
            Uploaded files
            <span>
                <strong>[<?=count($post[$module_input_slug_lang])?>]</strong>&nbsp;&nbsp;
                <span class="fas fa-chevron-circle-down"></span>
            </span>
        </span>
  </button>

    <div class="collapse dragula-container" id="file<?=$module['input_slug']?>">

    <?php
foreach ($post[$module_input_slug_lang] as $file) {
	$file_arr = $dash->get_uploaded_file_versions($file);?>
        <p class="file done d-flex justify-content-between align-items-center pb-2 pt-2 mb-0 dragula">
            <span>
                <?=($file_arr['url']['thumbnail'] ? '<img src="' . $file_arr['url']['thumbnail'] . '" style="max-width:80px; max-height:80px;"> ' . urldecode(basename($file)) : urldecode(basename($file)))?>
            </span>

            <span>
                <span class="btn-group">
                    <span class="delete_btn btn btn-sm btn-outline-danger">
                        <span class="fas fa-trash-alt"></span>
                    </span>
                    <input type="hidden" name="<?=$module_input_slug_lang?>[]" value="<?=$file?>">

                    <span class="copy_btn btn btn-sm btn-outline-primary" data-clipboard-text="<?=$file?>">
                        <span class="fas fa-link"></span>&nbsp;copy URL
                    </span>
                    <a
                        style="display: inline-block;"
                        class="btn btn-sm btn-outline-primary"
                        href="<?=$file?>"
                        target="new"
                    >
                        <span class="fas fa-external-link-alt"></span>&nbsp;view
                    </a>
                </span>

                <?php if (is_array($module['input_bunching'])): ?>
                    &nbsp;&nbsp;
                    <select
                        class="btn btn-sm btn-outline-primary"
                        name="<?=$module_input_slug_lang?>_bunching[]"
                    >
                        <option value="">file option</option>
                        <?php foreach ($module['input_bunching'] as $opt) {
		?>
                            <option
                                value="<?=$opt['slug']?>"
                                <?=
		$post[$module_input_slug_lang . '_bunching'][$i] === $opt['slug'] ? 'selected="selected"' :
		''
		?>
                            >
                                <?=$opt['title']?>
                            </option>
                        <?php }?>
                        </select>
                    <?php endif?>

                    <?php if ($module['input_descriptor']): ?>
                        &nbsp;&nbsp;
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary m-1"
                            data-toggle="modal"
                            data-target="#<?=$module_input_slug_lang?>_descriptor_m_<?=$i?>"
                        >
                            descriptor
                        </button>
                        <div
                            class="modal fade"
                            id="<?=$module_input_slug_lang?>_descriptor_m_<?=$i?>"
                            aria-hidden="true"
                        >
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">add file descriptor</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="close">
                                            <span aria-hidden="true">×</span>
                                        </button>
                                    </div>

                                    <div class="modal-body">
                                        <textarea
                                            name="<?=$module_input_slug_lang?>_descriptor[]"
                                            class="form-control"
                                            placeholder="enter file descriptor"
                                        >
                                            <?=$post[$module_input_slug_lang . '_descriptor'][$i]?>
                                        </textarea>
                                        <input
                                            name="<?=$module_input_slug_lang?>_descriptor_date[]"
                                            value="<?=$post[$module_input_slug_lang . '_descriptor_date'][$i]?>"
                                            type="date"
                                            class="form-control"
                                            placeholder="enter file date"
                                        >
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal"
                                        >save</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif?>
                </span>
            </p>
            <?php $i++?>
        <?php }?>

    </div> <!-- !.collapse -->
    <?php endif?>
</div>
