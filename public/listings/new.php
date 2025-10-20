<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/_css/main.css">
    <title>Nowa oferta</title>
    <style>
        .money-input {
            width: 5em;
        }
    </style>
</head>

<body>
    <h1>Nowa oferta</h1>
    <?php require $_SERVER['DOCUMENT_ROOT'] . '/../resources/hx-templates/message-box.php'; ?>
    <form action="/api/new-listing" method="POST">
        <p>
            <label>Tytuł: <input type="text" name="title" minlength="8" maxlength="100" required></label><br>
        </p>
        <p>
            <label>Opis: <br><textarea name="description" rows="10" cols="50" minlength="20" maxlength="1000" required></textarea></label><br>
        </p>
        <p>
            <label>
                Cena:
                <input
                    class="money-input"
                    type="text"
                    inputmode="numeric"
                    pattern="\d{,4}((,|\.)\d\d)?"
                    name="price"
                    placeholder="5,00"
                    required
                >
            </label> zł<br>
        </p>
        <input type="submit" value="Stwórz">
    </form>
</body>

</html>
