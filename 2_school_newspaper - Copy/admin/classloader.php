<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Article.php';
require_once 'classes/Notification.php';
require_once 'classes/EditRequest.php';
require_once 'classes/SharedArticle.php';
require_once 'classes/Category.php';

// Initialize objects
$databaseObj = new Database();
$userObj = new User();
$articleObj = new Article();
$notificationObj = new Notification();
$editRequestObj = new EditRequest();
$sharedArticleObj = new SharedArticle();
$categoryObj = new Category();

// Start session
$userObj->startSession();
?>