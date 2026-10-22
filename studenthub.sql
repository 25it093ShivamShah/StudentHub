-- StudentHub Database Schema
-- Practical 8: MySQL Schema Design
-- Author: Shivam Shah (25IT093)

CREATE DATABASE IF NOT EXISTS studenthub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE studenthub;

CREATE TABLE IF NOT EXISTS users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname    VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL UNIQUE,
    mobile      VARCHAR(15) NOT NULL,
    gender      ENUM('Male','Female','Other') NOT NULL,
    password    VARCHAR(255) NOT NULL,
    studentid   VARCHAR(30) NOT NULL UNIQUE,
    department  ENUM('cs','it','ece','mech','business','arts') NOT NULL,
    year        ENUM('1','2','3','4','graduate') NOT NULL,
    interests   VARCHAR(255) DEFAULT '',
    role        ENUM('student','admin') NOT NULL DEFAULT 'student',
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS events (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200) NOT NULL,
    category    ENUM('workshop','hackathon','seminar','cultural') NOT NULL,
    description TEXT NOT NULL,
    date        DATE NOT NULL,
    time        TIME NOT NULL,
    venue       VARCHAR(200) NOT NULL,
    speaker     VARCHAR(150) NOT NULL DEFAULT '',
    seats       INT UNSIGNED NOT NULL DEFAULT 50,
    fee         VARCHAR(20) NOT NULL DEFAULT 'Free',
    poster      VARCHAR(255) DEFAULT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS registrations (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    event_id   INT UNSIGNED NOT NULL,
    status     ENUM('pending','confirmed','waitlisted','cancelled') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_event (user_id, event_id),
    FOREIGN KEY (user_id)  REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

INSERT IGNORE INTO users (fullname, email, mobile, gender, password, studentid, department, year, interests, role)
VALUES (
    'Admin User', 'admin@studenthub.edu', '9000000001', 'Male',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXkKUiiq',
    'ADMIN001', 'cs', '1', 'coding', 'admin'
);

INSERT IGNORE INTO events (title, category, description, date, time, venue, speaker, seats, fee)
VALUES
('Web Development Workshop',    'workshop',  'Learn HTML, CSS, JavaScript from peer instructors.', '2026-11-05', '10:00:00', 'Lab 402', 'Campus Coding Club', 50, 'Free'),
('Annual Tech Hackathon',       'hackathon', 'Build solutions for real-world campus problems.',    '2026-11-20', '09:00:00', 'Main Auditorium', 'Technical Committee', 200, 'Free'),
('AI & ML Guest Seminar',       'seminar',   'Industry professionals on AI and Generative AI.',   '2026-12-02', '14:00:00', 'Seminar Hall B', 'Dept. of CS', 100, 'Free'),
('Annual Campus Cultural Fest', 'cultural',  'Music, food stalls, art exhibitions, talent shows.', '2026-12-10', '09:00:00', 'Open Ground', 'Student Council', 500, 'Free');
