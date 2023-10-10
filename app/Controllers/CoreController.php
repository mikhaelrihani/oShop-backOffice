<?php

namespace App\Controllers;

abstract class CoreController
{

	// Router de AltoRouter
	private $router;

	/**
	 * Constructeur commun à tous les Controller
	 *
	 * @param [string] action demandée par le visiteur
	 */
	public function __construct($router = null, $action = '')
	{
		$this->router = $router;

		$acl = [
			'main-home'            => ['admin', 'catalog-manager'],

			'category-list'        => ['admin', 'catalog-manager'],
			'category-add'         => ['admin', 'catalog-manager'],
			'category-create'      => ['admin', 'catalog-manager'],
			'category-update'      => ['admin', 'catalog-manager'],
			'category-update-post' => ['admin', 'catalog-manager'],
			'category-delete'      => ['admin', 'catalog-manager'],
			'category-homeOrder'   => ['admin', 'catalog-manager'],
			'category-homeOrderPost' => ['admin', 'catalog-manager'],

			'product-list'         => ['admin', 'catalog-manager'],
			'product-add'          => ['admin', 'catalog-manager'],
			'product-create'       => ['admin', 'catalog-manager'],
			'product-update'       => ['admin', 'catalog-manager'],
			'product-update-post'  => ['admin', 'catalog-manager'],
			'product-delete'       => ['admin', 'catalog-manager'],

			'user-list'            => ['admin'],
			'user-add'             => ['admin'],
			'user-create'          => ['admin'],
			'user-delete'          => ['admin'],
		];

		// Est-ce que l'action demandée est protégée par les ACL ?
		// Si oui je vérifie que les droits de l'utilisateur sont suffisants
		// Si non, je laisse passer
		if (array_key_exists($action, $acl)) { // l'action demandée est une clé du tableau ACL 
			$this->checkAuthorization($acl[$action]); // je vérifie les droits
		}

		// Route à protéger contre les attaques CSRF en POST
		$csrfTokenPost = [
			'category-create',
			'category-updatePost',
			'category-homeOrderPost',
			'product-create',
			'product-updatePost',
			'user-create',
			'user-updatePost'
		];

		$csrfTokenGet = [
			'category-delete',
			'product-delete',
			'user-delete'
		];

		// dump($action);
		
		// Est-ce que la page demandé (POST) doit etre protégée par un token
		if( in_array($action, $csrfTokenPost) ) { // OUI
			$tokenCsrf = filter_input(INPUT_POST, 'tokenCsrf'); // récupère le token du formulaire 
			// Comparer le token du forumaire $tokenCsrf avec le token en Sessions
			// dump($tokenCsrf);
			$this->checkToken($tokenCsrf);
		} else {
			// Est ce que la page demandée (GET) doit etre protégée par un token
			if( in_array($action, $csrfTokenGet) ) { // OUI
				$tokenCsrf = filter_input(INPUT_GET, 'tokenCsrf');
				// Comparer le token de l'URL $tokenCsrf avec le token en Sessions
				$this->checkToken($tokenCsrf);
			}
		}

		// L'utilisateur est bien connecté
		// je génère un token que j'enregistre en session
		$_SESSION['tokenCsrf'] = bin2hex(random_bytes(32)); // exemple de token : kdjfsdf354sdf6sd4f65sd76s46dfs45d4f6s4fds4f65s
	}

	/**
	 * Compare $tokenCsrf avec le token enregistré en SESSION
	 * Si ok
	 * Si KO -> erreur 403
	 * 
	 * @param [string] $tokenCsrf
	 * @return void
	 */
	private function checkToken($tokenCsrf) {
		// récupère le tokken en session
		$tokenSession = isset($_SESSION['tokenCsrf']) ? $_SESSION['tokenCsrf'] : null;

		// dump($tokenCsrf);
		// dump($tokenSession);

		// Compare le token en session avec le token passé en argument
		if( $tokenSession == $tokenCsrf) { // OK le visiteur peut faire l'action
			unset($_SESSION['tokenCsrf']); // supprime le token de la sessions
		} else { // KO pas le droit de faire l'action
			$this->redirect('error-403');
		}
	}

