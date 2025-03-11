<?php
  session_start();
  
  require_once 'config/database.php';
  require_once 'controllers/ProductController.php';
  require_once 'controllers/DashboardController.php';
  require_once 'controllers/CategoryController.php';
  require_once 'controllers/DiscountController.php';
  require_once 'controllers/CustomerController.php';
  require_once 'controllers/OrderController.php';
  require_once 'controllers/AdminController.php';
  require_once 'controllers/ContactController.php';

  if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'superadmin')) {
      header("Location: /watch_store/public");
      exit();
  }

  $controller = isset($_GET['controller']) ? $_GET['controller'] : 'dashboard';
  $action = isset($_GET['action']) ? $_GET['action'] : 'index';

  $controllers = [
    'dashboard' => new DashboardController(),
    'product' => new ProductController(),
    'category' => new CategoryController(),
    'discount' => new DiscountController(),
    'customer' => new CustomerController(),
    'order' => new OrderController(),
    'admin' => new AdminController(),
    'contact' => new ContactController()
  ];
  
  if (array_key_exists($controller, $controllers)) {
      $ctrl = $controllers[$controller];

      if (method_exists($ctrl, $action)) {
          $ctrl->$action();
      } else {
          echo "Action not found!";
      }
  } else {
      header("Location: /watch_store/public");
  }
?>