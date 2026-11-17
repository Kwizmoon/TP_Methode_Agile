<?php 

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// redirection vers accueil.php si la session n'est pas authentifier
if (empty($_SESSION['authenticated'])) {
    header('Location: accueil.php');
    exit;
}

?>