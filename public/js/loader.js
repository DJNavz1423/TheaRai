window.addEventListener("load", function(){
  const loader = this.document.querySelector(".loader-wrapper");

  setTimeout(() => {
    loader.style.transition = "opacity 600ms ease";
    loader.style.opacity = "0";

    setTimeout(() => {
      loader.remove();
    }, 600);
  }, 600);
});