/* ── Toast ── */
function showToast(message, type) {
    const container = document.getElementById('toastContainer');
    const icons = {
        success: '<i class="fas fa-check"></i>',
        cancel:  '<i class="fas fa-times"></i>',
        warning: '<i class="fas fa-exclamation-circle"></i>'
    };
    const toast = document.createElement('div');
    toast.className = 'custom-toast toast-' + type;
    toast.innerHTML = (icons[type] || '') + '<span>' + message + '</span>';
    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(60px)';
        toast.style.transition = '0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/* ── Show toasts from URL params ── */
(function () {
    const params  = new URLSearchParams(window.location.search);
    const success = params.get('success');
    if (success === 'added')   showToast('Product added successfully!', 'success');
    if (success === 'updated') showToast('Product updated successfully!', 'success');
    if (success === 'deleted') showToast('Product deleted successfully!', 'success');
    if (success === 'error')   showToast('Something went wrong. Please try again.', 'cancel');
    if (success) history.replaceState(null, '', window.location.pathname);
})();

/* ── Bootstrap modal instances ── */
const addModalEl     = document.getElementById('addModal');
const editModalEl    = document.getElementById('editModal');
const deleteModalEl  = document.getElementById('deleteModal');
const confirmModalEl = document.getElementById('confirmModal');

const addModal     = new bootstrap.Modal(addModalEl);
const editModal    = new bootstrap.Modal(editModalEl);
const deleteModal  = new bootstrap.Modal(deleteModalEl);
const confirmModal = new bootstrap.Modal(confirmModalEl);

let pendingForm = null;

/* ── ADD ── */
document.getElementById('addSaveBtn').addEventListener('click', function () {
    const form = document.getElementById('addProductForm');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    pendingForm = form;
    document.getElementById('confirmTitle').textContent   = 'Add Product';
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to add this product?';
    document.getElementById('confirmYesBtn').className    = 'btn btn-success btn-sm';
    addModal.hide();
    addModalEl.addEventListener('hidden.bs.modal', function open() {
        confirmModal.show();
        addModalEl.removeEventListener('hidden.bs.modal', open);
    });
});

document.getElementById('addCancelBtn').addEventListener('click', () => showToast('Add cancelled.', 'cancel'));
document.getElementById('addCloseX').addEventListener('click',    () => showToast('Add cancelled.', 'cancel'));

/* ── EDIT ── */
document.getElementById('editSaveBtn').addEventListener('click', function () {
    const form = document.getElementById('editProductForm');
    if (!form.checkValidity()) { form.reportValidity(); return; }
    pendingForm = form;
    document.getElementById('confirmTitle').textContent   = 'Edit Product';
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to save these changes?';
    document.getElementById('confirmYesBtn').className    = 'btn btn-success btn-sm';
    editModal.hide();
    editModalEl.addEventListener('hidden.bs.modal', function open() {
        confirmModal.show();
        editModalEl.removeEventListener('hidden.bs.modal', open);
    });
});

document.getElementById('editCancelBtn').addEventListener('click', () => showToast('Edit cancelled.', 'cancel'));
document.getElementById('editCloseX').addEventListener('click',    () => showToast('Edit cancelled.', 'cancel'));

/* ── DELETE ── */
document.getElementById('deleteConfirmBtn').addEventListener('click', function () {
    pendingForm = document.getElementById('deleteProductForm');
    document.getElementById('confirmTitle').textContent   = 'Delete Product';
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to delete this product? This cannot be undone.';
    document.getElementById('confirmYesBtn').className    = 'btn btn-danger btn-sm';
    deleteModal.hide();
    deleteModalEl.addEventListener('hidden.bs.modal', function open() {
        confirmModal.show();
        deleteModalEl.removeEventListener('hidden.bs.modal', open);
    });
});

document.getElementById('deleteCancelBtn').addEventListener('click', () => showToast('Delete cancelled.', 'cancel'));
document.getElementById('deleteCloseX').addEventListener('click',    () => showToast('Delete cancelled.', 'cancel'));

/* ── CONFIRM YES / NO ── */
document.getElementById('confirmYesBtn').addEventListener('click', function () {
    confirmModal.hide();
    if (pendingForm) pendingForm.submit();
});

document.getElementById('confirmNoBtn').addEventListener('click', function () {
    confirmModal.hide();
    if (pendingForm) {
        const id = pendingForm.id;
        if (id === 'addProductForm')    showToast('Add cancelled.', 'cancel');
        if (id === 'editProductForm')   showToast('Edit cancelled.', 'cancel');
        if (id === 'deleteProductForm') showToast('Delete cancelled.', 'cancel');
        pendingForm = null;
    }
});
