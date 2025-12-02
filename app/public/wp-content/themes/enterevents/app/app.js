import Swiper from "swiper";

const swiper = new Swiper(".swiper", {
  slidesPerView: 2,
  speed: 500,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  autoplay: {
    delay: 2000,
  },
});

const hamburger = document.querySelector(".hamburger-menu");
const navMenu = document.querySelector(".nav-menu");

hamburger.addEventListener("click", () => {
  hamburger.classList.toggle("active");
  navMenu.classList.toggle("active");
});
