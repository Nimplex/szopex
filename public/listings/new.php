<?php

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/check-auth.php';

$title = "Nowe ogłoszenie";

function render_head()
{
    return <<<HTML
        <link rel="stylesheet" href="{WebpackManifest::asset('new.css')}">
        <style>
        .money-input {
            width: 5em;
        }
    </style>
    HTML;
}

function render_content()
{
    return <<<HTML
    <h1>Nowe ogłoszenie</h1>
    <hr>
    <div>
        <form action="/api/new-listing" method="POST">
            <label>
                Tytuł:
                <input type="text" name="title" minlength="8" maxlength="100" required>
            </label>
            <br>
            <label>
                Opis:
                <textarea name="description" minlength="20" maxlength="1000" required></textarea>
            </label>
            <br>
            <label>
                Cena:
                <input
                    class="money-input"
                    type="text"
                    inputmode="numeric"
                    pattern="\d{,4}((,|\.)\d\d)?"
                    name="price"
                    placeholder="5,00zł"
                    required
                >
            </label>
            <br>
            <input type="submit" value="Stwórz">
        </form>
        <div class="right">
            <p></p>
        </div>
    </div>
    HTML;
}

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
