# TicketApp (Twig Version)

## Overview
TicketApp is a ticket management application built using Twig templates, HTML, CSS, and JS.

## Features
- Landing page with hero wave & decorative circles
- Authentication (Login/Signup) using localStorage
- Dashboard with ticket stats
- Full CRUD ticket management with validation
- Responsive and accessible design

## Setup
1. Install PHP and Composer
2. Install Twig via Composer: `composer require "twig/twig:^3.0"`
3. Run a local PHP server: `php -S localhost:8000 -t public`
4. Open `http://localhost:8000` in a browser

## Test User
- Email: test@example.com
- Password: password123

## Notes
- Tickets stored in `localStorage` (simulated backend)
- Accessible design: semantic HTML, alt attributes, focus states


TicketApp (Twig)

TicketApp is a responsive, accessible, and secure Ticket Management System built with Twig templates and PHP.
It allows users to sign up, log in, and manage tickets (Create, View, Edit, Delete) in a visually consistent interface.

This project is part of a multi-framework challenge, implemented in React, Vue.js, and Twig, each following the same layout, features, and design rules.

🧱 Features
🌐 1. Landing Page

Welcoming hero section with wavy SVG background and decorative circles.

Clear Call-to-Action buttons: Login and Get Started.

Fully responsive layout (mobile → tablet → desktop).

Consistent footer on all pages.

🔐 2. Authentication

Login and Signup pages with form validation.

Inline and toast-style error messages for invalid inputs or existing users.

Simulated authentication using PHP sessions:

Session key: ticketapp_session

User storage: JSON file or database (depending on setup)

Logout clears session and redirects to Landing Page.

📊 3. Dashboard

Displays ticket summary:

Total tickets

Open

In Progress

Closed

Navigation to the Ticket Management screen.

Protected route, only accessible with a valid session.

🎟 4. Ticket Management (CRUD)

Create, View, Edit, and Delete tickets.

Real-time form validation with server-side checks.

Success/error messages for user feedback.

Status color coding:

🟢 open → Green

🟠 in_progress → Amber

⚪ closed → Gray

Data stored in JSON file, database, or session (depending on setup).

🎨 Design Consistency Rules
Element	Rule
Max Width	1440px, centered horizontally
Hero Section	Wavy SVG background
Decorative Shapes	At least two circles across site
Boxes	Rounded corners + shadow
Responsive	Works across mobile, tablet, and desktop
Colors	Open (Green), In Progress (Amber), Closed (Gray)
Accessibility	Semantic HTML, focus states, sufficient contrast
♿ Accessibility Features

Semantic HTML structure (<main>, <section>, <article>, <header>, <footer>).

Fully responsive design.

User-friendly notifications for success and error messages.

🧩 Project Structure
ticketapp-twig/
├── templates/
│   ├── components/
│   │   ├
│   │   └── 
│   ├── pages/
│   │   ├── landing.twig
│   │   ├── login.twig
│   │   ├── signup.twig
│   │   ├── dashboard.twig
│   │   └── ticket_management.twig
├── public/
│   └── assets/
│       └── css/
│           ├── styles.css
│           ├── landing.css
│           ├── dashboard.css
│           ├── tickets.css
│           └── auth.css
            |--Login.css
├── src/
│   ├── index.php
│   ├── auth.php
│   ├── dashboard.php
│   └── ticket_management.php
└── composer.json

⚙️ Setup & Run Instructions
1️⃣ Clone the Repository
git clone https://github.com/yourusername/ticketapp-twig.git
cd ticketapp-twig

2️⃣ Install Dependencies
composer install

3️⃣ Start Development Server
php -S localhost:8000 -t public


App runs on → http://localhost:8000