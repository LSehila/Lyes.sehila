<?php

namespace Squadro;

use Squadro\PieceSquadro;

/**
 * Class PlateauSquadro
 *
 * Représente le plateau de jeu de Squadro, constitué d'une grille 7x7.
 * Ce plateau est initialisé avec des cases vides, des cases neutres aux coins,
 * des pièces blanches sur la première colonne (hors coins) et des pièces noires sur la dernière ligne (hors coins).
 * Des méthodes permettent de récupérer et modifier des pièces, de calculer les coordonnées de destination,
 * et de convertir le plateau en JSON.
 *
 * @package Squadro
 */
class PlateauSquadro {
    // Constantes pour les vitesses de déplacement
    public const BLANC_V_ALLER = [0, 1, 3, 2, 3, 1, 0];
    public const BLANC_V_RETOUR = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_ALLER = [0, 3, 1, 2, 1, 3, 0];
    public const NOIR_V_RETOUR = [0, 1, 3, 2, 3, 1, 0];

    /**
     * @var array Le plateau de jeu sous forme de tableau 2D (7x7) de PieceSquadro.
     */
    private array $plateau = [];

    /**
     * @var array Indices des lignes jouables (pour les pièces blanches).
     */
    private array $lignesJouables = [1, 2, 3, 4, 5];

    /**
     * @var array Indices des colonnes jouables (pour les pièces noires).
     */
    private array $colonnesJouables = [1, 2, 3, 4, 5];

    /**
     * Constructeur.
     *
     * Initialise le plateau de jeu en appelant les méthodes d'initialisation des cases.
     */
    public function __construct() {
        $this->initPlateau();
    }

    /**
     * Initialise l'ensemble du plateau en appelant les méthodes spécifiques.
     *
     * @return void
     */
    private function initPlateau(): void {
        $this->initCasesVides();
        $this->initCasesNeutres();
        $this->initCasesBlanches();
        $this->initCasesNoires();
    }

