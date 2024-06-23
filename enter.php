<?php
require("main/php/core.php"); // Подгружаем ядро проекта

// Если он уже авторизирован то переводим в ЛК (Защита от ЗЛА или ТУПОСТИ)
if($AUTH){
header("Location: lk.php");
exit;
}


$stepForm = "enter"; // enter - форма входа открывается, иначе восстановление пароля
$stepRecovery = "1"; // 3 шага восстановления пароля 1 отправка логина 2 - ввод одноразового кода 3 сохранение пароля


// ВОССТАНОВЛЕНИЕ ПАРОЛЯ
$errorRecoveryArray = [ // Создаем массив и присваеваем ключи
    "login" => "",
    "code" => "",
    "password_1" => "",
    "password_2" => ""
    ];

$valueRecoveryArray = [
    "login" => "",
    "code" => "",
    "password_1" => "",
    "password_2" => ""
    ];


if((isset($_POST["recSendStep1"]) or isset($_POST["recSendStep2"]) or isset($_POST["recSendStep3"])) and isset($_POST["login"]) and isset($_POST["code"]) and isset($_POST["password_1"]) and isset($_POST["password_2"])){

// Открыта форма восстановления пароля
$stepForm = "recovery";

// Количество ошибок
$errorNum = 0;

$valueRecoveryArray["login"] = $_POST["login"];

    // Проверка ЛОГИНА - Через функцию
    $temp = myFunctCheckData(Array("login" => $valueRecoveryArray["login"]));
        if($temp["result"] == true){
        $valueRecoveryArray["login"] = $temp["newValue"];

        // Ищем пользователя по догину в БД
        $SQL = "SELECT `id`, `phone`, `count_block_sms`, `time_block_sms` FROM `users` WHERE(`login` = :login) LIMIT 1";
        $data = $BD->prepare($SQL);
        $data->execute([
            "login" => $valueRecoveryArray["login"]
            ]);
            if(!$resultUser = $data->fetch(PDO::FETCH_ASSOC)){
            $errorRecoveryArray["login"] = "Пользователя с таким логином не существует.";
            $errorNum++;
            }
        }
        else {
        $errorNum++;
        $errorRecoveryArray["login"] = $temp["textError"];
        }


    // Если ошибок не было
    if($errorNum == 0){
        if(isset($_POST["recSendStep1"])){ // Это первый шаг восстановления пароля
            if($resultUser["count_block_sms"] == 0){
                if(date("U") > $resultUser["time_block_sms"]+3600){
                $resultUser["count_block_sms"] = 3;
                }
            }

            if($resultUser["count_block_sms"] == 0){
            $errorRecoveryArray["login"] = "Лимит sms исчерпан. Попробуйте через: ".($resultUser["time_block_sms"]+3600-date("U"))." секунд.";
            $errorNum++;
            }
            else {
            // Генерируем код и отправем его в БД и на смс
            $code = mt_rand(1000, 9999);

            $SQL = "DELETE FROM `recovery_password` WHERE(`user` = ".$resultUser["id"].")";
            $BD->exec($SQL);

            $SQL = "INSERT INTO `recovery_password` SET `user` = ".$resultUser["id"].", `code` = ".$code.", `count_block` = 3, `date` = ".date("U");
            $BD->query($SQL);
            
            // Начало
            // Создает объект класса (prostor_sms_JsonGate) для работы с смс (от смс-простор)
            $gate = new prostor_sms_JsonGate('t89827163054', '654595');
            
            $messages = [ // Формируем СМС сообщение (Кому на телефон и какой текст сообщения)
                0 => [
                    "clientId" => $resultUser["id"],
                    "phone" => mb_substr($resultUser["phone"], 1),// +7989899898 берем телефон со второго символа
                    "text" => "Ваш код: ".$code." для восстановления доступа.",
                    "sender" => "Sapiens"
                    ]
                ];
            
            $resSmsSend = $gate->send($messages); // С помощью метода send в объекте $gate, отправляем пакет sms
            
                // Если смс не отправилось
                if(!($resSmsSend["status"] == "ok" or $resSmsSend["messages"][0]["status"] == "accepted")){
                $errorRecoveryArray["login"] = "Смс не отправилось по техническим причинам. Повторите попытку позже.";
                $errorNum++;
                }
            // Конец

                // Если не было ошибок выше
                if($errorNum == 0){
                $resultUser["count_block_sms"]--;
                    if($resultUser["count_block_sms"] == 0){
                    $resultUser["time_block_sms"] = date("U");
                    }

                $SQL = "UPDATE `users` SET `count_block_sms` = ".$resultUser["count_block_sms"].", `time_block_sms` = ".$resultUser["time_block_sms"]." WHERE(`id` = ".$resultUser["id"].") LIMIT 1";
                $BD->exec($SQL);

                $errorRecoveryArray["code"] = "Вам отправленно смс сообщение с кодом.";
                $stepRecovery = "2";
                }
            }
        }
        else { // Прием одноразового кода
        $valueRecoveryArray["code"] = $_POST["code"];

            if($valueRecoveryArray["code"] != ""){
                if(!preg_match("/^[0-9]{4}$/u", $valueRecoveryArray["code"])){
                $errorRecoveryArray["code"] = "Одноразовый код не корректен.";
                $errorNum++;
                }
            }
            else {
            $errorRecoveryArray["code"] = "Введите одноразовый код.";
            $errorNum++;
            }
        
            if($errorNum == 0){
            $SQL = "SELECT `id`, `count_block`, `date`, `code` FROM `recovery_password` WHERE(`user` = ".$resultUser["id"].") LIMIT 1";
            $data = $BD->query($SQL);
                if($resultCode = $data->fetch(PDO::FETCH_ASSOC)){
                    if($resultCode["count_block"] > 0 and $resultCode["date"]+3600 > date("U")){
                        if($resultCode["code"] == $valueRecoveryArray["code"]){
                        $stepRecovery = "3";
                            if(isset($_POST["recSendStep3"])){
                            $valueRecoveryArray["password_1"] = $_POST["password_1"];
                            $valueRecoveryArray["password_2"] = $_POST["password_2"];

                            $temp = myFunctCheckData(Array("password" => $valueRecoveryArray["password_1"], "password_2" => $valueRecoveryArray["password_2"]));
                                if($temp["result"] == false){
                                $errorNum++;
                                    if(isset($temp["textError"])){
                                    $errorRecoveryArray["password_1"] = $temp["textError"];
                                    }
                                    else {
                                    $errorRecoveryArray["password_2"] = $temp["textError_2"];
                                    }
                                }

                                if($errorNum == 0){
                                $hashPass = password_hash($valueRecoveryArray["password_1"], PASSWORD_DEFAULT);
                                $SQL = "UPDATE `users` SET `password` = :password";
                                $data = $BD->prepare($SQL);
                                $data->execute([
                                    "password" => $hashPass
                                    ]);
                                
                                $forCookie = [
                                    "id" => $resultUser["id"],
                                    "password" => $hashPass
                                    ];
                                
                                $textCookie = json_encode($forCookie);//json_encode()-эта функция переводит в строку
                                setcookie("auth", $textCookie, date("U")+360000000);
                                
                                $SQL = "DELETE FROM `recovery_password` WHERE(`id` = ".$resultCode["id"].") LIMIT 1";
                                $BD->exec($SQL);

                                $SQL = "UPDATE `users` SET `count_block_sms` = 3, `time_block_sms` = 0 WHERE(`id` = ".$resultUser["id"].") LIMIT 1";
                                $BD->exec($SQL);

                                header("Location: lk.php"); // Перевод на другую страницу(В Личный Кабинет)
                                exit;
                                }
                            }
                        }
                        else {
                        $resultCode["count_block"]--;
                            if($resultCode["count_block"] == 0){
                            $SQL = "DELETE FROM `recovery_password` WHERE(`id` = ".$resultCode["id"].") LIMIT 1";
                            $errorRecoveryArray["login"] = "Код не действителен. Отправте его повторно.";
                            $errorNum++;
                            $stepRecovery = "1";
                            }
                            else {
                            $SQL = "UPDATE `recovery_password` SET `count_block` = ".$resultCode["count_block"]." WHERE(`id` = ".$resultCode["id"].") LIMIT 1";
                            $errorRecoveryArray["code"] = "Код не верный. Осталось попыток: ".$resultCode["count_block"];
                            $errorNum++;
                            $stepRecovery = "2";
                            }
                        }
                    }
                    else {
                    $SQL = "DELETE FROM `recovery_password` WHERE(`id` = ".$resultCode["id"].") LIMIT 1";
                    $errorRecoveryArray["login"] = "Срок жизни кода истек. Повторите отправку.";
                    $errorNum++;
                    $stepRecovery = "1";
                    }
                $BD->exec($SQL);
                }
                else {
                $errorRecoveryArray["login"] = "Повторите отправку кода.";
                $errorNum++;
                $stepRecovery = "1";
                }
            }
            else {
            $stepRecovery = "2";
            }
        }
    }
}




