<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

class Category extends CoreModel
{

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $subtitle;
	/**
	 * @var string
	 */
	private $picture;
	/**
	 * @var int
	 */
	private $home_order;

	/**
	 * Get the value of name
	 *
	 * @return  string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @param  string  $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * Get the value of subtitle
	 */
	public function getSubtitle()
	{
		return $this->subtitle;
	}

	/**
	 * Set the value of subtitle
	 */
	public function setSubtitle($subtitle)
	{
		$this->subtitle = $subtitle;
	}

	/**
	 * Get the value of picture
	 */
	public function getPicture()
	{
		return $this->picture;
	}

	/**
	 * Set the value of picture
	 */
	public function setPicture($picture)
	{
		$this->picture = $picture;
	}

	/**
	 * Get the value of home_order
	 */
	public function getHomeOrder()
	{
		return $this->home_order;
	}

	/**
	 * Set the value of home_order
	 */
	public function setHomeOrder($home_order)
	{
		$this->home_order = $home_order;
	}

	/**
	 * Méthode permettant de récupérer un enregistrement de la table Category en fonction d'un id donné
	 *
	 * @param int $categoryId ID de la catégorie
	 * @return Category
	 */
	public static function find(int $categoryId)
	{
		// se connecter à la BDD
		$pdo = Database::getPDO();

		// écrire notre requête
		$sql = 'SELECT * FROM `category` WHERE `id` =' . $categoryId;

		// exécuter notre requête
		$pdoStatement = $pdo->query($sql);

		// un seul résultat => fetchObject
		// fetchObject retourne soit une instance de Category soit FAUX
		$category = $pdoStatement->fetchObject(self::class);

		// retourner le résultat
		return $category;
	}

	/**
	 * Méthode permettant de récupérer tous les enregistrements de la table category
	 *
	 * @return Category[]
	 */
	public static function findAll()
	{
		$pdo = Database::getPDO();
		$sql = 'SELECT * FROM `category`';
		$pdoStatement = $pdo->query($sql);
		// fetchAll retour un tabelau d'instanceS de Category
		$results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

		return $results;
	}

	/**
	 * Récupérer les 5 catégories mises en avant sur la home
	 *
	 * @return Category[]
	 */
	public static function findAllHomepage()
	{
		$pdo = Database::getPDO();
		$sql = '
            SELECT *
            FROM category
            WHERE home_order > 0
            ORDER BY home_order ASC
        ';
		$pdoStatement = $pdo->query($sql);
		$categories = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Category');

		return $categories;
	}

	/**
	 * Méthode permettant d'ajouter un enregistrement dans la table category
	 * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
	 *
	 * @return bool
	 */
	public function insert()
	{
		// Récupération de l'objet PDO représentant la connexion à la DB
		$pdo = Database::getPDO();

		// Ecriture de la requête INSERT INTO
		$sql = "INSERT INTO `category` (name, subtitle, picture, home_order)
            VALUES (:name, :subtitle, :picture, :home_order)";
		
		$query = $pdo->prepare($sql);

		$query->bindValue(':name', $this->name);
		$query->bindValue(':subtitle', $this->subtitle);
		$query->bindValue(':picture', $this->picture);
		$query->bindValue(':home_order', $this->home_order);

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


	/**
	 * Méthode permettant de mettre à jour un enregistrement dans la table category
	 * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
	 *
	 * @return bool
	 */
	public function update()
	{
		// Récupération de l'objet PDO représentant la connexion à la DB
		$pdo = Database::getPDO();

		// Ecriture de la requête INSERT INTO
		$sql = "UPDATE `category` SET 
			name = :name,
			subtitle = :subtitle,
			picture = :picture,
			home_order = :home_order
			WHERE id = :id";
		
		$query = $pdo->prepare($sql);

		$query->bindValue(':name', $this->name);
		$query->bindValue(':subtitle', $this->subtitle);
		$query->bindValue(':picture', $this->picture);
		$query->bindValue(':home_order', $this->home_order);
		$query->bindValue(':id', $this->id);

		// Execution de la requête d'insertion (exec, pas query)
		$updatedRows = $query->execute();

		return ($updatedRows == 1); // retourne VRAI si $updatedRows == 1 sinon FAUX
	}

	/**
	 * Remove a category from database
	 */
	public function delete()
	{
		 // Récupération de l'objet PDO représentant la connexion à la DB
		 $pdo = Database::getPDO();

		 // Déclaration de la requête
		 $sql = 'DELETE FROM `category` WHERE id = :id';
     
		 // Préparation de la requête
		 $query = $pdo->prepare($sql);
     
		 // On passe l'id
		 $query->bindValue(':id', $this->id, PDO::PARAM_INT);
     
		 $deletedRows = $query->execute();
     
		 // On retourne VRAI, si une ligne supprimée FAUX sinon
		 return ($deletedRows == 1);
	}

	/**
	 * Remet à 0 le champ home-order pour toutes les catégories en BDD qui ont un home_order différent de 0
	 *
	 * @return void
	 */
	public static function resetHomeOrder() {

		// Récupération de l'objet PDO représentant la connexion à la DB
		$pdo = Database::getPDO();

		$sql = 'UPDATE category SET home_order = 0 WHERE home_order != 0';

		// Je fais un exec uniquement parce-que la requete SQL ne contient aucune variable -> elle est figée, c'est moi qui la maitrise
		$pdo->exec($sql);
	} 
}