	/**
	 * Vérifier si l'utilisateur connecté possède le bon role
	 *
	 * @param array $roles tableau des roles authorisés pour une action
	 * @return Si l'utilisateur connecté possède le bon role return Vrai, sinon redirection vers la page d'accueil
	 */
	private function checkAuthorization($roles = [])
	{

		// 0. est ce que l'utilisateur est connecté ?
		if (isset($_SESSION['user'])) {

			// 1. récupère le role de l'utilisateur connecté
			$user = $_SESSION['user']; // unserialize transforme une chaine de carectères en objet binaire
			$userRole = $user->getRole();

			// 2. compare le role de l'utilisateur avec la liste des roles passée en argument ($roles)
			if (in_array($userRole, $roles)) { // le role de l'utilisateur connecté est dans la liste des roles ($roles)
				// 3. si l'utilisateur a le bon role -> return Vrai
				return true;
			}

			// le role de l'utilisateur n'est pas dans la liste des roles authorisés -> redirection
			$this->redirect('error-403');
		} else { // utilisateur non connecté

			// 4. sinon, utilisateur non connecté redirige vers la page d'accueil
			$this->redirect('user-login');
		}
	}

	/**
	 * Méthode permettant d'afficher du code HTML en se basant sur les views
	 *
	 * @param string $viewName Nom du fichier de vue
	 * @param array $viewData Tableau des données à transmettre aux vues
	 * @return void
	 */
	protected function show(string $viewName, $viewData = [])
	{
		// Créer une variable $router à partir de l'attribut $this-router
		// pour partager le router avec les vues
		$router = $this->router;

		/*
		 * Pour la page de la liste des catégories :
		 * 
		 * dump $viewData == [
		 * 		"categories"	=> Category[] -> tableau d'instances de Category
		 * ]
		 */
		// dump($viewData);

		// Comme $viewData est déclarée comme paramètre de la méthode show()
		// les vues y ont accès
		// ici une valeur dont on a besoin sur TOUTES les vues
		// donc on la définit dans show()
		$viewData['currentPage'] = $viewName;
		// définir l'url absolue pour nos assets
		$viewData['assetsBaseUri'] = $_SERVER['BASE_URI'] . 'assets/';
		// définir l'url absolue pour la racine du site
		// /!\ != racine projet, ici on parle du répertoire public/
		$viewData['baseUri'] = $_SERVER['BASE_URI'];

		/*
		 * Pour la page de la liste des catégories :
		 * 
		 * dump $viewData == [
		 * 		"currentPage" 	=> "<page courante>",
		 * 		"assetBaseUri" 	=> ".../assets/",
		 * 		"baseUri"		=> "<base de l'URL>",
		 * 		"categories"	=> Category[] -> tableau d'instances de Category
		 * ]
		 */
		// dump($viewData);

		// On veut désormais accéder aux données de $viewData, mais sans accéder au tableau
		// La fonction extract permet de créer une variable pour chaque élément du tableau passé en argument
		extract($viewData);
		// => la variable $currentPage existe désormais, et sa valeur est $viewName
		// => la variable $assetsBaseUri existe désormais, et sa valeur est $_SERVER['BASE_URI'] . '/assets/'
		// => la variable $baseUri existe désormais, et sa valeur est $_SERVER['BASE_URI']
		// => il en va de même pour chaque élément du tableau

		// $viewData est disponible dans chaque fichier de vue
		require_once __DIR__ . '/../views/layout/header.tpl.php';
		require_once __DIR__ . '/../views/' . $viewName . '.tpl.php';
		require_once __DIR__ . '/../views/layout/footer.tpl.php';
	}

	/**
	 * Redirige le client vers la $page demandé
	 *
	 * @param [string] $page la page où rediriger le client
	 * @return void
	 */
	protected function redirect(string $page)
	{
		// global $router; --> terminé global router, on passe par les arguments du constructeur
		header('location: ' . $this->router->generate($page));
		exit();
	}
}
