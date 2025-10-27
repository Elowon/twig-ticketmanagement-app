<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

$usersFile = sys_get_temp_dir() . '/users.json';
$ticketsFile = sys_get_temp_dir() . '/tickets.json';


if (!file_exists($usersFile)) {
    file_put_contents($usersFile, json_encode([]));
}
if (!file_exists($ticketsFile)) {
    file_put_contents($ticketsFile, json_encode([]));
}

$session_timeout = 60; 
if (isset($_SESSION['ticketapp_session'], $_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $session_timeout) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['toast'] = [
            'message' => 'You have been logged out due to inactivity.',
            'type' => 'error'
        ];
        header('Location: /auth/login');
        exit;
    }
}
if (isset($_SESSION['ticketapp_session'])) {
    $_SESSION['last_activity'] = time();
}

require_once __DIR__ . '/../vendor/autoload.php';
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader);
$twig->addGlobal('session', $_SESSION);


$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/auth/signup') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        if (!$email || !$password || !$confirm) {
            echo $twig->render('auth/signup.html.twig', [
                'toast' => ['message' => 'All fields are required.', 'type' => 'error'],
                'email' => $email
            ]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo $twig->render('auth/signup.html.twig', [
                'toast' => ['message' => 'Please enter a valid email address.', 'type' => 'error'],
                'email' => $email
            ]);
            exit;
        }

        $users = json_decode(file_get_contents($usersFile), true);

        foreach ($users as $user) {
            if ($user['email'] === $email) {
                echo $twig->render('auth/signup.html.twig', [
                    'toast' => ['message' => 'Email is already registered.', 'type' => 'error'],
                    'email' => $email
                ]);
                exit;
            }
        }

        if ($password !== $confirm) {
            echo $twig->render('auth/signup.html.twig', [
                'toast' => ['message' => 'Passwords do not match.', 'type' => 'error'],
                'email' => $email
            ]);
            exit;
        }
        $users[] = [
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ];
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));

        $_SESSION['ticketapp_session'] = $email;
        $_SESSION['last_activity'] = time();

        echo $twig->render('dashboard.html.twig', [
            'user' => $email,
            'toast' => ['message' => 'Signup successful! Welcome.', 'type' => 'success']
        ]);
        exit;
    }

    echo $twig->render('auth/signup.html.twig');
    exit;
}


if ($path === '/auth/login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$email || !$password) {
            echo $twig->render('auth/login.html.twig', [
                'toast' => ['message' => 'Email and password are required.', 'type' => 'error']
            ]);
            exit;
        }

        $users = json_decode(file_get_contents($usersFile), true);

        $loggedIn = false;
        foreach ($users as $user) {
            if ($user['email'] === $email && password_verify($password, $user['password'])) {
                $_SESSION['ticketapp_session'] = $email;
                $_SESSION['last_activity'] = time();
                $loggedIn = true;
                break;
            }
        }

        if ($loggedIn) {
            echo $twig->render('dashboard.html.twig', [
                'user' => $email,
                'toast' => ['message' => 'Login successful!', 'type' => 'success']
            ]);
            exit;
        } else {
            echo $twig->render('auth/login.html.twig', [
                'toast' => ['message' => 'Invalid email or password.', 'type' => 'error'],
                'email' => $email
            ]);
            exit;
        }
    }

    $toast = $_SESSION['toast'] ?? null;
    unset($_SESSION['toast']);
    echo $twig->render('auth/login.html.twig', ['toast' => $toast]);
    exit;
}


if ($path === '/logout') {
    session_unset();
    session_destroy();
    session_start();
    $_SESSION['toast'] = [
        'message' => 'You logged out.',
        'type' => 'success'
    ];
    header('Location: /auth/login');
    exit;
}

if ($path === '/dashboard') {
    if (!isset($_SESSION['ticketapp_session'])) {
        header('Location: /auth/login');
        exit;
    }

    $tickets = json_decode(file_get_contents($ticketsFile), true);

    $open = count(array_filter($tickets, fn($t) => isset($t['status']) && strtolower($t['status']) === 'open'));
    $in_progress = count(array_filter($tickets, fn($t) => isset($t['status']) && strtolower($t['status']) === 'in_progress'));
    $closed = count(array_filter($tickets, fn($t) => isset($t['status']) && strtolower($t['status']) === 'closed'));
    $total = count($tickets);

    $toast = $_SESSION['toast'] ?? null;
    unset($_SESSION['toast']);

    echo $twig->render('dashboard.html.twig', [
        'user' => $_SESSION['ticketapp_session'],
        'open' => $open,
        'in_progress' => $in_progress,
        'closed' => $closed,
        'total' => $total,
        'toast' => $toast
    ]);
    exit;
}

if (strpos($path, '/tickets') === 0) {
    if (!isset($_SESSION['ticketapp_session'])) {
        header('Location: /auth/login');
        exit;
    }

    $tickets = json_decode(file_get_contents($ticketsFile), true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $status = $_POST['status'] ?? 'open';
        $description = trim($_POST['description'] ?? '');

        if ($id) {
            foreach ($tickets as &$t) {
                if ($t['id'] == $id) {
                    $t['title'] = $title;
                    $t['status'] = $status;
                    $t['description'] = $description;
                    break;
                }
            }
        } else {
            $tickets[] = [
                'id' => time(),
                'title' => $title,
                'status' => $status,
                'description' => $description,
                'user' => $_SESSION['ticketapp_session']
            ];
        }

        file_put_contents($ticketsFile, json_encode($tickets, JSON_PRETTY_PRINT));
        header('Location: /tickets');
        exit;
    }

    if (preg_match('#/tickets/edit/(\d+)#', $path, $matches)) {
        $editId = $matches[1];
        $ticket = null;
        foreach ($tickets as $t) {
            if ($t['id'] == $editId) {
                $ticket = $t;
                break;
            }
        }

        echo $twig->render('tickets.html.twig', [
            'ticket' => $ticket,
            'tickets' => $tickets
        ]);
        exit;
    }

    if (preg_match('#/tickets/delete/(\d+)#', $path, $matches)) {
        $deleteId = $matches[1];
        $tickets = array_filter($tickets, fn($t) => $t['id'] != $deleteId);
        file_put_contents($ticketsFile, json_encode(array_values($tickets), JSON_PRETTY_PRINT));
        header('Location: /tickets');
        exit;
    }

    echo $twig->render('tickets.html.twig', ['tickets' => $tickets]);
    exit;
}

echo $twig->render('landing.html.twig');
