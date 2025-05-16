<div class="modal fade" id="pay-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body position-relative">
                <div role="button" class="position-absolute top-0 end-0 pt-3 pe-4 fs-4 text-secondary"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times text-danger"></i>
                </div>
                <form id="payment-form" class="d-flex flex-column row-gap-4 p-5">
                    <div class="mb-2">
                        <h5 class="modal-title"></h5>
                    </div>
                    <input required type="hidden" name="stall_slots_id" value="<?= $get_stall_slots_id; ?>"
                        class="form-control rounded-1">
                    <input type="hidden" name="transaction_history_id"
                        class="transaction_history_id form-control rounded-1">
                    <div class="form-floating w-100">
                        <input required type="number" name="amount_paid" id="amount_paid" class="form-control rounded-1"
                            placeholder="input here">
                        <label for="amount_paid">Amount Paid</label>
                    </div>
                    <div class="form-floating w-100">
                        <input required type="number" name="downpayment" id="downpayment" class="form-control rounded-1"
                            placeholder="input here" value="0">
                        <label for="amount_paid">Advance Payment (if available)</label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="save-button btn btn-primary rounded-1">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal modal-lg fade" id="upload-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body position-relative">
                <div role="button" class="position-absolute top-0 end-0 pt-3 pe-4 fs-4 text-secondary"
                    data-bs-dismiss="modal">
                    <i class="fas fa-times text-danger"></i>
                </div>
                <form method="POST" action="controller/upload-transaction-file.php" enctype="multipart/form-data"
                    class="d-flex flex-column row-gap-4 p-5">
                    <div class="mb-2">
                        <h5 class="modal-title">Upload more file</h5>
                    </div>

                    <input type="text" name="transaction_history_id" id="transaction_history_id2"
                        class="form-control rounded-1" placeholder="input here" hidden>
                    <input type="text" name="stall_slots_id" id="stall_slots_id2" class="form-control rounded-1"
                        placeholder="input here" hidden>

                    <div class="form-floating w-100">
                        <input type="file" name="transaction_file[]" id="transaction_file"
                            class="form-control rounded-1" placeholder="input here" multiple>
                        <label for="stall_file">Upload File</label>
                    </div>
                    <button type="submit" class="save-button btn btn-primary rounded-1">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>