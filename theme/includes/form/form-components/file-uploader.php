<?php
/**
 * @var string $module_input_slug_lang
 * @var string $module_input_placeholder
 * @var object $dash
 */
?>
<!-- uploader UI -->
<div class="input-group mt-5">
    <div class="input-group-prepend">
        <span class="input-group-text border-top-0 border-left-0 border-right-0 rounded-0" id="inputGroupFileAddon01">
            <span class="fas fa-upload"></span>
        </span>
    </div>

    <div class="custom-file border-top-0 border-left-0 border-right-0 rounded-0">
        <input
            type="file"
            class="custom-file-input border-top-0 border-left-0 border-right-0 rounded-0"
            id="<?=$module_input_slug_lang?>"
            data-bunching="<?=isset($module['input_bunching']) ? json_encode($module['input_bunching']) : ''?>"
            data-descriptor="<?= ($module['input_descriptor'] ?? null) ? '1' : '' ?>"
            data-url="/admin/uploader"
            multiple
        >
        <label class="custom-file-label border-top-0 border-left-0 border-right-0 rounded-0" for="fileupload">Choose
            file
        </label>
    </div>

    <?php if ($module_input_placeholder): ?>
    <div class="col-12 row text-muted small m-0">
        <span class="ml-auto mr-0"><?=$module_input_placeholder?></span>
    </div>
    <?php endif?>
</div>
<!-- /uploader UI -->

<!-- upload progress bar -->
<div id="<?=$module_input_slug_lang?>_fileuploads" class="col-12 p-0 mb-4 d-none">
    <div id="progress">
        <div style="width: 0%;" class="bar"></div>
    </div>
</div>
<!-- /upload progress bar -->

<!-- uploaded files list -->
<div class="col-12 p-0 mb-4 d-block">
    <?php if (isset($post)): ?>

    <button class="btn btn-light w-100 ex-area position-sticky mb-1" style="top: 0; z-index: 98;" type="button"
        data-toggle="collapse" data-target="#file<?=$module['input_slug']?>" aria-expanded="false"
        aria-controls="file<?=$module['input_slug']?>">
        <span class="d-flex justify-content-between align-items-center">
            Old Uploads
            <span>
                <strong>[<?= count($post[$module_input_slug_lang] ?? []) ?>]</strong>&nbsp;&nbsp;
                <span class="fas fa-chevron-circle-down"></span>
            </span>
        </span>
    </button>

    <div id="file<?=$module['input_slug']?>" class="dragula-container container collapse show collapsable-scroll-min">
        <?php
        foreach (($post[$module_input_slug_lang] ?? []) as $i => $file) :
            $file_arr = $dash->get_uploaded_file_versions($file);
        ?>
        <div class="file done d-flex justify-content-between align-items-center pb-2 pt-2 mb-0 dragula">
            <span>
                <?php if ($file_arr['url']['thumbnail']): ?>
                <img
                    src="<?=$file_arr['url']['thumbnail']?>"
                    class="thumb-preview mr-2"
                    alt=""
                >
                <?=urldecode(basename($file))?>
                <?php endif?>
            </span>

            <span class="d-flex">
                <span class="btn-group">
                    <span class="delete_btn btn btn-sm btn-outline-danger px-3">
                        <i class="fas fa-trash-alt"></i>
                    </span>

                    <input type="hidden" name="<?=$module_input_slug_lang?>[]" value="<?=$file?>">

                    <span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize"
                        data-clipboard-text="<?=$file?>">
                        <i class="fas fa-copy mr-1"></i>copy URL
                    </span>

                    <span class="copy_btn btn btn-sm btn-outline-primary px-3 text-capitalize"
                        data-clipboard-text="[[<?=$file?>]]">
                        <i class="fas fa-copy mr-1"></i>copy shortcode
                    </span>

                    <a style="display: inline-block;" class="btn btn-sm btn-outline-primary text-capitalize px-3"
                        href="<?=$file?>" target="new">
                        <i class="fas fa-external-link-alt mr-1"></i>view
                    </a>
                </span>

                <?php if (is_array($module['input_bunching'] ?? null)): ?>
                <select class="btn btn-sm btn-outline-primary" name="<?=$module_input_slug_lang?>_bunching[]">
                    <option value="">file option</option>
                    <?php foreach ($module['input_bunching'] as $opt): ?>
                    <option
                        value="<?=$opt['slug']?>"
                        <?=
                            $post["{$module_input_slug_lang}_bunching"][$i] === $opt['slug'] ?
                            'selected="selected"' :
                            ''
                        ?>
                        ><?=$opt['title']?>
                    </option>
                    <?php endforeach;?>
                </select>
                <?php endif;?>

                <!-- file descriptor field -->
                <?php if ($module['input_descriptor'] ?? null): ?>
                <button type="button" class="btn btn-sm btn-outline-primary text-capitalize" data-toggle="modal"
                    data-target="#<?=$module_input_slug_lang?>_descriptor_m_<?=$i?>">
                    <i class="fas fa-align-left m-1"></i>
                    descriptor
                </button>
                <div class="modal fade" id="<?=$module_input_slug_lang?>_descriptor_m_<?=$i?>" data-keyboard="false" aria-hidden="true" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content shadow-lg">
                            <div class="modal-header">
                                <h5 class="modal-title">add file descriptor</h5>
                                <button type="button" onclick="handleDescriptorClose(event)" class="close" data-target="#<?=$module_input_slug_lang?>_descriptor_m_<?=$i?>"
                                        aria-label="close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <textarea name="<?=$module_input_slug_lang?>_descriptor[]" class="form-control"
                                    placeholder="enter file descriptor"><?=$post[$module_input_slug_lang . '_descriptor'][$i]?></textarea>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-primary" data-target="#<?=$module_input_slug_lang?>_descriptor_m_<?=$i?>" onclick="handleDescriptorClose(event)">save</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif?>
            </span>
        </div>
        <?php endforeach; ?>
    </div> <!-- !.collapse -->
    <?php endif?>
</div>
