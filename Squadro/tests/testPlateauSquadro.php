<?php

require_once '../src/PlateauSquadro.php';
require_once '../src/PieceSquadro.php';

use Squadro\PlateauSquadro;
use Squadro\PieceSquadro;

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

echo "<h2>Tests pour la classe PlateauSquadro</h2>";

$plateau = new PlateauSquadro();

// Test: __toString()
$output = $plateau->__toString();
echo "<pre>Plateau initial:\n$output</pre>";

// Test: getPiece() pour (0,0) (coin neutre)
try {
    $piece00 = $plateau->getPiece(0, 0);
    assertEqual(PieceSquadro::NEUTRE, $piece00->getCouleur(), "getPiece(0,0) doit retourner une pièce NEUTRE");
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Erreur getPiece(0,0): " . $e->getMessage() . "</p>";
}

// Test: setPiece() (modification d'une case)
try {
    $plateau->setPiece(1, 1, PieceSquadro::initNeutre());
    $piece11 = $plateau->getPiece(1, 1);
    assertEqual(PieceSquadro::NEUTRE, $piece11->getCouleur(), "Après setPiece, la pièce en (1,1) doit être NEUTRE");
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Erreur setPiece(): " . $e->getMessage() . "</p>";
}

// Test: getCoordDestination() pour une pièce blanche en (3,0)
try {
    $coord = $plateau->getCoordDestination(3, 0);
    echo "<pre>Coordonnées destination pour (3,0): (" . implode(", ", $coord) . ")</pre>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Erreur getCoordDestination(3,0): " . $e->getMessage() . "</p>";
}

// Test: toJson() et fromJson()
$jsonPlateau = $plateau->toJson();
try {
    $plateauReconstitue = PlateauSquadro::fromJson($jsonPlateau);
    $outputReconstitue = $plateauReconstitue->__toString();
    echo "<pre>Plateau reconstitué:\n$outputReconstitue</pre>";
} catch (Exception $e) {
    echo "<p style='color:red;'>❌ Erreur fromJson(): " . $e->getMessage() . "</p>";
}

// Test: getLignesJouables() et getColonnesJouables()
$lignes = $plateau->getLignesJouables();
$colonnes = $plateau->getColonnesJouables();
echo "<pre>Lignes jouables: " . print_r($lignes, true) . "</pre>";
echo "<pre>Colonnes jouables: " . print_r($colonnes, true) . "</pre>";

echo "<h2>Fin des tests pour PlateauSquadro</h2>";
?>
