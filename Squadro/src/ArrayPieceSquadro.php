<?php

namespace Squadro;

use ArrayAccess;
use Countable;
use InvalidArgumentException;

/**
 * Class ArrayPieceSquadro
 *
 * Représente un conteneur pour stocker des objets de type PieceSquadro.
 * Implémente les interfaces Countable et ArrayAccess pour permettre
 * un accès type tableau et le comptage des éléments.
 *
 * @package Squadro
 */
class ArrayPieceSquadro implements Countable, ArrayAccess
{
    /**
     * @var array Liste des pièces (instances de PieceSquadro)
     */
    private array $pieces;

    /**
     * Constructeur.
     *
     * Initialise le conteneur avec un tableau optionnel de pièces.
     *
     * @param array $pieces Tableau de pièces initial (optionnel)
     */
    public function __construct(array $pieces = [])
    {
        $this->pieces = $pieces;
    }

    /**
     * Retourne le nombre de pièces dans le conteneur.
     *
     * @return int Le nombre d'éléments
     */
    public function count(): int
    {
        return count($this->pieces);
    }

    /**
     * Vérifie si l'indice donné existe dans le tableau.
     *
     * @param mixed $offset L'indice à vérifier
     * @return bool True si l'indice existe, false sinon.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->pieces[$offset]);
    }

    /**
     * Retourne la pièce à l'indice donné.
     *
     * @param mixed $offset L'indice de la pièce
     * @return mixed La pièce à l'indice ou null si non défini.
     */
    public function offsetGet($offset): mixed
    {
        return $this->pieces[$offset] ?? null;
    }

    /**
     * Définit ou ajoute une pièce dans le conteneur.
     *
     * @param mixed $offset L'indice où placer la pièce (si null, ajoute en fin de tableau)
     * @param mixed $value La pièce (doit être une instance de PieceSquadro)
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if ($offset === null) {
            $this->pieces[] = $value;
        } else {
            $this->pieces[$offset] = $value;
        }
    }

    /**
     * Supprime la pièce à l'indice donné.
     *
     * @param mixed $offset L'indice de la pièce à supprimer
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->pieces[$offset]);
    }

    /**
     * Ajoute une pièce au conteneur.
     *
     * @param PieceSquadro $piece La pièce à ajouter.
     * @return void
     */
    public function add(PieceSquadro $piece): void
    {
        $this->pieces[] = $piece;
    }

    /**
     * Supprime la pièce à l'indice donné et réindexe le tableau.
     *
     * @param int $index L'indice de la pièce à supprimer.
     * @return void
     */
    public function remove(int $index): void
    {
        if (isset($this->pieces[$index])) {
            unset($this->pieces[$index]);
            $this->pieces = array_values($this->pieces); // Réindexer le tableau
        }
    }

    /**
     * Retourne une représentation textuelle du conteneur.
     *
     * @return string La chaîne représentant les pièces contenues.
     */
    public function __toString(): string
    {
        return "ArrayPieceSquadro{" . implode(", ", array_map(fn($piece) => $piece->__toString(), $this->pieces)) . "}";
    }

    /**
     * Convertit le conteneur en JSON.
     *
     * @return string La représentation JSON des pièces.
     */
    public function toJson(): string
    {
        $piecesData = array_map(function ($piece) {
            return [
                'couleur' => $piece->getCouleur(),
                'direction' => $piece->getDirection()
            ];
        }, $this->pieces);

        return json_encode($piecesData);
    }

    /**
     * Crée un conteneur ArrayPieceSquadro à partir d'une chaîne JSON.
     *
     * @param string $json La chaîne JSON représentant les pièces.
     * @return ArrayPieceSquadro Le conteneur reconstruit.
     * @throws InvalidArgumentException Si le JSON est invalide ou incomplet.
     */
    public static function fromJson(string $json): ArrayPieceSquadro
    {
        $piecesData = json_decode($json, true); // Décoder en tableau associatif

        if (!is_array($piecesData)) {
            throw new InvalidArgumentException("Données JSON invalides");
        }

        $pieces = [];
        foreach ($piecesData as $pieceData) {
            if (!isset($pieceData['couleur'], $pieceData['direction'])) {
                throw new InvalidArgumentException("Données JSON invalides");
            }
            $pieces[] = PieceSquadro::fromJson(json_encode($pieceData)); // Encoder en JSON avant de passer à fromJson
        }

        return new ArrayPieceSquadro($pieces);
    }
}
