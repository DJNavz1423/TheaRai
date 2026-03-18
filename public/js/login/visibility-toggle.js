document.addEventListener('DOMContentLoaded',function(){
  const passwordInput = document.getElementById('password');
  const toggleBtn = document.getElementById('toggle-btn');
  const visibilityOn = document.getElementById('visibility-on');
  const visibilityOff = document.getElementById('visibility-off');

  toggleBtn.addEventListener('click', function(){
    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
    passwordInput.setAttribute('type', type);

    if(type === 'text'){
      visibilityOn.style.display = 'none';
      visibilityOff.style.display = 'flex';
    } else{
      visibilityOn.style.display = 'flex';
      visibilityOff.style.display = 'none';
    }
  });
});