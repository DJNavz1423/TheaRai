document.addEventListener('DOMContentLoaded', function(){
  const emailInput = document.getElementById('email');
  const rememberCheckbox = document.getElementById('remember');
  const loginForm = document.querySelector('form');
  
  const savedEmail = localStorage.getItem('remembered_email');
  if(savedEmail){
    emailInput.value = savedEmail;
    rememberCheckbox.checked = true;
  }

  loginForm.addEventListener('submit', function(){
    if(rememberCheckbox.checked){
      localStorage.setItem('remembered_email', emailInput.value);
    }

    else{
      localStorage.removeItem('remembered_email')
    }
  });
});