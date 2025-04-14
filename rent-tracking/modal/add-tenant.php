<div class="modal modal-lg fade" id="tenant-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-body position-relative">
                <div role="button" class="position-absolute top-0 end-0 pt-3 pe-4 fs-4 text-secondary" data-bs-dismiss="modal">
      			    <i class="fas fa-times text-danger"></i>
      		    </div>
                <form method="POST" action="controller/add-tenant.php" enctype="multipart/form-data" class="d-flex flex-column row-gap-4 p-5">
    			    <div class="mb-2">
    			        <h5 class="modal-title"></h5>
    			    </div>
    			    <input type="text" name="stall_slots_id" id="stall_slots_id" class="form-control rounded-1"  placeholder="input here" hidden>
    			    <div class="d-flex flex-column flex-lg-row gap-2 ">
    			    	<div class="form-floating w-100">
	    			        <input required type="text" name="tenantname" id="tenantname" class="form-control rounded-1"  placeholder="input here">
	    			        <label for="tenantname">Stall Name</label>
	    			    </div>

	    			    <div class="form-floating w-100">
	    			        <input required type="number" name="monthly" id="monthly" class="form-control rounded-1" placeholder="input here">
	    			        <label for="monthly">Monthly</label>
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
    			        <input required type="text" name="manager_name" id="manager_name" class="form-control rounded-1"  placeholder="input here">
    			        <label for="manager_name">Manager Name</label>
    			    </div>
    			    <div class="form-floating">
    			    	<select name="location" id="location" class="form-select rounded-1" required>
    			    		<option value="" selected hidden>Choose Below</option>
    			    		<option value="1">USA BED Campus</option>
    			    		<option value="2">USA Main Campus</option>
    			    		<option value="3">USA Main Kiosks</option>
    			    	</select>
    			        <label for="location">Location</label>
    			    </div>
    			    <button type="submit" class="save-button btn btn-primary rounded-1">Save</button>
			    </form>
      		</div>
  		</div>
	</div>
</div>




<div class="modal modal-lg fade" id="upload-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  	<div class="modal-dialog">
    	<div class="modal-content">
      		<div class="modal-body position-relative">
                <div role="button" class="position-absolute top-0 end-0 pt-3 pe-4 fs-4 text-secondary" data-bs-dismiss="modal">
      			    <i class="fas fa-times text-danger"></i>
      		    </div>
                <form method="POST" action="controller/upload-stall-file.php" enctype="multipart/form-data" class="d-flex flex-column row-gap-4 p-5">
    			    <div class="mb-2">
    			        <h5 class="modal-title">Upload more file</h5>
    			    </div>
    			    <input type="text" name="stall_slots_id" id="stall_slots_id2" class="form-control rounded-1"  placeholder="input here" hidden>
    			    
    			    <div class="form-floating w-100">
    					<input type="file" name="stall_file[]" id="stall_file" class="form-control rounded-1" placeholder="input here" multiple>
    			        <label for="stall_file">Upload File</label>
    			    </div>
    			    <button type="submit" class="save-button btn btn-primary rounded-1">Save</button>
			    </form>
      		</div>
  		</div>
	</div>
</div>

