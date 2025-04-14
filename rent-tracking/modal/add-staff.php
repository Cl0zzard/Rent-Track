<div class="modal modal-lg fade" id="staff-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-body position-relative">
                <div role="button" class="position-absolute top-0 end-0 pt-3 pe-4 fs-4 text-secondary" data-bs-dismiss="modal">
      			    <i class="fas fa-times text-danger"></i>
      		    </div>
                <form method="POST" action="controller/add-staff.php" class="d-flex flex-column row-gap-4 p-5">
    			    <div class="mb-2">
    			        <h5 class="modal-title"></h5>
    			    </div>
    			    <input type="text" name="admin_id" id="admin_id" class="form-control rounded-1"  placeholder="input here" hidden>
    			    <div class="d-flex flex-column flex-lg-row gap-2 ">
    			    	<div class="form-floating w-100">
	    			        <input required type="text" name="name" id="name" class="form-control rounded-1"  placeholder="input here">
	    			        <label for="name">Name</label>
	    			    </div>

	    			    <div class="form-floating w-100">
	    			        <input required type="text" name="username" id="username" class="form-control rounded-1" placeholder="input here">
	    			        <label for="username">Username</label>
	    			    </div>
    			    </div>
    			    <div class="d-flex flex-column flex-lg-row gap-2 ">
    			    	<div class="form-floating w-100">
	    			        <input required type="email" name="email" id="email" class="form-control rounded-1"  placeholder="input here">
	    			        <label for="email">Email</label>
	    			    </div>

	    			    <div class="form-floating w-100">
	    			        <input required type="number" name="phonenumber" id="phonenumber" class="form-control rounded-1" placeholder="input here">
	    			        <label for="phonenumber">Phone No.</label>
	    			    </div>
    			    </div>
    			    <div class="form-floating w-100">
    			        <input required type="text" name="address" id="address" class="form-control rounded-1"  placeholder="input here">
    			        <label for="address">Address</label>
    			    </div>
    			    <div class="form-floating w-100">
    			        <input type="password" name="password" id="password" class="form-control rounded-1"  placeholder="input here">
    			        <label for="password">Password</label>
    			    </div>
    			    <button type="submit" class="save-button btn btn-primary rounded-1">Save</button>
			    </form>
      		</div>
  		</div>
	</div>
</div>

