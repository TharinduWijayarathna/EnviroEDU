# EnviroEDU Wireframe Spec (for Figma)

Use this spec to create UI wireframes in Figma. Each section maps to `resources/views/` in the app.

---

## 1. Landing (layout: `layouts/landing`)

- **Header**: Logo, nav (Home, Join, Platform, How it works), language switcher, Login button.
- **Footer**: Copyright.

### 1.1 Home (`home.blade.php`)
- Hero: Headline, subheadline.
- Stats row: 3 cards (Schools count, Teachers/Students count, Workspace per school).
- CTAs: “Register school”, “Join school”.
- Section “Built for schools”: H2 + 2 paragraphs.
- Section “Who uses”: H2 + list (Admin, Teacher, Student, Parent).
- Section “Learn more”: 2 cards (Platform, How it works) with title, description, arrow.
- Final CTA: same two buttons.

### 1.2 Join (`pages/join.blade.php`)
- H2, description, prose.
- Join box: “Have a code?” label, school code input, 3 buttons (Join as teacher, student, parent).
- “How to join” steps (numbered list).
- “Sign in by role”: 3 role cards (Teacher, Student, Parent) with icon, title, description.
- “No school code?” prose + link to register school.

### 1.3 Platform (`pages/platform.blade.php`)
- Content section (platform description).

### 1.4 How it works (`pages/how-it-works.blade.php`)
- Content section (how it works).

---

## 2. Auth

### 2.1 Login – choose role (`auth/login-choose.blade.php`)
- Centered card: logo, “Log in” title, 4 role options (Teacher, Student, Parent, School admin), “Back to home”.

### 2.2 Login – per role (`auth/login.blade.php`)
- Centered card: logo, role-specific title, email + password inputs, “Remember me”, Sign in button.
- Links: “Register as [role]”, “Different role”, “Back to home”.

### 2.3 Register (`auth/register.blade.php`)
- Form: name, email, password, role-specific fields.
- **Admin**: school_name, school_code.
- **Teacher/Student**: school_code.
- **Student**: grade_level, class_id (optional; dropdown populated via `/register/classes` API when school code is valid).
- **Parent**: child_email (optional).
- Submit, link to login.

### 2.4 Register classes API (`/register/classes`)
- JSON API: returns classes for a given school code. Used by student registration form to populate class dropdown.

### 2.5 Approval pending (`auth/approval-pending.blade.php`)
- Message that account is pending approval.

---

## 3. Student (layout: `layouts/student`)

- **Header**: Logo, greeting "Hi, [name]! 👋", language switcher, badge count + link to badges, Leave (logout). No Topics/Quizzes/Games in header – navigation is via dashboard gateway cards.

### 3.1 Student dashboard (`dashboard/student.blade.php`)
- Greeting “Hi, [name]!”
- Subtext “Choose where to go.”
- 3 gateway cards: Topics, Games, Quizzes (icon, label, arrow). Optional background/canvas area.

### 3.2 Topics list (`dashboard/student-topics.blade.php`)
- “Back to my learning” link.
- Panel: “Topics” title, short description.
- List of topic cards: icon, title, “Open →”.

### 3.3 Topic detail (`dashboard/student-topic.blade.php`)
- "Back to my learning" link, topic title.
- Optional: video embed (YouTube) or link.
- Section "Play quiz or game": buttons for linked quizzes and mini games.

### 3.4 Quizzes list (`dashboard/student-quizzes.blade.php`)
- List of available quizzes (e.g. title, link to play).

### 3.5 Games list (`dashboard/student-games.blade.php`)
- Two sections:
  - **Platform Games** (🌟): Built-in educational games (Photosynthesis, Seed Grow, Vine Growth, Star Patterns, Rainbow, Water Cycle, Day/Night, Solar Eclipse, Lunar Eclipse).
  - **Teacher Games** (🎮): Mini games created by teachers in the same school.
- Each item: icon, title, "Play →".

### 3.6 Badges (`dashboard/student-badges.blade.php`)
- List or grid of earned badges.

### 3.7 Quiz play (`play/quiz.blade.php`)
- Question, answer options, next/submit, progress.

