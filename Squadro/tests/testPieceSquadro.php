<?php

require_once '../src/PieceSquadro.php';

use Squadro\PieceSquadro;

/**
 * Fonction d'assertion simple.
 *
 * Compare la valeur attendue et la valeur réelle, et affiche le résultat du test.
 *
 * @param mixed  $expected La valeur attendue.
 * @param mixed  $actual La valeur réelle.
 * @param string $message Le nom ou la description du test.
 */
function assertEqual($expected, $actual, string $message): void {
    if ($expected === $actual) {
        echo "<p style='color:green;'>[Test validé] - $message</p>";
    } else {
        echo "<p style='color:red;'>[Test non validé] - $message<br>";
        echo "  Valeur attendue: " . htmlspecialchars(print_r($expected, true)) . "<br>";
        echo "  Valeur réelle  : " . htmlspecialchars(print_r($actual, true)) . "</p>";
    }
}

echo "<h2>Tests pour la classe PieceSquadro</h2>";

// Test de initVide()
echo "<h3>Test: initVide()</h3>";
$pieceVide = PieceSquadro::initVide();
assertEqual(PieceSquadro::VIDE, $pieceVide->getCouleur(), "initVide() - Couleur");
assertEqual(PieceSquadro::VIDE, $pieceVide->getDirection(), "initVide() - Direction");

// Test de initNeutre()
echo "<h3>Test: initNeutre()</h3>";
$pieceNeutre = PieceSquadro::initNeutre();
assertEqual(PieceSquadro::NEUTRE, $pieceNeutre->getCouleur(), "initNeutre() - Couleur");
assertEqual(PieceSquadro::NEUTRE, $pieceNeutre->getDirection(), "initNeutre() - Direction");

// Test de initNoirNord()
echo "<h3>Test: initNoirNord()</h3>";
$pieceNoirNord = PieceSquadro::initNoirNord();
assertEqual(PieceSquadro::NOIR, $pieceNoirNord->getCouleur(), "initNoirNord() - Couleur");
assertEqual(PieceSquadro::NORD, $pieceNoirNord->getDirection(), "initNoirNord() - Direction");

// Test de initNoirSud()
echo "<h3>Test: initNoirSud()</h3>";
$pieceNoirSud = PieceSquadro::initNoirSud();
assertEqual(PieceSquadro::NOIR, $pieceNoirSud->getCouleur(), "initNoirSud() - Couleur");
assertEqual(PieceSquadro::SUD, $pieceNoirSud->getDirection(), "initNoirSud() - Direction");

// Test de initBlancEst()
echo "<h3>Test: initBlancEst()</h3>";
$pieceBlancEst = PieceSquadro::initBlancEst();
assertEqual(PieceSquadro::BLANC, $pieceBlancEst->getCouleur(), "initBlancEst() - Couleur");
assertEqual(PieceSquadro::EST, $pieceBlancEst->getDirection(), "initBlancEst() - Direction");

// Test de initBlancOuest()
echo "<h3>Test: initBlancOuest()</h3>";
$pieceBlancOuest = PieceSquadro::initBlancOuest();
assertEqual(PieceSquadro::BLANC, $pieceBlancOuest->getCouleur(), "initBlancOuest() - Couleur");
assertEqual(PieceSquadro::OUEST, $pieceBlancOuest->getDirection(), "initBlancOuest() - Direction");

// Test de inverseDirection()
// Pour ce test, nous allons inverser la direction d'une pièce blanche initialement à l'Est.
echo "<h3>Test: inverseDirection()</h3>";
$pieceTest = PieceSquadro::initBlancEst();
$pieceTest->inverseDirection(); // De EST vers OUEST
assertEqual(PieceSquadro::OUEST, $pieceTest->getDirection(), "inverseDirection() - Passage de EST à OUEST");

// Test de toJson() et fromJson()
// Convertir une pièce en JSON puis la reconstruire.
echo "<h3>Test: toJson() et fromJson()</h3>";
$json = $pieceNoirNord->toJson();
$pieceReconstitue = PieceSquadro::fromJson($json);
assertEqual($pieceNoirNord->getCouleur(), $pieceReconstitue->getCouleur(), "fromJson() - Couleur");
assertEqual($pieceNoirNord->getDirection(), $pieceReconstitue->getDirection(), "fromJson() - Direction");

echo "<h2>Fin des tests pour PieceSquadro</h2>";
?>
