<?php

namespace Squadro;

use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;

/**
 * Class ActionSquadro
 *
 * Gère les actions de déplacement dans le jeu Squadro.
 * Cette classe met à jour l'état du plateau, gère les collisions,
 * effectue le déplacement des pièces, et vérifie les conditions de victoire.
 *
 * @package Squadro
 */
class ActionSquadro
{
    /**
     * @var PlateauSquadro Le plateau de jeu.
     */
    private PlateauSquadro $plateau;

    /**
     * @var int Nombre de pièces blanches sorties.
     */
    private int $countBlancSortie = 0;

    /**
     * @var int Nombre de pièces noires sorties.
     */
    private int $countNoirSortie = 0;

    /**
     * @var bool Indique si la partie est terminée (victoire).
     */
    private bool $partieTerminee = false;

    /**
     * @var array Historique des positions de déplacement.
     */
    private array $historiquePositions = [];

    /**
     * Constructeur.
     *
     * Initialise l'action en liant le plateau passé en paramètre.
     * Initialise les compteurs dans la session si non définis.
     *
     * @param PlateauSquadro $plateau Le plateau de jeu.
     */
    public function __construct(PlateauSquadro $plateau)
    {
        $this->plateau = $plateau;
        if (!isset($_SESSION['countNoirSortie'])) {
            $_SESSION['countNoirSortie'] = $this->countNoirSortie;
        }
        if (!isset($_SESSION['countBlancSortie'])) {
            $_SESSION['countBlancSortie'] = $this->countBlancSortie;
        }
    }

    /**
     * Vérifie si la pièce à la position donnée est jouable pour le joueur actif.
     *
     * @param int $x Position X de la pièce.
     * @param int $y Position Y de la pièce.
     * @param int $joueurActif Couleur du joueur actif.
     * @return bool True si la pièce est jouable, false sinon.
     */
    public function estJouablePiece(int $x, int $y, int $joueurActif): bool
    {
        return !$this->partieTerminee && $this->plateau->getPiece($x, $y)->getCouleur() === $joueurActif;
    }

    /**
     * Joue la pièce à la position ($x, $y) pour le joueur actif.
     * Effectue le déplacement, gère les collisions et met à jour l'état du plateau.
     *
     * @param int $x Position X de la pièce à déplacer.
     * @param int $y Position Y de la pièce à déplacer.
     * @param int $joueurActif Couleur du joueur actif.
     * @return void
     * @throws \InvalidArgumentException Si la pièce ne vous appartient pas.
     * @throws \OutOfBoundsException Si le mouvement est hors limites.
     */
    public function jouerPiece(int $x, int $y, int $joueurActif): void
    {
        if ($this->partieTerminee) {
            return; // Empêche tout déplacement après victoire
        }

        $piece = $this->plateau->getPiece($x, $y);
        if ($piece->getCouleur() !== $joueurActif) {
            throw new \InvalidArgumentException("Cette pièce ne vous appartient pas !");
        }

        [$newX, $newY] = $this->plateau->getCoordDestination($x, $y);

        if ($newX < 0 || $newX >= 7 || $newY < 0 || $newY >= 7) {
            throw new \OutOfBoundsException("Mouvement hors limites !");
        }

        // Vérifie si la case d'arrivée est libre
        if ($this->plateau->getPiece($newX, $newY)->getCouleur() !== PieceSquadro::VIDE) {
            return; // Empêche le déplacement si la case est occupée
        }

        $this->gererCollisionsSurTrajet($x, $y, $newX, $newY, $piece);
        $this->gererCollisionsMultiples($newX, $newY, $piece);
        $this->historiquePositions["$newX-$newY"][] = ["x" => $x, "y" => $y];

        $this->plateau->setPiece($newX, $newY, $piece);
        $this->plateau->setPiece($x, $y, PieceSquadro::initVide());

        if ($this->aAtteintZoneRetournement($newX, $newY, $piece)) {
            $piece->inverseDirection();
        }

        if ($this->aTermineAllerRetour($newX, $newY)) {
            $this->sortirPiece($piece->getCouleur(), $newX, $newY);
        }

        // Vérification de victoire après chaque coup
        if ($this->remporteVictoire()) {
            $this->afficherMessageVictoire();
        }
    }

