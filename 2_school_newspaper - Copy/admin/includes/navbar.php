<?php 
// Get notification count for badge
$unreadCount = 0;
if (isset($notificationObj) && isset($_SESSION['user_id'])) {
    $unreadCount = $notificationObj->getUnreadCount($_SESSION['user_id']);
}

// Determine if we're in admin or writer section
$isAdminSection = strpos($_SERVER['REQUEST_URI'], '/admin/') !== false;
$navbarColor = $isAdminSection ? '#008080' : '#355E3B';
$brandText = $isAdminSection ? 'Admin Panel' : 'Writer Panel';
?>

<nav class="navbar navbar-expand-lg navbar-dark p-4" style="background-color: <?php echo $navbarColor; ?>;">
    <a class="navbar-brand" href="index.php">
        <i class="<?php echo $isAdminSection ? 'fas fa-user-shield' : 'fas fa-pen'; ?>"></i>
        <?php echo $brandText; ?>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">
                    <i class="fas fa-home"></i> Home
                </a>
            </li>
            
            <?php if ($isAdminSection): ?>
                <!-- Admin-specific navigation -->
                <li class="nav-item">
                    <a class="nav-link" href="articles_from_students.php">
                        <i class="fas fa-clock"></i> Pending Articles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="articles_submitted.php">
                        <i class="fas fa-newspaper"></i> My Articles
                    </a>
                </li>
            <?php else: ?>
                <!-- Writer-specific navigation -->
                <li class="nav-item">
                    <a class="nav-link" href="articles_submitted.php">
                        <i class="fas fa-newspaper"></i> My Articles
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="shared_articles.php">
                        <i class="fas fa-share-alt"></i> Shared Articles
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
        <ul class="navbar-nav">
            <!-- Notifications -->
            <li class="nav-item">
                <a class="nav-link position-relative" href="notifications.php">
                    <i class="fas fa-bell"></i>
                    Notifications
                    <?php if ($unreadCount > 0): ?>
                        <span class="badge badge-danger badge-pill position-absolute" style="top: 5px; right: 5px; font-size: 0.7em;">
                            <?php echo $unreadCount > 99 ? '99+' : $unreadCount; ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            
            <!-- User Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-user"></i>
                    <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User'; ?>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="../index.php">
                        <i class="fas fa-globe"></i> Main Homepage
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="core/handleForms.php?logoutUserBtn=1">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>

<!-- Include Bootstrap JS for dropdown functionality -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Auto-refresh notification count every 30 seconds -->
<script>
$(document).ready(function() {
    // Only auto-refresh if we have notifications object available
    <?php if (isset($notificationObj) && isset($_SESSION['user_id'])): ?>
    function updateNotificationCount() {
        $.ajax({
            url: 'core/get_notification_count.php',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                const badge = $('.navbar .badge');
                if (response.count > 0) {
                    if (badge.length === 0) {
                        $('.nav-link[href="notifications.php"]').append(
                            '<span class="badge badge-danger badge-pill position-absolute" style="top: 5px; right: 5px; font-size: 0.7em;">' +
                            (response.count > 99 ? '99+' : response.count) + '</span>'
                        );
                    } else {
                        badge.text(response.count > 99 ? '99+' : response.count);
                    }
                } else {
                    badge.remove();
                }
            }
        });
    }
    
    // Update every 30 seconds
    setInterval(updateNotificationCount, 30000);
    <?php endif; ?>
});
</script>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">