<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

/**
 * Une instance de Product = un produit dans la base de données
 * Product hérite de CoreModel
 */
class Product extends CoreModel
{

	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $description;
	/**
	 * @var string
	 */
	private $picture;
	/**
	 * @var float
	 */
	private $price;
	/**
	 * @var int
	 */
	private $rate;
	/**
	 * @var int
	 */
	private $status;
	/**
	 * @var int
	 */
	private $brand_id;
	/**
	 * @var int
	 */
	private $category_id;
	/**
	 * @var int
	 */
	private $type_id;

	/**
	 * Méthode permettant de récupérer un enregistrement de la table Product en fonction d'un id donné
	 *
	 * @param int $productId ID du produit
	 * @return Product
	 */
	public static function find(int $productId)
	{
		// récupérer un objet PDO = connexion à la BDD
		$pdo = Database::getPDO();

		// on écrit la requête SQL pour récupérer le produit
		$sql = '
            SELECT *
            FROM product
            WHERE id = ' . $productId;

		// query ? exec ?
		// On fait de la LECTURE = une récupration => query()
		// si on avait fait une modification, suppression, ou un ajout => exec
		$pdoStatement = $pdo->query($sql);

		// fetchObject() pour récupérer un seul résultat
		// si j'en avais eu plusieurs => fetchAll
		$result = $pdoStatement->fetchObject('App\Models\Product');

		return $result;
	}

	/**
	 * Méthode permettant de récupérer tous les enregistrements de la table product
	 *
	 * @return Product[]
	 */
	public static function findAll()
	{
		$pdo = Database::getPDO();
		$sql = 'SELECT * FROM `product`';
		$pdoStatement = $pdo->query($sql);
		$results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, 'App\Models\Product');

