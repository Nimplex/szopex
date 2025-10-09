<?php
require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nowa oferta</title>
    <style>
        .money-input {
            width: 5em;
        }
    </style>
</head>

<body>
    <h1>Nowa oferta</h1>
    <?php if ($_GET['err']): ?>
    <div class="err">
        Wystąpił błąd podczas tworzenia nowej oferty. Spróbuj ponownie.
    </div>
    <?php endif; ?>
    <form action="/api/new-listing" method="POST">
        <p>
            <label>Tytuł: <input type="text" name="title" pattern=".{8,100}" required></label><br>
        </p>
        <p>
            <label>Opis: <br><textarea name="description" rows="10" cols="50" pattern=".{20,1000}" required></textarea></label><br>
        </p>
        <p>
            <label>
                Cena:
                <input
                    class="money-input"
                    type="text"
                    inputmode="numeric"
                    pattern="\d+((,|\.)\d\d)?"
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
