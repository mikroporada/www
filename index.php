<?php
require_once 'config/database.php';

// Funkcja do łączenia z bazą danych
function connectDb() {
    $config = require 'config/database.php';
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        return new PDO($dsn, $config['user'], $config['password']);
    } catch (PDOException $e) {
        die("Błąd połączenia z bazą danych: " . $e->getMessage());
    }
}

// Funkcja do zapisu danych użytkownika
function saveUser($email) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO users (email) VALUES (?)");
    $stmt->execute([$email]);
    return $pdo->lastInsertId();
}

// Funkcja do zapisu interakcji
function saveInteraction($user_id, $issue_type) {
    $pdo = connectDb();
    $stmt = $pdo->prepare("INSERT INTO user_interactions (user_id, issue_type) VALUES (?, ?)");
    $stmt->execute([$user_id, $issue_type]);
}

include 'templates/header.php';
?>

    <h2>Wybierz swój problem prawny</h2>
    <form action="result.php" method="post">
        <div class="email-form">
            <label for="email">Twój email:</label>
            <input type="email" id="email" name="email" required placeholder="np. jan.kowalski@example.com">
        </div>
        <div class="issue-form">
            <label><input type="radio" name="issue" value="mandate"> Otrzymałem mandat</label><br>
            <label><input type="radio" name="issue" value="defamation"> Ktoś mnie poniżył online</label><br>
            <label><input type="radio" name="issue" value="court_notice"> Otrzymałem wezwanie sądowe</label><br>
        </div>
        <button type="submit">Pokaż rozwiązanie</button>
    </form>

<?php include 'templates/footer.php'; ?>