    
    <!-- From Uiverse.io by Shoh2008 loading div --> 
    <div id="loader-div" class="d-none position-fixed h-100 w-100 top-0 end-0 start-0 bottom-0" style="background: rgba(0, 0, 0, 0.6); z-index: 3000;">
      <div class="d-flex align-items-center justify-content-center w-100 h-100">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>

    <div class="sidebar shadow-sm" style="background-color: var(--dark-red) !important;">
        <div id="logo-div" class="d-flex align-items-center fw-bolder px-3 shadow-sm">
            <div class="w-100 h-100 d-flex align-items-center justify-content-center column-gap-2">
                <img src="images/logo.png" width="50" height="50">
                <h2 class="m-0">RentTrack</h2>
            </div>
            <div class="position-relative p-3 ms-auto d-flex align-items-center ">
                <div type="button" id="menu-icon" class="d-flex justify-content-end py-2 px-1 w-100 dark-gray">
                    <i class="fs-4 fa-solid fa-bars text-dark"></i>
                </div>
            </div>
        </div>
        <ul class="navbar-nav mt-3 px-3 gap-1 position-relative h-100">
            <li class="nav-item">
                <a href="dashboard" class="py-2 px-3 nav-link d-flex align-items-center justify-content-start column-gap-3">
                    <div class="icon-size text-dark">
                        <i class="fa-solid fa-gauge-high"></i>
                    </div>
                    <small class="fw-bold text-nowrap">Dashboard</small>
                </a>
            </li>

            <?php 
            if ($_SESSION['admin']['role'] === 1) {
            ?>

            <li class="nav-item">
                <a href="#accountList" data-bs-toggle="collapse" aria-expanded="false" aria-controls="accountList" class="py-2 px-3 nav-link d-flex align-items-center justify-content-start column-gap-3 position-relative">
                    <i class="fa-solid fa-user-gear text-dark"></i>
                    <small class="fw-bold text-nowrap">
                        <span>Management</span>
                        <i class="fa-solid fa-angle-down position-absolute end-0 pe-3" style="top: 50%; transform: translateY(-50%);"></i>
                    </small>
                    
                </a>

                <div class="collapse" id="accountList">
                    <a href="management?account_status=1" class="py-2 nav-link">
                        <div class="ms-5 sub text-nowrap">
                            <small><i class="fa-solid fa-chevron-right me-1 text-dark"></i></small>
                            <small class="fw-bold ">Registered Account</small>
                        </div>
                    </a>
                    <a href="management?account_status=2" class="py-2 nav-link">
                        <div class="ms-5 sub text-nowrap">
                            <small><i class="fa-solid fa-chevron-right me-1 text-dark"></i></small>
                            <small class="fw-bold ">Archived Account</small>
                        </div>
                    </a>
                </div>
            </li>
            <?php 
            }
            ?>
            <li class="nav-item">
                <a href="stall-slots" class="py-2 px-3 nav-link d-flex align-items-center justify-content-start column-gap-3">
                    <div class="icon-size text-dark">
                        <i class="fa-solid fa-building"></i>
                    </div>
                    <small class="fw-bold text-nowrap">Stall Slots</small>
                </a>
            </li>
            <li class="nav-item">
                <a href="archived-history" class="py-2 px-3 nav-link d-flex align-items-center justify-content-start column-gap-3">
                    <div class="icon-size text-dark">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                    <small class="fw-bold text-nowrap">Archived History</small>
                </a>
            </li>




            <li class="nav-item position-absolute z-3 w-100 start-0 end-0" style="bottom: 86px;">
                <div class="px-4 d-flex align-items-center py-2 column-gap-3">
                    <img src="images/default-profile.jpg" width="30%" height="auto" class="rounded-circle">
                    <div>
                        <strong><?= $_SESSION['admin']['username']?></strong>
                        <br>
                        <small class="m-0">Role: <?php if ($_SESSION['admin']['role'] == 1) {
                           echo "Admin";
                        }else{
                            echo "Staff";
                        }?>
                            
                        </small>
                    </div>
                </div>
                <a href="logout.php" class="text-decoration-none">
                    <div  class="dark-yellow text-white w-100 text-center py-2">
                        Sign Out
                    </div>
                </a>

            </li>


        </ul>
    </div>
    <!-- sidebar end -->