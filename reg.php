<?php
require("main/php/core.php"); // Подгружаем ядро проекта

// Массив для строковых ошибок
$errorArray = [
    "paul" => "",
    "login" => "",
    "email" => "",
    "phone" => "",
    "password_1" => "",
    "password_2" => ""
    ];

// Массив для значений элементов формы (для сохранения введенных данных в строках "что бы он видел что надо исправлять")
$valueArray = [ // доустановить куки, для сохранения и удобства
    "paul" => "",
    "paulCheck" => [
        1 => "",
        2 => ""
        ],
    "login" => "",
    "email" => "",
    "phone" => "", 
    "password_1" => "",
    "password_2" => ""
    ];

// Проверяем на отправку всех полей формы
if(isset($_POST["send"]) and isset($_POST["paul"]) and isset($_POST["login"]) and isset($_POST["email"]) and isset($_POST["phone"]) and isset($_POST["password_1"]) and isset($_POST["password_2"])){

    $errorNum = 0; // Ошибка числовая счетчик ошибок

// Присваиваем значения, которые нам отправил пользователь
$valueArray["paul"] = $_POST["paul"];
$valueArray["login"] = $_POST["login"];
$valueArray["email"] = $_POST["email"];
$valueArray["phone"] = $_POST["phone"];
$valueArray["password_1"] = $_POST["password_1"];
$valueArray["password_2"] = $_POST["password_2"];

// Проверяем на отправку всех полей формы
    // Элемент формы отвечающий за пол
    if(!($valueArray["paul"] == 1 or $valueArray["paul"] == 2)){
    $errorArray["paul"] = "Пол не указан.";
    $errorNum++;
    }
    else {
    $valueArray["paulCheck"][$valueArray["paul"]] = ' selected="selected"';
    }
    // Создаем безымянный массив, (для передачи его в функцию)
    // ЛОГИН
    $temp = myFunctCheckData(Array("login" => $valueArray["login"]));
        if($temp["result"] == true){
        $valueArray["login"] = $temp["newValue"];
        }
        else {
        $errorNum++;
        $errorArray["login"] = $temp["textError"];
        }

    // ЭЛЕКТРОННАЯ ПОЧТА
    $temp = myFunctCheckData(Array("email" => $valueArray["email"]));
        if($temp["result"] == true){
        $valueArray["email"] = $temp["newValue"];
        }
        else {
        $errorNum++;
        $errorArray["email"] = $temp["textError"];
        }

    // ТЕЛЕФОН
    $temp = myFunctCheckData(Array("phone" => $valueArray["phone"]));
        if($temp["result"] == true){
        $valueArray["phone"] = $temp["newValue"];
        }
        else {
        $errorNum++;
        $errorArray["phone"] = $temp["textError"];
        }

    // ПАРОЛЬ И ПОВТОР ПАРОЛЯ
    $temp = myFunctCheckData(Array("password" => $valueArray["password_1"], "password_2" => $valueArray["password_2"]));
        if($temp["result"] == false){
        $errorNum++;
            if(isset($temp["textError"])){
            $errorArray["password_1"] = $temp["textError"];
            }
            else {
            $errorArray["password_2"] = $temp["textError_2"];
            }
        }

    if($errorNum == 0){
    $SQL = "SELECT `id`, `login`, `email`, `phone` FROM `users` WHERE(`login` = :log or `email` = :ema or `phone` = :phone) LIMIT 3";
    $data = $BD->prepare($SQL);

    $data->execute([
        "log" => $valueArray["login"],
        "ema" => $valueArray["email"],
        "phone" => $valueArray["phone"]
        ]);

        while($result = $data->fetch(PDO::FETCH_ASSOC)){ // Создаем цикл, для извлечения всех найденых данных (что бы не останавливаться на первой найденной записи)
            if($result["login"] == $valueArray["login"]){
            $errorArray["login"] =  "Пользователь с таким логином уже существует.";
            $errorNum++;
            }
            if($result["email"] == $valueArray["email"]){
            $errorArray["email"] =  "Пользователь с такой электронной почтой уже существует.";
            $errorNum++;
            }
            if($result["phone"] == $valueArray["phone"]){
            $errorArray["phone"] =  "Пользователь с таким номером телефона уже существует.";
            $errorNum++;
            }
        }
    
        if($errorNum == 0){
        $hashPass = password_hash($valueArray["password_1"], PASSWORD_DEFAULT);
        $SQL = "INSERT INTO `users` SET `email` = :email, `login` = :login, `phone` = :phone, `password` = :password, `paul` = :paul, `date_reg` = :datereg, `count_block` = 5, `time_block` = 0";
        $data = $BD->prepare($SQL);
        $data->execute([
            "login" => $valueArray["login"],
            "email" => $valueArray["email"],
            "phone" => $valueArray["phone"],
            "password" => $hashPass,
            "paul" => $valueArray["paul"],
            "datereg" => date("U")
            ]);


        $forCookie = [
            "id" => $BD->lastInsertId(),
            "password" => $hashPass
            ];


        $textCookie = json_encode($forCookie);//json_encode()-эта функция переводит в строку
        setcookie("auth", $textCookie, date("U")+360000000);// Установили КУКИ и тем самым АВТОРИЗОВАЛИ

        header("Location: lk.php"); // Перевод на другую страницу(В Личный Кабинет)
        exit;
        }
    }
}

$TITLE = "Регистрация";
$BODY = '<div class="wrapper">
    <div class="block-enter-reg">
    <h1>Регистрация</h1>
    <form action="reg.php" method="POST"> <!--форма для отправки -->
    <span class="input-title" id="paul_info">Имеются различия в формах глаголов и местоимений в зависимости от пола говорящего </span>
    <span class="input-error" id="paul_error">'.$errorArray["paul"].'</span>
    <select name="paul"  id="paul" title="Выберите ваш пол">
        <option value="0">Ваш пол:</option>
        <option value="1"'.$valueArray["paulCheck"][1].'>Мужчина</option>
        <option value="2"'.$valueArray["paulCheck"][2].'>Женщина</option>
    </select>
    <span class="input-title" id="login_info">Логин должен состоять от 3-ех до 30-ти символов.</span>
    <span class="input-error" id="login_error">'.$errorArray["login"].'</span>
    <input type="text" name="login" id="login" placeholder="Логин" value="'.$valueArray["login"].'">

    <span class="input-title" id="email_info">Введите Ваш e-mail.</span>
    <span class="input-error" id="email_error">'.$errorArray["email"].'</span>
    <input type="text" name="email" id="email" placeholder="Электронная почта" value="'.$valueArray["email"].'">

    <span class="input-title" id="phone_info">Например: +79999999999</span>
    <span class="input-error" id="phone_error">'.$errorArray["phone"].'</span>
    <input type="text" name="phone" id="phone" placeholder="Телефон" value="'.$valueArray["phone"].'">

    <span class="input-title" id="password_info_1">1</span>
    <span class="input-error" id="password_error_1">'.$errorArray["password_1"].'</span>
    <input type="password" name="password_1" id="password_1" placeholder="Придумайте пароль" value="'.$valueArray["password_1"].'">

    <span class="input-title" id="password_info_2">2</span>
    <span class="input-error" id="password_error_2">'.$errorArray["password_2"].'</span>
    <input type="password" name="password_2" id="password_2" placeholder="Повторите пароль" value="'.$valueArray["password_2"].'">

    <input type="submit" name="send" value="Зарегистрироваться">
    <!--<a href="">Вспомнить пароль</a>-->
    </form> <!--форма для отправки -->
    </div>
</div>';


require("main/php/end.php");
?>