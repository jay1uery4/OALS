<?php
// Assuming teacher_id is available
$teacher_id = $_SESSION['teacher_id']; // Or retrieve it from the session or request

// SQL query to fetch sent messages from the given teacher
$sent_query = "
    SELECT ms.message_sent_id, ms.reciever_id, ms.content, ms.date_sended, ms.sender_id, ms.reciever_name, ms.sender_name
    FROM message_sent ms
    WHERE ms.sender_id = '$teacher_id'
    ORDER BY ms.date_sended DESC
";

// Execute the query and check for errors
$sent_result = mysqli_query($conn, $sent_query);

if (!$sent_result) {
    echo "Error: " . mysqli_error($conn);
    exit;
}
?>

<!-- Sent Messages Section -->
<div id="sent" class="tab-content">
    <?php if (mysqli_num_rows($sent_result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($sent_result)): ?>
            <div class="message">
                <p><strong>Sent to: <?php echo htmlspecialchars($row['reciever_name']); ?></strong></p>
                <p>Date: <?php echo htmlspecialchars($row['date_sended']); ?></p>
                <p><?php echo htmlspecialchars($row['content']); ?></p>
                <button class="reply"><i class="fas fa-reply"></i></button>
                <button class="remove"><i class="fas fa-trash"></i></button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No sent messages found.</p>
    <?php endif; ?>
</div>
