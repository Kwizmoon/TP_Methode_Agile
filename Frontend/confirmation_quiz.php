<?php
    session_start();
    require_once '../middleware/auth.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Soumis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once 'header.php'; ?>

    <main class="main-quiz-unique" style="text-align: center;">
        <h2 style="color: #16a34a; font-size: 2rem; margin-bottom: 1rem;">🎉 Quiz soumis avec succès !</h2>
        <p style="color: #475569; margin-bottom: 2rem;">Vos réponses ont bien été enregistrées.</p>
        <a href="public_quiz.php" class="btn" style="display: inline-block; text-decoration: none; padding: 0.75rem 1.5rem; background-color: #2563eb; color: white; border-radius: 6px;">Retour au tableau de bord</a>
    </main>
</body>
</html>