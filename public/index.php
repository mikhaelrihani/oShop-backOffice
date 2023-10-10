<?php


// POINT D'ENTRÉE UNIQUE :
// FrontController

// inclusion des dépendances via Composer
// autoload.php permet de charger d'un coup toutes les dépendances installées avec composer
// mais aussi d'activer le chargement automatique des classes (convention PSR-4)
require_once '../vendor/autoload.php';


// Création de la sessions -> à placer toujours au début de index.php
session_start();

/* ==== DEMO SESSIONS === */

// Manipulation des variables de sessions
// Enregistre une chaine de caractère
// $_SESSION['user'] = "toto";

// Enregistre un objet (instance de Category)
// $category = new Category();
// $category->setName("Coucou");
// $_SESSION['category'] = [$category];
// dump($_SESSION['category']);

// Destruction des variables de la session
// @see https://www.php.net/manual/fr/function.session-destroy
// destruction de la session, MAIS les variables globales associées à la session
// ne sont pas détruites
// session_destroy();
// on détruit le tableau des sessions
// unset($_SESSION);

/* ==== DEMO SESSIONS === */

/* ------------
--- ROUTAGE ---
-------------*/


// création de l'objet router
// Cet objet va gérer les routes pour nous, et surtout il va
$router = new AltoRouter();

// le répertoire (après le nom de domaine) dans lequel on travaille est celui-ci
// Mais on pourrait travailler sans sous-répertoire
// Si il y a un sous-répertoire
if (array_key_exists('BASE_URI', $_SERVER)) {
	// Alors on définit le basePath d'AltoRouter
	$router->setBasePath($_SERVER['BASE_URI']);
	// ainsi, nos routes correspondront à l'URL, après la suite de sous-répertoire
} else { // sinon
	// On donne une valeur par défaut à $_SERVER['BASE_URI'] car c'est utilisé dans le CoreController
	$_SERVER['BASE_URI'] = '/';
}

// On doit déclarer toutes les "routes" à AltoRouter,
// afin qu'il puisse nous donner LA "route" correspondante à l'URL courante
// On appelle cela "mapper" les routes
// 1. méthode HTTP : GET ou POST (pour résumer)
// 2. La route : la portion d'URL après le basePath
// 3. Target/Cible : informations contenant
//      - le nom de la méthode à utiliser pour répondre à cette route
//      - le nom du controller contenant la méthode
// 4. Le nom de la route : pour identifier la route, on va suivre une convention
//      - "NomDuController-NomDeLaMéthode"
//      - ainsi pour la route /, méthode "home" du MainController => "main-home"
$router->map(
	'GET',
	'/',
	[
		'method' => 'home',
		'controller' => '\App\Controllers\MainController' // On indique le FQCN de la classe
	],
	'main-home'
);