    /**
     * Initialise toutes les cases vides du plateau.
     *
     * @return void
     */
    private function initCasesVides(): void {
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $this->plateau[$i][$j] = PieceSquadro::initVide();
            }
        }
    }

    /**
     * Initialise les cases neutres (coins) du plateau.
     *
     * @return void
     */
    private function initCasesNeutres(): void {
        $this->plateau[0][0] = PieceSquadro::initNeutre();
        $this->plateau[0][6] = PieceSquadro::initNeutre();
        $this->plateau[6][0] = PieceSquadro::initNeutre();
        $this->plateau[6][6] = PieceSquadro::initNeutre();
    }

    /**
     * Initialise les cases contenant les pièces blanches.
     *
     * @return void
     */
    private function initCasesBlanches(): void {
        for ($j = 1; $j <= 5; $j++) {
            $this->plateau[$j][0] = PieceSquadro::initBlancEst();
        }
    }

    /**
     * Initialise les cases contenant les pièces noires.
     *
     * @return void
     */
    private function initCasesNoires(): void {
        for ($i = 1; $i <= 5; $i++) {
            $this->plateau[6][$i] = PieceSquadro::initNoirNord();
        }
    }

    /**
     * Retourne le plateau de jeu sous forme de tableau 2D.
     *
     * @return array Le plateau de jeu.
     */
    public function getPlateau(): array {
        return $this->plateau;
    }

    /**
     * Récupère la pièce se trouvant aux coordonnées ($x, $y).
     *
     * @param int $x La ligne de la pièce.
     * @param int $y La colonne de la pièce.
     * @return PieceSquadro La pièce à ces coordonnées.
     *
     * @throws \OutOfBoundsException Si les coordonnées sont hors du plateau.
     */
    public function getPiece(int $x, int $y): PieceSquadro {
        if ($x < 0 || $x >= 7 || $y < 0 || $y >= 7) {
            throw new \OutOfBoundsException("Coordonnées invalides ($x, $y)");
        }
        return $this->plateau[$x][$y];
    }

    /**
     * Place une pièce à la position spécifiée sur le plateau.
     *
     * @param int $x La ligne.
     * @param int $y La colonne.
     * @param PieceSquadro $piece La pièce à placer.
     * @return void
     *
     * @throws \OutOfBoundsException Si les coordonnées sont hors du plateau.
     */
    public function setPiece(int $x, int $y, PieceSquadro $piece): void {
        if ($x < 0 || $x >= 7 || $y < 0 || $y >= 7) {
            throw new \OutOfBoundsException("Coordonnées invalides ($x, $y)");
        }
        $this->plateau[$x][$y] = $piece;
    }

    /**
     * Retourne les indices des lignes jouables pour les pièces blanches.
     *
     * @return array Les indices des lignes jouables.
     */
    public function getLignesJouables(): array {
        return $this->lignesJouables;
    }

    /**
     * Retourne les indices des colonnes jouables pour les pièces noires.
     *
     * @return array Les indices des colonnes jouables.
     */
    public function getColonnesJouables(): array {
        return $this->colonnesJouables;
    }

    /**
     * Supprime un indice de ligne jouable.
     *
     * @param int $index L'indice de la ligne à retirer.
     * @return void
     */
    public function retireLigneJouable(int $index): void {
        $this->lignesJouables = array_values(array_diff($this->lignesJouables, [$index]));
    }

    /**
     * Supprime un indice de colonne jouable.
     *
     * @param int $index L'indice de la colonne à retirer.
     * @return void
     */
    public function retireColonneJouable(int $index): void {
        $this->colonnesJouables = array_values(array_diff($this->colonnesJouables, [$index]));
    }

    /**
     * Calcule les coordonnées de destination d'une pièce se trouvant à ($x, $y).
     *
     * Pour une pièce blanche, le mouvement est horizontal, et pour une pièce noire, le mouvement est vertical.
     *
     * @param int $x La ligne de départ.
     * @param int $y La colonne de départ.
     * @return array Un tableau contenant les nouvelles coordonnées [newX, newY].
     *
     * @throws \OutOfBoundsException Si le mouvement calculé est hors des limites du plateau.
     */
    public function getCoordDestination(int $x, int $y): array {
        $piece = $this->getPiece($x, $y);
        if ($piece->getCouleur() === PieceSquadro::BLANC) {
            // Pièce blanche (mouvement horizontal)
            if ($piece->getDirection() === PieceSquadro::EST) {
                $vitesse = self::BLANC_V_ALLER[$x];
                $newX = $x;
                $newY = $y + $vitesse;
            } else {
                $vitesse = self::BLANC_V_RETOUR[$x];
                $newX = $x;
                $newY = $y - $vitesse;
            }
        } else {
            // Pièce noire (mouvement vertical)
            if ($piece->getDirection() === PieceSquadro::NORD) {
                $vitesse = self::NOIR_V_ALLER[$y];
                $newX = $x - $vitesse;
                $newY = $y;
            } else {
                $vitesse = self::NOIR_V_RETOUR[$y];
                $newX = $x + $vitesse;
                $newY = $y;
            }
        }

        // Vérification des limites
        if ($newX < 0 || $newX >= 7 || $newY < 0 || $newY >= 7) {
            throw new \OutOfBoundsException("Mouvement hors limites ($newX, $newY)");
        }

        return [$newX, $newY];
    }

    /**
     * Retourne une représentation JSON du plateau.
     *
     * @return string Le plateau converti en JSON.
     */
    public function toJson(): string {
        return json_encode($this->plateau);
    }

    /**
     * Crée un objet PlateauSquadro à partir d'une chaîne JSON.
     *
     * @param string $json La chaîne JSON représentant le plateau.
     * @return self Un nouvel objet PlateauSquadro.
     *
     * @throws \InvalidArgumentException Si le format JSON est invalide.
     */
    public static function fromJson(string $json): self {
        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new \InvalidArgumentException("Format JSON invalide");
        }
        $plateau = new self();
        foreach ($data as $i => $row) {
            foreach ($row as $j => $pieceData) {
                $plateau->setPiece($i, $j, PieceSquadro::fromJson(json_encode($pieceData)));
            }
        }
        return $plateau;
    }

    /**
     * Retourne une représentation textuelle du plateau.
     *
     * @return string Le plateau sous forme de chaîne de caractères.
     */
    public function __toString(): string {
        $result = "PlateauSquadro :\n";
        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                $piece = $this->plateau[$i][$j];
                $result .= match ($piece->getCouleur()) {
                    PieceSquadro::BLANC => " B ",
                    PieceSquadro::NOIR => " N ",
                    PieceSquadro::NEUTRE => " X ",
                    default => " . ",
                };
            }
            $result .= "\n";
        }
        return $result;
    }
}
