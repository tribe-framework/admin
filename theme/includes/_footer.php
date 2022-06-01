<?php
$app_title = $types['webapp']['headmeta_title'] ?? false;
?>

</div>
<footer class="pt-4 pt-md-5 bg-white">
	<hr class="bg-primary" style="background-image: none;">

	<div class="container my-5">
		<div class="row">
			<?php if (!($types['webapp']['hide_wildfire_logo'] ?? false)): ?>
			<div class="col-md">
				<a href="https://wildfire.world">
					<img class="w-40" src="/vendor/wildfire/admin/theme/assets/img/logo.png">
				</a>

				<p class="text-muted small mb-3 mt-4 pr-5">
					Made with <span class="fas fa-heart"></span>
					<?php if ($app_title): ?>
					<br><em>for <?= $app_title ?></em>
					<?php endif; ?>
				</p>

				<p class="text-muted small my-3 pr-5">
					Wildfire is a technology consultancy based in New Delhi, India
				</p>

				<p class="text-muted small my-3 pr-5">
					<?php $year = date('Y'); ?>
					&copy; <?= $year == '2020' ? $year : "2020 - $year" ?>
				</p>
			</div>
			<?php endif; ?>

			<div class="col-md">
				<?=
					$theme->get_menu(
						$admin_menus['admin_footer_1'],
						[
							'ul' => 'list-unstyled mt-5 pt-2 pl-md-5',
							'li' => '',
							'a' => 'small'
						]
					)
				?>
			</div>

			<div class="col-md">
				<?=
					$theme->get_menu(
						$admin_menus['admin_footer_2'],
						[
							'ul' => 'list-unstyled mt-5 pt-2 pl-md-5',
							'li' => '',
							'a' => 'small'
						]
					);
				?>
			</div>
		</div>
	</div>
</footer>
<?php if ('dev' == strtolower($_ENV['ENV'])): ?>
<script>
	console.info('Running in dev mode');
</script>
<?php else: ?>
<script>
	window.onerror = function () {
		return true;
	}
</script>
<?php endif ?>

<script src="/vendor/wildfire/admin/theme/assets/plugins/popper/popper.min.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/plugins/moment.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/plugins/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/plugins/clipboard.min.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/plugins/keymaster.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/editorjs@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/header@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/paragraph@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/simple-image@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/embed@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/@editorjs/delimiter@latest"></script>
    
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.24.0/axios.min.js"></script>

<?php // js files to be loaded for specific pages - to reduce network requests ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.flash.min.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/plugins/datatables/datatables.min.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/js/list.js"></script>

<script src="https://blueimp.github.io/jQuery-File-Upload/js/vendor/jquery.ui.widget.js"></script>
<script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.iframe-transport.js"></script>
<script src="https://blueimp.github.io/jQuery-File-Upload/js/jquery.fileupload.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dragula/3.7.3/dragula.min.js"></script>
<script src="https://unpkg.com/draggabilly@2/dist/draggabilly.pkgd.min.js"></script>
<script src="https://unpkg.com/packery@2/dist/packery.pkgd.min.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/js/edit.js"></script>
<script src="/vendor/wildfire/admin/theme/assets/js/custom.js"></script>

</body>

</html>
