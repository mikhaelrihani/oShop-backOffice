<div class="container my-4">
	<a href="<?= $router->generate('product-list'); ?>" class="btn btn-success float-end">Retour</a>
	<h2>Ajouter d'un produit</h2>

	<?php require_once  __DIR__ . '/../partials/form-errors.tpl.php'; ?>

	<form action="" method="POST" class="mt-5">
		<div class="mb-3">
			<label for="name" class="form-label">Nom</label>
			<input type="text" class="form-control" value="<?= isset($product) ? $product->getName() : ''; ?>" id="name" name="name" placeholder="Nom du produit">
		</div>
		<div class="mb-3">
			<label for="description" class="form-label">Description</label>
			<input type="text" class="form-control" id="description" name="description" value="<?= isset($product) ? $product->getDescription() : ''; ?>" placeholder="Description" aria-describedby="descriptionHelpBlock">
			<small id="descriptionHelpBlock" class="form-text text-muted">
				Description du produit
			</small>
		</div>
		<div class="mb-3">
			<label for="picture" class="form-label">Image</label>
			<input type="text" class="form-control" id="picture" name="picture" value="<?= isset($product) ? $product->getPicture() : ''; ?>" placeholder="image jpg, gif, svg, png" aria-describedby="pictureHelpBlock">
			<small id="pictureHelpBlock" class="form-text text-muted">
				URL relative d'une image (jpg, gif, svg ou png) fournie sur <a href="https://benoclock.github.io/S06-images/" target="_blank">cette page</a>
			</small>
		</div>
		<div class="mb-3">
			<label for="price" class="form-label">Prix</label>
			<input type="number" step="0.01" min="0" class="form-control" id="price" name="price" value="<?= isset($product) ? $product->getPrice() : ''; ?>" placeholder="Prix" aria-describedby="priceHelpBlock">
			<small id="priceHelpBlock" class="form-text text-muted">
				Prix du produit
			</small>
		</div>
		<div class="mb-3">
			<label for="rate" class="form-label">Note</label>
			<input type="number" min="0" max="5" class="form-control" id="rate" name="rate" value="<?= isset($product) ? $product->getRate() : ''; ?>" placeholder="Note" aria-describedby="rateHelpBlock">
			<small id="rateHelpBlock" class="form-text text-muted">
				Note du produit
			</small>
		</div>
		<div class="mb-3">
			<label for="status" class="form-label">Statut</label>
			<select class="form-control" id="status" name="status" aria-describedby="statusHelpBlock">
				<option value="1">Dispo</option>
				<option value="2">Pas dispo</option>
			</select>
			<small id="statusHelpBlock" class="form-text text-muted">
				Statut du produit
			</small>
		</div>

		<div class="mb-3">
			<label for="category" class="form-label">Catégorie</label>
			<select class="form-control" id="category" name="category" aria-describedby="categoryHelpBlock">
				<?php foreach ($categories as $category) : ?>
					<option value="<?= $category->getId(); ?>"><?= $category->getName(); ?></option>
				<?php endforeach; ?>
			</select>
			<small id="categoryHelpBlock" class="form-text text-muted">
				Catégorie du produit
			</small>
		</div>

		<div class="mb-3">
			<label for="brand" class="form-label">Marque</label>
			<select class="form-control" id="brand" name="brand" aria-describedby="brandHelpBlock">
				<?php foreach ($brands as $brand) : ?>
					<option value="<?= $brand->getId(); ?>"><?= $brand->getName(); ?></option>
				<?php endforeach; ?>
			</select>
			<small id="brandHelpBlock" class="form-text text-muted">
				Marque du produit
			</small>
		</div>

		<div class="mb-3">
			<label for="type" class="form-label">Type</label>
			<select class="form-control" id="type" name="type" aria-describedby="typeHelpBlock">
				<?php foreach ($types as $type) : ?>
					<option value="<?= $type->getId(); ?>"><?= $type->getName(); ?></option>
				<?php endforeach; ?>
			</select>
			<small id="typeHelpBlock" class="form-text text-muted">
				Type du produit
			</small>
		</div>

		<div class="mb-3">
			<label for="tags" class="form-label">Tags</label>
			<select class="form-control" id="tags" name="tags[]" size="<?= count($tags); ?>" aria-describedby="tagsHelpBlock" multiple>
				<?php foreach ($tags as $tag) : ?>
					<option value="<?= $tag->getId() ?>" <?php if (in_array($tag, $productTags)) echo ' selected' ?>><?= $tag->getName() ?></option>
				<?php endforeach ?>
			</select>
			<small id="tagsHelpBlock" class="form-text text-muted">
				Tags du produit, vous pouvez utiliser la touche Ctrl + clic souris pour sélectionner plusieurs tags
			</small>
		</div>

		<div class="d-grid gap-2">
			<input type="hidden" name="tokenCsrf" value="<?= $_SESSION['tokenCsrf']; ?>">
			<button type="submit" class="btn btn-primary mt-5">Valider</button>
		</div>
	</form>
</div>