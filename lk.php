<?php
require("main/php/core.php"); // Подгружаем ядро проекта


$TITLE = "Личный кабинет";
$BODY = '<div class="wrapper">
<div class="block-main-lk">
    <div class="top">
        <div class="custom-button" id="selectLevel">
            <span>ЖАНР И КОЛ-ВО ПРОСМОТРОВ</span>
        </div>
        <div class="custom-button" id="searchVideo">
            <span>ПОИСК ВИДЕО</span>
        </div>
        <div class="custom-button" id="uploadYourVideo">
            <span>ЗАГРУЗИТЬ СВОЕ ВИДЕО</span>
        </div>
        <div class="custom-button" id="inviteFriend">
            <span>Пригласить друга</span>
        </div>
        <div class="custom-button" id="newsPromotions">
            <span>НОВОСТИ / АКЦИИ</span>
        </div>
        <div class="custom-button" id="deleteAccount">
            <span>УДАЛЕНИЕ СВОЕГО АКАУНТА</span>
        </div>
    </div>
    <div class="text">
    Вы посмотрели 1 фильм
    </div>
    <div class="start">
        <div class="custom-button" id="start">
            <span>СТАРТ</span>
        </div>
    </div>
</div>
</div>';


require("main/php/end.php");
?>