<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <script src="https://kit.fontawesome.com/d890c03bb3.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/hero.css">
    <link rel="stylesheet" href="assets/css/featured.css">
    <link rel="stylesheet" href="assets/css/categories.css">
    <link rel="stylesheet" href="assets/css/about.css">
    <link rel="stylesheet" href="assets/css/footer.css">
</head>
<body>
    <?php include 'views/components/navbar.php'; ?>
    <?php include 'views/components/hero.html'; ?>
    <?php include 'views/components/featured.php'; ?>
    <?php include 'views/components/collections.html'; ?>
    <?php include 'views/components/newArrival.php'; ?>
    <?php include 'views/components/aboutSection.html'; ?>
    <?php include 'views/components/footer.html'; ?>

    <script src="assets/js/categories.js"></script>
    <script src="assets/js/featured.js"></script>
    <script src="assets/js/hero.js"></script>
    <script src="assets/js/navbar.js"></script>
</body>
</html>