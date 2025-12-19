<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <!-- Page Title - Will be set by individual pages -->
    <title><?php echo isset($page_title) ? $page_title . ' - EHR System' : 'EHR System'; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
    
    <!-- Additional CSS for specific pages -->
    <?php if(isset($extra_css)): ?>
        <?php foreach($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $base_url . $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    
    <!-- Navigation Bar -->
    <?php include 'navbar.php'; ?>
    
    <!-- Main Content Container -->
    <main class="main-content">
