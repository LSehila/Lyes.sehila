<?php
namespace Squadro;

session_start();

require_once '../src/PieceSquadro.php';
require_once '../src/PlateauSquadro.php';
require_once '../src/ActionSquadro.php';

class TraiteActionSquadro
{
    public static function run(): void
    {
        // Récupération de l'action depuis le formulaire
        $action = $_POST['action'] ?? null;
        if (!$action) {
            $_SESSION['etat'] = 'Erreur';
            header('Location: index.php');
            exit;
        }

        switch ($action) {
            case 'ChoisirPiece':
                // Action : mémoriser la position de la pièce sélectionnée
                // Origine : état ChoixPièce  -> Destination : état ConfirmationPiece
                if (!isset($_POST['ligne']) || !isset($_POST['colonne'])) {
                    $_SESSION['etat'] = 'Erreur';
                    break;
                }
                $ligne = $_POST['ligne'];
                $colonne = $_POST['colonne'];
                $_SESSION['selectedPiece'] = ['ligne' => $ligne, 'colonne' => $colonne];
                $_SESSION['etat'] = 'ConfirmationPiece';
                break;

            case 'confirmerChoix':
                // Action : déplacer la pièce sélectionnée et mettre à jour l'état
                // Origine : état ConfirmationPiece  -> Destination : état ChoixPiece ou état Victoire
                if (!isset($_SESSION['etat']) || $_SESSION['etat'] !== 'ConfirmationPiece' || !isset($_SESSION['selectedPiece'])) {
                    $_SESSION['etat'] = 'Erreur';
                    break;
                }
                $ligne = $_SESSION['selectedPiece']['ligne'];
                $colonne = $_SESSION['selectedPiece']['colonne'];

                if (!isset($_SESSION['plateau']) || !isset($_SESSION['joueurActif'])) {
                    $_SESSION['etat'] = 'Erreur';
                    break;
                }
                $plateau = unserialize($_SESSION['plateau']);
                $joueurActif = $_SESSION['joueurActif'];

                // Création de l'objet ActionSquadro pour exécuter le déplacement
                $actionSquadro = new ActionSquadro($plateau);
                try {
                    $actionSquadro->jouerPiece($ligne, $colonne, $joueurActif);
                } catch (\Exception $e) {
                    // En cas d'erreur lors du déplacement, on passe en état Erreur
                    $_SESSION['etat'] = 'Erreur';
                    header('Location: index.php');
                    exit;
                }

                // On oublie la sélection précédente
                unset($_SESSION['selectedPiece']);

                // Vérification de la condition de victoire
                if ($actionSquadro->remporteVictoire()) {
                    $_SESSION['etat'] = 'Victoire';
                } else {
                    // Changement de joueur actif et retour à l'état de choix
                    $_SESSION['joueurActif'] = ($joueurActif === PieceSquadro::BLANC) ? PieceSquadro::NOIR : PieceSquadro::BLANC;
                    $_SESSION['etat'] = 'ChoixPiece';
                }
                // Mise à jour du plateau dans la session
                $_SESSION['plateau'] = serialize($plateau);
                break;

            case 'AnnulerChoix':
                // Action : annuler le choix en cours
                // Origine : état ConfirmationPiece  -> Destination : état ChoixPiece
                unset($_SESSION['selectedPiece']);
                $_SESSION['etat'] = 'ChoixPiece';
                break;

            case 'rejouer':
                // Action : redémarrer une nouvelle partie
                // Origine : état Victoire ou état Erreur  -> Destination : état ChoixPiece
                session_destroy();
                session_start();
                $_SESSION['plateau'] = serialize(new PlateauSquadro());
                $_SESSION['joueurActif'] = PieceSquadro::BLANC;
                $_SESSION['etat'] = 'ChoixPiece';
                $_SESSION['countBlancSortie'] = 0;
                $_SESSION['countNoirSortie'] = 0;
                break;

            default:
                // Toute action non conforme renvoie en état Erreur
                $_SESSION['etat'] = 'Erreur';
                break;
        }

        // Redirection vers la page principale
        header('Location: index.php');
        exit;
    }
}

// Lancement du traitement dès l'appel du script
TraiteActionSquadro::run();
