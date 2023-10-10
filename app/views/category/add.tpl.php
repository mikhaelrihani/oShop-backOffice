<div class="container my-4">
	<a href="<?= $router->generate('category-list'); ?>" class="btn btn-success float-end">Retour</a>
	<h2><?= $title; ?></h2>

	<?php require_once __DIR__ . '/../partials/form-errors.tpl.php'; ?>

	<form action="" method="POST" class="mt-5">
		<div class="mb-3">
			<label for="name" class="form-label">Nom</label>
			<input type="text" class="form-control" id="name" name="name" value="<?php
																					if (isset($category)) {
																						echo $category->getName();
																					}
																					?>" placeholder="Nom de la catégorie">
		</div>
		<div class="mb-3">
			<label for="subtitle" class="form-label">Sous-titre</label>
			<input type="text" class="form-control" id="subtitle" name="subtitle" value="<?= isset($category) ? $category->getSubtitle() : ''; ?>" placeholder="Sous-titre" aria-describedby="subtitleHelpBlock">
			<small id="subtitleHelpBlock" class="form-text text-muted">
				Sera affiché sur la page d'accueil comme bouton devant l'image
			</small>
		</div>
		<div class="mb-3">
			<label for="picture" class="form-label">Image</label>
			<input type="text" class="form-control" id="picture" name="picture" value="<?= isset($category) ? $category->getPicture() : ''; ?>" placeholder="image jpg, gif, svg, png" aria-describedby="pictureHelpBlock">
			<small id="pictureHelpBlock" class="form-text text-muted">
				URL relative d'une image (jpg, gif, svg ou png) fournie sur <a href="https://benoclock.github.io/S06-images/" target="_blank">cette page</a>
			</small>
		</div>
		<div class="mb-3">
			<label for="rate" class="form-label">Ordre</label>
			<input type="number" min="0" max="100" class="form-control" id="order" value="<?= isset($category) ? $category->getHomeOrder() : ''; ?>" name="order" placeholder="0" aria-describedby="rateHelpBlock">
		</div>
		<div class="d-grid gap-2">
			<input type="hidden" name="tokenCsrf" value="<?= $_SESSION['tokenCsrf']; ?>">
			<button type="submit" class="btn btn-primary mt-5">Valider</button>
		</div>
	</form>

</div>