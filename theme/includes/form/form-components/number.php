<?php
/**
 * @var string $module_input_slug_lang
 * @var string $module_input_default_value
 * @var string $module_input_type
 * @var string $module_input_placeholder
 * @var string $type
 * @var integer $module_input_min
 * @var integer $module_input_max
 * @var array $post
 * @var array $types
 */
?>
<div class="number-group" id="number-group-<?php echo $module_input_slug_lang; ?>">
    <?php
    $i=0;
    $type_name_values=array();

    if (isset($post[$module_input_slug_lang]) && is_array($post[$module_input_slug_lang])) {
        $type_name_values=$post[$module_input_slug_lang];
    }
    elseif (isset($post[$module_input_slug_lang]) && $post[$module_input_slug_lang]) {
        $type_name_values[0]=$post[$module_input_slug_lang];
    }
    else {
        $type_name_values[0]=$module_input_default_value;
    }
    ?>
    <?php
    foreach ($type_name_values as $type_name_value) :
        if ($i<1 || trim($type_name_value)) :
            $step = (int) ($module_input_step ?? 1);
            $min = $module_input_min ? "min='{$module_input_min}'" : '';
            $max = $module_input_max ? "max='{$module_input_max}'" : '';
            $name = $module_input_slug_lang.(($module_input_type=='multi_number' || $module_input_type=='multi-number')?'[]':'');
            $placeholder = $module_input_placeholder ?? ucfirst($types[$type]['name']).' '.$module_input_slug_lang;
    ?>
    <div class="input-group mt-5">
        <div class="input-group-prepend">
            <span class="input-group-text border-top-0 border-left-0 border-right-0 rounded-0" id="basic-addon1"><span class="fas fa-sort-numeric-up-alt"></span></span>
        </div>

        <?php
        // input:number
        echo "<input type='number' step='{$step}' {$min} {$max} name='{$name}' class='form-control border-top-0 border-left-0 border-right-0 rounded-0 m-0' placeholder='{$placeholder}' value='{$type_name_value}'/>";

        // add button for multi-number
        echo(($module_input_type=='multi_number' || $module_input_type=='multi-number')?'<div class="input-group-append multi_add_btn" data-group-class="number-group" data-input-slug="'.$module_input_slug_lang.'"><button class="btn btn-outline-primary" type="button"><span class="fas fa-plus"></span></button></div>':'');
        ?>
    </div>
    <?php
        if (isset($module_input_placeholder) && trim ($module_input_placeholder)) {
            echo "<div class='col-12 row text-muted small m-0'><span class='ml-auto mr-0'>{$module_input_placeholder}</span></div>";
        }
        endif;
        $i++;
    endforeach;
    ?>
</div>
