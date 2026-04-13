const openTab = (event, tabName) => {
  
  const tabContainer = event.currentTarget.closest('.tab-container');

  const localTabLinks = tabContainer.querySelectorAll(".tab-links");
  const localTabContents = tabContainer.querySelectorAll(".tab-contents");

  localTabLinks.forEach(link => link.classList.remove("active-link"));
  localTabContents.forEach(content => content.classList.remove("active-tab"));

  event.currentTarget.classList.add("active-link");
  document.getElementById(tabName).classList.add("active-tab");
};