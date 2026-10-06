<?php
// ---------- Connexion à la base de données (MySQLi) ----------
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $mysqli = new mysqli("localhost", "root", "", "ticket");
    $mysqli->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Connexion impossible à la base de données.");
}

// ---------- Traitement du formulaire ----------
$message = "";

if (isset($_POST['code'])) {
    $saisie = trim($_POST['code']);

    if (!ctype_digit($saisie)) {
        $message = "Le code doit être un nombre entier positif.";
    } else {
        $code = (int) $saisie;

        if ($code >= 1 && $code <= 999) {
            // Code valide : on cherche l'article correspondant
            $stmt = $mysqli->prepare("SELECT article FROM articles WHERE code = ?");
            $stmt->bind_param("i", $code);
            $stmt->execute();
            $stmt->bind_result($article);

            if ($stmt->fetch()) {
                $message = "Le code $code est correct. L'information correspondante est : "
                         . htmlspecialchars($article) . ".";
            } else {
                $message = "Le code $code est correct mais aucun article ne lui correspond.";
            }
            $stmt->close();
        } else {
            // 0 ou plus de 999
            $message = "Le code est incorrect, trop grand !<br>Le code zéro est un cas particulier !";
        }
    }
}

$mysqli->close();
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
        <input type="text" name="code" id="code" autofocus>
        <button type="submit">Valider</button>
    </form>

    <?php if ($message !== ""): ?>
        <p><?= $message ?></p>
    <?php endif; ?>
</body>
</html>