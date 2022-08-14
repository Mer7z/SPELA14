const pass1 = $('#password1')
const pass2 = $('#password2')
const passWarn = $('#password-warning')
const regsBtn = $('#reg-submit-btn')
var pw1 = ''
var isLong = false

function checkPassword(){
  if(isLong){
    if(pw1 === $(pass2).val()){
      $(passWarn).html("")
      $(regsBtn).attr("disabled", false)
    } else{
      $(passWarn).html("Las contraseñas no coinciden")
      $(regsBtn).attr("disabled", true)
    }
  }
}

function savePass(){
  pw1 = $(pass1).val()
}

function checkPassLength(){
  var pw = $(pass1).val()
  if(pw.length >= 8){
    isLong = true
    $(passWarn).html("")
  } else{
    isLong = false
    $(passWarn).html("La contraseña debe tener almenos 8 caracteres")
    $(regsBtn).attr("disabled", true)
  }
}