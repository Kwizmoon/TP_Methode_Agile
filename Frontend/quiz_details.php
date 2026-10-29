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



        fetch(`../api/get_quiz_details.php?id=${quizId}`)



            .then(response => {
                console.log("Status :", response.status);
                return response.json();
            })



            .then(data => {



                console.log(data);



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
                        </div>
                    `;



                });



                document.getElementById("quiz-container").innerHTML = html;



            })



            .catch(error => {



                console.error(error);



                document.getElementById("quiz-container").innerHTML =
                    `<p>Erreur lors du chargement du quiz.</p>`;



            });
    }



</script>



</body>
</html>