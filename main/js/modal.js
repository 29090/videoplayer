
function openModal(type){
    // В эти переменные мы заносим элементы html страницы - модального окна
    let modal = document.getElementById('myModal');
    let customModalContent = document.getElementById('custom-modal-content');
    let content = document.getElementById("content");
    
    // Тут мы делаем модальное оконо видимым через "'flex'" - CSS
    modal.style.display = 'flex'; 
    
    customModalContent.style.width = '70%';
    //customModalContent.style.height = '70%';
    
        if(type == "startVideo"){
            content.innerHTML = '<h2>Загрузка...</h2>';
        }
        else if(type == "selectLevel"){
            content.innerHTML = '<h2>Загрузка...</h2>';
        }
        else if(type == "searchVideo"){
            content.innerHTML = '<h2>Поиск видео</h2>';
        }
        else if(type == "uploadYourVideo"){
            content.innerHTML = '<img src="main/image/imag.gif" >'; 
        }
        else if(type == "inviteFriend"){
            content.innerHTML = '<h2>Пригласить друга. Идет загрузка формы...</h2>'; // .textContent - добавляет только текст, отсекая теги
        }
        else if(type == "newsPromotions"){
            content.innerHTML = '<h2>Новости/Акции</h2>';
        }
        else if(type == "deleteAccount"){
            content.innerHTML = '<h2>Удаление Аккаунта</h2><p>Вы действительно хотите удалить аккаунт?</p><p><button class="delete_yes">Да</button><button class="delete_no">Нет</button></p>';

        }
    
        
        document.getElementById("closeModal").addEventListener("click", closeModel);
    
    }


function closeModel(){
document.getElementById("content").innerHTML = ""; // Тут мы опусашаем/перезаписываем содержимое модального окна.
document.getElementById('myModal').style.display = 'none'; // Тут мы заного получаем доступ к модальному окну, это новая функция и переменные из других не видит
}