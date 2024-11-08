const menuToggle = document.querySelector(".menu-toggle");
const navbar = document.querySelector(".navbar");

menuToggle.addEventListener("click", () => {
  navbar.classList.toggle("active");
});

const signup = document.getElementById("signup");
const login = document.getElementById("login");
const getStarted = document.getElementById("getStarted");

signup.addEventListener("click", () => {
  window.location.href = "../Register/Registrationpage.html";
});

login.addEventListener("click", () => {
  window.location.href = "../Login/Login page.html";
});

getStarted.addEventListener("click", () => {
  window.location.href = "../Register/Registrationpage.html";
});
