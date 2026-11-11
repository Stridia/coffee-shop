<?php
// menu_management.php
$currPage = 'menu_management';
include 'config.php';
include 'helpers.php';

// Protect route for admin/owner roles
need_role(['owner', 'admin']);

include 'header.php';
include 'navbar.php';
?>

<div class="bg-blur-overlay"></div>

<div class="container my-4">
    <div class="row g-4">
        <!-- Left Column: Products Grid & Header -->
        <div class="col-lg-8">
            <!-- Header & Category Filter Buttons -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <div>
                    <h2 class="fw-bold text-black mb-1">Menu Management</h2>
                    <p class="text-muted small mb-0">Kelola, tambah, edit, dan hapus menu produk cafe</p>
                </div>
                
                <div id="category-filters" class="d-flex flex-wrap gap-2">
                    <!-- Category filter buttons loaded dynamically -->
                </div>
            </div>
            <hr class="mt-0 mb-4 text-primary opacity-75 fw-bold">

            <div id="product-grid">
                <!-- Dynamic Category Headings, Dividers, and Cards -->
            </div>
        </div>

        <!-- Right Column: Sticky Create / Edit Menu Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-4 sticky-top w-100 overflow-hidden" style="top: 80px; max-height: calc(100vh - 100px); display: flex; flex-direction: column;">
                <div class="card-header bg-dark text-white py-3 rounded-top-4 d-flex justify-content-between align-items-center flex-shrink-0">
                    <h5 id="form-card-title" class="mb-0 fw-bold fs-6">Add New Menu</h5>
                    <button id="btn-cancel-edit" class="btn btn-sm btn-light rounded-pill px-3" style="display: none;" onclick="resetForm()">
                        Cancel
                    </button>
                </div>
                <div class="card-body p-3 overflow-y-auto" style="max-height: calc(100vh - 160px);">
                    <form id="menuForm">
                        <input type="hidden" id="product-id" name="id" value="">

                        <!-- Menu Name -->
                        <div class="form-floating mb-2">
                            <input type="text" class="form-control rounded-3" id="prod-name" name="name" placeholder="Menu Name" required>
                            <label for="prod-name">Menu Name</label>
                        </div>

                        <!-- Category / Type -->
                        <div class="form-floating mb-2">
                            <select class="form-select rounded-3" id="prod-type" name="type_id" required>
                                <option value="" disabled selected>Select Category</option>
                                <!-- Categories populated dynamically -->
                            </select>
                            <label for="prod-type">Category</label>
                        </div>

                        <!-- Price -->
                        <div class="form-floating mb-2">
                            <input type="number" class="form-control rounded-3" id="prod-price" name="price" min="1" placeholder="Price (Rp)" required>
                            <label for="prod-price">Price (Rp)</label>
                        </div>

                        <!-- Status -->
                        <div class="form-floating mb-2">
                            <select class="form-select rounded-3" id="prod-status" name="status" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                            <label for="prod-status">Status</label>
                        </div>

                        <!-- Description -->
                        <div class="form-floating mb-2">
                            <textarea class="form-control rounded-3" id="prod-desc" name="description" placeholder="Description" style="height: 75px;"></textarea>
                            <label for="prod-desc">Description</label>
                        </div>

                        <!-- Image URL Field & Live Preview -->
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="url" class="form-control rounded-3" id="prod-image" name="image" placeholder="https://example.com/image.jpg" oninput="updateImagePreview(this.value)">
                                <label for="prod-image">Image URL / Link</label>
                            </div>

                            <!-- Image Preview -->
                            <div id="image-preview-container" class="mt-2 p-2 border rounded-3 bg-light d-none align-items-center gap-2">
                                <img id="image-preview" src="" alt="Preview" class="rounded-2 border flex-shrink-0" style="width: 55px; height: 55px; object-fit: cover;">
                                <div class="flex-grow-1 overflow-hidden">
                                    <small class="fw-semibold text-dark d-block" style="font-size: 0.75rem;">Image Preview</small>
                                    <small id="image-preview-error" class="text-danger d-none" style="font-size: 0.72rem;">
                                        Invalid image URL
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" id="submit-btn" class="btn btn-primary w-100 py-2 rounded-3 fw-bold">
                            + Add Menu
                        </button>
                        
                        <div id="form-msg" class="mt-2"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p class="mb-0">Are you sure you want to delete <strong id="delete-product-name" class="text-black"></strong>?</p>
                <small class="text-muted">This action will deactivate the item from the customer menu.</small>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-3 px-3 fw-semibold" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btn-confirm-delete" class="btn btn-danger rounded-3 px-3 fw-bold">Delete Menu</button>
            </div>
        </div>
    </div>
</div>

<script src="js/menu_management.js"></script>

<?php include 'footer.php'; ?>