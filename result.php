<?php
require_once 'config/database.php';

// Funkcja do zapisu interakcji
function saveInteraction($user_id, $issue_type) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO user_interactions (user_id, issue_type) VALUES (?, ?)");
    $stmt->execute([$user_id, $issue_type]);
}

// Funkcja do zapisu pobrania raportu
function saveReportDownload($user_id, $report_type) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("UPDATE user_interactions SET report_downloaded = TRUE WHERE user_id = ? AND issue_type = ?");
    $stmt->execute([$user_id, $report_type]);
}

include 'templates/header.php';

// Sprawdź czy użytkownik jest zalogowany
if (!isset($_POST['email']) || !isset($_POST['issue'])) {
    echo "<p>Nie wybrano żadnego problemu lub nie podano emaila.</p>";
    echo "<a href='index.php'>Wróć</a>";
    exit;
}

$email = $_POST['email'];
$issue = $_POST['issue'];

// Sprawdź czy użytkownik już istnieje
$pdo = connectDb();
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // Jeśli nie istnieje, utwórz nowego użytkownika
    $user_id = saveUser($email);
} else {
    $user_id = $user['id'];
}

// Zapisz interakcję
saveInteraction($user_id, $issue);

// Przygotuj odpowiednie dane dla wybranego problemu
$issue_data = [
    'mandate' => [
        'title' => "Mandat - Co możesz zrobić?",
        'description' => "Dowiedz się, jak odwołać się od mandatu i jakie masz prawa.",
        'reportLink' => "reports/mandate-guide.pdf",
        'price' => 29.99
    ],
    'defamation' => [
        'title' => "Defamacja online - Jak się bronić?",
        'description' => "Krok po kroku: jak zgłosić fałszywe informacje i chronić swoją reputację.",
        'reportLink' => "reports/defamation-guide.pdf",
        'price' => 29.99
    ],
    'court_notice' => [
        'title' => "Wezwanie sądowe - Co dalej?",
        'description' => "Przygotuj się poprawnie – dowiedz się, co robić dalej.",
        'reportLink' => "reports/court-notice-guide.pdf",
        'price' => 29.99
    ]
];

if (!isset($issue_data[$issue])) {
    echo "<p>Nieprawidłowy wybór.</p>";
    echo "<a href='index.php'>Wróć</a>";
    exit;
}

$data = $issue_data[$issue];
?>

    <div class="result-container">
        <h2><?= $data['title'] ?></h2>
        <p><?= $data['description'] ?></p>

        <div class="options">
            <div class="free-option">
                <h3>Bezpłatna wersja</h3>
                <p>Podstawowy poradnik PDF</p>
                <a href="<?= $data['reportLink'] ?>" download onclick="saveReportDownload(<?= $user_id ?>, '<?= $issue ?>')">
                    <button class="download-btn">Pobierz raport (PDF)</button>
                </a>
            </div>

            <div class="premium-option">
                <h3>Premium - <?= number_format($data['price'], 2) ?> PLN</h3>
                <p>Pełny pakiet z:</p>
                <ul>
                    <li>Skorygowany formularz odwołania</li>
                    <li>Poradnik PDF</li>
                    <li>Przykłady listów</li>
                    <li>Wideo poradnik</li>
                </ul>
                <a href="payment.php?issue=<?= $issue ?>&email=<?= urlencode($email) ?>">
                    <button class="premium-btn">Kup pełny pakiet</button>
                </a>
            </div>
        </div>
    </div>

<?php include 'templates/footer.php'; ?>