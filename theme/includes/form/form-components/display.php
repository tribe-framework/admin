<?php
/**
 * @var array $post
 * @var array $module
 */
?>
<div>
    <div class='overflow-auto border border-light small' style="max-height: 50vh;">
        <pre><?php echo json_encode($post[$module['input_slug']], JSON_PRETTY_PRINT); ?></pre>
    </div>
    <div class='col-12 row text-muted small m-0'>
        <span class="ml-auto mr-0">
            <?php echo $module['input_placeholder'] ?> (Read only)
        </span>
    </div>
</div>

