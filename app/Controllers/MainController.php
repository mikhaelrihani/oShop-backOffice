<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\CoreModel;
use App\Models\Product;
use App\Models\Type;

// Si j'ai besoin du Model Category
// use App\Models\Category;

class MainController extends CoreController
{
	/**
	 * Méthode s'occupant de la page d'accueil
	 *
	 * @return void
	 */
	public function home()
	{

		// Récupérer la liste de toutes les catégories
		$categories = Category::findAll(); // tableau d'instances de Category
		$viewData['categories'] = $categories;

		// Récupérer la liste des produits
		$products = Product::findAll();
		$viewData['products'] = $products;

		// On appelle la méthode show() de l'objet courant
		// En argument, on fournit le fichier de Vue
		// Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller
		$this->show('main/home', $viewData);
	}
}
