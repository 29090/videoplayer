<?php
require("main/php/core.php"); // Подгружаем ядро проекта


    if($AUTH){
        $BODY='Вы авторизованы!';
        //$BODY= "Привет, ".$AUTH["name"]."!";
    } else{
        $BODY='Главная страница <a href="reg.php">Регистрация</a><a href="enter.php">Авторизация</a>';
    }


$TITLE = "SAPIENS";


require("main/php/end.php");
?>