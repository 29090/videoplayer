function myfunct_call_server(dataForServer){

let secondParam = {  // здесь мы создаем объект "КЛАССА" в котором храним информацию о запросе на сервер
                     // Вся новая информация дописывается в объект класса, через запятую
    }

    // ОТПРАВЛЯЕМ JSON текст и получаем текст
    if(dataForServer["start"]){
    secondParam = {
        "method": "POST", // это "method"- свойство/"конверт"- хранит в себе строку ("POST")
        // это "headers" - свойство/"конверт"- хранит в себе ОБЪЕКТ ({......})
        "headers":{ // явно указываем заголовок (В этот ключ мы записываем объект, который получяем из брауз)
                "Content-type":"application/json;charset=utf-8" 
                },
        "body": JSON.stringify(dataForServer) // Перевод объекта в строку - json
        }
    }
    else { // ОТПРАВЛЯЕМ ФАЙЛЫ
    secondParam = { // В этом случае заголовки ставит сам браузер
        "method": "POST",
        "body": dataForServer // Отправляем объект с данными-файлами из формы
        }
    }

let callServer = fetch("ajax_server.php", secondParam);


callServer.then(function(answerServer){
        if(answerServer.status == 200){
        answerServer.text().then(function(t){
            let data = JSON.parse(t);

                if(data["start"] == "search_video_abc"){
                let htmlFragment = "";
                    data["data"].forEach(function(element){
                        htmlFragment += '<li><button data-video="'+element["id"]+'" data-type="'+element["type"]+'">'+element["name"]+'</button></li>';
                    });
                
                let result = document.querySelector(".searche_video_result");
                result.innerHTML = '<ul>'+htmlFragment+'</ul>';

                    
                document.querySelector(".search_video_close").addEventListener("click", function(){
                    document.querySelector(".search_video_one_block").style.display = "block";
                    document.querySelector(".search_video_two_block .videos").innerHTML ="";
                    document.querySelector(".search_video_two_block").style.display = "none";
                });

                let buttonAll = result.querySelectorAll("button");
                    for(let elem of buttonAll){
                        elem.addEventListener("click", function(){
                        let id = this.dataset.video;
                        let type = this.dataset.type;
                        document.querySelector(".search_video_one_block").style.display = "none";
                        document.querySelector(".search_video_two_block").style.display = "block";
                        document.querySelector(".search_video_two_block .videos").innerHTML = '<video class="video" loop autoplay data-number="0"><source src="http://project/main/videos/videos/'+id+type+'" type="video/mp4">Ваш браузер не поддерживает видео-файлы</video>';
                        
                            setTimeout(function(){
                            document.querySelector(".search_video_two_block").innerHTML = "";
                            document.querySelector(".search_video_one_block").style.display = "block";
                            }, 15000);
                        });
                    }

                }

                if(data["start"] == "searchVideo"){
                    document.getElementById("content").innerHTML = data["content"];
                    document.querySelector(".search_video_ganre").addEventListener("input", function(){
                    // alert(this.value)
                    document.querySelector(".searche_video_abc").style.display = "block";

                    });

                    let allSpans = document.querySelectorAll(".searche_video_abc button");

                    for(let span of allSpans){
                        span.addEventListener("click", function(){
                            //console.log(span.innerHTML);
                            
                        let ob = {
                            "start":"search_video_abc", //свойство : значение
                            "word":span.innerHTML,
                            "ganre":document.querySelector(".search_video_ganre").value 
                            }
                        myfunct_call_server(ob);
                        });
                    }
                }


                if(data["start"] == "getGenresLevels"){
                document.getElementById("content").innerHTML = data["content"];
                document.getElementById("genre_level_save").addEventListener("click", function(){
                    let genre = 0;
                    let level = 0;

                        for(let n = 1; n <= n+1; n++){
                            if(document.getElementById("genre_"+n)){
                                if(document.getElementById("genre_"+n).checked){
                                    genre = n;
                                    break;
                                }
                            }
                            else {
                            break;
                            }
                        }

                        for(let n = 1; n <= n+1; n++){
                            if(document.getElementById("level_"+n)){
                                if(document.getElementById("level_"+n).checked){
                                    level = n;
                                    break;
                                }
                            }
                            else {
                            break;
                            }
                        }

                        if(genre == 0 || level == 0){
                        alert("Выберите жанр или уровень!");
                        }
                        else{
                            // Создаем запрос AJAX с выбранными жанром и уровнем
                            // Мы сохраняем в БД
                        let obj = {
                            "start":"saveGenreLevel",
                            "genre":genre,
                            "level":level
                            }
                        
                        myfunct_call_server(obj);
                        }
                    });
                }

                if(data["start"] == "getVideo"){
                window.VIDEOS = data["videos"];
                document.getElementById("content").innerHTML = '<video class="video" loop autoplay id="video-player" data-number="0"><source src="http://project/main/videos/videos/'+VIDEOS[0]+'" type="video/mp4">Ваш браузер не поддерживает видео-файлы</video>';
                    setTimeout(nextVideo, 5000);
                    function nextVideo(){
                    let player = document.getElementById("video-player");
                    let numberVideo = player.getAttribute("data-number");

                        for(let n = 0; n <= VIDEOS.length; n++){
                            if(numberVideo == n){
                                if(VIDEOS[n+1] !== undefined){
                                document.getElementById("content").innerHTML = '<video class="video" loop autoplay id="video-player" data-number="'+(n+1)+'"><source src="http://project/main/videos/videos/'+VIDEOS[n+1]+'" type="video/mp4">Ваш браузер не поддерживает видео-файлы</video>';
                                setTimeout(nextVideo, 5000);
                                }
                                else {
                                closeModel();
                                }
                            break;
                            }
                        }
                    }
                }

                if(data["start"] == "saveGenreLevel"){
                document.getElementById("content").innerHTML = '<p><b>Ваш выбор сохранен!</b></p>';
                }
            
                if(data["start"] == "uploadYourVideo"){
                document.getElementById("content").innerHTML = data["content"];

                
                document.getElementById("formUploadVideo").addEventListener("submit", function(event){
                    event.preventDefault(); // Останавливаем отправку форму по-умолчанию браузером, иначе страница перезагрузится
                    let dataForm = new FormData(this); // Создаем объект с данными формы
                    myfunct_call_server(dataForm);
                    });


                // Продолжение логики
                }

                if(data["start"] == "inviteFriendGetForm"){
                document.getElementById("content").innerHTML = data["content"];
                document.getElementById("formInviteFriend").addEventListener("submit", function(event){
                    event.preventDefault(); // Останавливаем отправку форму по-умолчанию браузером, иначе страница перезагрузится
                    let obj = {
                        "start" : "inviteFriend",
                        "email" : document.getElementById("inviteFrindEmail").value,
                        "text" : document.getElementById("inviteFrindText").value,
                        }
                    myfunct_call_server(obj);
                    });
                }
            
                if(data["start"] == "inviteFriend"){
                    document.getElementById("answerFriend").innerHTML = data["content"];
                }

                if(data["start"] == "videoUploadOk"){
                    if(data["error"]){
                    document.getElementById("content").innerHTML = "<b>Ошибка загрузки видео.</b>";
                    }
                    else {
                    document.getElementById("content").innerHTML = "<b>Вкдко успешно сохранено и ему присвоен номер: "+data["result"]+"</b>";
                    }
                }

                if(data["start"] == "deleteAccount"){
                    if(data["result"] == true){
                    window.location.href = "http://project/index.php";
                    }
                }
            // 
            });
        }
        else 
        {
        // Ошибка запроса
        }
    }).catch(function(errorServer){
    //....
    });
}