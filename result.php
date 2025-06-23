<?php include 'templates/header.php'; ?>

<?php
if (!isset($_POST['issue'])) {
    echo "<p>Nie wybrano żadnego problemu.</p>";
    echo "<a href='index.php'>Wróć</a>";
    exit;
}

$issue = $_POST['issue'];

switch ($issue) {
    case 'mandate':
        $title = "Mandat - Co możesz zrobić?";
        $description = "Dowiedz się, jak odwołać się od mandatu i jakie masz prawa.";
        $reportLink = "reports/mandate-guide.pdf";
        break;
    case 'defamation':
        $title = "Defamacja online - Jak się bronić?";
        $description = "Krok po kroku: jak zgłosić fałszywe informacje i chronić swoją reputację.";
        $reportLink = "reports/defamation-guide.pdf";
        break;
    case 'court_notice':
        $title = "Wezwanie sądowe - Co dalej?";
        $description = "Przygotuj się poprawnie – dowiedz się, co robić dalej.";
        $reportLink = "reports/court-notice-guide.pdf";
        break;
    default:
        echo "<p>Nieprawidłowy wybór.</p>";
        echo "<a href='index.php'>Wróć</a>";
        exit;
}
?>

    <h2><?= $title ?></h2>
    <p><?= $description ?></p>

    <a href="<?= $reportLink ?>" download>
        <button>Pobierz raport (PDF)</button>
    </a>

    <br><br>
    <!-- Możesz tu dodać przycisk do płatności -->
    <!-- <a href="payment.php"><button>Zamów pomoc specjalisty</button></a> -->

<?php include 'templates/footer.php'; ?>