document.addEventListener('click', function (e) {
    const btn = e.target.closest('.editBtn');
    if (btn) {
        document.getElementById('edit_product_id').value       = btn.dataset.id       || '';
        document.getElementById('edit_product_name').value     = btn.dataset.name     || '';
        document.getElementById('edit_price').value            = btn.dataset.price    || '';
        document.getElementById('edit_quantity').value         = btn.dataset.quantity || '';
    }
});
