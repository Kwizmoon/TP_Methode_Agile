<?php

require __DIR__ . '/../config/Database.php';

header("Content-Type: application/json; charset=utf-8");

$requete = $pdo->prepare(
    "SELECT q.id,
            q.titre,
            q.description,
            COUNT(qs.id) AS nb_questions
     FROM quiz q
     LEFT JOIN questions qs
        ON qs.quiz_id = q.id
     WHERE q.statut = 'Publié'
     GROUP BY q.id, q.titre, q.description
     ORDER BY q.id DESC"
);

$requete->execute();

$quiz = $requete->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "quiz" => $quiz
]);

exit();