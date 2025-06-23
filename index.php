<?php include 'templates/header.php'; ?>

    <h2>Wybierz swój problem prawny</h2>
    <form action="result.php" method="post">
        <label><input type="radio" name="issue" value="mandate"> Otrzymałem mandat</label><br>
        <label><input type="radio" name="issue" value="defamation"> Ktoś mnie poniżył online</label><br>
        <label><input type="radio" name="issue" value="court_notice"> Otrzymałem wezwanie sądowe</label><br><br>
        <button type="submit">Pokaż rozwiązanie</button>
    </form>

<?php include 'templates/footer.php'; ?>