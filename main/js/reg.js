document.addEventListener("DOMContentLoaded", function(){



    if(document.getElementById("paul")){
    document.getElementById("paul").addEventListener("change", function(){
        if(document.getElementById("paul").value == 0)
            document.getElementById("paul_info").style.display = "block";
            if(document.getElementById("paul").value != 0){
            document.getElementById("paul_error").innerHTML = "";
            }
        });
    document.getElementById("paul").addEventListener("blur", function(){
            document.getElementById("paul_info").style.display = "none";
        });
    }
    

    if(document.getElementById("login")){

    document.getElementById("login").addEventListener("focus", function(){
        document.getElementById("login_info").style.display = "block";
        document.getElementById("login_error").innerHTML = "";
        });

    document.getElementById("login").addEventListener("blur", function(){
        document.getElementById("login_info").style.display = "none";
        });
    
    
    }
    if(document.getElementById("email")){
    document.getElementById("email").addEventListener("focus", function(){
        document.getElementById("email_info").style.display = "block";
        document.getElementById("email_error").innerHTML = "";
        });

    document.getElementById("email").addEventListener("blur", function(){
        document.getElementById("email_info").style.display = "none";
        });
    }

    
    if(document.getElementById("phone")){
    document.getElementById("phone").addEventListener("focus", function(){
        document.getElementById("phone_info").style.display = "block";
        document.getElementById("phone_error").innerHTML = "";
        });

    document.getElementById("phone").addEventListener("blur", function(){
        document.getElementById("phone_info").style.display = "none";
        });
    }


    if (document.getElementById("password_1")){
    document.getElementById("password_1").addEventListener("focus", function(){
        document.getElementById("password_info_1").style.display = "block";
        document.getElementById("password_error_1").innerHTML = "";
        });

    document.getElementById("password_1").addEventListener("blur", function(){
        document.getElementById("password_info_1").style.display = "none";
        });
    
    }

    if (document.getElementById("password_2")){
    document.getElementById("password_2").addEventListener("focus", function(){
        document.getElementById("password_info_2").style.display = "block";
        document.getElementById("password_error_2").innerHTML = "";
        });
    
        document.getElementById("password_2").addEventListener("blur", function(){
            document.getElementById("password_info_2").style.display = "none";
        });
        
    }


});