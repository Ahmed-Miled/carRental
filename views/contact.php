<?php
require __DIR__ . '/includes/header.php';  // Chemin relatif à contact.php
?>
<head>
    <link rel="stylesheet" href="/carRental/assets/css/contact.css">
</head>
<div class="contact-container">
    <h1>Contactez-nous</h1>
    <form action="process_contact.php" method="POST">
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="subject">Objet :</label>
            <input type="text" id="subject" name="subject" required>
        </div>
        <div class="form-group">
            <label for="message">Message :</label>
            <textarea id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="submit-btn">Envoyer</button>
    </form>
</div>

<?php
require __DIR__ . '/includes/footer.php';
?>