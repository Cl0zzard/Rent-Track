<div class="modal fade" id="transaction-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body position-relative">
                <div role="button" class="position-absolute top-0 end-0 pt-3 pe-4 fs-4 text-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times text-danger"></i>
                </div>
                <form method="POST" action="controller/create-transaction.php" class="d-flex flex-column row-gap-4 p-5">
                    <div class="mb-2">
                        <h5 class="modal-title"></h5>
                    </div>
                    <input required type="hidden" name="transaction_type" value="1">
                    <input type="hidden" name="transaction_history_id" class="transaction_history_id form-control rounded-1" >
                    <input required type="hidden" name="stall_slots_id" value="<?= $get_stall_slots_id; ?>" class="form-control rounded-1" >

                    <div class="form-floating w-100">
                        <input required type="number" name="balance" id="balance" class="form-control rounded-1"  placeholder="input here">
                        <label for="balance">Balance</label>
                    </div>
                        
                    <div class="form-floating w-100">
                        <input required type="number" name="penalty" id="penalty" class="form-control rounded-1"  placeholder="input here">
                        <label for="penalty">Penalty</label>
                    </div>
                    <div class="form-floating w-100">
                        <input required type="date" name="duedate" id="duedate" class="form-control rounded-1" placeholder="input here">
                        <label for="duedate">Due Date</label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="save-button btn btn-primary rounded-1">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

