<div class="container my-4">

	<h2>Login</h2>

	<?php require_once __DIR__ . '/../partials/form-errors.tpl.php'; ?>

	<form action="" method="POST" class="mt-5">
		<div class="mb-3">
			<label for="email" class="form-label">Email</label>
			<input type="email" class="form-control" id="email" name="email" value="" placeholder="Email">
		</div>
		<div class="mb-3">
			<label for="password" class="form-label">Mot de passe</label>
			<input type="password" class="form-control" id="password" name="password" value="" placeholder="Mot de passe">
		</div>
		<div class="d-grid gap-2">
			<button type="submit" class="btn btn-primary mt-5">Se connecter</button>
		</div>
	</form>

</div>