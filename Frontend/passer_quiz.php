<?php
session_start();
require_once '../middleware/auth.php';
require __DIR__ . '/../config/Database.php';

$quiz_id = $_GET['id'] ?? $_GET['quiz_id'] ?? null;

if (!$quiz_id) {
    die("Quiz non spécifié.");
}

// 1. Traitement de la soumission du formulaire (POST) sans base de données
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $reponsesEtudiant = $_POST['reponses'] ?? []; 

    try {
        // Vérifier combien de questions comporte le quiz
        $stmtQ = $pdo->prepare("SELECT id FROM questions WHERE quiz_id = ?");
        $stmtQ->execute([$quiz_id]);
        $questions = $stmtQ->fetchAll(PDO::FETCH_COLUMN);

        // Vérifier que chaque question a bien une réponse soumise
        foreach ($questions as $qId) {
            if (!isset($reponsesEtudiant[$qId]) || empty($reponsesEtudiant[$qId])) {
                header("Location: passer_quiz.php?id=$quiz_id&erreur=incomplet");
                exit;
            }
        }

        // Redirection directe vers la page de confirmation de succès
        header("Location: confirmation_quiz.php");
        exit;

    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}

// 2. Chargement des données pour l'affichage (GET)
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
    unset($q);

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($quiz['titre'] ?? 'Quiz') ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'header.php'; ?>

    <main class="main-quiz-unique">
        <form id="formPasserQuiz" action="passer_quiz.php?id=<?= $quiz_id ?>" method="POST" novalidate>
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

            <div class="quiz-titre-section">
                <h1><?= htmlspecialchars($quiz['titre']) ?></h1>
                <p><?= nl2br(htmlspecialchars($quiz['description'] ?? '')) ?></p>
            </div>

            <hr class="quiz-divider">

            <?php if (isset($_GET['erreur']) && $_GET['erreur'] === 'incomplet'): ?>
                <p class="erreur" style="margin-bottom: 15px; font-weight: bold;">Veuillez répondre à toutes les questions avant de soumettre.</p>
            <?php endif; ?>

            <div class="quiz-questions-container">
                <?php if (empty($questions)): ?>
                    <p class="etat-vide">Ce quiz ne contient pas encore de questions.</p>
                <?php else: ?>
                    <?php foreach ($questions as $index => $q): ?>
                        <div class="question-block" data-question-id="<?= $q['id'] ?>">
                            <h3 class="question-intitule">Question <?= $index + 1 ?> : <?= htmlspecialchars($q['question']) ?></h3>
                            
                            <div class="options-reponses">
                                <?php foreach ($q['reponses'] as $rep): ?>
                                    <label class="option-label">
                                        <input type="radio" name="reponses[<?= $q['id'] ?>]" value="<?= $rep['id'] ?>" required>
                                        <span><?= htmlspecialchars($rep['text']) ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p class="erreur" id="erreur-global"></p>

            <?php if (!empty($questions)): ?>
                <div class="form-actions">
                    <button type="submit" class="btn-soumettre">Soumettre le quiz</button>
                </div>
            <?php endif; ?>
        </form>
    </main>

    <script>
        document.getElementById('formPasserQuiz')?.addEventListener('submit', function(e) {
            document.getElementById('erreur-global').textContent = '';

            const questionBlocks = document.querySelectorAll('.question-block');
            let toutesRepondues = true;

            questionBlocks.forEach(block => {
                const qId = block.getAttribute('data-question-id');
                const radios = block.querySelectorAll(`input[name="reponses[${qId}]"]`);
                const uneCochee = Array.from(radios).some(r => r.checked);

                if (!uneCochee) {
                    toutesRepondues = false;
                    block.classList.add('erreur-question');
                } else {
                    block.classList.remove('erreur-question');
                }
            });

            if (!toutesRepondues) {
                e.preventDefault();
                document.getElementById('erreur-global').textContent = 'Veuillez répondre à toutes les questions avant de soumettre.';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    </script>
</body>
</html>