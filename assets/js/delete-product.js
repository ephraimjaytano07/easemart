document.addEventListener('click', function (e) {
    const btn = e.target.closest('.deleteBtn');
    if (btn) {
        document.getElementById('deleteProductID').value = btn.dataset.id || '';
    }
});
