document.addEventListener("DOMContentLoaded", function(){

    if(document.getElementById("form_recovery_password")){
    myFunctStepForm();

    // ENTER
        if(document.getElementById("b_recovery")){
            document.getElementById("b_recovery").addEventListener("click", function(){
                document.getElementById("form_enter").style.display = "none";
                document.getElementById("form_recovery_password").style.display = "block";
            })
        }

        if(document.getElementById("b_enter")){
            document.getElementById("b_enter").addEventListener("click", function(){
                document.getElementById("form_enter").style.display = "block";
                document.getElementById("form_recovery_password").style.display = "none";
            })
        }




    // RECOVERY PASSWORD

    myFunctRecPassStep();
    }
});


function myFunctStepForm(){
let step = document.getElementById("wrraper").dataset.stepform;
    if(step == "enter"){
    document.getElementById("form_enter").style.display = "block";
    document.getElementById("form_recovery_password").style.display = "none";
    }
    else {
    document.getElementById("form_enter").style.display = "none";
    document.getElementById("form_recovery_password").style.display = "block";
    }
}



function myFunctRecPassStep(){
let step = document.getElementById("form_recovery_password").dataset.step; // 1, 2, 3

let step1 = document.getElementById("recPassStep_1");
let step2 = document.getElementById("recPassStep_2");
let step3 = document.getElementById("recPassStep_3");

    if(step == 1){
    step1.style.display = "block";
    step2.style.display = "none";
    step3.style.display = "none";
    } 
    else if(step == 2){
    step1.style.display = "none";
    step2.style.display = "block";
    step3.style.display = "none";
    }
    else {
    step1.style.display = "none";
    step2.style.display = "none";
    step3.style.display = "block";
    }
}