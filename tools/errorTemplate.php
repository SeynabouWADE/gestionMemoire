<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title><?= $title ?></title>
</head>

<body <?=bgBlack()?>>
  
    <br>

    <?= $content?><br>

    <footer>
      <?php
        if( ! devMode)
          echo $errorMessage;
      ?>
    </footer>
</body>