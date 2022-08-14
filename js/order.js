const productSelectButtons = $(".select-button")
const productsInfo = $(".product-info-container")
const warningText = $(".product-info-warning")
selectProduct();
$(productSelectButtons).click(function (e) { 
  $(e.target).toggleClass("button-selected");
  console.log(e.target)
  selectProduct()
});

function selectProduct(){
  for(var i = 0; i<productSelectButtons.length; i++){
    if($(productSelectButtons[i]).hasClass("button-selected")){
      $(productsInfo[i]).removeClass("hidden")
    } else{
      $(productsInfo[i]).addClass("hidden")
    }
  }
  var hasHidden = productsInfo.length
  var numProducts = 0
  for(var i = 0; i<productsInfo.length; i++){
    if($(productsInfo[i]).hasClass("hidden")){
      numProducts++;
    }
  }
  console.log(numProducts, hasHidden);
  if(numProducts === hasHidden){
    $(warningText).removeClass("hidden");
  } else{
    $(warningText).addClass("hidden");
  }
}