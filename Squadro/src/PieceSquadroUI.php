<?php

namespace Squadro;

use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;

/**
 * Class PieceSquadroUI
 *
 * Fournit des méthodes statiques pour générer l'interface utilisateur du plateau de jeu Squadro.
 * Ces méthodes génèrent le HTML pour :
 * - Les cases vides, neutres et rouges (pour les vitesses)
 * - Les pièces de jeu (blanches ou noires) avec des formulaires pour les déplacer
 * - Le plateau complet de jeu, incluant les lignes de vitesses
 *
 * @package Squadro
 */
class PieceSquadroUI
{
    /**
     * Génère le HTML d'une case vide.
     *
     * @return string Le HTML de la case vide.
     */
    public static function generationCaseVide(): string
    {
        // Fond sombre et dégradé pour une case vide
        return '<button type="button" class="h-12 w-12 border border-gray-300 bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-400 rounded-lg" disabled></button>';
    }

    /**
     * Génère le HTML d'une case neutre.
     *
     * @return string Le HTML de la case neutre.
     */
    public static function generationCaseNeutre(): string
    {
        return '<button type="button" class="h-12 w-12 border border-gray-300 bg-gradient-to-r from-indigo-400 via-purple-400 to-pink-300 rounded-lg" disabled></button>';
    }

    /**
     * Génère le HTML d'une case rouge affichant une valeur.
     *
     * @param int $valeur La valeur à afficher.
     * @return string Le HTML de la case rouge.
     */
    public static function generationCaseRouge(int $valeur): string
    {
        return '<div class="h-12 w-12 flex items-center justify-center border border-red-600 bg-red-500 text-white font-bold rounded-lg">' . $valeur . '</div>';
    }

    /**
     * Génère le HTML pour une pièce jouable ou non.
     *
     * Si la pièce appartient au joueur actif et que la case d'arrivée est libre,
     * un formulaire permettant de la sélectionner est généré. Sinon, un bouton désactivé est retourné.
     *
     * @param PieceSquadro $piece La pièce à afficher.
     * @param int $ligne La position ligne de la pièce.
     * @param int $colonne La position colonne de la pièce.
     * @param bool $estActif True si la pièce est jouable par le joueur actif.
     * @param PlateauSquadro $plateau Le plateau de jeu actuel.
     * @return string Le HTML généré pour la pièce.
     */
    public static function generationPiece(PieceSquadro $piece, int $ligne, int $colonne, bool $estActif, PlateauSquadro $plateau): string
    {
        $couleur = ($piece->getCouleur() === PieceSquadro::BLANC) ? 'bg-white border-black' : 'bg-black border-white';

        // Obtenir les coordonnées de destination
        [$newX, $newY] = $plateau->getCoordDestination($ligne, $colonne);
        $caseDestination = $plateau->getPiece($newX, $newY);

        // Cas où la pièce appartient à l'adversaire
        if (!$estActif) {
            return self::genererBoutonBloque($couleur, "Cette pièce appartient à l'adversaire.");
        }

        // Cas où la case d'arrivée est occupée, empêchant le déplacement
        if ($caseDestination->getCouleur() !== PieceSquadro::VIDE) {
            return self::genererBoutonBloque($couleur, "Case d'arrivée occupée, déplacement impossible.");
        }

        // Si la pièce est jouable, générer le formulaire de déplacement
        return '
        <form action="traiteActionSquadro.php" method="POST">
          <input type="hidden" name="ligne" value="' . $ligne . '">
          <input type="hidden" name="colonne" value="' . $colonne . '">
          <input type="hidden" name="action" value="ChoisirPiece">
          <button class="h-12 w-12 border rounded-full shadow-md ' . $couleur . '" type="submit">
          </button>
      </form>';
    }

    /**
     * Génère un bouton désactivé avec un message d'information.
     *
     * @param string $couleur La classe CSS définissant la couleur du bouton.
     * @param string $message Le message d'information (non affiché dans le HTML généré).
     * @return string Le HTML du bouton désactivé.
     */
    private static function genererBoutonBloque(string $couleur, string $message): string
    {
        return '<button class="h-12 w-12 border rounded-full shadow-md ' . $couleur . ' cursor-not-allowed" type="button" disabled>
        </button>';
    }

