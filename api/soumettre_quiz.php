<?php
session_start();
require_once '../middleware/auth.php';
require __DIR__ . '/../config/Database.php';

$quiz_id = $_GET['id'] ?? $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    die("Quiz non spécifié.");
}

// Si le formulaire vient d'être soumis en POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reponsesEtudiant = $_POST['reponses'] ?? []; 
    $etudiant_id = $_SESSION['user_id'] ?? null;

    try {
        // 1. Récupérer toutes les questions de ce quiz
        $stmtQ = $pdo->prepare("SELECT id FROM questions WHERE quiz_id = ?");
        $stmtQ->execute([$quiz_id]);
        $questions = $stmtQ->fetchAll(PDO::FETCH_COLUMN);

        // 2. Vérifier que chaque question a bien une réponse soumise
        foreach ($questions as $qId) {
            if (!isset($reponsesEtudiant[$qId]) || empty($reponsesEtudiant[$qId])) {
                header("Location: passer_quiz.php?id=$quiz_id&erreur=incomplet");
                exit;
            }
        }

        // 3. Enregistrement des réponses de l'étudiant
        $insertion = $pdo->prepare("INSERT INTO reponses_etudiants (etudiant_id, quiz_id, question_id, reponse_id) VALUES (?, ?, ?, ?)");

        foreach ($reponsesEtudiant as $qId => $rId) {
            $insertion->execute([$etudiant_id, $quiz_id, $qId, $rId]);
        }

        // Redirection vers la page de succès
        header("Location: confirmation_quiz.php");
        exit;

    } catch (Exception $e) {
        die("Erreur lors de la soumission : " . $e->getMessage());
    }
}

// --- Le reste pour l'affichage du quiz en GET ---
try {
    $stmtQuiz = $pdo->prepare("SELECT * FROM quiz WHERE id = ?");
    $stmtQuiz->execute([$quiz_id]);
    $quiz = $stmtQuiz->fetch(PDO::FETCH_ASSOC);

    if (!$quiz) {
        die("Quiz introuvable.");
    }

    $stmtQ = $pdo->prepare("SELECT id, text AS question FROM questions WHERE quiz_id = ?");
    $stmtQ->execute([$quiz_id]);
    $questions = $stmtQ->fetchAll(PDO::FETCH_ASSOC);

    foreach ($questions as &$q) {
        $stmtR = $pdo->prepare("SELECT id, text FROM reponses WHERE question_id = ?");
        $stmtR->execute([$q['id']]);
        $q['reponses'] = $stmtR->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>