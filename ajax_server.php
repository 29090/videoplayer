<?php

require("main/php/core.php"); // Подгружаем ядро проекта


//sleep(7);
$data = file_get_contents("php://input"); // Приняла строку от файла SERVER.JS

$arr = json_decode($data, true); // из строки в массив

$result_array = "";

   // ТОЛЬКО С JSON
    if(isset($arr["start"])){
        // Удаление аккаута
        if($arr["start"] == "deleteAccount"){
        $SQL = "DELETE FROM `users` WHERE(`id` = ".$AUTH["id"].") LIMIT 1";
        $BD->exec($SQL);
        setcookie("auth", "", date("u")-3600);
        $result_array = [
            "start" => "deleteAccount",
            "result" => true
            ];
        }
    
        // Получить HTML-код для выбора жанров и уровня
        if($arr["start"] == "getGenresLevels"){
        $content = file_get_contents("main/fragment_html_text/genre_level.html");
        $result_array = [
            "start" => "getGenresLevels",
            "content" => $content
            ];
        }

        // Получить HTML-код для поиска видео
        if($arr["start"] == "searchVideo"){
        $content = file_get_contents("main/fragment_html_text/searchVideo.html");
        $result_array = [
            "start" => "searchVideo",
            "content" => $content
            ];
        }
        // Получить HTML-код для поиска видео ПО ПЕРВЫМ БУКВАМ
        if($arr["start"] == "search_video_abc"){
            if(preg_match("/^[А-Я]$/u", $arr["word"])){
                if(isset($_GENRE[$arr["ganre"]])){
                $sql = "SELECT * FROM `videos` WHERE(`name` LIKE '".$arr["word"]."%')";
                $data = $BD->query($sql);
                $forData = [];
                    while($result = $data->fetch(PDO::FETCH_ASSOC)){
                    $forData[] = [
                        "id" => $result["id"],
                        "name" => $result["name"],
                        "type" => $result["type"]
                        ];
                    }
                $result_array = [
                    "start" => "search_video_abc",
                    "data" => $forData
                    ];
                }
            }
        }

        // Получить видео
        if($arr["start"] == "getVideo"){
        $result_array = [
            "start" => "getVideo",
            "videos" => getfiles()
            ];
        }

        // Сохранить жанр и уровень
        if($arr["start"] == "saveGenreLevel"){
        setcookie("selectLevel", $arr["level"], date("U")+999999999);
        setcookie("selectGenre", $arr["genre"], date("U")+999999999);
        $result_array = [
            "start" => "saveGenreLevel",
            "result" => "ok"
            ];
        }

        // Получение формы для загрузки видео фойла
        if($arr["start"] == "uploadYourVideo"){
        $content = file_get_contents("main/fragment_html_text/uploadVideos.html");
        $result_array = [
            "start" => "uploadYourVideo",
            "content" => $content
            ];
        }

        // Получение формы для приглашения друга
        if($arr["start"] == "inviteFriendGetForm"){
        $content = file_get_contents("main/fragment_html_text/inviteFriend.html");
        $result_array = [
            "start" => "inviteFriendGetForm",
            "content" => $content
            ];
        }

        // Отправка приглашения другу по почте
        if($arr["start"] == "inviteFriend"){


            if(mail($arr["email"], "Приглашение", "Ссылка на наш ресурс. ".$arr["text"], "")){
                $info = "Приглашение отправлено!";
            }
            else {
                $info = "Ошибка отправки приглашения!";
            }

        $result_array = [
            "start" => "inviteFriend",
            "content" => $info
            ];
        }

    }
    else {
    // ЗАГРУЗКА ВИДЕО
/*
Спроектировать таблицу
Проверить имя файла, которые ввел пользователь
Проверить все входные данные
Создать запись в таблице и получить ИД
Скопировать все файлы с четкой структурой по папкам

print_r($_POST);
print_r($_FILES);     

*/
    /* 
$_FILES = [
    "video" => [
        "error" => 0,
        "type" => "....",
        "size" => 1234567,
        "tmp_name" => "..."
        ],
    "doc1" => [
        "error" => 0,
        "type" => "....",
        "size" => 1234567,
        "tmp_name" => "..."
        ],
    "doc2" => [
        "error" => 0,
        "type" => "....",
        "size" => 1234567,
        "tmp_name" => "..."
        ]
    ];
 */
$error = 0;

$dataForBase = [//ЭТОТ МАССИВ МЫ СОЗДАЛИ И БУДЕМ ЗАПОЛНЯТЬ ДЛЯ БД
    "doc1" => 0,
    "doc2" => 0,
    "name" => "",
    "genre" => 0,
    "videoSize" => 0,
    "type" => ""
    ];

$result_array = [// Туит мы формируем ответ браузеру (формируем массив) (МЫ ЕГО БУДЕМ НИЖЕ ПО КОДУ ДОПОЛНЯТЬ КЛЮЧАМИ И СОБЕРЖИМЫМ)
    "start" => "videoUploadOk"
    ];

        if(isset($_POST["name"]) and $_POST["name"] != ""){
        $dataForBase["name"] = $_POST["name"];
        $dataForBase["name"] = trim($dataForBase["name"]);// функция trim()-удаляет пробелы в начале и в конце строки 
        $dataForBase["name"] = preg_replace("/\s+/u", " ", $dataForBase["name"]);// (\s- спец-символ, пустоты,ентер, пробелы и т.п. + - это 1 или больше)
        $dataForBase["name"] = preg_replace("/[^-A-Za-zА-Яа-яЁё&\s№0-9`'!?(),\.]/u", "", $dataForBase["name"]);
        }
        else {
        $error++;
        }

        if(isset($_POST["genre"]) and isset($_GENRE[$_POST["genre"]])){
        $dataForBase["genre"] = $_POST["genre"];
        }
        else {
        $error++;
        }
    
        if(isset($_FILES["video"]) and $_FILES["video"]["error"] == 0){
        $dataForBase["videoSize"] = $_FILES["video"]["size"];
        }
        else {
        $error++;
        }

        if(isset($_FILES["doc1"]) and $_FILES["doc1"]["size"] != 0 and $_FILES["doc1"]["error"] == 0){
        $dataForBase["doc1"] = 1;
        }
    
        if(isset($_FILES["doc2"]) and $_FILES["doc2"]["size"] != 0 and $_FILES["doc2"]["error"] == 0){
        $dataForBase["doc2"] = 1;
        }

        if($error == 0){
        $dataForBase["type"] = myExtensionFile($_FILES["video"]["type"]);
        $SQL = "INSERT INTO `videos` SET `doc1` = ".$dataForBase["doc1"].", `doc2` = ".$dataForBase["doc2"].", `name` = '".$dataForBase["name"]."', `genre` = ".$dataForBase["genre"].", `size` = ".$dataForBase["videoSize"].", `type` = ".$dataForBase["type"].", `date` = ".date("U");
        $BD->query($SQL);
        $id = $BD->lastInsertId();
        copy($_FILES["video"]["tmp_name"], "main/videos/videos/".$id.$dataForBase["type"]);
        
            if($dataForBase["doc1"] == 1){
            $type = myExtensionFile($_FILES["doc1"]["type"]);
            copy($_FILES["doc1"]["tmp_name"], "main/videos/docs/".$id."_1".$type);
            }
        
            if($dataForBase["doc2"] == 1){
            $type = myExtensionFile($_FILES["doc2"]["type"]);
            copy($_FILES["doc2"]["tmp_name"], "main/videos/docs/".$id."_2".$type);
            }
        
        $result_array["result"] = $id;
        }
        else {
        $result_array["error"] = "Ошибка с загрузкой видео.";
        }
    }



echo json_encode($result_array);


?>
