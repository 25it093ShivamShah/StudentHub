<?php require_once __DIR__ . '/../php/auth_admin.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - AJAX Event &amp; Student Manager</title>
    <link rel="stylesheet" href="/css/style3.css">
    <link rel="icon" type="image/png" href="/images/favicon.png">
</head>
<body>
    <header>
        <img src="/images/logo.png" alt="Studenthub Logo" height="100">
        <h1>StudentHub</h1>
        <p>Your One Stop Platform For Campus Engagement &amp; Events.</p>
    </header>
    <nav>
        <a href="/pages/index.html">Home</a>
        <a href="/pages/admin/">Admin Dashboard</a>
        <a href="/pages/admin/students.php">Manage Students</a>
        <a href="/pages/admin/events.php">Manage Events</a>
        <a href="/pages/ajax_manager.php">AJAX Manager</a>
        <a href="/php/logout.php">Logout (<?= htmlspecialchars($_SESSION['user_name']) ?>)</a>
    </nav>
    <hr>
    <main>
        <section>
            <h2 style="text-decoration: underline; color:#1a365d ;">Practical 13: AJAX / Fetch API Manager</h2>
            <p>All operations below use the <strong>Fetch API</strong> against PHP JSON endpoints &mdash; no full page reload.</p>
            <div id="globalFeedback" style="margin:10px 0; font-weight:bold; min-height:24px;"></div>
        </section>

        <section>
            <h3 style="text-decoration: underline; color:#1a365d ;">Events (API: /php/api/events.php)</h3>

            <details open>
                <summary style="cursor:pointer; font-weight:bold; color:#1a365d; padding:4px 0;">Add / Edit Event (AJAX)</summary>
                <form id="ajaxEventForm" style="margin-top:10px;">
                    <input type="hidden" id="ajaxEvId" name="id" value="">
                    <p><label>Title:<br><input type="text" id="ajaxEvTitle" name="title" required placeholder="Event Title"></label></p>
                    <p><label>Category:<br>
                        <select id="ajaxEvCat" name="category" required>
                            <option value="">--Select--</option>
                            <option value="workshop">Workshop</option>
                            <option value="hackathon">Hackathon</option>
                            <option value="seminar">Seminar</option>
                            <option value="cultural">Cultural</option>
                        </select>
                    </label></p>
                    <p><label>Description:<br><textarea id="ajaxEvDesc" name="description" rows="2" required></textarea></label></p>
                    <p><label>Date:<br><input type="date" id="ajaxEvDate" name="date" required></label></p>
                    <p><label>Time:<br><input type="time" id="ajaxEvTime" name="time" required></label></p>
                    <p><label>Venue:<br><input type="text" id="ajaxEvVenue" name="venue" required></label></p>
                    <p><label>Speaker:<br><input type="text" id="ajaxEvSpeaker" name="speaker"></label></p>
                    <p><label>Seats:<br><input type="number" id="ajaxEvSeats" name="seats" value="50" min="1"></label></p>
                    <p><label>Fee:<br><input type="text" id="ajaxEvFee" name="fee" value="Free"></label></p>
                    <p><label>Poster (image, max 2MB):<br><input type="file" id="ajaxEvPoster" name="poster" accept="image/*"></label></p>
                    <p>
                        <button type="submit" id="ajaxEvSubmit">Add Event (AJAX)</button>
                        <button type="button" id="ajaxEvCancel" style="display:none;">Cancel Edit</button>
                    </p>
                </form>
            </details>

            <h4>Events List</h4>
            <div style="margin-bottom:10px; display:flex; gap:8px; flex-wrap:wrap;">
                <input type="text" id="evSearch" placeholder="Search events..." style="padding:6px; border:1px solid #cbd5e1; border-radius:4px;">
                <select id="evCatFilter" style="padding:6px; border:1px solid #cbd5e1; border-radius:4px;">
                    <option value="all">All Categories</option>
                    <option value="workshop">Workshop</option>
                    <option value="hackathon">Hackathon</option>
                    <option value="seminar">Seminar</option>
                    <option value="cultural">Cultural</option>
                </select>
                <button onclick="loadEvents()">Refresh</button>
            </div>
            <table border="1" cellpadding="6" cellspacing="0" id="evTable">
                <thead>
                    <tr><th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Venue</th><th>Seats</th><th>Actions</th></tr>
                </thead>
                <tbody id="evBody"><tr><td colspan="7">Loading...</td></tr></tbody>
            </table>
        </section>

        <section style="margin-top:30px;">
            <h3 style="text-decoration: underline; color:#1a365d ;">Students (API: /php/api/students.php)</h3>

            <details>
                <summary style="cursor:pointer; font-weight:bold; color:#1a365d; padding:4px 0;">Add Student (AJAX)</summary>
                <form id="ajaxStuForm" style="margin-top:10px;">
                    <input type="hidden" id="ajaxStuId" name="id" value="">
                    <p><label>Full Name:<br><input type="text" id="ajaxStuFn" name="fullname" required placeholder="John Doe"></label></p>
                    <p><label>Email:<br><input type="email" id="ajaxStuEm" name="email" required></label></p>
                    <p><label>Mobile:<br><input type="text" id="ajaxStuMb" name="mobile" required maxlength="10"></label></p>
                    <p><label>Gender:<br>
                        <select id="ajaxStuGn" name="gender" required>
                            <option value="">--Select--</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </label></p>
                    <p><label>Student ID:<br><input type="text" id="ajaxStuSid" name="studentid" required></label></p>
                    <p><label>Department:<br>
                        <select id="ajaxStuDep" name="department" required>
                            <option value="">--Select--</option>
                            <option value="cs">CS & Engineering</option>
                            <option value="it">Info. Technology</option>
                            <option value="ece">Electronics & Comm</option>
                            <option value="mech">Mechanical Engg</option>
                            <option value="business">Business Admin</option>
                            <option value="arts">Arts & Humanities</option>
                        </select>
                    </label></p>
                    <p><label>Year:<br>
                        <select id="ajaxStuYr" name="year" required>
                            <option value="">--Select--</option>
                            <option value="1">1st Year</option>
                            <option value="2">2nd Year</option>
                            <option value="3">3rd Year</option>
                            <option value="4">4th Year</option>
                            <option value="graduate">Graduate</option>
                        </select>
                    </label></p>
                    <p><label>Password <span id="stuPwNote">(required)</span>:<br><input type="password" id="ajaxStuPw" name="password"></label></p>
                    <p>
                        <button type="submit" id="ajaxStuSubmit">Add Student (AJAX)</button>
                        <button type="button" id="ajaxStuCancel" style="display:none;">Cancel Edit</button>
                    </p>
                </form>
            </details>

            <h4>Students List</h4>
            <div style="margin-bottom:10px; display:flex; gap:8px; flex-wrap:wrap;">
                <input type="text" id="stuSearch" placeholder="Search students..." style="padding:6px; border:1px solid #cbd5e1; border-radius:4px;">
                <button onclick="loadStudents()">Refresh</button>
            </div>
            <table border="1" cellpadding="6" cellspacing="0">
                <thead>
                    <tr><th>ID</th><th>Name</th><th>Email</th><th>Dept</th><th>Year</th><th>Actions</th></tr>
                </thead>
                <tbody id="stuBody"><tr><td colspan="6">Loading...</td></tr></tbody>
            </table>
        </section>
    </main>
    <hr>
    <footer><p>&copy; 2026 StudentHub. All Rights Reserved.</p></footer>

    <script>
    const EV_API  = '/php/api/events.php';
    const STU_API = '/php/api/students.php';
    const fb = document.getElementById('globalFeedback');

    function flash(msg, ok=true) {
        fb.innerHTML = `<span style="color:${ok?'green':'red'}">${msg}</span>`;
        setTimeout(() => fb.innerHTML = '', 5000);
    }
    function esc(s) { const d=document.createElement('div'); d.textContent=String(s||''); return d.innerHTML; }

    /* ── EVENTS ─────────────────────────────────────────── */
    function loadEvents() {
        const q   = document.getElementById('evSearch').value;
        const cat = document.getElementById('evCatFilter').value;
        fetch(`${EV_API}?search=${encodeURIComponent(q)}&category=${encodeURIComponent(cat)}`)
            .then(r => r.json())
            .then(d => {
                const body = document.getElementById('evBody');
                body.innerHTML = (d.data||[]).length
                    ? d.data.map(ev => `<tr>
                        <td>${ev.id}</td>
                        <td>${esc(ev.title)}</td>
                        <td style="text-transform:capitalize">${esc(ev.category)}</td>
                        <td>${esc(ev.date)}</td>
                        <td>${esc(ev.venue)}</td>
                        <td>${esc(ev.seats)}</td>
                        <td>
                            <button onclick="editEvent(${ev.id})">Edit</button>
                            <button onclick="deleteEvent(${ev.id},'${esc(ev.title)}')">Delete</button>
                        </td>
                    </tr>`).join('')
                    : '<tr><td colspan="7">No events.</td></tr>';
            })
            .catch(() => flash('Failed to load events.', false));
    }

    document.getElementById('ajaxEventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('ajaxEvId').value;
        const fd = new FormData(this);
        if (id) fd.set('id', id);
        fetch(EV_API, { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                if (d.success) { flash(d.message); this.reset(); resetEvForm(); loadEvents(); }
                else flash(d.error || (d.errors||[]).join(', '), false);
            })
            .catch(() => flash('Request failed.', false));
    });

    function editEvent(id) {
        fetch(`${EV_API}?id=${id}`).then(r=>r.json()).then(d => {
            if (!d.success) return flash('Could not load event.', false);
            const ev = d.data;
            document.getElementById('ajaxEvId').value      = ev.id;
            document.getElementById('ajaxEvTitle').value   = ev.title;
            document.getElementById('ajaxEvCat').value     = ev.category;
            document.getElementById('ajaxEvDesc').value    = ev.description;
            document.getElementById('ajaxEvDate').value    = ev.date;
            document.getElementById('ajaxEvTime').value    = (ev.time||'').substring(0,5);
            document.getElementById('ajaxEvVenue').value   = ev.venue;
            document.getElementById('ajaxEvSpeaker').value = ev.speaker;
            document.getElementById('ajaxEvSeats').value   = ev.seats;
            document.getElementById('ajaxEvFee').value     = ev.fee;
            document.getElementById('ajaxEvSubmit').textContent = 'Update Event (AJAX)';
            document.getElementById('ajaxEvCancel').style.display = 'inline';
            document.querySelector('details').open = true;
            window.scrollTo(0,0);
        });
    }

    function deleteEvent(id, title) {
        if (!confirm(`Delete event: "${title}"?`)) return;
        fetch(`${EV_API}?id=${id}`, { method: 'DELETE' })
            .then(r => r.json())
            .then(d => { flash(d.success ? d.message : d.error, d.success); if (d.success) loadEvents(); })
            .catch(() => flash('Delete failed.', false));
    }

    function resetEvForm() {
        document.getElementById('ajaxEvId').value = '';
        document.getElementById('ajaxEvSubmit').textContent = 'Add Event (AJAX)';
        document.getElementById('ajaxEvCancel').style.display = 'none';
    }
    document.getElementById('ajaxEvCancel').addEventListener('click', () => { document.getElementById('ajaxEventForm').reset(); resetEvForm(); });
    document.getElementById('evSearch').addEventListener('input', loadEvents);
    document.getElementById('evCatFilter').addEventListener('change', loadEvents);

    /* ── STUDENTS ────────────────────────────────────────── */
    let allStu = [];
    function loadStudents() {
        const q = document.getElementById('stuSearch').value;
        fetch(`${STU_API}?search=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(d => {
                allStu = d.data || [];
                const body = document.getElementById('stuBody');
                body.innerHTML = allStu.length
                    ? allStu.map(s => `<tr>
                        <td>${s.id}</td>
                        <td>${esc(s.fullname)}</td>
                        <td>${esc(s.email)}</td>
                        <td>${esc(s.department).toUpperCase()}</td>
                        <td>${esc(s.year)}</td>
                        <td>
                            <button onclick="editStudent(${s.id})">Edit</button>
                            <button onclick="deleteStudent(${s.id},'${esc(s.fullname)}')">Delete</button>
                        </td>
                    </tr>`).join('')
                    : '<tr><td colspan="6">No students.</td></tr>';
            })
            .catch(() => flash('Failed to load students.', false));
    }

    document.getElementById('ajaxStuForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const id = document.getElementById('ajaxStuId').value;
        if (id) {
            const body = {};
            ['fullname','email','mobile','gender','studentid','department','year'].forEach(f => {
                const el = document.getElementById('ajaxStu'+f.charAt(0).toUpperCase()+f.slice(1).replace('name','Fn').replace('email','Em').replace('mobile','Mb').replace('gender','Gn').replace('studentid','Sid').replace('department','Dep').replace('year','Yr'));
            });
            const fd = new FormData(this);
            const obj = {};
            fd.forEach((v,k) => { if(k!=='id'&&k!=='password'&&v) obj[k]=v; });
            const pw = document.getElementById('ajaxStuPw').value;
            if (pw) obj.password = pw;
            fetch(`${STU_API}?id=${id}`, { method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify(obj) })
                .then(r=>r.json())
                .then(d => { flash(d.success?d.message:(d.error||'Failed'),d.success); if(d.success){this.reset();resetStuForm();loadStudents();} })
                .catch(()=>flash('Update failed.',false));
        } else {
            const fd = new FormData(this);
            fetch(STU_API, { method:'POST', body:fd })
                .then(r=>r.json())
                .then(d => { flash(d.success?d.message:((d.errors||[]).join(', ')||d.error||'Failed'),d.success); if(d.success){this.reset();loadStudents();} })
                .catch(()=>flash('Add failed.',false));
        }
    });

    function editStudent(id) {
        const s = allStu.find(x=>x.id==id);
        if (!s) return;
        document.getElementById('ajaxStuId').value  = s.id;
        document.getElementById('ajaxStuFn').value  = s.fullname;
        document.getElementById('ajaxStuEm').value  = s.email;
        document.getElementById('ajaxStuMb').value  = s.mobile;
        document.getElementById('ajaxStuGn').value  = s.gender;
        document.getElementById('ajaxStuSid').value = s.studentid;
        document.getElementById('ajaxStuDep').value = s.department;
        document.getElementById('ajaxStuYr').value  = s.year;
        document.getElementById('ajaxStuPw').placeholder = 'Leave blank to keep';
        document.getElementById('stuPwNote').textContent = '(optional, leave blank to keep)';
        document.getElementById('ajaxStuSubmit').textContent = 'Update Student (AJAX)';
        document.getElementById('ajaxStuCancel').style.display = 'inline';
        document.querySelectorAll('details')[1].open = true;
        window.scrollTo(0, document.querySelector('section:last-of-type').offsetTop);
    }

    function deleteStudent(id, name) {
        if (!confirm(`Delete student: "${name}"?`)) return;
        fetch(`${STU_API}?id=${id}`, { method:'DELETE' })
            .then(r=>r.json())
            .then(d => { flash(d.success?d.message:d.error, d.success); if(d.success) loadStudents(); })
            .catch(()=>flash('Delete failed.',false));
    }

    function resetStuForm() {
        document.getElementById('ajaxStuId').value = '';
        document.getElementById('ajaxStuSubmit').textContent = 'Add Student (AJAX)';
        document.getElementById('ajaxStuCancel').style.display = 'none';
        document.getElementById('ajaxStuPw').placeholder = '';
        document.getElementById('stuPwNote').textContent = '(required)';
    }
    document.getElementById('ajaxStuCancel').addEventListener('click', () => { document.getElementById('ajaxStuForm').reset(); resetStuForm(); });
    document.getElementById('stuSearch').addEventListener('input', loadStudents);

    loadEvents();
    loadStudents();
    </script>
</body>
</html>
