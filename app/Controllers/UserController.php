<?php

namespace App\Controllers;

use App\Controllers\CoreController;
use App\Models\AppUser;

class UserController extends CoreController
{

	/*
	 * #1. Comment gérer une connexion ?
	 * -> récupérer la saisie de l'utilisateur dans $_POST
	 * -> vérifier que les informations sont correctes en BDD
	 * -> si les informations sont correctes, on connecte l'utilisateur (on stocke les informations utiles en session)
	 * 
	 * #2. Est-ce que l'utilisateur est connecté ? (a-t-on une information en session ?)
	 * -> si l'utilisateur est connecté, il peut accéder au contenu
	 * -> si non, on affiche le formulaire de connexion (email / password)
	 */

	/*
	 * $_SESSION est tableau associatif qui est vide à l'origine : https://www.php.net/manual/fr/reserved.variables.session.php
	 * On peut y stocker toutes les informations que l'on souhaite
	 * Et grâce à ce système, on peut retrouver les informations de session entre chaque pages appelées par l'internaute.
	 *
	 * À la connexion de l'utilisateur on peut par exemple enregistré en session l'objet AppUser qui correspond à l'utilisateur
	 * 
	 * Si les informations ne sont pas correctes : message d'erreur sur la page de connexion
	 */

	/**
	 * Présente au visiteur le formulaire de connexion
	 *
	 * @return void
	 */
	public function login()
	{
		// 1. présenter au visiteur le formulaire de connextion
		$this->show('user/login');
	}

	/**
	 * Gère l'authentification d'un utilisateur
	 *
	 * @return void
	 */
	public function loginPost()
	{

		// 1. Récupère les données du formulaire
		$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
		$password = htmlspecialchars(filter_input(INPUT_POST, 'password'));

		// 2. Récupère les données de la BDD
		$appUser = AppUser::findByEmail($email);

		// 3. Compare données du formulaire VS données de la BDD -> login ou pas
		if ((false === $appUser)		// la requete a échoué -> email pas trouvé -> login KO
			|| false === password_verify($password, $appUser->getPassword())
		) {		// mot de passe saisi != mot de passe BDD 
			// Erreur de login
			$viewData['errors'] = ['Erreur saisie email ou mot de passe'];
			$this->show('user/login', $viewData);
		} else {	// user bien authentifé
			// Enregistre en session l'instance de AppUser qui contient toutes les info de l'user connecté
			$_SESSION['user'] = $appUser; // serialize transforme un objet binaire en chaine de caractères
			// redirige le visiteur vers la page d'accueil
			$this->redirect('main-home');
		}
	}

	/**
	 * Déconnecte un utilisateur
	 *
	 * @return void
	 */
	public function logout()
	{
		// 1. detruire les variables de session

		// @see https://www.php.net/manual/fr/function.session-destroy
		// destruction de la session, MAIS les variables globales associées à la session
		// ne sont pas détruites
		session_destroy();
		// on détruit le tableau des sessions
		unset($_SESSION);

		// 2. rediriger vers la page de login
		$this->redirect('user-login');
	}

	/**
	 * Récupère la liste des utilisateurs enregistrés en BDD
	 * Puis charge le template pour les afficher
	 *
	 * @return void
	 */
	public function list()
	{
		// 	// 1. récupérer la liste des utilisateurs
		// 	$users = AppUser::findAll();

		// 	// 2. charge le template d'affichage de la liste des utilisateurs
		// 	$viewData['users'] = $users;
		// 	$this->show('user/list', $viewData);

		// Meme instructions que ci dessus, mais en une seule ligne
		$this->show('user/list', ['users' => AppUser::findAll()]);
	}

	/**
	 * Charger le template du formulaire d'ajout d'un utilisateur
	 *
	 * @return void
	 */
	public function add() {

		$this->show('user/add');
	}

	/**
	 * Enregistre un nouvel utilisateur en BDD
	 * 
	 * 1. récupérer les données du formulaire
	 * 
	 * 2. vérifier les valeurs saisies et renvoyer des messages d'erreur si nécessaire
	 * 
	 * 3. Enregistre le nouvel utilisateur dans la BDD
	 *
	 * @return void
	 */
	public function create() {

		// 1. récupérer les données du formulaire
		$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
		$password = filter_input(INPUT_POST, 'password');
		$firstname = htmlspecialchars(filter_input(INPUT_POST, 'firstname'));
		$lastname = htmlspecialchars(filter_input(INPUT_POST, 'lastname'));
		$role = filter_input(INPUT_POST, 'role');
		$status = filter_input(INPUT_POST, 'status');

		// 2. Vérifier les données saisies
		$errorsMessage = [];
		if( $email === false ) {
			$errorsMessage[] = 'Email invalide';
		}
		if( strlen($password) < 8 ) {
			$errorsMessage[] = 'Mot de passe trop court, au moins 8 caractères';
		}
		if( empty($firstname) ) {
			$errorsMessage[] = 'Prénom obligatoire';
		}
		if( empty($lastname) ) {
			$errorsMessage[] = 'Nom de famille obligatoire';
		}
		if( !in_array($role, ['admin', 'catalog-manager']) ) {
			$errorsMessage[] = 'Role invalid';
		}
		if( $status < 1 || $status > 2) {
			$errorsMessage[] = 'Status invalid';
		}

		// S'il y a des erreurs dans les données saisies
		if( !empty($errorsMessage) ) {
			// enregistre les messages d'erreur dans $viewData
			$viewData['errors'] = $errorsMessage;
			// recharge le formulaire d'ajout d'un utilisateur avec les messages d'erreurs
			$this->show('user/add', $viewData);
			// quitte la fonction
			exit();
		}

		// S'il n'y a PAS d'erreur dans les données saisies, je peux continuer
		// 3. Enregistre le nouvel utilisateur dans la BDD

		// 3.1 créer une nouvelle instance de AppUser
		$user = new AppUser();
		$user->setEmail($email);
		$user->setPassword( password_hash($password, PASSWORD_DEFAULT) ); // enregistre dans l'objet $user le hash du password
		$user->setFirstname($firstname);
		$user->setLastname($lastname);
		$user->setRole($role);
		$user->setStatus($status);

		// 3.2 Sauvegrde en BDD l'instance de AppUser
		$insertOK = $user->insert();

		if($insertOK) { // l'insertion en BDD s'est bien passée
			$this->redirect('user-list');
		} else { // l'insertion en BDD a échoué -> enregistre un nouveau message d'erreur et affiche le formulaire
			$errorsMessage[] = "Erreur durant l'enregistrement de l'utilisateur";
			// enregistre les messages d'erreur dans $viewData
			$viewData['errors'] = $errorsMessage;
			// recharge le formulaire d'ajout d'un utilisateur avec les messages d'erreurs
			$this->show('user/add', $viewData);
			// quitte la fonction
			exit();
		}
	}
}
