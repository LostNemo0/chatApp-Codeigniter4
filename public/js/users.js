const searchBar = document.querySelector(".search input"),
searchIcon = document.querySelector(".search button"),
usersList = document.querySelector(".users-list");

searchIcon.onclick = ()=>{
  searchBar.classList.toggle("show");
  searchIcon.classList.toggle("active");
  searchBar.focus();
  if(searchBar.classList.contains("active")){
    searchBar.value = "";
    searchBar.classList.remove("active");
  }
}

searchBar.onkeyup = ()=>{
  let searchTerm = searchBar.value;
  if(searchTerm != ""){
    searchBar.classList.add("active");
  }else{
    searchBar.classList.remove("active");
  }

  $.ajax({
      url: '/search',
      type: 'post',
      data: {'searchBar': searchTerm},
      success: function(response){
          // Perform operation on return value
          if(response){
            usersList.innerHTML = response;
          }
          
      }
  });
}

function fetchdata(){
  $.ajax({
      url: '/chat-users',
      type: 'get',
      success: function(data){
          // Perform operation on return value
          if(data){
            if(!searchBar.classList.contains("active")){
              usersList.innerHTML = data;
            }
          }
         
      },
      complete:function(data){
          setTimeout(fetchdata,500);
      }
  });
}

$(document).ready(function(){
  setTimeout(fetchdata,500);
});

