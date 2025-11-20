<!-- Record Payment Modal -->
<div class="modal fade" id="recordPaymentModal" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form method="POST" action="{{ route('loans.payments.store', $loan) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title"><i class="fas fa-money-bill-wave mr-2"></i>Record Payment</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>Remaining Balance:</strong> ₱{{ number_format($loan->balance, 2) }}<br>
            <strong>Suggested Payment:</strong> ₱{{ number_format($loan->next_payment_amount, 2) }}
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label required">Payment Date</label>
                <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="form-group">
                <label class="form-label required">Amount Paid</label>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">₱</span>
                  </div>
                  <input type="number" step="0.01" name="amount_paid" class="form-control" value="{{ $loan->next_payment_amount }}" min="0" max="{{ $loan->balance }}" required>
                </div>
                <small class="text-muted">Maximum: ₱{{ number_format($loan->balance, 2) }}</small>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <label class="form-label required">Mode of Payment</label>
            <select name="mode_of_payment" id="paymentMode" class="form-control" required>
              <option value="Cash">Cash</option>
              <option value="Bank Transfer">Bank Transfer</option>
              <option value="GCash">GCash</option>
              <option value="PayMaya">PayMaya</option>
              <option value="Check">Check</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <!-- Online Payment Fields -->
          <div id="onlinePaymentFields" style="display: none;">
            <div class="alert alert-warning">
              <i class="fas fa-info-circle mr-1"></i>
              <strong>Online Payment:</strong> Reference number and proof of payment are required.
            </div>

            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Bank/Platform</label>
                  <input type="text" name="payment_bank" class="form-control" placeholder="e.g., BPI, BDO, GCash">
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Payment Timestamp</label>
                  <input type="datetime-local" name="payment_timestamp" class="form-control">
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label required" id="refRequired">Reference Number</label>
              <input type="text" name="reference_number" id="referenceNumber" class="form-control" placeholder="e.g., BPI-123456789">
            </div>
            
            <div class="form-group">
              <label class="form-label required" id="proofRequired">Proof of Payment</label>
              <input type="file" name="proof_image" id="proofImage" class="form-control-file" accept="image/*">
              <small class="text-muted">JPG, PNG, GIF (max 2MB)</small>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">
            <i class="fas fa-save mr-1"></i> Save Payment
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
