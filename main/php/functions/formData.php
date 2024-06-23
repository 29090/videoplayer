<?php
// Функция для проверки данных из формы (ПРИНИМАЕТ В КАЧЕСТВЕ АРГУМЕНТА ТОЛЬКО МАССИВ)
function myFunctCheckData(array $arrayData){
/*
Входные данные
$arrayData["ТИП ПОЛЯ"] = данные поля
*/
    if(isset($arrayData["login"])){
        if($arrayData["login"] != ""){
        $arrayData["login"] = mb_strtolower($arrayData["login"]); // mb_strtolower()-эта функция переводит строку в нижний регистр (если пользователь ввел в ВЕРХНЕМ РЕГИСТРЕ)
        $arrayData["login"] = preg_replace("/\s+/", "", $arrayData["login"]);// preg_replace()-эта функция ЗАМЕНЯЕТ по правилам регулярного выражения (УБИРАЕТ ПРОБЕЛЫ, ТАБУЛЯЦИЮ И ПУСТ СИМВОЛЫ)
        
            if(mb_strlen($arrayData["login"]) >= 3 and mb_strlen($arrayData["login"]) <= 30){ //mb_strlen()-Эта функция возвращает длинну строки
                if(!preg_match("/^[a-z]{1}[-a-z0-9_\.]{1,29}$/u", $arrayData["login"])){ //preg_match()-эта функция ПРОВЕРЯЕТ по регулярному выражению
                return [
                    "result" => false,
                    "textError" => "Логин введен не корректно."
                    ];
                }
            return [
                "result" => true,
                "newValue" => $arrayData["login"]
                ];
            }
        return [
            "result" => false,
            "textError" => "Длина логина должна быть от 3-ех до 30-ти символов."
            ];
        }
    return [
        "result" => false,
        "textError" => "Логин не указан."
        ];
    }
    else if(isset($arrayData["phone"])){
        if($arrayData["phone"] != ""){
        $arrayData["phone"] = preg_replace("/[^+0-9]+/", "", $arrayData["phone"]); // Удаляем все символы, которые не являются числом или +
        $arrayData["phone"] = preg_replace("/^[8]/", "+7", $arrayData["phone"]); // Меняем 8 без плюса на +7 в начеле номера
            if(!(mb_strlen($arrayData["phone"]) >= 10 and mb_strlen($arrayData["phone"]) <= 12)){ //mb_strlen()-Эта функция возвращает длинну строки
            return [
                "result" => false,
                "textError" => "Не корректно указан телефон (см. подсказку)"
                ];
            }
        return [
            "result" => true,
            "newValue" => $arrayData["phone"]
        ];
        }
    return [
        "result" => false,
        "textError" => "Телефон не указан."
        ];
    }
    else if(isset($arrayData["email"])){
        if($arrayData["email"] != ""){
        $arrayData["email"] = mb_strtolower($arrayData["email"]);
        $arrayData["email"] = preg_replace("/\s+/", "", $arrayData["email"]);
            if(!preg_match("/^(([^<>()\[\]\.,;:\s@\"]+(\.[^<>()\[\]\.,;:\s@\"]+)*)|(\".+\"))@(([^<>()[\]\.,;:\s@\"]+\.)+[^<>()[\]\.,;:\s@\"]{2,})$/u", $arrayData["email"])){ //preg_match()-эта функция ПРОВЕРЯЕТ по регулярному выражению
            return [
                "result" => false,
                "textError" => "Электронная почта введена не корректно."
                ];
            }
        return [
            "result" => true,
            "newValue" => $arrayData["email"]
            ];
        }
    return [
        "result" => false,
        "textError" => "Электронная почта не указана."
        ];
    }
    else if(isset($arrayData["password"])){
        if($arrayData["password"] != ""){
            if(mb_strlen($arrayData["password"]) < 8){ //mb_strlen()-Эта функция возвращает длинну строки
            return [
                "result" => false,
                "textError" => "Пароль слишком короткий."
                ];
            }
            else {
                if(isset($arrayData["password_2"])){
                    if($arrayData["password_2"] != ""){
                        if(mb_strlen($arrayData["password_2"]) < 8){ //mb_strlen()-Эта функция возвращает длинну строки
                        return [
                            "result" => false,
                            "textError_2" => "Повторный пароль слишком короткий."
                            ];
                        }
                        else {
                            if($arrayData["password"] !== $arrayData["password_2"]){
                            return [
                                "result" => false,
                                "textError" => "Пароли не совподают."
                                ];
                            }
                        }
                    }
                    else {
                    return [
                        "result" => false,
                        "textError_2" => "Повторный пароль не введен."
                        ];
                    }
                }
            return [
                "result" => true
                ];
            }
        }
    return [
        "result" => false,
        "textError" => "Пароль не введен."
        ];
    }

}
?>
