function initialEmbedPreviewLoad(){
    let privateTitel = document.getElementById("privateTitel").value;
    let privateMessage = document.getElementById("privateMessage").value;
    let privateColor = document.getElementById("privateColor").value;

    let embedPreview = document.getElementById("embedPreview");
    let embedPreviewBotLine = document.getElementById("embedPreviewBotLine");
    let embedPreviewTitel = document.getElementById("embedPreviewTitel");
    let embedPreviewMessage = document.getElementById("embedPreviewMessage");

    embedPreviewTitel.innerHTML = privateTitel;
    embedPreviewMessage.innerHTML = privateMessage;
    if(embedPreviewTitel.innerHTML != "" || embedPreviewMessage.innerHTML != ""){
        embedPreview.style.display = "block";
        embedPreviewBotLine.style.display = "block";
    }
    embedPreview.style.borderLeft = "4px solid "+privateColor;
}


function genEmbedPreview(elem){
    let embedPreview = document.getElementById("embedPreview");
    let embedPreviewBody = document.getElementById("embedPreviewBody");
    let embedPreviewBotLine = document.getElementById("embedPreviewBotLine");

    if(embedPreview.style.display == "none"){
        embedPreview.style.display = "block";
        embedPreviewBotLine.style.display = "block";
    }
    
    if(document.getElementById("embedPreviewTitel").innerHTML == "" && document.getElementById("embedPreviewMessage").innerHTML == ""){
        embedPreview.style.display = "none"; 
        embedPreviewBotLine.style.display = "none";
    }
    

    //change titel in preview
    if(elem.id == "privateTitel"){
        if(document.contains(document.getElementById("embedPreviewTitel"))){
            document.getElementById("embedPreviewTitel").remove();
        }
        let newTitel = document.createElement("p");
        newTitel.setAttribute("id","embedPreviewTitel");
        newTitel.innerHTML = elem.value;
        embedPreviewBody.prepend(newTitel);
    }

    if(elem.id == "privateColor"){
        if(document.getElementById("embedPreviewTitel") != "" || document.getElementById("embedPreviewMessage") != ""){
             embedPreview.style.borderLeft = "4px solid "+ elem.value;
        }
    }

    //change message in preview
    if(elem.id == "privateMessage"){
        if(document.contains(document.getElementById("embedPreviewMessage"))){
            document.getElementById("embedPreviewMessage").remove();
        }
        let newMessage = document.createElement("p");
        newMessage.setAttribute("id","embedPreviewMessage");
        newMessage.innerHTML = elem.value;
        embedPreviewBody.appendChild(newMessage);
    }
}