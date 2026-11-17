<?php

require_once '../middleware/auth.php';

?>

<!DOCTYPE html>
<html lang="fr">



<head>
    <meta charset="UTF-8">
    <title>Détail du Quiz</title>



    <link rel="stylesheet" href="style.css">
</head>



<body>

<?php require_once 'header.php'; ?>


<div class="dashboard">



    <div id="quiz-container">
        <p>Chargement du quiz...</p>
    </div>



</div>



<script>



    const params = new URLSearchParams(window.location.search);
    const quizId = params.get("id");



    console.log("Quiz ID :", quizId);



    if (!quizId) {
        document.getElementById("quiz-container").innerHTML =
            "<p>Aucun quiz sélectionné.</p>";
    } else {     


        fetch(`../api/get_quiz_details.php?id=${encodeURIComponent(quizId)}`)
            .then(async response => {
                const text = await response.text();

                console.log("HTTP Status :", response.status);
                console.log("Réponse brute du serveur :", text);

                try {
                    return JSON.parse(text);
                } catch (error) {
                    throw new Error("La réponse du serveur n'est pas un JSON valide. Vérifie la console.");
                }
            })
            .then(data => {
                console.log("Données reçues :", data);

                if (!data.success) {
                    document.getElementById("quiz-container").innerHTML =
                        `<p>${data.message}</p>`;
                    return;
                }

                const quiz = data.quiz;

                let html = `
                    <div class="welcome-card">
                        <h1>${quiz.titre}</h1>
                        <p>${quiz.description}</p>
                        ${quiz.statut === 'Publié'
                            ? '<p>✅ Quiz publié</p>'
                            : '<button class="btn" onclick="publierQuiz()">Publier</button>'}
                    </div>
                `;

                quiz.questions.forEach(question => {

                    html += `
                        <div class="card">
                            <h2>${question.text}</h2>
                            <ul>
                    `;

                    question.reponses.forEach(reponse => {

                        html += `
                            <li>
                                ${reponse.text}
                                ${reponse.est_vrai == 1 ? '✅' : ''}
                            </li>
                        `;

                    });

                    html += `
                            </ul>
                            <button class="btn" onclick="window.location.href='modifier_question.php?id=${question.id}'">Modifier</button>
                            <button class="btn" onclick="supprimerQuestion(${question.id})">Supprimer</button>
                        </div>
                    `;

                });

                document.getElementById("quiz-container").innerHTML = html;

            })

            .catch(error => {
                 console.error("Erreur complète :", error);

                document.getElementById("quiz-container").innerHTML =
                    `<p>${error.message}</p>`;;
            });
    }

    // Publier le quiz (statut -> Publié)
    async function publierQuiz() {
        try {
            const response = await fetch('../api/publier_quiz.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ quiz_id: quizId })
            });
            const result = await response.json();

            if (result.success) {
                location.reload();
            } else {
                alert(result.message || 'Erreur lors de la publication.');
            }
        } catch (error) {
            console.error(error);
            alert('Impossible de communiquer avec le serveur.');
        }
    }

    // Supprimer une question apres confirmation
    async function supprimerQuestion(questionId) {
        if (!confirm('Voulez-vous vraiment supprimer cette question ?')) {
            return;
        }

        try {
            const response = await fetch('../api/supprimer_question.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ question_id: questionId })
            });
            const result = await response.json();

            if (result.success) {
                location.reload();
            } else {
                alert(result.message || 'Erreur lors de la suppression.');
            }
        } catch (error) {
            console.error(error);
            alert('Impossible de communiquer avec le serveur.');
        }
    }

</script>



</body>
</html>