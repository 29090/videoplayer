<?php

function myExtensionFile($type){
    if($type=="image/jpeg"){
    return ".jpeg";
    }
    else if($type=="image/jpg"){
    return ".jpg";
    }
    else if($type=="application/vnd.openxmlformats-officedocument.wordprocessingml.document"){
    return ".docx";
    }
    else if($type=="video/mp4"){
    return ".mp4";
    }
    else if($type=="video/webm"){
    return ".webm";
    }
}

?>