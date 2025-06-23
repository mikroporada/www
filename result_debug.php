<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to prevent any accidental output
ob_start();

// Log file for debugging
$logFile = __DIR__ . '/debug.log';
$log = function($message) use ($logFile) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    file_put_contents($logFile, $logMessage, FILE_APPEND);
};

// Log the start of the script
$log("=== Starting result_debug.php ===");
$log("POST data: " . print_r($_POST, true));

// Check required parameters
if (!isset($_POST['email']) || !isset($_POST['issue'])) {
    $error = "Missing required parameters. Email: " . (isset($_POST['email']) ? 'set' : 'not set') . 
            ", Issue: " . (isset($_POST['issue']) ? 'set' : 'not set');
    $log("Error: $error");
    die("<div style='padding:20px;font-family:Arial;color:#721c24;background-color:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;'>
            <h2>Error</h2>
            <p>$error</p>
            <p>Please make sure you're submitting the form correctly.</p>
            <p><a href='index.php' style='color:#721c24;text-decoration:underline;'>Return to home page</a></p>
        </div>");
}

$email = $_POST['email'];
$issue = $_POST['issue'];
$log("Processing request for email: $email, issue: $issue");

// Try to include database configuration
try {
    if (!file_exists('config/database.php')) {
        throw new Exception("Database configuration file not found");
    }
    require_once 'config/database.php';
    
    // Test database connection
    $pdo = connectDb();
    $log("Database connection successful");
    
    // Check if users table exists
    $tables = $pdo->query("SHOW TABLES LIKE 'users'")->fetchAll();
    if (empty($tables)) {
        throw new Exception("Users table does not exist in the database");
    }
    
    // Check if user exists or create new
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        // Create new user if not exists
        $log("Creating new user with email: $email");
        $stmt = $pdo->prepare("INSERT INTO users (email, created_at) VALUES (?, NOW())");
        $stmt->execute([$email]);
        $user_id = $pdo->lastInsertId();
        $log("Created new user with ID: $user_id");
    } else {
        $user_id = $user['id'];
        $log("Found existing user with ID: $user_id");
    }
    
    // Log the interaction
    $stmt = $pdo->prepare("INSERT INTO user_interactions (user_id, issue_type, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $issue]);
    $log("Logged interaction for user $user_id with issue: $issue");
    
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
    $log("ERROR: $error");
    die("<div style='padding:20px;font-family:Arial;color:#721c24;background-color:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;'>
            <h2>Database Error</h2>
            <p>$error</p>
            <p>Please try again later or contact support.</p>
        </div>");
}

// Define issue data
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
    $error = "Nieprawidłowy wybór problemu: " . htmlspecialchars($issue);
    $log("ERROR: $error");
    die("<div style='padding:20px;font-family:Arial;color:#721c24;background-color:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;'>
            <h2>Błąd</h2>
            <p>$error</p>
            <p><a href='index.php'>Wróć do strony głównej</a></p>
        </div>");
}

$data = $issue_data[$issue];

// Include header
try {
    if (!file_exists('templates/header.php')) {
        throw new Exception("Header template not found");
    }
    include 'templates/header.php';
} catch (Exception $e) {
    $log("WARNING: Could not include header: " . $e->getMessage());
    // Continue without header
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h2 class="h4 mb-0"><?= htmlspecialchars($data['title']) ?></h2>
                </div>
                <div class="card-body">
                    <p class="lead"><?= htmlspecialchars($data['description']) ?></p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h3 class="h5">Bezpłatna wersja</h3>
                                    <p>Podstawowy poradnik PDF</p>
                                    <a href="<?= htmlspecialchars($data['reportLink']) ?>" 
                                       class="btn btn-outline-primary" 
                                       download 
                                       onclick="saveReportDownload(<?= (int)$user_id ?>, '<?= htmlspecialchars($issue) ?>')">
                                        Pobierz raport (PDF)
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <h3 class="h5">Premium - <?= number_format($data['price'], 2) ?> PLN</h3>
                                    <p>Pełny pakiet z:</p>
                                    <ul class="text-left">
                                        <li>Skorygowany formularz odwołania</li>
                                        <li>Poradnik PDF</li>
                                        <li>Przykłady listów</li>
                                        <li>Wideo poradnik</li>
                                    </ul>
                                    <a href="payment.php?issue=<?= urlencode($issue) ?>&email=<?= urlencode($email) ?>" 
                                       class="btn btn-success">
                                        Kup pełny pakiet
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function saveReportDownload(userId, issueType) {
    // Simple AJAX call to log the download
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'log_download.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.send('user_id=' + userId + '&issue_type=' + encodeURIComponent(issueType));
}
</script>

<?php
// Include footer
try {
    if (file_exists('templates/footer.php')) {
        include 'templates/footer.php';
    }
} catch (Exception $e) {
    $log("WARNING: Could not include footer: " . $e->getMessage());
    // Continue without footer
}

// Log successful completion
$log("Script completed successfully");
ob_end_flush();
?>
