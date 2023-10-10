<?php

namespace App\Models;

use App\Utils\Database;

use PDO;

class Tag extends CoreModel
{

	private $name;

	public static function find($id)
	{
	}

	/**
	 * Retourne la liste des tags.
	 *
	 * @return Tag[]
	 */
	public static function findAll()
	{
		$pdo = Database::getPDO();
		$sql = 'SELECT * FROM `tag`';
		$pdoStatement = $pdo->query($sql);
		$results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

		return $results;
	}

	/**
	 * Retourne la liste des tags pour un produit.
	 *
	 * @param int $productId Identifiant du produit.
	 * @return Tag[]
	 */
	public static function findByProductId($productId)
	{
		$sql = 'SELECT tag.* FROM tag
				INNER JOIN product_tag ON tag.id = product_tag.tag_id
				WHERE product_tag.product_id = :product_id';

		$pdoStatement = Database::getPDO()->prepare($sql);
		$pdoStatement->bindValue(':product_id', $productId);
		$pdoStatement->execute();

		return $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
	}

	/**
	 * Retourne une liste d'instances de Tag à partir d'une liste d'identifiants.
	 * 
	 * @param array $ids Liste d'identifiants
	 * @return Tag[]
	 */
	public static function findByIds($ids)
	{
		if (empty($ids)) {
			return [];
		}

		// préparation des paramètres
		// on parcours le tableau des identifiants pour créer un tableau de la forme
		// [ ':nom_parametre_index' => identifiant]
		$paramsIds = [];
		foreach ($ids as $index => $value) {
			$paramsIds[':id_' . $index] = $value;
		}

		// dump($ids);

		// construction de la requête avec les clés du tableau
		// implode concatène toutes les valeurs du tableau en une chaîne de caractères avec comme séparateur ','
		// @see https://www.php.net/manual/fr/function.implode.php
		$sql = 'SELECT tag.* FROM tag
			WHERE tag.id IN (' . implode(',', array_keys($paramsIds)) . ')';

		// dump($sql, $paramsIds);

		$pdoStatement = Database::getPDO()->prepare($sql);
		$pdoStatement->execute($paramsIds); // remplace tous les :id_XXX par les valeurs des id à rechercher

		return $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);
	}

	public function insert()
	{
	}
	public function update()
	{
	}
	public function delete()
	{
	}

	/**
	 * Get the value of name
	 *
	 * @return mixed
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the value of name
	 *
	 * @param   mixed  $name  
	 * @return  self
	 */
	public function setName($name)
	{
		$this->name = $name;

		return $this;
	}
}