    /**
     * Génère le HTML complet du plateau de jeu.
     *
     * Affiche les lignes de vitesses et le plateau de jeu contenant les pièces.
     *
     * @param PlateauSquadro $plateau Le plateau de jeu.
     * @param int $joueurActif La couleur du joueur actif.
     * @return string Le HTML complet du plateau.
     */
    public static function generatePlateau(PlateauSquadro $plateau, int $joueurActif): string
    {
        $vitessesBlanchesRetour = [1, 3, 2, 3, 1];
        $vitessesBlanchesAller   = [3, 1, 2, 1, 3];
        $vitessesNoiresAller     = [3, 1, 2, 1, 3];
        $vitessesNoiresRetour    = [1, 3, 2, 3, 1];

        // Conteneur global pour le plateau stylisé
        $html = '<div class="flex justify-center items-center p-4">
                    <table class="border-collapse bg-gradient-to-br from-gray-800 to-gray-900 rounded-xl shadow-2xl overflow-hidden animate-scale-up">';

        // Ligne supérieure : vitesses de retour des noirs
        $html .= '<tr class="animate-fade-in-up delay-100">
                    <td class="p-2"></td>
                    <td class="p-2"></td>';
        foreach ($vitessesNoiresRetour as $valeur) {
            $html .= '<td class="p-2">
                        <div class="h-16 w-16 flex items-center justify-center rounded-full bg-red-500 text-white font-bold shadow-lg transform hover:scale-110 transition-all duration-200">
                            ' . $valeur . '
                        </div>
                      </td>';
        }
        $html .= '</tr>';

        // Lignes centrales du plateau
        for ($ligne = 0; $ligne < 7; $ligne++) {
            $html .= '<tr class="animate-fade-in-up delay-200">';

            // Colonne de gauche : vitesses de retour des blancs
            if ($ligne === 0) {
                $html .= '<td class="p-2"></td>';
            } elseif ($ligne >= 1 && $ligne <= 5) {
                $html .= '<td class="p-2">
                            <div class="h-16 w-16 flex items-center justify-center rounded-full bg-red-500 text-white font-bold shadow-lg transform hover:scale-110 transition-all duration-200">
                                ' . $vitessesBlanchesRetour[$ligne - 1] . '
                            </div>
                          </td>';
            } else {
                $html .= '<td class="p-2"></td>';
            }

            // Plateau de jeu : génération des cases et pièces
            for ($colonne = 0; $colonne < 7; $colonne++) {
                $piece = $plateau->getPiece($ligne, $colonne);
                $html .= '<td class="p-2">';
                if ($piece->getCouleur() === PieceSquadro::VIDE) {
                    $html .= self::generationCaseVide();
                } elseif ($piece->getCouleur() === PieceSquadro::NEUTRE) {
                    $html .= self::generationCaseNeutre();
                } else {
                    $isActive = ($piece->getCouleur() === $joueurActif);
                    $html .= self::generationPiece($piece, $ligne, $colonne, $isActive, $plateau);
                }
                $html .= '</td>';
            }

            // Colonne de droite : vitesses d'aller des blancs
            if ($ligne === 0) {
                $html .= '<td class="p-2"></td>';
            } elseif ($ligne >= 1 && $ligne <= 5) {
                $html .= '<td class="p-2">
                            <div class="h-16 w-16 flex items-center justify-center rounded-full bg-red-500 text-white font-bold shadow-lg transform hover:scale-110 transition-all duration-200">
                                ' . $vitessesBlanchesAller[$ligne - 1] . '
                            </div>
                          </td>';
            } else {
                $html .= '<td class="p-2"></td>';
            }

            $html .= '</tr>';
        }

        // Ligne inférieure : vitesses d'aller des noirs
        $html .= '<tr class="animate-fade-in-up delay-300">
                    <td class="p-2"></td>
                    <td class="p-2"></td>';
        foreach ($vitessesNoiresAller as $valeur) {
            $html .= '<td class="p-2">
                        <div class="h-16 w-16 flex items-center justify-center rounded-full bg-red-500 text-white font-bold shadow-lg transform hover:scale-110 transition-all duration-200">
                            ' . $valeur . '
                        </div>
                      </td>';
        }
        $html .= '</tr>';

        $html .= '</table></div>';
        return $html;
    }
}