### 3.8 Mini game play (`play/mini-game.blade.php`)
- Game UI (varies by type: drag_drop, multiple_choice, matching).

### 3.9 Platform game (`play/platform-game.blade.php`)
- Platform game canvas/UI. Games: photosynthesis, seed-grow, vine-growth, star-patterns, rainbow, water-cycle, day-night, solar-eclipse, lunar-eclipse.

---

## 4. Teacher (layout: `layouts/teacher`)

- **Header**: Logo, nav (Dashboard, Classes, Topics, Quizzes, Mini games, Badges, Student progress), user name, Logout.

### 4.1 Teacher dashboard (`dashboard/teacher.blade.php`)
- Title “Dashboard”, short description.
- Grid: 3 stat cards (Classes, Topics, Quizzes) + Quick actions card.
- Each stat card: icon, number, title, short description, “Manage …” button.
- Quick actions: list (Create Topic, Create Quiz, Create Mini game, Badges, Classes, Student progress).
- Second row: 3 cards (Mini games, Badges, Student progress).
- Tip banner at bottom.

### 4.2 Classes (`teacher/class-rooms/`)
- **Index**: List/grid of classes, “Create class” CTA.
- **Create**: Form (name, etc.).
- **Show**: Class name, list of students, actions.
- **Edit**: Form to edit class.

### 4.3 Topics (`teacher/topics/`)
- **Index**: List of topics, “Create topic”.
- **Create/Edit**: Form (title, description, etc.).
- **Show**: Topic detail, linked quizzes/games, edit link.

### 4.4 Quizzes (`teacher/quizzes/`)
- **Index**: List of quizzes, “Create quiz”.
- **Create/Edit**: Form + question list (add/edit/delete questions, options).
- **Show**: Quiz detail, questions summary, edit.

### 4.5 Mini games (`teacher/mini-games/`)
- **Index**: List of mini games, “Create mini game”.
- **Create/Edit**: Form (type, content, etc.).
- **Show**: Game detail, edit.

### 4.6 Badges (`teacher/badges/`)
- **Index**: List of badges, “Create badge”.
- **Create/Edit**: Form (name, image, topic, etc.).
- **Show**: Badge detail.

### 4.7 Progress (`teacher/progress/`)
- **Index**: List of students or classes with progress summary.
- **Show**: One student’s attempts, scores, badges.

---

## 5. Parent (custom header in view)

- **Header**: Logo, Dashboard, user name, Logout.

### 5.1 Parent dashboard (`dashboard/parent.blade.php`)
- Title, description.
- Card: “Link child” – email input, “Link account” button.
- “My children”: list of children with “View badges & progress” per child.

### 5.2 Child detail (`parent/children/show.blade.php`)
- Child name, badges earned, progress summary.

---

## 6. Admin (layout: `layouts/admin`)

- **Header**: Logo, Dashboard, Teachers, Students, Pending approvals, user name, Logout.

### 6.1 Admin dashboard (`dashboard/admin.blade.php`)
- School name, school code (share with teachers/students).
- Analytics grid: Teachers, Students, Classes, Topics, Quizzes, Games, Quiz attempts, Badges earned (clickable links where applicable).
- Pending approvals card (if any) with "Review pending" button.

### 6.2 Teachers (`admin/users/teachers.blade.php`)
- List of teachers (name, email, classes count, pending badge if not approved).

### 6.3 Students (`admin/users/students.blade.php`)
- List of students (name, email, grade, classes, quiz attempts, game attempts, badges, pending badge if not approved).

### 6.4 Approvals (`admin/approvals/index.blade.php`)
- List of pending teachers and students, approve action per user.

---

## Design tokens (from app)

- **Primary**: teal/mint (e.g. `#4ECDC4` / `var(--eco-primary)`).
- **Font**: “Bubblegum Sans” for headings; Instrument Sans or system for body.
- **Cards**: White background, rounded corners (e.g. 20–24px), light border or shadow.
- **Buttons**: `eco-btn` (primary), `eco-btn-outline`.
- **Inputs**: `eco-input` (rounded, border).

Use the FigJam sitemap for navigation flow; use this spec to draw each screen in Figma.