		return $results;
	}

	/**
	 * Méthode permettant d'ajouter un enregistrement dans la table product
	 * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
	 *
	 * @return bool
	 */
	public function insert()
	{
		// Récupération de l'objet PDO représentant la connexion à la DB
		$pdo = Database::getPDO();

		// Ecriture de la requête INSERT INTO
		$sql = "INSERT INTO `product` (name, description, picture, price, rate, status, brand_id, category_id, type_id)
		VALUES (:name, :description, :picture, :price, :rate, :status, :brand_id, :category_id, :type_id)";

		$query = $pdo->prepare($sql);

		$query->bindValue(':name', $this->name);
		$query->bindValue(':description', $this->description);
		$query->bindValue(':picture', $this->picture);
		$query->bindValue(':price', $this->price);
		$query->bindValue(':rate', $this->rate);
		$query->bindValue(':status', $this->status);
		// $query->bindValue(':created_at', $this->created_at);
		// $query->bindValue(':updated_at', $this->updated_at);
		$query->bindValue(':brand_id', $this->brand_id);
		$query->bindValue(':category_id', $this->category_id);
		$query->bindValue(':type_id', $this->type_id);

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
		// Récupération de l'objet PDO représentant la connexion à la DB
		$pdo = Database::getPDO();

		// Ecriture de la requête UPDATE
		$sql = "
		    UPDATE `product`
		    SET
			  name = :name,
			  description = :description,
			  picture = :picture,
			  price = :price,
			  rate = :rate,
			  status = :status,
			  brand_id = :brand_id,
			  category_id = :category_id,
			  type_id = :type_id,
			  updated_at = NOW()
		    WHERE id = :id
		";

		// on indique à PDO que l'on une requête préparée
		$query = $pdo->prepare($sql);

		// exécution de la requête avec les valeurs de l'objet
		$updatedRows =  $query->execute([
			':name' => $this->name,
			':description' => $this->description,
			':picture' => $this->picture,
			':price' => $this->price,
			':rate' => $this->rate,
			':status' => $this->status,
			':brand_id' => $this->brand_id,
			':category_id' => $this->category_id,
			':type_id' => $this->type_id,
			':id' => $this->id
		]);

		// On retourne VRAI, si au moins une ligne ajoutée
		return ($updatedRows > 0);
	}

	/**
	 * Remove a product from database
	 */
	public function delete()
	{
		// Récupération de l'objet PDO représentant la connexion à la DB
		$pdo = Database::getPDO();

		// Déclaration de la requête
		$sql = 'DELETE FROM `product` WHERE id = :id';

		// Préparation de la requête
		$query = $pdo->prepare($sql);

		// On passe l'id
		$query->bindValue(':id', $this->id, PDO::PARAM_INT);

		$deletedRows = $query->execute();

		// On retourne VRAI, si au moins une ligne supprimée
		return ($deletedRows > 0);
	}

	/**
	 * Enregistre la liste des tags du produit
	 *
	 * @param Tag[] liste des tags à enregistrer
	 * @return bool
	 */
	public function updateTags($tags)
	{
		// on doit avoir un produit enregistré
		// pour sauvegarder les relations Produit - Tags
		if ($this->id) {
			// 1 - on commence par supprimer les relations existantes
			$pdoStatement = Database::getPDO()->prepare('DELETE FROM product_tag WHERE product_id = :product_id');
			$pdoStatement->bindValue(':product_id', $this->id, PDO::PARAM_INT);
			if (!$pdoStatement->execute()) {
				return false;
			}

			// 2 - on crée les nouveaux enregistrements
			foreach ($tags as $tag) {
				$pdoStatement = Database::getPDO()->prepare('INSERT INTO product_tag (tag_id, product_id) VALUES (:tag_id, :product_id)');
				$pdoStatement->bindValue(':tag_id', $tag->getId(), PDO::PARAM_INT);
				$pdoStatement->bindValue(':product_id', $this->id, PDO::PARAM_INT);
				if (!$pdoStatement->execute()) {
					return false;
				}
			}
			return true;
		}

		return false;
	}

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
	 * Get the value of description
	 *
	 * @return  string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Set the value of description
	 *
	 * @param  string  $description
	 */
	public function setDescription(string $description)
	{
		$this->description = $description;
	}

	/**
	 * Get the value of picture
	 *
	 * @return  string
	 */
	public function getPicture()
	{
		return $this->picture;
	}

	/**
	 * Set the value of picture
	 *
	 * @param  string  $picture
	 */
	public function setPicture(string $picture)
	{
		$this->picture = $picture;
	}

	/**
	 * Get the value of price
	 *
	 * @return  float
	 */
	public function getPrice()
	{
		return $this->price;
	}

	/**
	 * Set the value of price
	 *
	 * @param  float  $price
	 */
	public function setPrice(float $price)
	{
		$this->price = $price;
	}

	/**
	 * Get the value of rate
	 *
	 * @return  int
	 */
	public function getRate()
	{
		return $this->rate;
	}

	/**
	 * Set the value of rate
	 *
	 * @param  int  $rate
	 */
	public function setRate(int $rate)
	{
		$this->rate = $rate;
	}

	/**
	 * Get the value of status
	 *
	 * @return  int
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * Set the value of status
	 *
	 * @param  int  $status
	 */
	public function setStatus(int $status)
	{
		$this->status = $status;
	}

	/**
	 * Get the value of brand_id
	 *
	 * @return  int
	 */
	public function getBrandId()
	{
		return $this->brand_id;
	}

	/**
	 * Set the value of brand_id
	 *
	 * @param  int  $brand_id
	 */
	public function setBrandId(int $brand_id)
	{
		$this->brand_id = $brand_id;
	}

	/**
	 * Get the value of category_id
	 *
	 * @return  int
	 */
	public function getCategoryId()
	{
		return $this->category_id;
	}

	/**
	 * Set the value of category_id
	 *
	 * @param  int  $category_id
	 */
	public function setCategoryId(int $category_id)
	{
		$this->category_id = $category_id;
	}

	/**
	 * Get the value of type_id
	 *
	 * @return  int
	 */
	public function getTypeId()
	{
		return $this->type_id;
	}

	/**
	 * Set the value of type_id
	 *
	 * @param  int  $type_id
	 */
	public function setTypeId(int $type_id)
	{
		$this->type_id = $type_id;
	}
}
