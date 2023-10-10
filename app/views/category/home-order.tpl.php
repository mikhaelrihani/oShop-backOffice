<div class="container my-4">
	<a href="<?= $router->generate('category-list'); ?>" class="btn btn-success float-end">Retour</a>
	<h2>Catégories en page d'accueil</h2>

	<?php require_once __DIR__ . '/../partials/form-errors.tpl.php'; ?>

	<form action="" method="POST" class="mt-5">
		<div class="row">
			<?php for ($i = 1; $i < 6; $i++) : ?>
				<div class="col">
					<div class="form-group">
						<label for="emplacement<?= $i; ?>">Emplacement #<?= $i; ?></label>
						<select class="form-control" id="emplacement<?= $i; ?>" name="emplacement[<?= $i; ?>]">
							<option value="">choisissez :</option>
							<?php foreach ($categories as $category) : ?>
								<option value="<?= $category->getId(); ?>" <?= $category->getHomeOrder() == $i ? 'selected' : ''; ?>><?= $category->getName(); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			<?php endfor; ?>

			<!-- <div class="col">
				<div class="form-group">
					<label for="emplacement2">Emplacement #2</label>
					<select class="form-control" id="emplacement2" name="emplacement[]">
						<option value="">choisissez :</option>
						<?php foreach ($categories as $category) : ?>
							<option value="<?= $category->getId(); ?>" <?php
																			if ($category->getHomeOrder() == 2) {
																				echo 'selected';
																			}
																			?>><?= $category->getName(); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col">
				<div class="form-group">
					<label for="emplacement3">Emplacement #3</label>
					<select class="form-control" id="emplacement3" name="emplacement[]">
						<option value="">choisissez :</option>
						<?php foreach ($categories as $category) : ?>
							<option value="<?= $category->getId(); ?>" <?= $category->getHomeOrder() == 3 ? 'selected' : ''; ?>><?= $category->getName(); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label for="emplacement4">Emplacement #4</label>
					<select class="form-control" id="emplacement4" name="emplacement[]">
						<option value="">choisissez :</option>
						<?php foreach ($categories as $category) : ?>
							<option value="<?= $category->getId(); ?>" <?= $category->getHomeOrder() == 4 ? 'selected' : ''; ?>><?= $category->getName(); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<div class="col">
				<div class="form-group">
					<label for="emplacement5">Emplacement #5</label>
					<select class="form-control" id="emplacement5" name="emplacement[]">
						<option value="">choisissez :</option>
						<?php foreach ($categories as $category) : ?>
							<option value="<?= $category->getId(); ?>" <?= $category->getHomeOrder() == 5 ? 'selected' : ''; ?>><?= $category->getName(); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div> -->
		</div>
		<input type="hidden" name="tokenCsrf" value="<?= $_SESSION['tokenCsrf']; ?>">
		<button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>
	</form>

</div>