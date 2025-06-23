<?php
require_once 'config/database.php';

// Funkcja do zapisu płatności
function savePayment($user_id, $issue_type, $amount, $payment_method = 'stripe') {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO payments (user_id, amount, payment_status, payment_method) VALUES (?, ?, 'pending', ?)");
    $stmt->execute([$user_id, $amount, $payment_method]);
    return $pdo->lastInsertId();
}

// Funkcja do generowania sesji płatności Stripe
function createStripeSession($user_id, $issue_type, $amount) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    \Stripe\Stripe::setApiKey('sk_test_your_key_here');
    
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'pln',
                'product_data' => [
                    'name' => "Premium Legal Help - " . ucfirst($issue_type),
                    'description' => "Pełny pakiet pomocy prawnej dla " . $issue_type,
                ],
                'unit_amount' => $amount * 100,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'https://your-domain.com/success.php?session_id={CHECKOUT_SESSION_ID}',
        'cancel_url' => 'https://your-domain.com/cancel.php',
    ]);
    
    return $session;
}

// Sprawdź czy przybyły wymagane parametry
if (!isset($_GET['issue']) || !isset($_GET['email'])) {
    header('Location: index.php');
    exit;
}

$issue = $_GET['issue'];
$email = $_GET['email'];

// Sprawdź czy użytkownik istnieje
$pdo = connectDb();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: index.php');
    exit;
}

// Pobierz dane o problemie
$issue_data = [
    'mandate' => ['price' => 29.99],
    'defamation' => ['price' => 29.99],
    'court_notice' => ['price' => 29.99]
];

if (!isset($issue_data[$issue])) {
    header('Location: index.php');
    exit;
}

$price = $issue_data[$issue]['price'];

// Zapisz płatność jako pending
$payment_id = savePayment($user['id'], $issue, $price);

// Utwórz sesję Stripe
$session = createStripeSession($user['id'], $issue, $price);

// Przekieruj do Stripe Checkout
header("Location: " . $session->url);
exit;
