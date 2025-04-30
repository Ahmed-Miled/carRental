<?php

require __DIR__ . '/includes/header.php';

// Get form data and errors from session
$formData = $_SESSION['form_data'] ?? [];
$errors = $_SESSION['form_errors'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['form_data']);
?>
<head>
    <link rel="stylesheet" href="/carRental/assets/css/contact.css">
</head>
<div class="contact-container">
    <h1>Contactez-nous</h1>

    <?php if (isset($_SESSION['success'])) : ?>
        <div class="success-message">
            <div class="checkmark">✓</div>
            <p>Message sent successfully!</p>
            <a href="/carRental/views/contact.php" class="btn">Send Another Message</a>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php else : ?>
        <form action="/carRental/controller/process_messages.php" method="POST">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" 
                       value="<?= htmlspecialchars($formData['email'] ?? '') ?>"
                       class="<?= isset($errors['email']) ? 'error-field' : '' ?>">
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= $errors['email'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="subject">Objet :</label>
                <input type="text" id="subject" name="subject"
                       value="<?= htmlspecialchars($formData['subject'] ?? '') ?>"
                       class="<?= isset($errors['subject']) ? 'error-field' : '' ?>">
                <?php if (isset($errors['subject'])): ?>
                    <span class="error-message"><?= $errors['subject'] ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="message">Message :</label>
                <textarea id="message" name="message" rows="5"
                          class="<?= isset($errors['message']) ? 'error-field' : '' ?>"><?= htmlspecialchars($formData['message'] ?? '') ?></textarea>
                <?php if (isset($errors['message'])): ?>
                    <span class="error-message"><?= $errors['message'] ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="submit-btn">Envoyer</button>
        </form>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>