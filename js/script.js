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

  const regForm = document.getElementById("registrationForm");
  if (regForm) {
    const fn = document.getElementById("fullname");
    const em = document.getElementById("email");
    const mb = document.getElementById("mobile");
    const pw = document.getElementById("password");
    const cp = document.getElementById("confirm_password");
    const dp = document.getElementById("department");
    const yr = document.getElementById("year");
    const tm = document.getElementById("terms");
    const gg = document.getElementById("gender-group");
    const tg = document.getElementById("terms-group");

    const rName = /^[A-Za-z\s]{3,50}$/;
    const rEmail = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const rMobile = /^[6-9]\d{9}$/;
    const rCourse = /^(cs|it|ece|mech|business|arts)$/;
    const rYear = /^(1|2|3|4|graduate)$/;
    const rPass = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

    const meterWrap = document.createElement("div");
    meterWrap.style.cssText = "width:220px;height:6px;background:#e2e8f0;border-radius:3px;margin-top:6px;overflow:hidden;";
    const meterBar = document.createElement("div");
    meterBar.style.cssText = "height:100%;width:0%;transition:all 0.3s ease;";
    meterWrap.appendChild(meterBar);
    const meterLabel = document.createElement("small");
    meterLabel.style.cssText = "display:block;font-size:12px;margin-top:4px;";
    meterLabel.textContent = "Password Strength: None";
    pw.parentElement.appendChild(meterWrap);
    pw.parentElement.appendChild(meterLabel);

    function setErr(el, msg) {
      clrErr(el);
      if (el.tagName === "INPUT" || el.tagName === "SELECT") el.style.borderColor = "#ef4444";
      const s = document.createElement("span");
      s.className = "js-err";
      s.textContent = msg;
      s.style.cssText = "color:#ef4444;font-size:12px;display:block;margin-top:3px;";
      el.parentElement.appendChild(s);
    }

    function clrErr(el) {
      if (el.tagName === "INPUT" || el.tagName === "SELECT") el.style.borderColor = "";
      const ex = el.parentElement.querySelector(".js-err");
      if (ex) ex.remove();
    }

    function checkStrength(v) {
      let score = 0;
      if (v.length >= 8) score++;
      if (/[A-Z]/.test(v)) score++;
      if (/[a-z]/.test(v)) score++;
      if (/\d/.test(v)) score++;
      if (/[@$!%*?&]/.test(v)) score++;
      if (!v) {
        meterBar.style.width = "0%";
        meterBar.style.backgroundColor = "transparent";
        meterLabel.textContent = "Password Strength: None";
        meterLabel.style.color = "";
      } else if (score <= 2) {
        meterBar.style.width = "33%";
        meterBar.style.backgroundColor = "#ef4444";
        meterLabel.textContent = "Password Strength: Weak";
        meterLabel.style.color = "#ef4444";
      } else if (score <= 4) {
        meterBar.style.width = "66%";
        meterBar.style.backgroundColor = "#f59e0b";
        meterLabel.textContent = "Password Strength: Medium";
        meterLabel.style.color = "#f59e0b";
      } else {
        meterBar.style.width = "100%";
        meterBar.style.backgroundColor = "#10b981";
        meterLabel.textContent = "Password Strength: Strong";
        meterLabel.style.color = "#10b981";
      }
    }

    function valName() {
      const v = fn.value.trim();
      if (!v) { setErr(fn, "Full name is required."); return false; }
      if (!rName.test(v)) { setErr(fn, "Name must be 3-50 alphabetic characters."); return false; }
      clrErr(fn); return true;
    }

    function valEmail() {
      const v = em.value.trim();
      if (!v) { setErr(em, "Email address is required."); return false; }
      if (!rEmail.test(v)) { setErr(em, "Enter a valid email (e.g. name@university.edu)."); return false; }
      clrErr(em); return true;
    }

    function valMobile() {
      const v = mb.value.trim();
      if (!v) { setErr(mb, "Mobile number is required."); return false; }
      if (!rMobile.test(v)) { setErr(mb, "Enter a valid 10-digit number starting with 6-9."); return false; }
      clrErr(mb); return true;
    }

    function valPass() {
      const v = pw.value;
      checkStrength(v);
      if (!v) { setErr(pw, "Password is required."); return false; }
      if (!rPass.test(v)) { setErr(pw, "Min 8 chars, 1 uppercase, 1 lowercase, 1 number, 1 special (@$!%*?&)."); return false; }
      clrErr(pw); return true;
    }

    function valConf() {
      const v = cp.value;
      if (!v) { setErr(cp, "Please confirm your password."); return false; }
      if (v !== pw.value) { setErr(cp, "Passwords do not match."); return false; }
      clrErr(cp); return true;
    }

    function valCourse() {
      if (!rCourse.test(dp.value)) { setErr(dp, "Please select your department / course."); return false; }
      clrErr(dp); return true;
    }

    function valYear() {
      if (!rYear.test(yr.value)) { setErr(yr, "Please select your year of study."); return false; }
      clrErr(yr); return true;
    }

    function valGender() {
      const checked = document.querySelector('input[name="gender"]:checked');
      if (!checked) { setErr(gg, "Please select your gender."); return false; }
      clrErr(gg); return true;
    }

    function valTerms() {
      if (!tm.checked) { setErr(tg, "You must accept the terms and conditions."); return false; }
      clrErr(tg); return true;
    }

    fn.addEventListener("input", valName);
    em.addEventListener("input", valEmail);
    mb.addEventListener("input", valMobile);
    pw.addEventListener("input", () => { checkStrength(pw.value); valPass(); });
    cp.addEventListener("input", valConf);
    dp.addEventListener("change", valCourse);
    yr.addEventListener("change", valYear);
    tm.addEventListener("change", valTerms);
    document.querySelectorAll('input[name="gender"]').forEach((r) => r.addEventListener("change", valGender));

    regForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const valid = valName() & valEmail() & valMobile() & valGender() & valPass() & valConf() & valCourse() & valYear() & valTerms();
      if (valid) {
        const users = JSON.parse(localStorage.getItem("studentHub_users") || "[]");
        users.push({
          fullName: fn.value.trim(),
          email: em.value.trim().toLowerCase(),
          mobile: mb.value.trim(),
          gender: document.querySelector('input[name="gender"]:checked').value,
          department: dp.value,
          year: yr.value
        });
        localStorage.setItem("studentHub_users", JSON.stringify(users));
        alert("Registration successful!");
        regForm.reset();
        checkStrength("");
      }
    });

    regForm.addEventListener("reset", () => {
      document.querySelectorAll(".js-err").forEach((el) => el.remove());
      [fn, em, mb, pw, cp, dp, yr].forEach((el) => { el.style.borderColor = ""; });
      checkStrength("");
    });
  }
});