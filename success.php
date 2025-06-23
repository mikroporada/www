<?php
require_once 'config/database.php';

// Funkcja do aktualizacji statusu płatności
function updatePaymentStatus($payment_id, $status) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("UPDATE payments SET payment_status = ? WHERE id = ?");
    $stmt->execute([$status, $payment_id]);
}

// Sprawdź czy mamy sesję
if (!isset($_GET['session_id'])) {
    header('Location: index.php');
    exit;
}

$session_id = $_GET['session_id'];

// Pobierz szczegóły sesji z Stripe
require_once __DIR__ . '/vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_your_key_here');

try {
    $session = \Stripe\Checkout\Session::retrieve($session_id);
    
    if ($session->payment_status === 'paid') {
        // Znajdź płatność w bazie danych
        $pdo = connectDb();
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE payment_status = 'pending' ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $payment = $stmt->fetch();
        
        if ($payment) {
            // Zaktualizuj status płatności
            updatePaymentStatus($payment['id'], 'paid');
            
            // Znajdź użytkownika
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$payment['user_id']]);
            $user = $stmt->fetch();
            
            // Znajdź interakcję
            $stmt = $pdo->prepare("SELECT * FROM user_interactions WHERE user_id = ? AND issue_type = ?");
            $stmt->execute([$payment['user_id'], $payment['issue_type']]);
            $interaction = $stmt->fetch();
            
            // Wyślij email z potwierdzeniem
            $to = $user['email'];
            $subject = "Potwierdzenie zakupu Premium Legal Help";
            $message = "Dziękujemy za zakup premium pomocy prawnej!\n\n";
            $message .= "Twoje dane zamówienia:\n";
            $message .= "Numer zamówienia: " . $payment['id'] . "\n";
            $message .= "Typ problemu: " . ucfirst($payment['issue_type']) . "\n";
            $message .= "Kwota: " . $payment['amount'] . " PLN\n\n";
            $message .= "Dokumenty premium są już dostępne w sekcji Twojego konta.\n\n";
            $message .= "mikroporada.pl Team";
            
            $headers = "From: no-reply@mikroporada.ai\r\n";
            mail($to, $subject, $message, $headers);
        }
    }
} catch (Exception $e) {
    // Obsłuż błąd
    error_log("Błąd przy sprawdzaniu sesji Stripe: " . $e->getMessage());
}

// Pokaż stronę potwierdzenia
include 'templates/header.php';
?>

    <div class="success-container">
        <h2>Dziękujemy za zakup!</h2>
        <p>Twoje premium dokumenty są już dostępne w sekcji Twojego konta.</p>
        <p>Na Twój email wysłaliśmy potwierdzenie zakupu.</p>
        <a href="index.php"><button>Wróć do strony głównej</button></a>
    </div>

<?php include 'templates/footer.php'; ?>
