<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Quiz Disponibles</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<?php include 'header.php'; ?>

<div class="dashboard">

    <div class="welcome-card">
        <h1>Quiz Disponibles</h1>
        <p>Consultez les quiz publiés et commencez votre évaluation.</p>
    </div>

    <div id="quiz-container">
        <p>Chargement des quiz...</p>
    </div>

</div>

<script>

fetch('../api/get_public_quiz.php')
    .then(response => response.json())
    .then(data => {

        const container = document.getElementById('quiz-container');

        if (!data.success || data.quiz.length === 0) {
            container.innerHTML =
                '<p>Aucun quiz publié pour le moment.</p>';
            return;
        }

        let html = '';

        data.quiz.forEach(quiz => {

            html += `
                <div class="card">

                    <h2>${quiz.titre}</h2>

                    <p>
                        ${quiz.description}
                    </p>

                    <p>
                        <strong>${quiz.nb_questions}</strong>
                        question(s)
                    </p>

                    <a href="passer_quiz.php?id=${quiz.id}">
                        <button class="btn">
                            Commencer
                        </button>
                    </a>

                </div>
            `;
        });

        container.innerHTML = html;

    })

    .catch(error => {

        console.error(error);

        document.getElementById('quiz-container').innerHTML =
            '<p>Erreur lors du chargement des quiz.</p>';

    });

</script>

</body>
</html>