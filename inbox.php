<?php
// Assuming you have a connection to the database in $conn and teacher_id is available
$teacher_id = $_SESSION['teacher_id']; // Or retrieve it from the session or request

// SQL query to fetch inbox messages for students enrolled with the given teacher
$inbox_query = "
    SELECT m.message_id, m.reciever_id, m.content, m.date_sended, m.sender_id, m.reciever_name, m.sender_name, m.message_status
    FROM message m
    JOIN student_enrollment se ON se.student_id = m.reciever_id
    WHERE se.teacher_id = '$teacher_id'
    ORDER BY m.date_sended DESC
";

// Execute the query and check for errors
$inbox_result = mysqli_query($conn, $inbox_query);

if (!$inbox_result) {
    echo "Error: " . mysqli_error($conn);
    exit;
}
?>

<!-- Inbox Section -->
<div id="inbox" class="tab-content active">
    <?php if (mysqli_num_rows($inbox_result) > 0): ?>
        <?php while ($row = mysqli_fetch_assoc($inbox_result)): ?>
            <div class="message">
                <p><strong>Sent by: <?php echo htmlspecialchars($row['sender_name']); ?></strong></p>
                <p>Date: <?php echo htmlspecialchars($row['date_sended']); ?></p>
                <p><?php echo htmlspecialchars($row['content']); ?></p>
                <button class="reply"><i class="fas fa-reply"></i></button>
                <button class="remove"><i class="fas fa-trash"></i></button>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No messages found in the inbox.</p>
    <?php endif; ?>
</div>
