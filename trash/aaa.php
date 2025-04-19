<!-- Messages Section -->
<section class="content-section" id="messages">
    <div class="section-header">
        <h3>Messages</h3>
        <div class="section-actions">
            <span class="message-count"><?= count($messages) ?> messages</span>
        </div>
    </div>
    <div class="messages-list">
        <?php foreach ($messages as $message): ?>
        <div class="message-card">
            <div class="message-header">
                <div class="message-meta">
                    <span class="message-email"><?= htmlspecialchars($message['email']) ?></span>
                    <span class="message-date">
                        <i class="fas fa-calendar-alt"></i>
                        <?= date('M j, Y H:i', strtotime($message['created_at'])) ?>
                    </span>
                </div>
                <h4 class="message-subject"><?= htmlspecialchars($message['object']) ?></h4>
            </div>
            <div class="message-content">
                <?= nl2br(htmlspecialchars($message['content'])) ?>
            </div>
            <div class="message-actions">
                <form action="delete.php" method="POST" class="delete-form">
                    <input type="hidden" name="type" value="message">
                    <input type="hidden" name="id" value="<?= $message['id'] ?>">
                    <button type="submit" class="btn-delete">
                        <i class="fas fa-trash-alt"></i>
                        Delete Message
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?>
            <div class="no-messages">
                <i class="fas fa-inbox"></i>
                <p>No messages found</p>
            </div>
        <?php endif; ?>
    </div>
</section>


it's style 



/* Enhanced Styling */
.content-section {
    display: none;
    background: #ffffff;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.content-section.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid #f0f2f5;
}

.message-count {
    font-size: 0.9rem;
    color: #6c757d;
    background: #f8f9fa;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
}

.messages-list {
    display: grid;
    gap: 1.5rem;
}

.message-card {
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.message-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
}

.message-header {
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #f1f3f5;
}

.message-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
}

.message-email {
    color: #2b6cb0;
    font-weight: 500;
}

.message-date {
    color: #868e96;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.message-subject {
    color: #212529;
    margin: 0;
    font-size: 1.1rem;
}

.message-content {
    color: #495057;
    line-height: 1.6;
    margin-bottom: 1.5rem;
    white-space: pre-wrap;
}

.message-actions {
    border-top: 1px solid #f1f3f5;
    padding-top: 1rem;
    text-align: right;
}

.btn-delete {
    background: #fff0f0;
    color: #dc3545;
    border: 1px solid #ffd6d6;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-delete:hover {
    background: #ffe3e3;
    border-color: #ffb8b8;
}

.no-messages {
    text-align: center;
    padding: 3rem;
    color: #868e96;
}

.no-messages i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: #ced4da;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Font Awesome Icons */
.fas {
    font-size: 0.9em;
}