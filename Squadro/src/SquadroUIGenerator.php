<?php

namespace Squadro;

use Squadro\PieceSquadro;
use Squadro\PieceSquadroUI;
use Squadro\PlateauSquadro;

/**
 * Class SquadroUIGenerator
 *
 * Génère les différentes pages et composants HTML de l'interface utilisateur du jeu Squadro.
 * Cette classe fournit des méthodes statiques pour générer :
 * - L'en-tête et le pied de page de chaque page.
 * - La page de jeu, la page de confirmation de déplacement, la page de victoire et la page d'erreur.
 *
 * @package Squadro
 */
class SquadroUIGenerator
{
    /**
     * Génère l'en-tête HTML récurrent de la page.
     *
     * @param string $title Le titre de la page.
     * @return string Le code HTML de l'en-tête.
     */
    public static function genererEntete(string $title): string
    {
        return '<!DOCTYPE html>
     <html lang="fr">
     <head>
         <meta charset="UTF-8">
         <meta name="viewport" content="width=device-width, initial-scale=1.0">
         <title>' . htmlspecialchars($title) . '</title>
         <!-- Importation de Google Fonts pour un style moderne -->
         <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
         <!-- Tailwind CSS (pour bénéficier de quelques utilitaires) -->
         <script src="https://cdn.tailwindcss.com"></script>
         <style>
             :root {
                 --primary-color: #0f2027;
                 --secondary-color: #203a43;
                 --tertiary-color: #2c5364;
                 --accent-color: #ff7f50;
                 --accent-hover: #ff9060;
                 --bg-gradient: linear-gradient(135deg, var(--primary-color), var(--secondary-color), var(--tertiary-color));
             }

             /* Style global */
             body {
                 font-family: "Roboto", sans-serif;
                 background: var(--bg-gradient);
                 color: #f5f5f5;
                 margin: 0;
                 padding: 0;
                 overflow-x: hidden;
             }

             .container {
                 max-width: 1200px;
                 margin: 0 auto;
                 padding: 20px;
             }

             /* Animations */
             @keyframes fadeInUp {
                 from { opacity: 0; transform: translateY(20px); }
                 to { opacity: 1; transform: translateY(0); }
             }
             .animate-fade-in-up {
                 animation: fadeInUp 0.6s ease-out forwards;
             }

             @keyframes scaleUp {
                 from { transform: scale(0.95); opacity: 0; }
                 to { transform: scale(1); opacity: 1; }
             }
             .animate-scale-up {
                 animation: scaleUp 0.6s ease-out forwards;
             }

             /* Style du plateau */
             table {
                 width: 100%;
                 border-collapse: collapse;
                 background: rgba(255, 255, 255, 0.1);
                 backdrop-filter: blur(8px);
                 border-radius: 12px;
                 box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
                 margin: 20px 0;
                 animation: scaleUp 0.6s ease-out forwards;
             }
             td {
                 padding: 10px;
                 text-align: center;
             }

             /* Boutons */
             button {
                 background-color: var(--accent-color);
                 border: none;
                 color: #fff;
                 padding: 12px 24px;
                 font-size: 1rem;
                 border-radius: 8px;
                 cursor: pointer;
                 transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
             }
             button:hover:not([disabled]) {
                 transform: scale(1.05);
                 background-color: var(--accent-hover);
                 box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
             }

             /* Titre stylé */
             h1 {
                 font-size: 3rem;
                 font-weight: 700;
                 text-align: center;
                 margin: 20px 0;
                 background: linear-gradient(90deg, var(--accent-color), var(--accent-hover), #ffb07b);
                 -webkit-background-clip: text;
                 -webkit-text-fill-color: transparent;
                 animation: fadeInUp 0.8s ease-out forwards;
             }

             /* Formulaires de confirmation */
             .confirmation-form {
                 background: rgba(255, 255, 255, 0.15);
                 padding: 20px;
                 border-radius: 12px;
                 box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
                 animation: scaleUp 0.6s ease-out forwards;
             }

             /* Cartes pour compteurs */
             .card {
                 background: rgba(255, 255, 255, 0.1);
                 border-radius: 12px;
                 padding: 20px;
                 box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
                 transition: transform 0.3s ease;
             }
             .card:hover {
                 transform: translateY(-5px);
             }

             /* Exemple de bouton interactif */
             .interactive {
                 animation: scaleUp 2s infinite alternate;
             }
         </style>
     </head>
     <body>';
    }

    /**
     * Génère le pied de page HTML récurrent de la page.
     *
     * @return string Le code HTML du pied de page.
     */
    public static function genererPiedDePage(): string
    {
        return '</body></html>';
    }

    /**
     * Génère la page de jeu pour le joueur actif.
     *
     * @param PlateauSquadro $plateau Le plateau de jeu actuel.
     * @param int $joueurActif La couleur du joueur actif.
     * @return string Le HTML de la page de jeu.
     */
    public static function genererPageJouerPiece(PlateauSquadro $plateau, int $joueurActif): string
    {
        return self::genererEntete("Squadro - Jouer une pièce") . '
                <div class="text-center">
                    <h1 class="text-2xl font-bold">Jouer une pièce</h1>
                    <p class="text-lg">C\'est au tour des <span class="font-semibold">' .
            ($joueurActif === PieceSquadro::BLANC ? 'Blancs' : 'Noirs') . '</span> de jouer.</p>
                </div>
                ' . PieceSquadroUI::generatePlateau($plateau, $joueurActif) . '
                ' . self::genererPiedDePage();
    }

    /**
     * Génère la page de confirmation de déplacement.
     *
     * @param int $ligne La ligne de la pièce à déplacer.
     * @param int $colonne La colonne de la pièce à déplacer.
     * @param PlateauSquadro $plateau Le plateau de jeu actuel.
     * @param int $joueurActif La couleur du joueur actif.
     * @return string Le HTML de la page de confirmation.
     */
    public static function genererPageConfirmerDeplacement(int $ligne, int $colonne, PlateauSquadro $plateau, int $joueurActif): string
    {
        return self::genererEntete("Squadro - Confirmer déplacement") . '
     <div class="min-h-screen flex items-center justify-center p-4">
         <div class="animate-fade-in-up bg-white p-8 rounded-lg shadow-lg w-full max-w-md transform transition-all">
             <h1 class="text-2xl font-bold text-gray-800 mb-4 text-center">Confirmer le déplacement</h1>
             <p class="text-lg text-gray-600 mb-6 text-center">
                 Voulez-vous déplacer la pièce en position
                 <span class="font-semibold text-blue-600">(' . $ligne . ', ' . $colonne . ')</span> ?
             </p>

             <div class="flex justify-center space-x-4">
                 <form method="POST" action="traiteActionSquadro.php" class="flex-1">
                     <input type="hidden" name="ligne" value="' . $ligne . '">
                     <input type="hidden" name="colonne" value="' . $colonne . '">
                     <input type="hidden" name="action" value="confirmerChoix">
                     <button type="submit" class="w-full px-6 py-2 bg-green-500 hover:bg-green-600 text-white font-semibold rounded-lg transition-colors duration-200">
                         ✅ Continuer
                     </button>
                 </form>

                 <form method="POST" action="traiteActionSquadro.php" class="flex-1">
                     <input type="hidden" name="action" value="AnnulerChoix">
                     <button type="submit" class="w-full px-6 py-2 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-lg transition-colors duration-200">
                         ❌ Annuler
                     </button>
                 </form>
             </div>
         </div>
     </div>
     <style>
         @keyframes fadeInUp {
             from {
                 opacity: 0;
                 transform: translateY(20px);
             }
             to {
                 opacity: 1;
                 transform: translateY(0);
             }
         }
         .animate-fade-in-up {
             animation: fadeInUp 0.5s ease-out forwards;
         }
     </style>
     ' . self::genererPiedDePage();
    }

    /**
     * Génère la page de victoire.
     *
     * @param PlateauSquadro $plateau Le plateau de jeu final.
     * @param int $joueurGagnant La couleur du gagnant.
     * @return string Le HTML de la page de victoire.
     */
    public static function genererPageVictoire(PlateauSquadro $plateau, int $joueurGagnant): string {
        $gagnant = ($joueurGagnant === PieceSquadro::BLANC) ? "Blancs" : "Noirs";
        return self::genererEntete("Squadro - Victoire") . '
         <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-green-400 via-blue-500 to-purple-600 p-8">
             <div class="bg-white bg-opacity-90 p-10 rounded-xl shadow-2xl transform animate-scale-up">
                 <h1 class="text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-green-500 to-blue-500 mb-6 text-center">
                     Victoire !
                 </h1>
                 <p class="text-2xl font-semibold text-gray-800 mb-8 text-center">
                     Les <span class="text-indigo-600">' . $gagnant . '</span> ont remporté la partie !
                 </p>
                 <div class="mb-8">
                     ' . PieceSquadroUI::generatePlateau($plateau, $joueurGagnant) . '
                 </div>
                 <div class="flex justify-center">
                     <a href="traiteActionSquadro.php?action=rejouer" class="px-8 py-4 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-bold rounded-full shadow-lg transform hover:scale-105 transition duration-300">
                         Rejouer
                     </a>
                 </div>
             </div>
         </div>
         ' . self::genererPiedDePage();
    }

    /**
     * Génère la page d'erreur.
     *
     * @param string $message Le message d'erreur à afficher.
     * @return string Le HTML de la page d'erreur.
     */
    public static function genererPageErreur(string $message): string{
        return self::genererEntete("Squadro - Erreur") . '
                <div class="text-center">
                    <h1 class="text-3xl font-bold text-red-600">Erreur !</h1>
                    <p class="text-lg">' . htmlspecialchars($message) . '</p>
                </div>
                <a href="index.php" class="mt-4 px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg shadow-md hover:bg-blue-600">Revenir à l\'accueil</a>
                ' . self::genererPiedDePage();
    }
}
