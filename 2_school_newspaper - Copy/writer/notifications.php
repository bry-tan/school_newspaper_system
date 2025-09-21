<?php require_once 'classloader.php'; ?>

<?php 
if (!$userObj->isLoggedIn()) {
  header("Location: login.php");
  exit;
}

$notifications = $notificationObj->getUserNotifications($_SESSION['user_id']);
$editRequests = $editRequestObj->getRequestsForAuthor($_SESSION['user_id'], 'pending');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <style>
        body {
            font-family: "Arial";
        }
        .notification-item {
            transition: background-color 0.3s;
        }
        .notification-item.unread {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }
        .notification-item:hover {
            background-color: #e9ecef;
        }
    </style>
    <title>Notifications - School Publication</title>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php
                if (isset($_SESSION['message'])) {
                    $alertClass = $_SESSION['status'] == '200' ? 'alert-success' : 'alert-danger';
                    echo "<div class='alert {$alertClass}'>{$_SESSION['message']}</div>";
                    unset($_SESSION['message']);
                    unset($_SESSION['status']);
                }
                ?>
                
                <div class="display-4 text-center mb-4">Notifications & Requests</div>
                
                <!-- Edit Requests Section -->
                <?php if (!empty($editRequests)): ?>
                <div class="row justify-content-center mb-5">
                    <div class="col-md-8">
                        <h3 class="text-primary mb-3">
                            <i class="fas fa-edit"></i> Pending Edit Requests
                            <span class="badge badge-primary"><?php echo count($editRequests); ?></span>
                        </h3>
                        
                        <?php foreach ($editRequests as $request): ?>
                        <div class="card mb-3 border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title">
                                            Edit Request for "<?php echo htmlspecialchars($request['article_title']); ?>"
                                        </h5>
                                        <h6 class="card-subtitle mb-2 text-muted">
                                            From: <?php echo htmlspecialchars($request['requester_username']); ?>
                                        </h6>
                                        <p class="card-text">
                                            <strong>Message:</strong> 
                                            <?php echo $request['message'] ? nl2br(htmlspecialchars($request['message'])) : 'No message provided'; ?>
                                        </p>
                                        <small class="text-muted">Requested: <?php echo $request['created_at']; ?></small>
                                    </div>
                                </div>
                                
                                <div class="mt-3">
                                    <form method="POST" action="core/handleForms.php" class="d-inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <input type="hidden" name="action" value="approve">
                                        <button type="submit" name="handleEditRequest" class="btn btn-success mr-2">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="core/handleForms.php" class="d-inline">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <input type="hidden" name="action" value="reject">
                                        <button type="submit" name="handleEditRequest" class="btn btn-danger" 
                                                onclick="return confirm('Are you sure you want to reject this edit request?')">
                                            <i class="fas fa-times"></i> Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Notifications Section -->
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <h3 class="text-secondary mb-3">
                            <i class="fas fa-bell"></i> All Notifications
                            <?php 
                            $unreadCount = $notificationObj->getUnreadCount($_SESSION['user_id']);
                            if ($unreadCount > 0): 
                            ?>
                                <span class="badge badge-danger"><?php echo $unreadCount; ?> unread</span>
                            <?php endif; ?>
                        </h3>
                        
                        <?php if (empty($notifications)): ?>
                            <div class="alert alert-info text-center">
                                <h4>No Notifications</h4>
                                <p>You don't have any notifications yet.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notifications as $notification): ?>
                            <div class="card mb-3 notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>" 
                                 data-notification-id="<?php echo $notification['notification_id']; ?>">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="card-title d-flex align-items-center">
                                                <?php
                                                $iconClass = '';
                                                switch($notification['type']) {
                                                    case 'article_deleted': $iconClass = 'fas fa-trash text-danger'; break;
                                                    case 'edit_request': $iconClass = 'fas fa-edit text-primary'; break;
                                                    case 'edit_approved': $iconClass = 'fas fa-check text-success'; break;
                                                    case 'edit_rejected': $iconClass = 'fas fa-times text-danger'; break;
                                                }
                                                ?>
                                                <i class="<?php echo $iconClass; ?> mr-2"></i>
                                                <?php echo htmlspecialchars($notification['title']); ?>
                                                <?php if (!$notification['is_read']): ?>
                                                    <span class="badge badge-primary ml-2">New</span>
                                                <?php endif; ?>
                                            </h6>
                                            <p class="card-text"><?php echo nl2br(htmlspecialchars($notification['message'])); ?></p>
                                            <small class="text-muted">
                                                <?php echo $notification['created_at']; ?>
                                                <?php if ($notification['related_username']): ?>
                                                    | From: <?php echo htmlspecialchars($notification['related_username']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        
                                        <?php if (!$notification['is_read']): ?>
                                        <button class="btn btn-sm btn-outline-primary mark-read-btn" 
                                                data-notification-id="<?php echo $notification['notification_id']; ?>">
                                            Mark as Read
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Mark notification as read
        $('.mark-read-btn').on('click', function() {
            const notificationId = $(this).data('notification-id');
            const button = $(this);
            const notificationItem = button.closest('.notification-item');
            
            $.ajax({
                type: 'POST',
                url: 'core/handleForms.php',
                data: {
                    markNotificationRead: 1,
                    notification_id: notificationId
                },
                success: function(response) {
                    if (response) {
                        button.remove();
                        notificationItem.removeClass('unread');
                        notificationItem.find('.badge:contains("New")').remove();
                        
                        // Update unread count in navbar if it exists
                        const unreadBadge = $('.navbar .badge');
                        if (unreadBadge.length) {
                            const currentCount = parseInt(unreadBadge.text()) - 1;
                            if (currentCount <= 0) {
                                unreadBadge.remove();
                            } else {
                                unreadBadge.text(currentCount);
                            }
                        }
                    }
                }
            });
        });
    </script>

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>