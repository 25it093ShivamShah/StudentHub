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
        const fb = document.getElementById('reg-feedback');
        const submitBtn = regForm.querySelector('button[type="submit"]');
        if (fb) fb.innerHTML = '<span style="color:#555;">Submitting&hellip;</span>';
        if (submitBtn) submitBtn.disabled = true;
        const fd = new FormData(regForm);
        fetch('/STUDENTHUB/php/process_register.php', { method: 'POST', body: fd })
          .then(r => r.json())
          .then(d => {
            if (!fb) return;
            if (d.success) {
              fb.innerHTML = '<span style="color:green;font-weight:bold;">' + d.message + ' Redirecting to login in <span id="reg-countdown">3</span>s...</span>';
              regForm.reset();
              checkStrength('');
              // Auto-redirect to login
              let secs = 3;
              const countEl = document.getElementById('reg-countdown');
              const timer = setInterval(() => {
                secs--;
                if (countEl) countEl.textContent = secs;
                if (secs <= 0) {
                  clearInterval(timer);
                  window.location.href = d.redirect || '/STUDENTHUB/pages/login.html';
                }
              }, 1000);
            } else {
              const errs = d.errors || [d.error || 'Registration failed.'];
              fb.innerHTML = '<span style="color:red;">&#10006; ' + errs.join('<br>&#10006; ') + '</span>';
              if (submitBtn) submitBtn.disabled = false;
            }
          })
          .catch(() => {
            if (fb) fb.innerHTML = '<span style="color:red;">&#10006; Server error. Is XAMPP running?</span>';
            if (submitBtn) submitBtn.disabled = false;
          });
      }
    });

    regForm.addEventListener("reset", () => {
      document.querySelectorAll(".js-err").forEach((el) => el.remove());
      [fn, em, mb, pw, cp, dp, yr].forEach((el) => { el.style.borderColor = ""; });
      checkStrength("");
    });
  }

  function fetchData(endpoint) {
    if (endpoint === 'events.json') return fetch('/STUDENTHUB/php/api/events.php').then(r => r.json()).then(d => d.data || []);
    const rel = window.location.pathname.includes("/pages/") ? `../data/${endpoint}` : `./data/${endpoint}`;
    return fetch(rel).catch(() => fetch(`/STUDENTHUB/data/${endpoint}`)).then((r) => r.json());
  }

  const evSearch = document.getElementById("event-search");
  const evCat = document.getElementById("event-category");
  if (evSearch && evCat) {
    let allEvents = [];
    let curPage = 1;
    const perPage = 4;
    let sortMode = "date-asc";

    const filterForm = evSearch.closest("form");
    if (filterForm) {
      filterForm.addEventListener("submit", (e) => {
        e.preventDefault();
        curPage = 1;
        renderEvents();
      });

      const sortWrap = document.createElement("div");
      sortWrap.style.cssText = "margin-top:10px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;";
      sortWrap.innerHTML = `
        <label for="event-sort"><strong>Sort By:</strong></label>
        <select id="event-sort" style="padding:6px;border-radius:4px;border:1px solid #cbd5e1;">
          <option value="date-asc">Date (Earliest First)</option>
          <option value="date-desc">Date (Latest First)</option>
          <option value="title-asc">Title (A - Z)</option>
          <option value="seats-desc">Available Seats (High - Low)</option>
        </select>
      `;
      filterForm.appendChild(sortWrap);
      const sortSel = document.getElementById("event-sort");
      sortSel.addEventListener("change", (e) => {
        sortMode = e.target.value;
        curPage = 1;
        renderEvents();
      });
    }

    const cardsSection = Array.from(document.querySelectorAll("main section")).find((s) => {
      const h2 = s.querySelector("h2");
      return h2 && h2.textContent.includes("Upcoming Campus Events");
    });

    const tableTbody = document.querySelector("table tbody");

    let pageNav = document.createElement("div");
    pageNav.className = "events-pagination";
    pageNav.style.cssText = "display:flex;justify-content:center;gap:10px;align-items:center;margin:20px 0;";
    if (cardsSection) cardsSection.appendChild(pageNav);

    function renderEvents() {
      const query = evSearch.value.trim().toLowerCase();
      const cat = evCat.value;

      let filtered = allEvents
        .filter((ev) => {
          const matchCat = cat === "all" || !cat || ev.category.toLowerCase() === cat.toLowerCase();
          const matchQ =
            !query ||
            ev.title.toLowerCase().includes(query) ||
            ev.description.toLowerCase().includes(query) ||
            ev.venue.toLowerCase().includes(query) ||
            ev.speaker.toLowerCase().includes(query);
          return matchCat && matchQ;
        })
        .sort((a, b) => {
          if (sortMode === "date-asc") return new Date(a.date) - new Date(b.date);
          if (sortMode === "date-desc") return new Date(b.date) - new Date(a.date);
          if (sortMode === "title-asc") return a.title.localeCompare(b.title);
          if (sortMode === "seats-desc") return b.seats - a.seats;
          return 0;
        });

      const totalPages = Math.ceil(filtered.length / perPage) || 1;
      if (curPage > totalPages) curPage = totalPages;
      const start = (curPage - 1) * perPage;
      const pageItems = filtered.slice(start, start + perPage);

      if (cardsSection) {
        const oldArticles = cardsSection.querySelectorAll("article, hr.ev-sep, .ev-empty");
        oldArticles.forEach((el) => el.remove());

        if (pageItems.length === 0) {
          const empty = document.createElement("p");
          empty.className = "ev-empty";
          empty.textContent = "No campus events matched your criteria.";
          empty.style.color = "#64748b";
          cardsSection.insertBefore(empty, pageNav);
        } else {
          pageItems.forEach((ev, idx) => {
            const art = document.createElement("article");
            art.innerHTML = `
              <h3>${ev.title}</h3>
              <p><strong>Date:</strong> ${ev.date} (${ev.time}) | <strong>Location:</strong> ${ev.venue}</p>
              <p>${ev.description}</p>
              <p><strong>Speaker / Host:</strong> ${ev.speaker}</p>
              <p><strong>Available Seats:</strong> ${ev.seats} seats remaining</p>
              <button type="button" class="reg-btn" style="padding:8px 14px;background:#1a365d;color:#fff;border:none;border-radius:4px;cursor:pointer;">Register for Event</button>
            `;
            cardsSection.insertBefore(art, pageNav);
            if (idx < pageItems.length - 1) {
              const sep = document.createElement("hr");
              sep.className = "ev-sep";
              cardsSection.insertBefore(sep, pageNav);
            }
          });
        }

        pageNav.innerHTML = `
          <button id="ev-prev" ${curPage === 1 ? "disabled" : ""} style="padding:6px 14px;cursor:${curPage === 1 ? "not-allowed" : "pointer"};">❮ Prev</button>
          <span>Page <strong>${curPage}</strong> of <strong>${totalPages}</strong> (${filtered.length} total)</span>
          <button id="ev-next" ${curPage === totalPages ? "disabled" : ""} style="padding:6px 14px;cursor:${curPage === totalPages ? "not-allowed" : "pointer"};">Next ❯</button>
        `;
        document.getElementById("ev-prev")?.addEventListener("click", () => {
          if (curPage > 1) { curPage--; renderEvents(); }
        });
        document.getElementById("ev-next")?.addEventListener("click", () => {
          if (curPage < totalPages) { curPage++; renderEvents(); }
        });
      }

      if (tableTbody) {
        tableTbody.innerHTML = pageItems
          .map(
            (ev) => `
          <tr>
            <td>${ev.title}</td>
            <td style="text-transform:capitalize;">${ev.category}</td>
            <td>${ev.date} (${ev.time})</td>
            <td>${ev.venue}</td>
            <td>${ev.fee}</td>
          </tr>
        `
          )
          .join("");
      }
    }

    evSearch.addEventListener("input", () => { curPage = 1; renderEvents(); });
    evCat.addEventListener("change", () => { curPage = 1; renderEvents(); });

    fetchData("events.json")
      .then((data) => {
        allEvents = data;
        renderEvents();
      })
      .catch((e) => console.error(e));
  }

  const faqSearch = document.getElementById("faq-search");
  if (faqSearch) {
    let allFaqs = [];
    let curFaqPage = 1;
    const perPageFaq = 5;
    let selectedCat = "all";
    let sortModeFaq = "default";

    const faqForm = faqSearch.closest("form");
    if (faqForm) {
      faqForm.addEventListener("submit", (e) => {
        e.preventDefault();
        curFaqPage = 1;
        renderFaqs();
      });

      const controlsDiv = document.createElement("div");
      controlsDiv.style.cssText = "margin-top:10px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;";
      controlsDiv.innerHTML = `
        <label for="faq-cat"><strong>Category:</strong></label>
        <select id="faq-cat" style="padding:6px;border-radius:4px;border:1px solid #cbd5e1;">
          <option value="all">All Topics</option>
          <option value="events">Events</option>
          <option value="account">Account & Login</option>
          <option value="academic">Academic</option>
          <option value="general">General</option>
        </select>
        <label for="faq-sort"><strong>Sort:</strong></label>
        <select id="faq-sort" style="padding:6px;border-radius:4px;border:1px solid #cbd5e1;">
          <option value="default">Default Order</option>
          <option value="asc">Question (A - Z)</option>
          <option value="desc">Question (Z - A)</option>
        </select>
      `;
      faqForm.appendChild(controlsDiv);

      document.getElementById("faq-cat").addEventListener("change", (e) => {
        selectedCat = e.target.value;
        curFaqPage = 1;
        renderFaqs();
      });
      document.getElementById("faq-sort").addEventListener("change", (e) => {
        sortModeFaq = e.target.value;
        curFaqPage = 1;
        renderFaqs();
      });
    }

    const faqSection = Array.from(document.querySelectorAll("main section")).find((s) => {
      const h2 = s.querySelector("h2");
      return h2 && h2.textContent.includes("Frequently Asked Questions");
    });

    let faqNav = document.createElement("div");
    faqNav.style.cssText = "display:flex;justify-content:center;gap:10px;align-items:center;margin:20px 0;";
    if (faqSection) faqSection.appendChild(faqNav);

    function renderFaqs() {
      const q = faqSearch.value.trim().toLowerCase();
      let filtered = allFaqs
        .filter((f) => {
          const matchCat = selectedCat === "all" || f.category === selectedCat;
          const matchQ = !q || f.question.toLowerCase().includes(q) || f.answer.toLowerCase().includes(q);
          return matchCat && matchQ;
        })
        .sort((a, b) => {
          if (sortModeFaq === "asc") return a.question.localeCompare(b.question);
          if (sortModeFaq === "desc") return b.question.localeCompare(a.question);
          return a.id - b.id;
        });

      const totalPages = Math.ceil(filtered.length / perPageFaq) || 1;
      if (curFaqPage > totalPages) curFaqPage = totalPages;
      const start = (curFaqPage - 1) * perPageFaq;
      const pageItems = filtered.slice(start, start + perPageFaq);

      if (faqSection) {
        const oldItems = faqSection.querySelectorAll("article, br.faq-br, .faq-empty");
        oldItems.forEach((el) => el.remove());

        if (pageItems.length === 0) {
          const empty = document.createElement("p");
          empty.className = "faq-empty";
          empty.textContent = "No FAQs found matching your search.";
          empty.style.color = "#64748b";
          faqSection.insertBefore(empty, faqNav);
        } else {
          pageItems.forEach((f) => {
            const art = document.createElement("article");
            art.innerHTML = `<h3>${f.question}</h3><p>${f.answer}</p>`;
            faqSection.insertBefore(art, faqNav);
            const br = document.createElement("br");
            br.className = "faq-br";
            faqSection.insertBefore(br, faqNav);
          });
        }

        faqNav.innerHTML = `
          <button id="faq-prev" ${curFaqPage === 1 ? "disabled" : ""} style="padding:6px 14px;cursor:${curFaqPage === 1 ? "not-allowed" : "pointer"};">❮ Prev</button>
          <span>Page <strong>${curFaqPage}</strong> of <strong>${totalPages}</strong> (${filtered.length} FAQs)</span>
          <button id="faq-next" ${curFaqPage === totalPages ? "disabled" : ""} style="padding:6px 14px;cursor:${curFaqPage === totalPages ? "not-allowed" : "pointer"};">Next ❯</button>
        `;
        document.getElementById("faq-prev")?.addEventListener("click", () => {
          if (curFaqPage > 1) { curFaqPage--; renderFaqs(); }
        });
        document.getElementById("faq-next")?.addEventListener("click", () => {
          if (curFaqPage < totalPages) { curFaqPage++; renderFaqs(); }
        });
      }
    }

    faqSearch.addEventListener("input", () => { curFaqPage = 1; renderFaqs(); });

    fetchData("faqs.json")
      .then((data) => {
        allFaqs = data;
        renderFaqs();
      })
      .catch((e) => console.error(e));
  }

  const adminRegSection = Array.from(document.querySelectorAll("main section")).find((s) => {
    const h3 = s.querySelector("h3");
    return h3 && h3.textContent.includes("Pending Student Registrations");
  });

  if (adminRegSection) {
    const adminTbody = adminRegSection.querySelector("table tbody");
    if (adminTbody) {
      let allStudents = [];
      let curStuPage = 1;
      const perPageStu = 5;
      let stuFilterDept = "all";
      let stuSearchQuery = "";
      let stuSort = "date-desc";

      const filterBar = document.createElement("div");
      filterBar.style.cssText = "margin-bottom:14px;display:flex;gap:12px;align-items:center;flex-wrap:wrap;";
      filterBar.innerHTML = `
        <input type="text" id="stu-search" placeholder="Search student name, ID, email..." style="padding:6px 10px;border:1px solid #cbd5e1;border-radius:4px;min-width:220px;">
        <select id="stu-dept" style="padding:6px;border:1px solid #cbd5e1;border-radius:4px;">
          <option value="all">All Departments</option>
          <option value="Computer Science">Computer Science</option>
          <option value="Information Technology">Information Technology</option>
          <option value="Electrical Engineering">Electrical Engineering</option>
          <option value="Mechanical Engineering">Mechanical Engineering</option>
          <option value="Electronics & Communication">Electronics & Communication</option>
          <option value="Business Administration">Business Administration</option>
        </select>
        <select id="stu-sort" style="padding:6px;border:1px solid #cbd5e1;border-radius:4px;">
          <option value="date-desc">Newest First</option>
          <option value="date-asc">Oldest First</option>
          <option value="name-asc">Name (A - Z)</option>
        </select>
      `;
      adminRegSection.querySelector("table").before(filterBar);

      const stuNav = document.createElement("div");
      stuNav.style.cssText = "display:flex;justify-content:center;gap:10px;align-items:center;margin:15px 0;";
      adminRegSection.querySelector("table").after(stuNav);

      function renderStudents() {
        let filtered = allStudents
          .filter((s) => {
            const matchDept = stuFilterDept === "all" || s.department === stuFilterDept;
            const q = stuSearchQuery.toLowerCase();
            const matchQ =
              !q ||
              s.fullName.toLowerCase().includes(q) ||
              s.id.toLowerCase().includes(q) ||
              s.email.toLowerCase().includes(q);
            return matchDept && matchQ;
          })
          .sort((a, b) => {
            if (stuSort === "date-desc") return new Date(b.dateRegistered) - new Date(a.dateRegistered);
            if (stuSort === "date-asc") return new Date(a.dateRegistered) - new Date(b.dateRegistered);
            if (stuSort === "name-asc") return a.fullName.localeCompare(b.fullName);
            return 0;
          });

        const totalPages = Math.ceil(filtered.length / perPageStu) || 1;
        if (curStuPage > totalPages) curStuPage = totalPages;
        const start = (curStuPage - 1) * perPageStu;
        const pageItems = filtered.slice(start, start + perPageStu);

        adminTbody.innerHTML = pageItems
          .map(
            (s) => `
          <tr>
            <td>${s.id}</td>
            <td>${s.fullName}</td>
            <td>${s.email}</td>
            <td>${s.department}</td>
            <td>${s.dateRegistered}</td>
            <td>
              <button type="button" class="btn-approve" data-id="${s.id}" style="padding:4px 8px;cursor:pointer;">${s.status === "Approved" ? "Enrolled" : "Approve"}</button>
              <button type="button" class="btn-reject" data-id="${s.id}" style="padding:4px 8px;cursor:pointer;">Reject</button>
            </td>
          </tr>
        `
          )
          .join("");

        stuNav.innerHTML = `
          <button id="stu-prev" ${curStuPage === 1 ? "disabled" : ""} style="padding:4px 10px;cursor:${curStuPage === 1 ? "not-allowed" : "pointer"};">❮ Prev</button>
          <span>Page <strong>${curStuPage}</strong> of <strong>${totalPages}</strong> (${filtered.length} students)</span>
          <button id="stu-next" ${curStuPage === totalPages ? "disabled" : ""} style="padding:4px 10px;cursor:${curStuPage === totalPages ? "not-allowed" : "pointer"};">Next ❯</button>
        `;

        document.getElementById("stu-prev")?.addEventListener("click", () => {
          if (curStuPage > 1) { curStuPage--; renderStudents(); }
        });
        document.getElementById("stu-next")?.addEventListener("click", () => {
          if (curStuPage < totalPages) { curStuPage++; renderStudents(); }
        });
      }

      document.getElementById("stu-search").addEventListener("input", (e) => {
        stuSearchQuery = e.target.value.trim();
        curStuPage = 1;
        renderStudents();
      });
      document.getElementById("stu-dept").addEventListener("change", (e) => {
        stuFilterDept = e.target.value;
        curStuPage = 1;
        renderStudents();
      });
      document.getElementById("stu-sort").addEventListener("change", (e) => {
        stuSort = e.target.value;
        curStuPage = 1;
        renderStudents();
      });

      adminTbody.addEventListener("click", (e) => {
        const btn = e.target;
        if (btn.classList.contains("btn-approve")) {
          const s = allStudents.find((st) => st.id === btn.dataset.id);
          if (s) { s.status = "Approved"; renderStudents(); }
        } else if (btn.classList.contains("btn-reject")) {
          allStudents = allStudents.filter((st) => st.id !== btn.dataset.id);
          renderStudents();
        }
      });

      fetchData("students.json")
        .then((data) => {
          allStudents = data;
          renderStudents();
        })
        .catch((e) => console.error(e));
    }
  }
});