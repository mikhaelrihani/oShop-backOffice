<?php

namespace App\Models;

use App\Models\CoreModel;
use App\Utils\Database;
use PDO;

class AppUser extends CoreModel
{

	private $email;		// varchar(128)	
	private $password;	// varchar(60)	
	private $firstname;	// varchar(64) NULL	
	private $lastname;	// varchar(64) NULL	
	private $role;		// enum('admin','catalog-manager')	
	private $status;	// tinyint(3)

	public static function findByEmail($email) {
		// récupère la connexion à la BDD
		$pdo = Database::getPDO();

		// requete SQL à executer
		$sql = 'SELECT * FROM `app_user` WHERE email = :email';

		// prépare la requete
		$query = $pdo->prepare($sql);

		$query->bindValue(':email', $email);

		// execute la requete
		$query->execute();

		//$appUser est une instance de AppUser ou FAUX si aucun résultat
		$appUser = $query->fetchObject(self::class); // self::class = 'App\Models\AppUser'

		// return l'instance de AppUer avec les infos de la BDD ou FAUX si user pas trouvé
		return $appUser;
	} 

	/**
	 * Récupère tous les utilisateurs de la BDD
	 *
	 * @return AppUser[]
	 */
	public static function findAll()
	{
		// Récupère la connexion vers la BDdd
		$pdo = Database::getPDO();
		
		// Enregistre dans une chaine de caractères la requete SQL à executer
		$sql = 'SELECT * FROM `app_user`';

		// Excuter la requete $sql
		$pdoStatement = $pdo->query($sql);

		// Récupère les résultats de la requete et on les enregistre sous forme
		// de tableau d'instances de AppUser
		// self::class me retourne le nom complet de ma classe AppUser, c.a.d : \App\Models\AppUser
		$results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

		// retourne le résultat (tableau d'instances de la classe AppUser)
		return $results;
	}

	public static function find(int $id)
	{
		// !Not Implemented Yet!
	}

	/**
	 * Enregistre dans la BDD un nouvel utilisateur
	 *
	 * @return Bool True si utilisateur bien enregistré, False sinon
	 */
	public function insert()
	{
		// Récupération de l'objet PDO représentant la connexion à la DB
		$pdo = Database::getPDO();

		// Ecriture de la requête INSERT INTO
		$sql = "INSERT INTO `app_user` (email, password, firstname, lastname, role, status)
            VALUES (:email, :password, :firstname, :lastname, :role, :status)";
		
		$query = $pdo->prepare($sql);

		$query->bindValue(':email', $this->email);
		$query->bindValue(':password', $this->password);
		$query->bindValue(':firstname', $this->firstname);
		$query->bindValue(':lastname', $this->lastname);
		$query->bindValue(':role', $this->role);
		$query->bindValue(':status', $this->status);

		// Execution de la requête d'insertion (exec, pas query)
		$insertedRows = $query->execute();

		// Si au moins une ligne ajoutée
		if ($insertedRows == 1) {
			// Alors on récupère l'id auto-incrémenté généré par MySQL
			$this->id = $pdo->lastInsertId();

			// On retourne VRAI car l'ajout a parfaitement fonctionné
			return true;
			// => l'interpréteur PHP sort de cette fonction car on a retourné une donnée
		}

		// Si on arrive ici, c'est que quelque chose n'a pas bien fonctionné => FAUX
		return false;
	}
	
	public function update()
	{
		// !Not Implemented Yet!
	}
	public function delete()
	{
		// !Not Implemented Yet!
	}

	/**
	 * Get the value of email
	 */ 
	public function getEmail()
	{
		return $this->email;
	}

	/**
	 * Set the value of email
	 *
	 * @return  self
	 */ 
	public function setEmail($email)
	{
		$this->email = $email;

		return $this;
	}

	/**
	 * Get the value of password
	 */ 
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * Set the value of password
	 *
	 * @return  self
	 */ 
	public function setPassword($password)
	{
		$this->password = $password;

		return $this;
	}

	/**
	 * Get the value of firstname
	 */ 
	public function getFirstname()
	{
		return $this->firstname;
	}

	/**
	 * Set the value of firstname
	 *
	 * @return  self
	 */ 
	public function setFirstname($firstname)
	{
		$this->firstname = $firstname;

		return $this;
	}

	/**
	 * Get the value of lastname
	 */ 
	public function getLastname()
	{
		return $this->lastname;
	}

	/**
	 * Set the value of lastname
	 *
	 * @return  self
	 */ 
	public function setLastname($lastname)
	{
		$this->lastname = $lastname;

		return $this;
	}

	/**
	 * Get the value of role
	 */ 
	public function getRole()
	{
		return $this->role;
	}

	/**
	 * Set the value of role
	 *
	 * @return  self
	 */ 
	public function setRole($role)
	{
		$this->role = $role;

		return $this;
	}

	/**
	 * Get the value of status
	 */ 
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set the value of status
	 *
	 * @return  self
	 */ 
	public function setStatus($status)
	{
		$this->status = $status;

		return $this;
	}
}
