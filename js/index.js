const link = $(".nav-link");
const toggleButton = $(".toggle-button");
const nav = $(".nav-bottom");
const homePageMenu = $("#home-page");
const orderPageMenu = $("#order-page");


/*
link.on("click", (e) =>{
  console.log(e);
  var target = e.target;
  checkClicked(target);
});
*/
toggleButton.on("click", (e) =>{
  $(nav).toggleClass("active");
})
/*
function checkClicked(target){
  var menu;
  for(var i = 0; i<link.length; i++){
    if(link[i] === target){
      $(link[i]).addClass("selected");
      menu = i;
    } else{
      $(link[i]).removeClass("selected");
    }
  }
  showMenu(menu);
}
function showMenu(menu){
  switch(menu){
    case 0:
      $(homePageMenu).removeClass("hidden");
      $(orderPageMenu).addClass("hidden");
      break;
    case 1:
      $(orderPageMenu).removeClass("hidden");
      $(homePageMenu).addClass("hidden");
  }
  for(var i = 0; i<link.length; i++){
    if(link[i] === link[menu]){
      $(link[i]).addClass("selected");
      menu = i;
    } else{
      $(link[i]).removeClass("selected");
    }
  }

}
*/
