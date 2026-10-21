document.addEventListener("DOMContentLoaded", () => {
  const savedTheme = localStorage.getItem("theme");
  const themeBtn = document.getElementById("theamBtn") || document.getElementById("themeBtn");
  if (savedTheme === "dark") {
    document.body.classList.add("dark-mode");
    if (themeBtn) themeBtn.textContent = "Light Mode";
  }

  if (themeBtn) {
    themeBtn.addEventListener("click", () => {
      const isDark = document.body.classList.toggle("dark-mode");
      themeBtn.textContent = isDark ? "Light Mode" : "Dark Mode";
      localStorage.setItem("theme", isDark ? "dark" : "light");
    });
  }

  const notif = document.getElementById("notification");
  const closeNotif = document.getElementById("closeBtn");
  if (closeNotif && notif) {
    closeNotif.addEventListener("click", () => {
      notif.style.display = "none";
    });
  }

  const hamburger = document.getElementById("hamburger");
  const navLinks = document.getElementById("navLinks");
  if (hamburger && navLinks) {
    hamburger.addEventListener("click", () => {
      navLinks.classList.toggle("show");
    });
  }

  const modal = document.getElementById("myModal");
  const openModal = document.getElementById("openModeBtn") || document.getElementById("openModalBtn");
  const closeModal = document.querySelector(".close-modal");
  if (openModal && modal) {
    openModal.addEventListener("click", () => {
      modal.style.display = "block";
    });
  }
  if (closeModal && modal) {
    closeModal.addEventListener("click", () => {
      modal.style.display = "none";
    });
  }
  window.addEventListener("click", (e) => {
    if (e.target === modal) modal.style.display = "none";
  });

  const slides = document.querySelectorAll(".slide");
  const nextSlide = document.getElementById("nextSlide");
  const prevSlide = document.getElementById("prevSlide");
  let currentSlide = 0;
  if (slides.length) {
    if (nextSlide) {
      nextSlide.addEventListener("click", () => {
        slides[currentSlide].classList.remove("active");
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add("active");
      });
    }
    if (prevSlide) {
      prevSlide.addEventListener("click", () => {
        slides[currentSlide].classList.remove("active");
        currentSlide = (currentSlide - 1 + slides.length) % slides.length;
        slides[currentSlide].classList.add("active");
      });
    }
  }

  document.querySelectorAll(".faq-question").forEach((btn) => {
    btn.addEventListener("click", function () {
      const ans = this.nextElementSibling;
      if (ans) ans.classList.toggle("show");
    });
  });

  const changeBtn = document.getElementById("changeBtn");
  const welcomeHeading = document.querySelector("main section h2");
  if (changeBtn && welcomeHeading) {
    changeBtn.addEventListener("click", () => {
      welcomeHeading.textContent =
        welcomeHeading.textContent === "Welcome to StudentHub"
          ? "Welcome to CHARUSAT StudentHub!"
          : "Welcome to StudentHub";
    });
  }
});