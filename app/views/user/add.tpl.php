<div class="container my-4">
	<a href="<?= $router->generate('user-list'); ?>" class="btn btn-success float-end">Retour</a>
	<h2>Ajouter un utilisateur</h2>

	<?php require_once __DIR__ . '/../partials/form-errors.tpl.php'; ?>

	<form action="" method="POST" class="mt-5">
		<div class="mb-3">
			<label for="email" class="form-label">Email</label>
			<input type="email" class="form-control" id="email" name="email" value="<?php
																					if (isset($user)) {
																						echo $user->getEmail();
																					}
																					?>" placeholder="Email" aria-describedby="emailHelpBlock">
			<small id="emailHelpBlock" class="form-text text-muted">
				Email de l'utilisateur
			</small>
		</div>
		<div class="mb-3">
			<label for="password" class="form-label">Mot de passe</label>
			<input type="password" class="form-control" id="password" name="password" value="" placeholder="Mot de passe" aria-describedby="passwordHelpBlock">
			<small id="passwordHelpBlock" class="form-text text-muted">
				Mot de passe de l'utilisateur
			</small>
		</div>
		<div class="mb-3">
			<label for="firstname" class="form-label">Prénom</label>
			<input type="text" class="form-control" id="firstname" name="firstname" value="<?= isset($user) ? $user->getFirstname() : ''; ?>" placeholder="Prénom" aria-describedby="firstnameHelpBlock">
			<small id="firstnameHelpBlock" class="form-text text-muted">
				Prénom de l'utilisateur
			</small>
		</div>

		<div class="mb-3">
			<label for="lastname" class="form-label">Nom de famille</label>
			<input type="text" class="form-control" id="lastname" name="lastname" value="<?= isset($user) ? $user->getLastname() : ''; ?>" placeholder="Nom de famille" aria-describedby="lastnameHelpBlock">
			<small id="lastnameHelpBlock" class="form-text text-muted">
				Nom de famille de l'utilisateur
			</small>
		</div>

		<div class="mb-3">
			<label for="role" class="form-label">Role</label>
			<select class="form-control" id="role" name="role" aria-describedby="roleHelpBlock">
				<option value="admin" <?php if (isset($user) && ($user->getRole() == 'admin')) {
											echo 'selected';
										} ?>>Administrateur</option>
				<option value="catalog-manager" <?php if (isset($user) && ($user->getRole() == 'catalog-manager')) {
													echo 'selected';
												} ?>>Catalogue manager</option>
			</select>
			<small id="roleHelpBlock" class="form-text text-muted">
				Role de l'utilisateur
			</small>
		</div>

		<div class="mb-3">
			<label for="status" class="form-label">Status</label>
			<select class="form-control" id="status" name="status" aria-describedby="statusHelpBlock">
				<option value="1" <?php if (isset($user) && ($user->getStatus() == 1)) {
										echo 'selected';
									} ?>>Actif</option>
				<option value="2" <?php if (isset($user) && ($user->getRole() == 2)) {
										echo 'selected';
									} ?>>Désactivé</option>
			</select>
			<small id="statusHelpBlock" class="form-text text-muted">
				Status de l'utilisateur
			</small>
		</div>

		<div class="d-grid gap-2">
			<input type="hidden" name="tokenCsrf" value="<?= $_SESSION['tokenCsrf']; ?>">
			<button type="submit" class="btn btn-primary mt-5">Valider</button>
		</div>
	</form>

</div>