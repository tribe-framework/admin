<?php
$is_multi_text = (($module_input_type == 'multi_text' || $module_input_type == 'multi-text') ?? false);
?>

<div id="text-group-<?= $module_input_slug_lang ?>" class="text-group">
	<?php
	$i = 0;
	$type_name_values = array();

	if (
		isset($post[$module_input_slug_lang]) &&
		is_array($post[$module_input_slug_lang])
	) {
		$type_name_values = $post[$module_input_slug_lang];
	} else if (
		isset($post[$module_input_slug_lang]) &&
		$post[$module_input_slug_lang]
	) {
		$type_name_values[0] = $post[$module_input_slug_lang];
	} else {
		$type_name_values[0] = $module_input_default_value;
	}
	?>

	<?php if ($is_multi_text): ?>
	<button
		class="btn btn-light w-100 ex-area position-sticky mt-3"
		style="top: 0; z-index: 999;"
		type="button"
		data-toggle="collapse"
		data-target="#module<?=$module['input_slug']?>"
		aria-expanded="false"
		aria-controls="module<?=$module['input_slug']?>"
		>
		<span class="d-flex justify-content-between align-items-center">
			<span><?=$module['input_placeholder']?></span>
			<span>
				<strong>[<?=count($type_name_values)?>]</strong>&nbsp;&nbsp;
				<i class="fas fa-chevron-circle-down"></i>
			</span>
		</span>
	</button>

	<div id="module<?=$module['input_slug']?>" class="collapse show dragula-container p-1 collapsable-scroll-min">
	<?php endif?>
		<?php foreach ($type_name_values as $type_name_value): ?>
			<?php if ($is_multi_text): ?>
			<div class="dragula">
			<?php endif ?>

			<?php if ($i < 1 || trim($type_name_value)): // if: $i?>
				<div class="input-group <?= ($module_input_type == 'multi-text' || $module_input_type == 'multi_text') ? 'mt-2' : 'mt-5' ?>">
					<div
						class="input-group-prepend border-right"
						<?php if ($module_input_type == 'multi-text' || $module_input_type == 'multi_text'): ?>
						title="Drag to re-order"
						<?php endif ?>
						>
						<span
							class="input-group-text justify-content-center"
							id="basic-addon1"
							style="min-width: 3rem;"
							>
							<?php
								if ($module_input_type == 'multi-text' || $module_input_type == 'multi_text'):
									echo $i+1;
								else:
							?>
								<span class="fas fa-align-justify"></span>
							<?php endif ?>
						</span>
					</div>

					<input
						type="text"
						name="<?=$module_input_slug_lang . (($module_input_type == 'multi-text' || $module_input_type == 'multi_text') ? '[]' : '');?>"
						class="form-control m-0 border-top-0 border-right-0 border-left-0"
						data-child="text-group"
						placeholder="<?=$module_input_placeholder ?:
							ucfirst($types[$type]['name']) . ' ' . $module_input_slug_lang
						?>"
						value="<?=$type_name_value?>"
					>

					<?php if($is_multi_text): ?>
					<button
						title="Delete"
						class="input-group-append btn btn-outline-light text-danger align-items-center delete-multi-form border-top-0 border-right-0"
						type="button"
						>
						<i class="fas fa-trash-alt"></i>
					</button>
					<?php endif ?>
				</div>

				<?php if ($module_input_placeholder): ?>
					<div class="col-12 row text-muted small m-0">
						<span class="ml-auto mr-0"><?=$module_input_placeholder?></span>
					</div>
				<?php endif ?>

				<?php
				// if: slug_displayed
				if ($module_input_primary && $module_input_type != 'multi-text' && $module_input_type != 'multi_text' && !$slug_displayed):
					$slug_displayed = 1;
				?>
				<div class="input-group">
					<div
						id="slug_update_div"
						class="custom-control custom-switch <?=($_GET['id'] ?? false) ? 'd-block' : 'd-none'?>"
						>
						<input
							type="checkbox"
							class="custom-control-input"
							name="slug_update"
							id="slug_update"
						>
						<label class="custom-control-label" for="slug_update">
							Update the URL slug based on title (will change the link)
							<span id="title-slug" class="text-muted ml-4">
								<em><?=$post['type'] ?? ''?> / <span class="object_slug"><?=$post['slug'] ?? ''?></span></em>
							</span>
						</label>
					</div>
				</div>
				<?php endif // if: slug_displayed ?>
			<?php endif // if: $i ?>
			<?php $i++;?>

			<?php if ($is_multi_text): ?>
			</div> <!-- // .dragula -->
			<?php endif?>
		<?php endforeach ?>
	<?php if ($is_multi_text): ?>
	</div> <!-- // .collapse -->

	<div
		class="d-flex multi_add_btn_parent flex-column"
		data-group-class="text-group"
		data-input-slug="<?=$module_input_slug_lang?>"
		>
		<div class="input-append d-flex flex-column">
			<p class="d-none mb-0 mt-2">
				<span class="small text-light">New fields</span>
			</p>
		</div>

		<button class="btn btn-sm btn-outline-dark text-capitalize align-self-end mt-1 multi_add" type="button">
			<span style="vertical-align:sub"
				>Add new <?=$module['input_placeholder']?> <i class="fal fa-plus-square ml-2"></i>
			</span>
		</button>
	</div>
	<?php endif?>
</div> <!-- // .text-group  -->
