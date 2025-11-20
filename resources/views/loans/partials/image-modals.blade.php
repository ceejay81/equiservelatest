<!-- Payment Proof Image Modal -->
<div class="modal fade" id="proofImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header card-header-primary">
        <h5 class="modal-title">
          <i class="fas fa-image mr-2"></i>
          Payment Proof
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body text-center" style="background: #F8FAFC;">
        <div class="mb-3">
          <strong>Reference:</strong> <span id="proofReference" class="text-muted"></span>
        </div>
        <img id="proofImage" src="/placeholder.svg" alt="Payment Proof" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
      </div>
      <div class="modal-footer">
        <a id="proofDownload" href="" download class="btn btn-outline-primary">
          <i class="fas fa-download mr-1"></i>Download
        </a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- ID Image Modal -->
<div class="modal fade" id="idImageModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header card-header-primary">
        <h5 class="modal-title">
          <i class="fas fa-id-card mr-2"></i>
          ID Verification Document
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
      </div>
      <div class="modal-body" style="background: #F8FAFC;">
        <div class="row mb-3">
          <div class="col-6">
            <strong>ID Type:</strong> <span id="idTypeDisplay" class="text-muted"></span>
          </div>
          <div class="col-6">
            <strong>ID Number:</strong> <span id="idNumberDisplay" class="text-muted"></span>
          </div>
        </div>
        <div class="text-center">
          <img id="idImage" src="/placeholder.svg" alt="ID Document" style="max-width: 100%; max-height: 70vh; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
        </div>
      </div>
      <div class="modal-footer">
        <a id="idDownload" href="" download class="btn btn-outline-primary">
          <i class="fas fa-download mr-1"></i>Download
        </a>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
