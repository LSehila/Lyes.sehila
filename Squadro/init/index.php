<?php
session_start();

use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;
use Squadro\PieceSquadroUI;
use Squadro\SquadroUIGenerator;

require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadroUI.php';
require_once '../src/SquadroUIGenerator.php';

// Initialisation des compteurs
$compteurBlanc = $_SESSION['countBlancSortie'] ?? 0;
$compteurNoir = $_SESSION['countNoirSortie'] ?? 0;

// Vérification de l'existence du plateau, sinon initialisation
if (!isset($_SESSION['plateau'])) {
    $_SESSION['plateau'] = serialize(new PlateauSquadro());
    $_SESSION['joueurActif'] = PieceSquadro::BLANC; // Les Blancs commencent
}

// Chargement du plateau et du joueur actif
$plateau = unserialize($_SESSION['plateau']);
$joueurActif = $_SESSION['joueurActif'];

// Si l'état est "ConfirmationPiece", afficher la page de confirmation
if (isset($_SESSION['etat']) && $_SESSION['etat'] === 'ConfirmationPiece' && isset($_SESSION['selectedPiece'])) {
    $selected = $_SESSION['selectedPiece'];
    echo SquadroUIGenerator::genererPageConfirmerDeplacement(
        $selected['ligne'],
        $selected['colonne'],
        $plateau,
        $joueurActif
    );
    exit; // Terminer l'exécution pour ne pas afficher la vue classique
}

// Affichage de la page de jeu normale
echo SquadroUIGenerator::genererEntete("Squadro - Jeu");
?>

<div class="flex flex-col lg:flex-row justify-center items-start lg:items-center p-8 gap-8">

    <!-- Zone principale du jeu -->
    <div class="w-full lg:w-2/3 flex flex-col items-center">
        <!-- Titre stylé -->
        <h1 class="text-5xl font-extrabold text-center mb-8 text-transparent bg-clip-text bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 animate-fade-in-up">
            SQUADRO
        </h1>

        <!-- Indication du tour -->
        <p class="text-lg mb-4">
            C'est au tour des
            <span class="font-semibold"><?= ($joueurActif === PieceSquadro::BLANC) ? "Blancs" : "Noirs"; ?></span> de jouer.
        </p>

        <!-- Affichage du plateau -->
        <div class="mt-4">
            <?= PieceSquadroUI::generatePlateau($plateau, $joueurActif); ?>
        </div>

        <!-- Bouton de réinitialisation repositionné sous le plateau -->
        <form method="POST" action="traiteActionSquadro.php" class="mt-8">
            <input type="hidden" name="action" value="rejouer">
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 text-white font-bold rounded-full shadow-lg transform hover:scale-105 transition duration-300">
                🔄 Réinitialiser le jeu
            </button>
        </form>
    </div>
</div>

<?php
echo SquadroUIGenerator::genererPiedDePage();
?>
