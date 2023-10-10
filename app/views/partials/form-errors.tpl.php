<?php if (isset($errors)) : ?>
	<div class="alert alert-danger">
		<?php foreach ($errors as $error) : ?>
			<div><?= $error; ?></div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>