$router->map(
	'GET',
	'/category/list',
	[
		'method' => 'list',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-list'
);

$router->map(
	'GET',
	'/categorie/add',
	[
		'method' => 'add',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-add'
);

$router->map(
	'POST',
	'/categorie/add',
	[
		'method' => 'create',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-create'
);

$router->map(
	'GET',
	'/product/list',
	[
		'method' => 'list',
		'controller' => '\App\Controllers\ProductController' // On indique le FQCN de la classe
	],
	'product-list'
);

$router->map(
	'GET',
	'/product/add',
	[
		'method' => 'add',
		'controller' => '\App\Controllers\ProductController' // On indique le FQCN de la classe
	],
	'product-add'
);

$router->map(
	'POST',
	'/product/add',
	[
		'method' => 'create',
		'controller' => '\App\Controllers\ProductController' // On indique le FQCN de la classe
	],
	'product-create'
);

$router->map(
	'GET',
	'/category/[i:categoryId]/update',
	[
		'method' => 'update',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-update'
);

$router->map(
	'POST',
	'/category/[i:categoryId]/update',
	[
		'method' => 'updatePost',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-updatePost'
);

$router->map(
	'GET',
	'/category/[i:categoryId]/delete', // exemple pour la catégorie 12 : /category/12/delete
	[
		'method' => 'delete',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-delete'
);

$router->map(
	'GET',
	'/category/home-order', // exemple pour la catégorie 12 : /category/12/delete
	[
		'method' => 'homeOrder',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-homeOrder'
);

$router->map(
	'POST',
	'/category/home-order', // exemple pour la catégorie 12 : /category/12/delete
	[
		'method' => 'homeOrderPost',
		'controller' => '\App\Controllers\CategoryController' // On indique le FQCN de la classe
	],
	'category-homeOrderPost'
);

$router->map(
	'GET',
	'/product/[i:productId]/update',
	[
		'method' => 'update',
		'controller' => '\App\Controllers\ProductController' // On indique le FQCN de la classe
	],
	'product-update'
);

$router->map(
	'POST',
	'/product/[i:productId]/update',
	[
		'method' => 'updatePost',
		'controller' => '\App\Controllers\ProductController' // On indique le FQCN de la classe
	],
	'product-updatePost'
);

$router->map(
	'GET',
	'/product/[i:productId]/delete',
	[
		'method' => 'delete',
		'controller' => '\App\Controllers\ProductController' // On indique le FQCN de la classe
	],
	'product-delete'
);

$router->map(
	'GET',
	'/login',
	[
		'method' => 'login',
		'controller' => '\App\Controllers\UserController' // On indique le FQCN de la classe
	],
	'user-login'
);

$router->map(
	'POST',
	'/login',
	[
		'method' => 'loginPost',
		'controller' => '\App\Controllers\UserController' // On indique le FQCN de la classe
	],
	'user-loginPost'
);

$router->map(
	'GET',
	'/logout',
	[
		'method' => 'logout',
		'controller' => '\App\Controllers\UserController' // On indique le FQCN de la classe
	],
	'user-logout'
);

$router->map(
	'GET',
	'/403',
	[
		'method' => 'err403',
		'controller' => '\App\Controllers\ErrorController' // On indique le FQCN de la classe
	],
	'error-403'
);

$router->map(
	'GET',
	'/user/list',
	[
		'method' => 'list',
		'controller' => '\App\Controllers\UserController' // On indique le FQCN de la classe
	],
	'user-list'
);

$router->map(
	'GET',
	'/user/add',
	[
		'method' => 'add',
		'controller' => '\App\Controllers\UserController' // On indique le FQCN de la classe
	],
	'user-add'
);

$router->map(
	'POST',
	'/user/add',
	[
		'method' => 'create',
		'controller' => '\App\Controllers\UserController' // On indique le FQCN de la classe
	],
	'user-create'
);

/* -------------
--- DISPATCH ---
--------------*/

// On demande à AltoRouter de trouver une route qui correspond à l'URL courante
$match = $router->match();

// $match possède à la clé "name" le nom de la route demandée
// dump($match['name']);

// dump($match); // Soit faux si la route demandée n'existe pas, soit un tableau avec toutes les infos liées à la route demandée

// Ensuite, pour dispatcher le code dans la bonne méthode, du bon Controller
// On délègue à une librairie externe : https://packagist.org/packages/benoclock/alto-dispatcher
// 1er argument : la variable $match retournée par AltoRouter
// 2e argument : le "target" (controller & méthode) pour afficher la page 404
$dispatcher = new Dispatcher($match, '\App\Controllers\ErrorController::err404');

// Une fois le "dispatcher" configuré, on lance le dispatch qui va exécuter la méthode du controller

/*
 * Exemple, pour afficher la page d'accueil
 * dispatcher execute le code suivant :
 * $controller = new MainController(); 	// parce qu'on a dit dans la route de home qu'il faut utiliser MainController
 * 	-> on peut placer dans le contructeur de CoreController du code qui sera exécuté toujours avant les fonctions des controlleurs
 * $controller->home(); 				// parqu'on a dit dans la route de home d'utiliser la fonction home()
 */ 

// @see https://packagist.org/packages/benoclock/alto-dispatcher
// je donne en argument du constructeur le nom de la route demandée par le visiteur
if($match != false) {
    $dispatcher->setControllersArguments($router, $match['name']);
}

$dispatcher->dispatch(); 
