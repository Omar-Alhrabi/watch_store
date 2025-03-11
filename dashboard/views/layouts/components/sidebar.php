<div class="sidebar" data-background-color="dark">
  <div class="sidebar-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
      <a href="index.php?controller=dashboard&action=index" class="logo">
        <img
          src="<?php echo ($_SESSION['role'] == 'superadmin') ? 'assets/img/superadmin.png' : 'assets/img/dash-logo.png'; ?>"
          alt="navbar brand"
          class="navbar-brand"
          height="20"
        />
      </a>
      <div class="nav-toggle">
        <button class="btn btn-toggle toggle-sidebar">
          <i class="gg-menu-right"></i>
        </button>
        <button class="btn btn-toggle sidenav-toggler">
          <i class="gg-menu-left"></i>
        </button>
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <div class="sidebar-wrapper scrollbar scrollbar-inner">
    <div class="sidebar-content">
      <ul class="nav nav-secondary">
        <li class="nav-item">
          <a
            href="index.php?controller=dashboard&action=index"
            class="collapsed"
            aria-expanded="false"
          >
            <i class="fas fa-home"></i>
            <p>Home</p>
          </a>
        </li>
        <li class="nav-section">
          <span class="sidebar-mini-icon">
            <i class="fa fa-ellipsis-h"></i>
          </span>
          <h4 class="text-section">Sections</h4>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#base">
            <i class="fas fa-boxes"></i>
            <p>Products</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="base">
            <ul class="nav nav-collapse">
              <li>
                <a href="index.php?controller=product&action=index">
                  <span class="sub-item">All Products</span>
                </a>
              </li>
              <li>
                <a href="index.php?controller=product&action=create">
                  <span class="sub-item">Create new Product</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#orders">
            <i class="fas fa-shopping-cart"></i>
            <p>Orders</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="orders">
            <ul class="nav nav-collapse">
              <li>
                <a href="index.php?controller=order&action=index">
                  <span class="sub-item">Orders List</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#forms">
            <i class="fas fa-list-ul"></i>
            <p>Categories</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="forms">
            <ul class="nav nav-collapse">
              <li>
                <a href="index.php?controller=category&action=index">
                  <span class="sub-item">All Categories</span>
                </a>
              </li>
              <li>
                <a href="index.php?controller=category&action=create">
                  <span class="sub-item">Create new Category</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#discounts">
            <i class="fas fa-percentage"></i>
            <p>Discounts</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="discounts">
            <ul class="nav nav-collapse">
              <li>
                <a href="index.php?controller=discount&action=index">
                  <span class="sub-item">Discounts List</span>
                </a>
              </li>
              <li>
                <a href="index.php?controller=discount&action=create">
                  <span class="sub-item">Create new Discount</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <li class="nav-item">
          <a data-bs-toggle="collapse" href="#customers">
            <i class="fas fa-users"></i>
            <p>Customers</p>
            <span class="caret"></span>
          </a>
          <div class="collapse" id="customers">
            <ul class="nav nav-collapse">
              <li>
                <a href="index.php?controller=customer&action=index">
                  <span class="sub-item">Customers List</span>
                </a>
              </li>
              <li>
                <a href="index.php?controller=customer&action=create">
                  <span class="sub-item">Create new Customer</span>
                </a>
              </li>
            </ul>
          </div>
        </li>
        <?php if ($_SESSION['role'] == 'superadmin') : ?>
          <li class="nav-item">
            <a data-bs-toggle="collapse" href="#tables">
              <i class="fas fa-user-cog"></i>
              <p>Admins</p>
              <span class="caret"></span>
            </a>
            <div class="collapse" id="tables">
              <ul class="nav nav-collapse">
                <li>
            <a href="index.php?controller=admin&action=index">
              <span class="sub-item">Admins Data</span>
            </a>
                </li>
                <li>
            <a href="index.php?controller=admin&action=create">
              <span class="sub-item">Create new Admin</span>
            </a>
                </li>
              </ul>
            </div>
          </li>
        <?php endif; ?>
        <li class="nav-item">
            <a  href="index.php?controller=contact&action=index">
            <i class="fas fa-address-book"></i>
              <p>Contacts</p>
            </a>
          </li>
      </ul>
    </div>
  </div>
</div>
