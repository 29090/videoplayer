<?php

function getfiles(){
// Уровень сложности
$cookieLevel = 1;
    if(isset($_COOKIE["selectLevel"])){
    $cookieLevel = $_COOKIE["selectLevel"];
    }

    if(!isset($_COOKIE["dayTime"])){
    $cookieDay = 1;
    $cookieDate = date("U");

    $arr = [
        "day" => $cookieDay,
        "time" => $cookieDate
        ];
    
    $arrText = json_encode($arr);
    setcookie("dayTime", $arrText, date("U")+99999999);
    }
    else {
    $data = json_decode($_COOKIE["dayTime"], true);

        if(date("U") > ($data["time"]+30)){

        $data = [
            "day" => $data["day"]+1,
            "time" => date("U")
            ];

        setcookie("dayTime", json_encode($data), date("U")+99999999);
        }

    $cookieDay = $data["day"];
    $cookieDate = $data["time"];
    }

// Если жанр выбран
$selectGenre = "";
    if(isset($_COOKIE["selectGenre"])){// проверяем на наличие куки
        if(isset($GLOBALS["_GENRE"][$_COOKIE["selectGenre"]])){ // тут 1) мы читаем массив целиком. 2) через куки получаем конкретный индекс
        $selectGenre = " WHERE(`genre` = ".$_COOKIE["selectGenre"].")";
        }
    }


//echo "День ".$cookieDay." Время: ".($cookieDate - date("U"));

// start ---------
// $arrFiles = [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55];
/*
1. Открываем папку
2. Создаем массив из файлов в папке
*/
$arrFiles = []; // Создаем пустой массив

// ЧТЕНИЕ ИЗ БД
$SQL = "SELECT `id`, `type` FROM `videos`".$selectGenre;
$result = $GLOBALS["BD"]->query($SQL);
    while($video = $result->fetch(PDO::FETCH_ASSOC)){
    $arrFiles[] = $video["id"].$video["type"];
    }

// ЧТЕНИЕ ИЗ ПАПКИ
/* $dir = "main/videos/videos/"; // Указываем путь к папке с содеранием файлов
$folder = opendir($dir); // Открывает папку
    if($folder == true){ // Проверяем открылась ли папка
        while(($file = readdir($folder)) !== false){ // Читаем папку до тех пор не закончатся файлы в ней
            if($file != "." && $file != ".."){ // Фильтруем названия файлов (если "." или "..") 
            $arrFiles[] = $file;  // Мы дописываем последний индекс в массив, согласно нашей логики-ниже
            }
        }
    }
    else {
    echo 'Ошибка открытия папки!'; // Ошибка открытия папки
    } */

   // print_r ($arrFiles); // Выводим результат в массиве



$arrEchoFiles = [];
$limitFilesDay = [3,5,7];
$limitFilesLevel = [15,25,35];

//(countFilesDay-Кол-во файлов в день)=(limitFilesDay- Шаг файлов в день)
$countFilesDay = $limitFilesDay[$cookieLevel-1]; 

//(countFilesLevel-Кол-во файлов в уровень)=(limitFilesLevel-Шаг файлов в уровень)
$countFilesLevel = $limitFilesLevel[$cookieLevel-1];

// Всего файлов в массиве
$allFiles = count($arrFiles)-1;

// Всего дней для показа файлов
$allDays = floor($allFiles / $countFilesDay);

    if($allDays <= $cookieDay){
    $cookieDay = 1;
    }

//$min = $countFilesDay*$cookieDay-$countFilesLevel < 0 ? 0 : $countFilesDay*$cookieDay-$countFilesLevel;
// в min =  кол-во файлов в день * на текущий день - вычетаем кол-во файлов в уровень
    if($countFilesDay*$cookieDay-$countFilesLevel < 0){
        //если < 0 , то мы возвращаем 0 (?-условие )
        $min = 0;
    }
    // Иначе в min =  кол-во файлов в день * на текущий день - вычетаем кол-во файлов в уровень (3*3-15=6 - ОГРАНИЧИТЕЛЬ ГДЕ ОСТАНОВИТЬ)
    else {
        $min = $countFilesDay*$cookieDay-$countFilesLevel;
    }


//$max = $allDays == $cookieDay ? count($arrFiles)-1 : $countFilesDay*$cookieDay-1;

    // Если всего дней === с текущим днем пользователя
    if($allDays == $cookieDay){
    // заносим остаток (если он есть и если это последний день).
    $max = count($arrFiles)-1;
    }
    // Иначе шаг файла в день * на текщей день - 1
    else {
    $max = $countFilesDay*$cookieDay-1;
    }

    // запускаем цикл (макс > к минимуму; )
    for($n = $min; $n <= $max; $n++){
        // записываем в массив -результат
    $arrEchoFiles[] = $arrFiles[$n];
    }
    // берем массив переворачиваем и записываем в этуже перемеменную 
//$arrEchoFiles = array_reverse($arrEchoFiles);
// end ---------

/* echo "Отображаемые файлы:<br>".implode(", ", $arrEchoFiles);
echo "<p><b>Статистика</b><br>Отображаем индексы массива от ".$min." до ".$max."<br>";
echo "Уровень сложности: ".$cookieLevel." из ".count($limitFilesLevel)."<br>";
echo "День: ".$cookieDay." из ".$allDays."<br>";
echo "Отображаем файлов в уровне: ".$countFilesLevel." по ".$countFilesDay."<br>";
echo "Отображено видео: ".count($arrEchoFiles)." из ".count($arrFiles)."<br>";
echo "Массив файлов: ".implode(", ", $arrFiles)."</p>"; */

return $arrEchoFiles;
}
?>