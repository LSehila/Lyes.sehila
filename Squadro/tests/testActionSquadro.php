<?php

session_start();
require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/ActionSquadro.php';

use Squadro\PieceSquadro;
use Squadro\PlateauSquadro;
use Squadro\ActionSquadro;

/**
 * Fonction d'assertion simple avec émoji.
 */
function assertEqual($expected, $actual, string $message): void {
    if ($expected === $actual) {
        echo "<p style='color:green;'>✅ Test validé - $message</p>";
    } else {
        echo "<p style='color:red;'>❌ Test non validé - $message<br>";
        echo "   Attendu: " . htmlspecialchars(print_r($expected, true)) . "<br>";
        echo "   Obtenu: " . htmlspecialchars(print_r($actual, true)) . "</p>";
    }
}

echo "<h2>Tests pour la classe ActionSquadro</h2>";

// Initialisation du plateau et de la session
$_SESSION['countBlancSortie'] = 0;
$_SESSION['countNoirSortie'] = 0;
$plateau = new PlateauSquadro();
$action = new ActionSquadro($plateau);

// Test: estJouablePiece() pour une pièce blanche en (3,0)
echo "<h3>Test: estJouablePiece()</h3>";
$estJouable = $action->estJouablePiece(3, 0, PieceSquadro::BLANC);
assertEqual(true, $estJouable, "La pièce en (3,0) doit être jouable pour BLANC");

// Test: aTermineAllerRetour() pour une position où ce n'est pas terminé
echo "<h3>Test: aTermineAllerRetour()</h3>";
$resultAllerRetour = $action->aTermineAllerRetour(3, 0);
assertEqual(false, $resultAllerRetour, "aTermineAllerRetour() pour (3,0) doit retourner false");

// Pour tester jouerPiece() dans un cas standard, nous allons simuler un déplacement sur une pièce jouable.
// Note : Ce test modifie le plateau, il doit être exécuté dans l'ordre et en dernier.
echo "<h3>Test: jouerPiece()</h3>";
if ($estJouable) {
    // On sauvegarde l'état initial de la pièce en (3,0)
    $pieceInitiale = $plateau->getPiece(3, 0)->__toString();
    // On effectue le déplacement
    try {
        $action->jouerPiece(3, 0, PieceSquadro::BLANC);
        // On vérifie que la case de départ est devenue vide
        $pieceDepart = $plateau->getPiece(3, 0)->getCouleur();
        assertEqual(PieceSquadro::VIDE, $pieceDepart, "Après jouerPiece, la case (3,0) doit être vide");
    } catch (Exception $e) {
        echo "<p style='color:red;'>❌ Erreur lors de jouerPiece(): " . $e->getMessage() . "</p>";
    }
}

echo "<h2>Fin des tests pour ActionSquadro</h2>";
?>