// ВХОД
$errorArray = [
    "login" => "",
    "password" => ""
    ];

// Массив для значений элементов формы (для сохранения введенных данных в строках "что бы он видел что надо исправлять")
$valueArray = [
    "login" => "",
    "password" => ""
    ];

if(isset($_POST["send"]) and isset($_POST["login"]) and isset($_POST["password"])){
    
$errorNum = 0; // Ошибка числовая счетчик ошибок

$valueArray["login"] = $_POST["login"];
$valueArray["password"] = $_POST["password"];

     // Тут мы проверяем Логин на корректность АВТОРИЗАЦИЯ
    $temp = myFunctCheckData(Array("login" => $valueArray["login"]));
        if($temp["result"] == true){
        $valueArray["login"] = $temp["newValue"];
        }
        else{
        $errorNum++;
        $errorArray["login"] = $temp["textError"]; 
        }


    // Тут мы проверяем пароль на корректность АВТОРИЗАЦИЯ
    $temp = myFunctCheckData(Array("password" => $valueArray["password"]));
        if($temp["result"] == false){
        $errorNum++;
        $errorArray["password"] = $temp["textError"];
        }


    if($errorNum == 0){ // извлекаем count_block кол-во попыток, и time_block и время блокировки
    $timeBlock = 30;
    $SQL = "SELECT `id`, `password`, `count_block`, `time_block` FROM `users` WHERE(`login` = :log) LIMIT 1";
    $data = $BD->prepare($SQL);
    $data->execute(["log" => $valueArray["login"]]);
        if($result = $data->fetch(PDO::FETCH_ASSOC)){

// Данный блок для защиты от перебора паролей...
            if($result["count_block"] == 0){
                if(($result["time_block"] + $timeBlock) < date("U")){
                $result["count_block"] = 5;
                $result["time_block"] = 0;
                }
            }

            if($result["count_block"] > 0){
                if(password_verify($valueArray["password"], $result["password"])){
                $forCookie = [
                    "id" => $result["id"],
                    "password" => $result["password"]
                    ];
                $textCookie = json_encode($forCookie);//json_encode()-эта функция переводит в строку
                setcookie("auth", $textCookie, date("U")+360000000);
                // тут мы возвращаем в исходное состояние ПОПЫТОК ВХОДА В БД и ВРЕМЯ
                $SQL = "UPDATE `users` SET `count_block` = 5, `time_block` = 0 WHERE(`id` = ".$result["id"].") LIMIT 1";
                $BD->exec($SQL);
                header("Location: lk.php"); // Перевод на другую страницу(В Личный Кабинет)
                exit;
                }
                else {
                $result["count_block"]--;
                    if($result["count_block"] == 0){
                    $result["time_block"] = date("U");
                    $errorArray["login"] = "Ваша учетная запись заблокирована на ".$timeBlock." сек. Попробуйте позже.";
                    }
                    else {
                    $errorArray["login"] = "Пароль не верный. Осталось попыток: ".$result["count_block"];
                    }
                $SQL = "UPDATE `users` SET `count_block` = ".$result["count_block"].", `time_block` = ".$result["time_block"]." WHERE(`id` = ".$result["id"].") LIMIT 1";
                $BD->exec($SQL);
                $errorNum++;
                }
            }
            else {
            $errorArray["login"] = "Учетная запись разблокируется через: ".($result["time_block"] + $timeBlock) - date("U")." сек.";
            $errorNum++;
            }
        }
        else {
        $errorArray["login"] = "Такого пользователя не существует.";
        $errorNum++;
        }
    }

/*
1. от перебора пароля
2. от воровства куки
3. двухфакторная аутентификация
*/
}




