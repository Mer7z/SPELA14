const productSelectButtons = $(".select-button")
const productsInfo = $(".product-info-container")
const productsInfoInputs = $(".product-info")
const productsInput = $("#order-products-input input")
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
      var inputCont = $(productsInfoInputs[i]).children(".input-container")
      var inputs = $(inputCont).children("input")
      $(inputs).attr('disabled', false)
      $(productsInput[i]).attr('disabled', false)
    } else{
      $(productsInfo[i]).addClass("hidden")
      var inputCont = $(productsInfoInputs[i]).children(".input-container")
      var inputs = $(inputCont).children("input")
      $(inputs).attr('disabled', true)
      $(productsInput[i]).attr('disabled', true)
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
    $("#order-page-submit").attr('disabled', true)
  } else{
    $(warningText).addClass("hidden");
    $("#order-page-submit").attr('disabled', false)
  }
}