<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $TITLE;?></title>
    <link rel="stylesheet" href="main/styles/style.css?sd=<?php echo mt_rand(1,99999999); ?>">
    <link rel="stylesheet" href="main/styles/modal.css?sd=<?php echo mt_rand(1,99999999); ?>">
    <link rel="stylesheet" href="main/styles/reg.css?sd=<?php echo mt_rand(1,99999999); ?>">
    <script src="main/js/main.js?sd=<?php echo mt_rand(1,99999999); ?>"></script>
    <script src="main/js/modal.js?sd=<?php echo mt_rand(1,99999999); ?>"></script>
    <script src="main/js/ajax_client.js?sd=<?php echo mt_rand(1,99999999); ?>"></script>
    <script src="main/js/reg.js?sd=<?php echo mt_rand(1,99999999); ?>"></script>
    <script src="main/js/enter.js?sd=<?php echo mt_rand(1,99999999); ?>"></script>
</head>
<body>
  <!-- Модальное окно -->
  <div id="myModal" class="modal"><!-- Обертка растянута по ширене и высоте экрана браузера и содержимое отцентровано -->
    <div id="custom-modal-content" class="modal-content"><!-- Оберткой только для модального окна, цвет фона и содержимое -->
      <div id="content" class="content"></div><!-- Элемент содержимого окна -->
      <div id="closeModal" class="close-button">&#10006;</div><!-- Кнопка закрыть окно -->
    </div>
  </div>

<?php
echo $BODY;
//echo "<p>Время выполнения кода: ".round((microtime(true) - $__START_TIME), 4)."</p>";
?>


</body>
</html> 