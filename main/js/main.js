

document.addEventListener("DOMContentLoaded", myfunct_start);


function myfunct_start(){


    /// openModal("selectLevel"); // time


    if(document.getElementById("start")){
    document.getElementById("start").addEventListener("click", function(){
        openModal("startVideo");

        let obj = {
            "start":"getVideo"
            }

        myfunct_call_server(obj);
        });
    }

    if(document.getElementById("selectLevel")){
    document.getElementById("selectLevel").addEventListener("click", function(){
        openModal("selectLevel");
        let obj = {
            "start":"getGenresLevels"
            }
        myfunct_call_server(obj);
        });
    }

    if(document.getElementById("searchVideo")){
    document.getElementById("searchVideo").addEventListener("click", function(){
        openModal("searchVideo");

        myfunct_call_server({
            "start":"searchVideo"
            });

        });
    }

    if(document.getElementById("uploadYourVideo")){
    document.getElementById("uploadYourVideo").addEventListener("click", function(){
         openModal("uploadYourVideo"); 
         let obj = {
            "start":"uploadYourVideo"
        }; 
        myfunct_call_server(obj);
        });
    }

    if(document.getElementById("inviteFriend")){
    document.getElementById("inviteFriend").addEventListener("click", function(){
        openModal("inviteFriend");

        // Мы создали объект и заполнили его сразу при отправки его в функцию первым аргументом
        myfunct_call_server({
            "start":"inviteFriendGetForm"
            });
        });
    }

    if(document.getElementById("newsPromotions")){
    document.getElementById("newsPromotions").addEventListener("click", function(){
        openModal("newsPromotions");
        });
    }

    if(document.getElementById("deleteAccount")){    
    document.getElementById("deleteAccount").addEventListener("click", function(){
        openModal("deleteAccount");


            document.querySelector(".delete_yes").addEventListener("click", function(){
            let obj = {
                "start":"deleteAccount"
                };
            myfunct_call_server(obj);
            document.getElementById("content").innerHTML = '<img src="main/image/imag.gif">';
            });

            document.querySelector(".delete_no").addEventListener("click", function(){
                closeModel();
            });
        });
    }
}

