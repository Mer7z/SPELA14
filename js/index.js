const link = $(".nav-link");
const toggleButton = $(".toggle-button");
const nav = $(".nav-bottom");
const homePageMenu = $("#home-page");
const orderPageMenu = $("#order-page");

toggleButton.on("click", (e) =>{
  e.preventDefault();
  $(nav).toggleClass("active");
})
