const form = document.querySelector(".typing-area"),
incoming_id = form.querySelector(".incoming_id").value,
inputField = form.querySelector(".input-field"),
sendBtn = form.querySelector("button"),
chatBox = document.querySelector(".chat-box");

form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}

sendBtn.onclick = ()=>{
    
    var msg = $(".input-field").val();
    $.ajax({
        url: '/insert-chat',
        type: 'post',
        data: {'id': incoming_id,
            'msg' : msg
        },
        success: function(response){
            // Perform operation on return value
            console.log(response);
            if(response){
                inputField.value = "";
                scrollToBottom();
            }
           
        }
    });
}

chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}

function fetchdata(){
    var id = $(".incoming_id").val();
    $.ajax({
        url: '/get-chat',
        type: 'post',
        data: {'id': id},
        success: function(response){
            // Perform operation on return value
            if(response){
                if(!chatBox.classList.contains("active")){
                   scrollToBottom();
                }
            }
            chatBox.innerHTML = response;
        },
        complete:function(data){
            setTimeout(fetchdata,500);
        }
    });
  }
  $(document).ready(function(){
    setTimeout(fetchdata,500);
  });

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  