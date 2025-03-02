<?php

require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadro.php';
require_once '../src/PieceSquadroUI.php';
require_once '../src/SquadroUIGenerator.php';

use Squadro\PlateauSquadro;
use Squadro\PieceSquadro;
use Squadro\SquadroUIGenerator;

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

echo "<h2>Tests pour la classe SquadroUIGenerator</h2>";

// Test: genererEntete()
echo "<h3>Test: genererEntete()</h3>";
$entete = SquadroUIGenerator::genererEntete("Test Title");
if (strpos($entete, "<!DOCTYPE html>") !== false && strpos($entete, "<title>Test Title</title>") !== false) {
    echo "<p style='color:green;'>✅ Test validé - genererEntete()</p>";
} else {
    echo "<p style='color:red;'>❌ Test non validé - genererEntete()</p>";
}

// Test: genererPiedDePage()
echo "<h3>Test: genererPiedDePage()</h3>";
$pied = SquadroUIGenerator::genererPiedDePage();
if (strpos($pied, "</body></html>") !== false) {
    echo "<p style='color:green;'>✅ Test validé - genererPiedDePage()</p>";
} else {
    echo "<p style='color:red;'>❌ Test non validé - genererPiedDePage()</p>";
}

// Test: genererPageJouerPiece()
echo "<h3>Test: genererPageJouerPiece()</h3>";
$plateau = new PlateauSquadro();
$pageJouer = SquadroUIGenerator::genererPageJouerPiece($plateau, PieceSquadro::BLANC);
if (strpos($pageJouer, "Jouer une pièce") !== false) {
    echo "<p style='color:green;'>✅ Test validé - genererPageJouerPiece()</p>";
} else {
    echo "<p style='color:red;'>❌ Test non validé - genererPageJouerPiece()</p>";
}

// Test: genererPageConfirmerDeplacement()
echo "<h3>Test: genererPageConfirmerDeplacement()</h3>";
$pageConfirmer = SquadroUIGenerator::genererPageConfirmerDeplacement(3, 0, $plateau, PieceSquadro::BLANC);
if (strpos($pageConfirmer, "Confirmer le déplacement") !== false &&
    strpos($pageConfirmer, "('3, 0')") === false && // Vérification des valeurs intégrées
    strpos($pageConfirmer, 'value="3"') !== false &&
    strpos($pageConfirmer, 'value="0"') !== false) {
    echo "<p style='color:green;'>✅ Test validé - genererPageConfirmerDeplacement()</p>";
} else {
    echo "<p style='color:red;'>❌ Test non validé - genererPageConfirmerDeplacement()</p>";
}

// Test: genererPageVictoire()
echo "<h3>Test: genererPageVictoire()</h3>";
$pageVictoire = SquadroUIGenerator::genererPageVictoire($plateau, PieceSquadro::NOIR);
if (strpos($pageVictoire, "Victoire !") !== false &&
    strpos($pageVictoire, "Noirs") !== false) {
    echo "<p style='color:green;'>✅ Test validé - genererPageVictoire()</p>";
} else {
    echo "<p style='color:red;'>❌ Test non validé - genererPageVictoire()</p>";
}

// Test: genererPageErreur()
echo "<h3>Test: genererPageErreur()</h3>";
$pageErreur = SquadroUIGenerator::genererPageErreur("Message d'erreur de test");
if (strpos($pageErreur, "Erreur !") !== false &&
    strpos($pageErreur, "Message d'erreur de test") !== false) {
    echo "<p style='color:green;'>✅ Test validé - genererPageErreur()</p>";
} else {
    echo "<p style='color:red;'>❌ Test non validé - genererPageErreur()</p>";
}

echo "<h2>Fin des tests pour SquadroUIGenerator</h2>";
?>
