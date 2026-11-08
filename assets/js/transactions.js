/* ── Transaction details modal ── */
const modal      = document.getElementById('transactionModal');
const closeModal = document.getElementById('closeModal');

document.querySelectorAll('.open-modal').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('modalTransactionId').textContent  = btn.dataset.id;
        document.getElementById('modalCustomerName').textContent   = btn.dataset.customer;
        document.getElementById('modalTotalAmount').textContent    = btn.dataset.total;
        document.getElementById('modalAmountPaid').textContent     = btn.dataset.paid;
        document.getElementById('modalChange').textContent         = btn.dataset.change;
        document.getElementById('modalDate').textContent           = btn.dataset.date;
        modal.classList.add('show');
    });
});

if (closeModal) {
    closeModal.addEventListener('click', () => modal.classList.remove('show'));
}
window.addEventListener('click', e => {
    if (e.target === modal) modal.classList.remove('show');
});

/* ── Search ── */
const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('#transactionsTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(filter) ? '' : 'none';
        });
    });
}

/* ── Sidebar toggle ── */
const menuToggle = document.getElementById('menuToggle');
const sidebar    = document.getElementById('sidebar');
const overlay    = document.getElementById('overlay');

if (menuToggle && sidebar && overlay) {
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
    });
    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
    });
}

document.querySelectorAll('.sidebar-nav a').forEach(link => {
    link.addEventListener('click', () => {
        if (sidebar) sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('show');
    });
});

/* ── Receipt modal ── */
const receiptModal   = document.getElementById('receiptModal');
const receiptContent = document.getElementById('receiptContent');
const closeReceipt   = document.getElementById('closeReceipt');
const printBtn       = document.getElementById('printReceiptBtn');

document.querySelectorAll('.open-receipt').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        fetch(`fetch_receipt.php?id=${id}`)
            .then(res => res.text())
            .then(html => {
                receiptContent.innerHTML = html;
                receiptModal.classList.add('show');
            })
            .catch(() => {
                receiptContent.innerHTML = '<p style="text-align:center;color:#888;">Failed to load receipt.</p>';
                receiptModal.classList.add('show');
            });
    });
});

if (closeReceipt) {
    closeReceipt.addEventListener('click', () => receiptModal.classList.remove('show'));
}
window.addEventListener('click', e => {
    if (e.target === receiptModal) receiptModal.classList.remove('show');
});

/* ── Print receipt ── */
if (printBtn) {
    printBtn.addEventListener('click', () => {
        const content = receiptContent.innerHTML;
        const win = window.open('', '', 'width=420,height=620');
        win.document.write(`
            <html><head><title>Receipt</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                .receipt { max-width: 320px; margin: auto; }
                .receipt-header { text-align: center; margin-bottom: 14px; }
                .receipt-header img { width: 160px; }
                hr { border: 1px dashed #bbb; margin: 10px 0; }
                p { margin: 6px 0; font-size: 14px; }
                .receipt-footer { text-align: center; font-size: 13px; color: #666; margin-top: 14px; }
            </style>
            </head><body>${content}</body></html>
        `);
        win.document.close();
        win.focus();
        win.print();
    });
}
