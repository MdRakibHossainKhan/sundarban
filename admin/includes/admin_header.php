<?php
// Ensure session is started, as admin pages require session checks
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sundarban - Admin Panel</title>
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>
<header class="admin-main-header" style="background-color: #333; color: white; padding: 10px 0; text-align: center;">
    <h1>Sundarban Admin Panel</h1>
</header>

<?php include('admin_nav.php'); ?>

<div class="container">