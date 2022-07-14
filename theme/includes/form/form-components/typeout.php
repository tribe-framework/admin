<?php
/**
 * @var array $types
 * @var string $type
 * @var string $module_input_placeholder
 * @var string $module_input_slug_lang
 * @var string $module_input_default_value
 */
?>

<div class="typeout-menu mt-5">
    <?php if (isset($module_input_options) && in_array('fullScreen', $module_input_options)) {?>
    <button type="button" data-expanded="0" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-fullscreen" data-toggle="tooltip" data-placement="top" title="full screen"><span class="fas fa-compress"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('undo', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-undo" data-typeout-command="undo" data-toggle="tooltip" data-placement="top" title="undo"><span class="fas fa-undo"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('insertParagraph', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-insertParagraph" data-typeout-command="insertParagraph" data-toggle="tooltip" data-placement="top" title="insert paragraph break"><span class="fas fa-paragraph"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('heading', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-input-exec typeout-heading" data-typeout-command="heading" data-typeout-info="h3" data-toggle="tooltip" data-placement="top" title="heading"><span class="fas fa-heading"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('blockquote', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-input-exec typeout-heading" data-typeout-command="heading" data-typeout-info="h4" data-toggle="tooltip" data-placement="top" title="heading"><span class="fas fa-heading"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('bold', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-bold" data-typeout-command="bold" data-toggle="tooltip" data-placement="top" title="bold"><span class="fas fa-bold"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('italic', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-italic" data-typeout-command="italic" data-toggle="tooltip" data-placement="top" title="italic"><span class="fas fa-italic"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('justifyCenter', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-justifyCenter" data-typeout-command="justifyCenter" data-toggle="tooltip" data-placement="top" title="justifyCenter"><span class="fas fa-align-center"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('justifyFull', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-justifyFull" data-typeout-command="justifyFull" data-toggle="tooltip" data-placement="top" title="justifyFull"><span class="fas fa-align-justify"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('justifyLeft', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-justifyLeft" data-typeout-command="justifyLeft" data-toggle="tooltip" data-placement="top" title="justifyLeft"><span class="fas fa-align-left"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('justifyRight', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-justifyRight" data-typeout-command="justifyRight" data-toggle="tooltip" data-placement="top" title="justifyRight"><span class="fas fa-align-right"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('insertorderedlist', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-insertorderedlist" data-typeout-command="insertorderedlist" data-toggle="tooltip" data-placement="top" title="insertorderedlist"><span class="fas fa-list-ol"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('insertunorderedlist', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-insertunorderedlist" data-typeout-command="insertunorderedlist" data-toggle="tooltip" data-placement="top" title="insertunorderedlist"><span class="fas fa-list-ul"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('createLink', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-input" data-typeout-command="createLink" data-typeout-info="Enter link URL" data-toggle="tooltip" data-placement="top" title="create link"><span class="fas fa-link"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('unlink', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec typeout-unlink" data-typeout-command="unlink" data-toggle="tooltip" data-placement="top" title="un-link"><span class="fas fa-unlink"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('insertImage', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-input" data-typeout-command="insertImage" data-typeout-info="Enter image URL" data-toggle="tooltip" data-placement="top" title="insert image"><span class="fas fa-image"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('insertPDF', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-input" data-typeout-command="insertPDF" data-typeout-info="Enter PDF URL" data-toggle="tooltip" data-placement="top" title="insert PDF"><span class="fas fa-file-pdf"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('insertHTML', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-input" data-typeout-command="insertHTML" data-typeout-info="Enter HTML" data-toggle="tooltip" data-placement="top" title="insert HTML"><span class="fas fa-code"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('attach', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-input" data-typeout-command="attach" data-typeout-info="" data-toggle="tooltip" data-placement="top" title="add attachment"><span class="fas fa-paperclip"></span></button>
    <?php }?>

    <?php if (isset($module_input_options) && in_array('removeFormat', $module_input_options)) {?>
    <button type="button" class="btn btn-outline-primary border-0 rounded-0 mt-1 typeout typeout-exec" data-typeout-command="removeFormat" data-toggle="tooltip" data-placement="top" title="remove formatting"><span class="fas fa-remove-format"></span></button>
    <?php }?>
</div>

<?php
$placeholder = (isset($module_input_placeholder) && trim($module_input_placeholder)) ?
    $module_input_placeholder :
    ucfirst($types[$type]['name']) . " {$module_input_slug_lang}";
$typeout_data = $post[$module_input_slug_lang] ?? $module_input_default_value;

echo "<div id='typeout-{$module_input_slug_lang}'class='typeout-content mt-5 border-bottom overflow-auto' data-input-slug='{$module_input_slug_lang}' contenteditable='true' placeholder='{$placeholder}'>{$typeout_data}</div>
<input type='hidden' name='{$module_input_slug_lang}'/>";

echo $module_input_placeholder ? "<div class='col-12 row text-muted small m-0'><span class='ml-auto mr-0'>{$module_input_placeholder}</span></div>" : '';
?>
