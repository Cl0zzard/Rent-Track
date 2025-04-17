<?php include 'plugins-header.php'; ?>

<style>
  .login-main-div {
      background: url('images/bg-banner.png') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
  }

  .rgba-black {
      background: rgba(0, 0, 0, 0.5); /* Dark overlay for better readability */
  }
</style>

<div class="login-main-div">
  <div class="rgba-black landing-page-wrapper container-fluid d-flex align-items-center justify-content-center h-100">
  
    <div class="row mx-0 w-100 justify-content-center">
      <div class="col-12 col-lg-7 row mx-0 justify-content-center">
        <div class="col d-none d-xxl-flex flex-column align-items-center justify-content-center text-start text-white">
          <div>
            <img src="images/logo.png" width="160px" height="auto">
          </div>
          <h1>RentTrack</h1>
          <strong>Your one-stop solution for managing rental properties</strong>
        </div>
        <div class="col-11 col-sm-9 col-lg-8 col-xxl-5 py-5 px-4 landing-form bg-white border border-red rounded-1">
          <form method="POST" action="controller/login.php" class="d-flex flex-column row-gap-5">
            <h5 class="fw-bold">Sign In</h5>
            <div class="d-flex flex-column row-gap-3">
              <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="username" required>
                <label for="username">Username</label>
              </div>
              <div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                  <label for="password">Password</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="remember">
                  <label class="form-check-label" for="remember">
                   Remember me
                  </label>
                </div>
              </div>
              <div class="d-flex flex-column text-center">
                <button class="w-100 fw-bold btn btn-primary">Sign In</button>
                <a href="forgot-password" class="text-decoration-none mt-2">Forgot password?</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'plugins-footer.php'; ?>
