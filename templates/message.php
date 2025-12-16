<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Szopex – nowa wiadomość</title>
  <style type="text/css">
    body {
      margin: 0;
      padding: 0;
      background-color: #f6f6f6;
      font-family: Arial, sans-serif;
      color: #333;
    }
    .container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background-color: #ffffff;
      border: 1px solid #e0e0e0;
    }
    .header {
      background-color: #000000;
      border-border: 8px;
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid #e0e0e0;
    }
    .header img {
      max-width: 200px;
      height: auto;
    }
    .content {
      padding: 20px;
      line-height: 1.5;
    }
    .content p {
      margin: 0 0 15px 0;
    }
    .footer {
      background-color: #f9f9f9;
      padding: 15px 20px;
      font-size: 12px;
      color: #777;
      border-top: 1px solid #e0e0e0;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header">
      <img src="https://szopex.nimplex.xyz/_assets/logo.png" alt="Szopex Logo">
    </div>

    <div class="content">
      <p>Witaj <strong><?= htmlspecialchars($name) ?></strong>,</p>

      <p>
        Otrzymałeś nową wiadomość od użytkownika: <b><code><?= htmlspecialchars($messager) ?></code></b>
        <?= isset($listing) ? ", dotyczącej <b>„{$listing}”</b>" : "" ?>.
      </p>

      <p>Treść wiadomości:</p>
      <p style="background-color: #f4f4f4; padding: 10px; border-radius: 4px;">
        <?= htmlspecialchars($content) ?>
      </p>

      <a href="https://szopex.nimplex.xyz/messages/<?= $chat_id ?>">Otwórz czat</a>
    </div>

    <div class="footer">
      <p>
        Jeśli nie życzysz sobie otrzymywać powiadomień -- zmień swoje ustawienia powiadomień w <a href="https://szopex.nimplex.xyz/settings/notifications">panelu Szopex</a>.
      </p>
      <p>
        &copy; <?= date('Y') ?> Szopex. Wszelkie prawa zastrzeżone.
      </p>
    </div>
  </div>
</body>
</html>

