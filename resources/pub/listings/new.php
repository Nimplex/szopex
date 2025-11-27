<?php

$title = 'Nowe ogłoszenie';

$render_scripts = function (): string {
    return <<<HTML
    <script type="module" src="/_dist/js/file_picker.js"></script>
    HTML;
};

$render_content = function (): string {
    return <<<HTML
    <h1>Nowe ogłoszenie</h1>
    <hr>
    <div>
        <form action="/api/new-listing" method="POST" enctype="multipart/form-data">
            <label>
                Tytuł ogłoszenia:
                <input type="text" name="title" minlength="8" maxlength="100" required>
            </label>
            <br>
            <label>
                Opis:
                <textarea name="description" minlength="20" maxlength="1000" required></textarea>
            </label>
            <br>
            <div class="row">
                <div>
                    <label>
                        Cena:<br>
                        <input
                            class="money-input"
                            type="text"
                            inputmode="numeric"
                            pattern="\d{1,4}((,|\.)\d\d)?"
                            name="price"
                            placeholder="5,00"
                            required
                        >
                        <span>zł</span>
                    </label>
                    <br><br>
                </div>
                <div id="image-input-outer">
                    <h3>Zdjęcia</h3>
                    <br>
                    <div id="image-input">
                        <div>
                            <label>
                                +
                                <input
                                    type="file"
                                    class="sr-only"
                                    onchange="previewFile(this)"
                                    name="images[]"
                                    accept="image/jpeg,image/png"
                                >
                            </label>
                            <button onclick="removeFile(this); event.preventDefault(); event.stopPropagation();">×</button>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <input type="submit" value="Stwórz">
        </form>
    </div>
    HTML;
};

$render_head = function (): string {
    return <<<HTML
    <link rel="stylesheet" href="/_dist/css/new.css">
    HTML;
};

require $_SERVER['DOCUMENT_ROOT'] . '/../resources/components/container.php';
