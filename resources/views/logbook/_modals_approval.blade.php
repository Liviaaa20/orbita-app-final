{{-- ===== MODAL APPROVE KAPOK ===== --}}
<div class="modal fade" id="modalApproveKapok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-success text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-circle mr-2"></i> Setujui Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalApproveKapokForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Logbook akan diteruskan ke Koordinator setelah disetujui.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Catatan (opsional)</label>
                        <textarea name="catatan_kapok" class="form-control shadow-none" rows="3"
                                  placeholder="Catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check mr-1"></i> Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL REJECT KAPOK ===== --}}
<div class="modal fade" id="modalRejectKapok" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-danger text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> Tolak Logbook
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalRejectKapokForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Admin akan diminta merevisi dan mengajukan ulang.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan_kapok" class="form-control shadow-none" rows="3"
                                  placeholder="Tuliskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-times mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL APPROVE KOORDINATOR ===== --}}
<div class="modal fade" id="modalApproveKoordinator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-success text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-check-double mr-2"></i> Setujui Final
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalApproveKoordinatorForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">
                        Logbook akan berstatus <strong>Disetujui Final</strong> dan PDF dapat diunduh.
                    </p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">Catatan (opsional)</label>
                        <textarea name="catatan_koordinator" class="form-control shadow-none" rows="3"
                                  placeholder="Catatan persetujuan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success font-weight-bold px-4">
                        <i class="fas fa-check-double mr-1"></i> Ya, Setujui Final
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ===== MODAL REJECT KOORDINATOR ===== --}}
<div class="modal fade" id="modalRejectKoordinator" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-danger text-white border-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-times-circle mr-2"></i> Tolak (Koordinator)
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="modalRejectKoordinatorForm">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Logbook akan dikembalikan untuk direvisi.</p>
                    <div class="form-group">
                        <label class="font-weight-bold small text-uppercase">
                            Alasan Penolakan <span class="text-danger">*</span>
                        </label>
                        <textarea name="catatan_koordinator" class="form-control shadow-none" rows="3"
                                  placeholder="Tuliskan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0" style="background: #f8f9fa; border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger font-weight-bold px-4">
                        <i class="fas fa-times mr-1"></i> Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>