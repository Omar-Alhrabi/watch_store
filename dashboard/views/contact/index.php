<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Admin Dashboard</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport"/>
    
    <!-- Favicon -->
    <link rel="icon" href="assets/img/wrist-watch.ico" type="image/x-icon" />

    <!-- Fonts and icons -->
    <?php require_once "views/layouts/components/fonts.html"; ?>
  </head> 
  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <?php require_once "views/layouts/components/sidebar.php"; ?>

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <?php require_once "views/layouts/components/logoheader.php"; ?>
          </div>
          <!-- Navbar Header -->
          <?php require_once "views/layouts/components/navbar.php"; ?>          
        </div>  

        <!-- Main Content -->
        <div class="container">
          <div class="page-inner">
            <h1>Contact Messages</h1>
            
            <table class="table table-striped">
              <thead class="table-dark">
              <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Message</th>
              </tr>
              </thead>
              <tbody>
              <?php if (isset($_GET['error'])) : ?>
                <div class="alert alert-danger">
                  <?php echo $_GET['error']; ?>
                </div>
              <?php endif; ?>
              <?php foreach ($contacts as $contact): ?>
              <tr>
              <td><?= $contact['id'] ?></td>
              <td><?= $contact['name'] ?></td>
              <td><?= $contact['email'] ?></td>
              <td><?= $contact['message'] ?></td>
              
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Footer -->
        <?php require_once "views/layouts/components/footer.html"; ?>
      </div>
    </div> 
    
    <!--   Core JS Files   -->
    <?php require "views/layouts/components/scripts.html"; ?>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>