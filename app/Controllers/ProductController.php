<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Type;
use App\Models\Tag;

class ProductController extends CoreController
{

	public function list()
	{
		// Récupérer la liste des produits
		$products = Product::findAll();
		$viewData['products'] = $products;

		$this->show('product/list', $viewData);
	}

	public function add()
	{
		$this->loadAddForm();
	}

	public function create()
	{

		$name = htmlspecialchars(filter_input(INPUT_POST, 'name'));
		$description = htmlspecialchars(filter_input(INPUT_POST, 'description'));
		$picture = filter_input(INPUT_POST, 'picture', FILTER_VALIDATE_URL);
		$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
		$rate = filter_input(INPUT_POST, 'rate', FILTER_VALIDATE_INT);
		$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
		$brand_id = filter_input(INPUT_POST, 'brand', FILTER_VALIDATE_INT);
		$category_id = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
		$type_id = filter_input(INPUT_POST, 'type', FILTER_VALIDATE_INT);

		$errorMessages = [];

		if (empty($name)) {
			$errorMessages[] = "Champ nom obligatoire";
		}
		if (strlen($name) > 64) {
			$errorMessages[] = "Nom : 64 caractères max";
		}

		if (!empty($errorMessages)) { // Si error dans les données saisies
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$this->loadAddForm($viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}

		$product = new Product();
		$product->setName($name);
		$product->setDescription($description);
		$product->setPicture($picture);
		$product->setPrice($price);
		$product->setRate($rate);
		$product->setStatus($status);
		$product->setBrandId($brand_id);
		$product->setCategoryId($category_id);
		$product->setTypeId($type_id);

		$result = $product->insert();

		if ($result) { // si $result est vrai -> insertion OK	
			$this->redirect('product-list');
		} else { // si $result est faux -> insertion KO
			$errorMessages[] = "Un problème est survenu durant l'insertion";
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$this->loadAddForm($viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}
	}

	public function update($productId)
	{
		$product = Product::find($productId);

		// Bonus : si $product == false
		// renvoyer une erreur au visiteur

		$viewData['product'] = $product;
		$viewData['productTags'] = Tag::findByProductId($product->getId());

		$this->loadAddForm($viewData);
	}

	public function updatePost($productId)
	{
		// 1. extraire les infos du formulaire
		$name = htmlspecialchars(filter_input(INPUT_POST, 'name'));
		$description = htmlspecialchars(filter_input(INPUT_POST, 'description'));
		$picture = filter_input(INPUT_POST, 'picture', FILTER_VALIDATE_URL);
		$price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
		$rate = filter_input(INPUT_POST, 'rate', FILTER_VALIDATE_INT);
		$status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
		$brand_id = filter_input(INPUT_POST, 'brand', FILTER_VALIDATE_INT);
		$category_id = filter_input(INPUT_POST, 'category', FILTER_VALIDATE_INT);
		$type_id = filter_input(INPUT_POST, 'type', FILTER_VALIDATE_INT);

		$tags = filter_input(INPUT_POST, 'tags', FILTER_VALIDATE_INT, FILTER_REQUIRE_ARRAY);

		// dump($tags);
		// $tags = array:5 [▼
		// 0 => 1
		// 1 => 2
		// 2 => 3
		// 3 => 4
		// 4 => 7
		// ]
		// $tags = [ "clé" => "id des tags associé au produit" ]

		// 2. cas où un ou des filtres ont échoués
		$errorMessages = [];
		if ($name === false) {
			$errorMessages[] = 'Le nom est invalide.';
		}
		if (empty($name)) {
			$errorMessages[] = 'Le nom ne doit pas être vide';
		}
		if ($description === false) {
			$errorMessages[] = 'La description est invalide.';
		}
		if (empty($description)) {
			$errorMessages[] = 'La description ne doit pas être vide';
		}
		if ($picture === false) {
			$errorMessages[] = 'L\'adresse de l\'image est invalide.';
		}
		if ($price === false) {
			$errorMessages[] = 'Le prix est invalide';
		}
		if ($rate === false) {
			$errorMessages[] = 'La note est invalide';
		}
		if ($status === false) {
			$errorMessages[] = 'Le statut est invalide';
		}
		if ($brand_id === false) {
			$errorMessages[] = 'La marque est invalide';
		}
		if ($category_id === false) {
			$errorMessages[] = 'La catégorie est invalide';
		}
		if ($type_id === false) {
			$errorMessages[] = 'Le type est invalide';
		}

		// cas particulier : un ou des filtres n'ont pas pu s'appliquer (car data manquante)
		if (
			$name === null || $description === null || $picture === null ||
			$price === null || $rate === null || $status === null ||
			$brand_id === null || $category_id === null || $type_id === null
		) {
			$errorMessages[] = 'Erreur dans la saisie des données';
		}

		$product = Product::find($productId);

		if (!empty($errorMessages)) { // Si error dans les données saisies
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$viewData['product'] = $product;
			$this->loadAddForm($viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}

		$product->setName($name);
		$product->setDescription($description);
		$product->setPicture($picture);
		$product->setPrice($price);
		$product->setRate($rate);
		$product->setStatus($status);
		$product->setBrandId($brand_id);
		$product->setCategoryId($category_id);
		$product->setTypeId($type_id);

		$result = $product->update();

		if ($result) { // si $result est vrai -> insertion OK	
			$tags = Tag::findByIds($tags);
			// dump($tags);
			// $tags est maintenant une liste d'instances de Tag
			// Ça ermet de valider les tags sélectionné et d'éliminer les éventuels id qui n'existent pas
			
			// Met à jour les tags du produit dans la BDD
			$product->updateTags($tags);

			// Redirection vers la liste des produits
			$this->redirect('product-list');
		} else { // si $result est faux -> insertion KO
			$errorMessages[] = "Un problème est survenu durant l'insertion";
			$viewData['errors'] = $errorMessages; // enregistre les messages d'erreur dans $viewData pour les transmettre au template
			$viewData['product'] = $product;
			$this->loadAddForm($viewData); // affiche le formulaire d'ajout d'une catégorie
			exit(); // quitte le programme
		}
	}

	private function loadAddForm($viewData = [])
	{
		$viewData['categories'] = Category::findAll();
		$viewData['types'] = Type::findAll();
		$viewData['brands'] = Brand::findAll();
		$viewData['tags'] = Tag::findAll();

		$this->show('product/add', $viewData);
	}

	public function delete($productId)
	{
		$product = Product::find($productId);

		if ($product !== false) {
			$product->delete();
		}

		$this->redirect('product-list');
	}
}