$TITLE = "Вход";
$BODY = '<div class="wrapper">
    <div class="block-enter-reg" id="wrraper" data-stepform="'.$stepForm.'">

        <form action="enter.php" method="POST" id="form_enter">
        <h1>Вход</h1>
        
        <span class="input-error">'.$errorArray["login"].'</span>
        <input type="text" name="login" value="'.$valueArray["login"].'" placeholder="Логин">
        
        <span class="input-error">'.$errorArray["password"].'</span>
        <input type="password" name="password" value="'.$valueArray["password"].'" placeholder="Пароль">
        
        <input type="submit" name="send" value="Вход">

        <span id="b_recovery">Забыли пароль?</span>
        </form>



        <form action="enter.php" method="POST" id="form_recovery_password" data-step="'.$stepRecovery.'">
        <h1>Восстановить доступ</h1>
        
            <div id="recPassStep_1">
                <span class="input-error">'.$errorRecoveryArray["login"].'</span>
                <input type="text" name="login" value="'.$valueRecoveryArray["login"].'" placeholder="Логин">
                <input type="submit" name="recSendStep1" value="Продолжить">
            </div>

            <div id="recPassStep_2">
                <span class="input-error">'.$errorRecoveryArray["code"].'</span>
                <input type="text" name="code" value="'.$valueRecoveryArray["code"].'" placeholder="Одноразовый код">
                <input type="submit" name="recSendStep2" value="Продолжить">
            </div>

            <div id="recPassStep_3">
            <span class="input-error">'.$errorRecoveryArray["password_1"].'</span>
            <input type="password" name="password_1" value="'.$valueRecoveryArray["password_1"].'" placeholder="Введите новый пароль">
            
            <span class="input-error">'.$errorRecoveryArray["password_2"].'</span>
            <input type="password" name="password_2" value="'.$valueRecoveryArray["password_2"].'" placeholder="Повторите новый пароль">
            
            <input type="submit" name="recSendStep3" value="Продолжить">
            </div>

        <span id="b_enter">Войти</span>
        </form>

    </div>
</div>';


require("main/php/end.php");
?>