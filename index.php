<?php
$message = "";

if (isset($_POST['code'])) {
    $saisie = trim($_POST['code']);

    if (!ctype_digit($saisie)) {
        $message = "Le code doit être un nombre entier positif.";
    } else {
        $code = (int) $saisie;

        if ($code >= 1 && $code <= 999) {
            $message = "Le code $code est correct.";
        } else {
            $message = "Le code est incorrect, trop grand !<br>Le code zéro est un cas particulier !";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ticket</title>
</head>
<body>
    <h1>Ticket</h1>
    <form method="post" action="index.php">
        <label for="code">Entrez un code :</label>
        <input type="text" name="code" id="code">
        <button type="submit">Valider</button>
    </form>
    <?php if ($message !== ""): ?>
    <p><?= $message ?></p>
    <?php endif; ?>
</body>
</html>