    /**
     * Vérifie si la pièce a atteint la zone de retournement.
     *
     * @param int $x Nouvelle position X.
     * @param int $y Nouvelle position Y.
     * @param PieceSquadro $piece La pièce concernée.
     * @return bool True si la zone est atteinte, false sinon.
     */
    private function aAtteintZoneRetournement(int $x, int $y, PieceSquadro $piece): bool
    {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $y === 6) ||
               ($piece->getCouleur() === PieceSquadro::NOIR && $x === 0);
    }

    /**
     * Vérifie si la pièce a terminé son aller-retour.
     *
     * @param int $x Position X finale.
     * @param int $y Position Y finale.
     * @return bool True si la condition d'aller-retour est remplie, false sinon.
     */
    public function aTermineAllerRetour(int $x, int $y): bool
    {
        return ($x === 6 && $this->plateau->getPiece($x, $y)->getCouleur() === PieceSquadro::NOIR) ||
               ($y === 0 && $this->plateau->getPiece($x, $y)->getCouleur() === PieceSquadro::BLANC);
    }

    /**
     * Déplace la pièce adverse en reculant, en fonction de la trajectoire de collision.
     *
     * @param int $x Position X de la pièce adverse.
     * @param int $y Position Y de la pièce adverse.
     * @param PieceSquadro $pieceAdverse La pièce adverse à reculer.
     * @return void
     */
    private function gererReculPieceAdverse(int $x, int $y, PieceSquadro $pieceAdverse): void
    {
        if ($this->aDejaEffectueAller($pieceAdverse)) {
            if ($pieceAdverse->getCouleur() === PieceSquadro::BLANC) {
                $this->plateau->setPiece($x, 6, $pieceAdverse);
            } else {
                $this->plateau->setPiece(0, $y, $pieceAdverse);
            }
        } else {
            if ($pieceAdverse->getCouleur() === PieceSquadro::BLANC) {
                $this->plateau->setPiece($x, 0, $pieceAdverse);
            } else {
                $this->plateau->setPiece(6, $y, $pieceAdverse);
            }
        }

        $this->plateau->setPiece($x, $y, PieceSquadro::initVide());
    }

    /**
     * Gère les collisions sur le trajet du déplacement de la pièce.
     *
     * @param int $x Position de départ X.
     * @param int $y Position de départ Y.
     * @param int $newX Position d'arrivée X.
     * @param int $newY Position d'arrivée Y.
     * @param PieceSquadro $piece La pièce à déplacer.
     * @return void
     */
    private function gererCollisionsSurTrajet(int $x, int $y, int $newX, int $newY, PieceSquadro $piece): void
    {
        $couleur = $piece->getCouleur();

        if ($couleur === PieceSquadro::BLANC) {
            for ($col = min($y, $newY) + 1; $col <= max($y, $newY); $col++) {
                $pieceAdverse = $this->plateau->getPiece($x, $col);
                if ($pieceAdverse->getCouleur() === PieceSquadro::NOIR) {
                    $this->gererReculPieceAdverse($x, $col, $pieceAdverse);
                }
            }
        } else {
            for ($row = min($x, $newX) + 1; $row <= max($x, $newX); $row++) {
                $pieceAdverse = $this->plateau->getPiece($row, $y);
                if ($pieceAdverse->getCouleur() === PieceSquadro::BLANC) {
                    $this->gererReculPieceAdverse($row, $y, $pieceAdverse);
                }
            }
        }
    }

    /**
     * Gère les collisions multiples sur la case d'arrivée.
     *
     * @param int $x Position X de la case.
     * @param int $y Position Y de la case.
     * @param PieceSquadro $piece La pièce à déplacer.
     * @return void
     */
    private function gererCollisionsMultiples(int $x, int $y, PieceSquadro $piece): void
    {
        $pieceSurCase = $this->plateau->getPiece($x, $y);

        if ($pieceSurCase->getCouleur() !== PieceSquadro::VIDE && $pieceSurCase->getCouleur() !== $piece->getCouleur()) {
            $this->gererReculPieceAdverse($x, $y, $pieceSurCase);
            $this->plateau->setPiece($x, $y, $piece);
        }
    }

    /**
     * Vérifie si la pièce adverse a déjà effectué son aller.
     *
     * @param PieceSquadro $piece La pièce adverse.
     * @return bool True si l'aller a déjà été effectué, false sinon.
     */
    private function aDejaEffectueAller(PieceSquadro $piece): bool
    {
        return ($piece->getCouleur() === PieceSquadro::BLANC && $piece->getDirection() === PieceSquadro::OUEST) ||
               ($piece->getCouleur() === PieceSquadro::NOIR && $piece->getDirection() === PieceSquadro::SUD);
    }

    /**
     * Gère la sortie d'une pièce (lorsqu'elle a terminé son aller-retour).
     * Met à jour le compteur correspondant dans la session.
     *
     * @param int $couleur La couleur de la pièce.
     * @param int $x Position X de la pièce.
     * @param int $y Position Y de la pièce.
     * @return void
     */
    public function sortirPiece(int $couleur, int $x, int $y): void
    {
        $this->plateau->setPiece($x, $y, PieceSquadro::initVide());

        if ($couleur === PieceSquadro::BLANC) {
            if (isset($_SESSION['countBlancSortie'])) {
                $_SESSION['countBlancSortie']++;
            } else {
                $_SESSION['countBlancSortie'] = $this->countBlancSortie;
            }
        } else {
            if (isset($_SESSION['countNoirSortie'])) {
                $_SESSION['countNoirSortie']++;
            } else {
                $_SESSION['countNoirSortie'] = $this->countNoirSortie;
            }
        }
    }

    /**
     * Vérifie si les conditions de victoire sont remplies.
     *
     * @return bool True si l'une des conditions est remplie, false sinon.
     */
    public function remporteVictoire(): bool
    {
        return $_SESSION['countBlancSortie'] >= 4 || $_SESSION['countNoirSortie'] >= 4;
    }

    /**
     * Affiche le message de victoire et termine l'exécution.
     *
     * @return void
     */
    private function afficherMessageVictoire(): void
    {
        $gagnant = ($_SESSION['countBlancSortie'] >= 4) ? "Blancs" : "Noirs";
        $text = <<<TEXT
                   <script src="https://cdn.tailwindcss.com"></script>
                    <div class="flex items-center justify-center min-h-screen bg-cover bg-center bg-no-repeat">
                        <div class="max-w-md mx-auto text-center bg-white bg-opacity-60 p-8 rounded-lg shadow-lg">
                            <div class="text-9xl font-bold text-indigo-600 mb-4">YOU WIN</div>
                            <h1 class="text-4xl font-bold text-gray-800 mb-6">Le gagnant du jour est $gagnant</h1>
                            <a href="reset.php" class="inline-block bg-indigo-600 text-white font-semibold px-6 py-3 rounded-md hover:bg-indigo-700 transition-colors duration-300">Autre partie</a>
                        </div>
                    </div>
TEXT;
        echo $text;
        $this->partieTerminee = true;
        exit();
    }
}
