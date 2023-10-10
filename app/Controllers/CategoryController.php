<?php

namespace App\Controllers;

// Si j'ai besoin du Model Category
use App\Models\Category;

class CategoryController extends CoreController
{

	public function list()
	{
		// Récupérer la liste de toutes les catégories
		$categories = Category::findAll(); // tableau d'instances de Category
		$viewData['categories'] = $categories;
		$this->show('category/list', $viewData);
	}

	public function add()
	{
		$viewData['title'] = 'Ajouter une catégorie';
		$this->show('category/add', $viewData);
	}

	public function create()
	{
		$name = htmlspecialchars(filter_input(INPUT_POST, 'name'));
		$subtitle = htmlspecialchars(filter_input(INPUT_POST, 'subtitle'));
		if (!empty(filter_input(INPUT_POST, 'picture'))) {
			$picture = filter_input(INPUT_POST, 'picture', FILTER_VALIDATE_URL);
		} else {
			$picture = '';
		}
		$order = filter_input(INPUT_POST, 'order', FILTER_VALIDATE_INT);

		$errorMessages = [];

		if (empty($name)) {
			$errorMessages[] = "Champ nom obligatoire";
		}
		if (strlen($name) > 64) {
			$errorMessages[] = "Nom : 64 caracètres max";
		}
		if (!empty($subtitle) && strlen($subtitle) > 64) {
			$errorMessages[] = "Sous titre : 64 caractères max";
		}
		if (!empty($picture) && strlen($picture) > 128) {
			$errorMessages[] = "Image : 64 caractères max";
		}

		if (!empty($errorMessages)) { // Si error dans les données saisies
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$viewData['title'] = 'Erreur ajout catégorie';
			$this->show('category/add', $viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}

		$category = new Category();
		$category->setName($name);
		$category->setSubtitle($subtitle);
		$category->setPicture($picture);
		$category->setHomeOrder($order);

		$result = $category->insert();

		if ($result) { // si $result est vrai -> insertion OK	
			$this->redirect('category-list');
		} else { // si $result est faux -> insertion KO
			$errorMessages[] = "Un problème est survenu durant l'insertion";
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$viewData['title'] = 'Erreur ajout catégorie';
			$this->show('category/add', $viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}
	}

	public function update($categoryId)
	{
		// dump($categoryId); // $categoryId -> identifiant passé dans l'URL

		// 1. récupérer les informations de la catégorie à modifier depuis la base de données ($categoryId)
		$category = Category::find($categoryId);

		// 2. envoyer le formulaire pré-rempli avec les info. de la catégorie
		$viewData['category'] = $category;
		$viewData['title'] = 'Modifier la catégorie ' . $category->getName();
		$this->show('category/add', $viewData);
	}

	public function updatePost($categoryId)
	{
		// dump($categoryId); // id provenant de l'URL
		// dump($_POST); // données provenant du formulaire

		// 1. vérifier / controller les données saisies par l'utilisateur 
		$name = htmlspecialchars(filter_input(INPUT_POST, 'name'));
		$subtitle = htmlspecialchars(filter_input(INPUT_POST, 'subtitle'));
		if (!empty(filter_input(INPUT_POST, 'picture'))) {
			$picture = filter_input(INPUT_POST, 'picture', FILTER_VALIDATE_URL);
		} else {
			$picture = '';
		}
		$order = filter_input(INPUT_POST, 'order', FILTER_VALIDATE_INT);

		$errorMessages = [];

		// récupérer la catégorie à modifier dans la BDD 
		$category = Category::find($categoryId);

		if (empty($name)) {
			$errorMessages[] = "Champ nom obligatoire";
		}
		if (strlen($name) > 64) {
			$errorMessages[] = "Nom : 64 caracètres max";
		}
		if (!empty($subtitle) && strlen($subtitle) > 64) {
			$errorMessages[] = "Sous titre : 64 caractères max";
		}
		if ($picture === false) {
			$category->setPicture(htmlspecialchars(filter_input(INPUT_POST, 'picture'))); // copie le champ picture dans $category->picture
			$errorMessages[] = "L'image est invalide";
		}
		if ($order === false) {
			$category->setHomeOrder(htmlspecialchars(filter_input(INPUT_POST, 'order'))); // copie le champ order dans $category->order
			$errorMessages[] = "L'ordre est invalide";
		}

		if (!empty($errorMessages)) { // Si error dans les données saisies
			$viewData['title'] = 'Erreur ajout catégorie';
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$viewData['category'] = $category; // envoie de la catégorie à modifier dans le formulaire
			$this->show('category/add', $viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}

		// 2. modifier la catégorie avec les données du formulaire (instance / Objet PHP)
		$category->setName($name);
		$category->setSubtitle($subtitle);
		$category->setPicture($picture);
		$category->setHomeOrder($order);

		// 3. mettre à jour la catégorie dans la BDD
		$result = $category->update();

		if ($result) { // si $result est vrai -> update OK	
			$this->redirect('category-list');
		} else { // si $result est faux -> insertion KO
			$errorMessages[] = "Un problème est survenu durant la mise à jour";
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$viewData['title'] = 'Erreur ajout catégorie';
			$viewData['category'] = $category; // envoie de la catégorie à modifier dans le formulaire
			$this->show('category/add', $viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}
	}

	/**
	 * Supprime une catégorie
	 *
	 * @param [int] $categoryId identifiant de la catégorie à supprimer récupéré depuis l'URL
	 * @return void
	 */
	public function delete($categoryId)
	{

		// dump($categoryId);

		// récupére depuis la BDD la catégorie à supprimer
		$category = Category::find($categoryId);

		if ($category !== false) { // on a trouvé la catégorie à supprimer
			$category->delete(); // on supprime la catégorie en question
		}

		$this->redirect('category-list');
	}

	/**
	 * Affiche le formulaire de sélection des 5 catégories à mettre en avant
	 *
	 * @return void
	 */
	public function homeOrder()
	{

		// // 1. récupérer la liste des catégories à donner au template
		// $categories = Category::findAll();
		// $viewData['categories'] = $categories;

		// // 2. charge le formulaire de sélectetion des catégories AVEC la liste des catégories en BDD
		// $this->show('category/home-order', $viewData);

		// Meme code mais sur une seule ligne
		$this->show('category/home-order', ['categories' => Category::findAll()]);
	}

	/**
	 * Enregistrer la nouvelle liste de catégories à mettre en avant sur la page d'accueil
	 *
	 * @return void
	 */
	public function homeOrderPost()
	{

		// 1. récupère les données du formulaire

		// dump($_POST);
		// Dans $_POST['emplacement'] = Array [
		// 		1 => "1"
		// 		2 => "2"
		// 		3 => "3"
		// 		4 => "5"
		// 		5 => "6"
		// ]
		// On a forcé les clés du tableau ( @see template name="emplacement[$i]" )
		// emplacement => Array[ "clé = emplacement $i" => "id de la catégorie choisie" ]

		// Demande à filter_input de me valider un tableau d'entier
		// Si les données ne sont pas des entier, il remplace la valeur erronée par false
		$emplacements = filter_input(INPUT_POST, 'emplacement', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

		// 2. controller les données saisies par l'utilisateur
		// renvoyer des messages d'erreur si besoin

		$errorsMessages = [];

		// 2.1 Est-ce qu'il y a false dans le tableau ?
		if (in_array(false, $emplacements)) {
			$errorMessages[] = 'Une ou plusieurs catégories sont invalides';
		}

		// 2.2 Est-ce toutes les valeurs du tableau sont unique ?
		// @see https://www.php.net/manual/fr/function.array-unique.php
		// supprime les doublons
		$emplacements = array_unique($emplacements);

		// Est-ce que j'ai toujours 5 valeurs dans mon tableau
		// Si ça n'est pas le cas, array_unique à supprimé des doublons
		if (count($emplacements) != 5) {
			$errorMessages[] = 'Des catégories sont en doublon';
		}

		// Si des erreurs ont été enregistrée, envois du formulaire avec les erreurs
		if (!empty($errorMessages)) { // Si error dans les données saisies
			// je récupère la liste des catégories
			$categories = Category::findAll();
			// j'enregistre dans viewData les catégories + les messages d'erreur
			$viewData['categories'] = $categories;
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$this->show('category/home-order', $viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}

		// 3. reset du home-order pour toutes les catégories de la BDD => home-order = 0 pour toutes les catégories
		// Pour éviter les boublons du genre : deux catégories possèdent le meme home_order (1 par exemple)
		Category::resetHomeOrder();

		// 4. mettre à jour la liste des catégories à mettre avant (update du home-order pour les catégories concernée)
		foreach ($emplacements as $homeOrder => $categoryId) {
			// 4.1 je dois récupérer la catégorie concernée
			$category = Category::find($categoryId);

			// Uniquement si $category n'est pas faux, alors :
			// 4.2 je dois changer son home_order (dans l'instance PHP)
			if ($category != false) { // la catégorie existe en BDD
				$category->setHomeOrder($homeOrder);

				// 4.3 mettre à jour la BDD avec ma catégorie modifiée
				if (!$category->update()) { // update renvoie FAUX => la MAJ ne s'est pas bien passée
					// $this->redirect('category-homeOrder');
					$errorMessages[]  = 'Mise à jour a échoué pour une catégorie';
				}
			} else {
				$errorMessages[] = 'Catégorie inexistante';
			}
		}

		// Si des erreurs ont été enregistrée, envois du formulaire avec les erreurs
		if (!empty($errorMessages)) { // Si error dans les données saisies
			// je récupère la liste des catégories
			$categories = Category::findAll();
			// j'enregistre dans viewData les catégories + les messages d'erreur
			$viewData['categories'] = $categories;
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$this->show('category/home-order', $viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		} else { // Aucun message d'erreur, la MAJ s'est bien passée
			$this->redirect('category-homeOrder');
		}
	}
}
