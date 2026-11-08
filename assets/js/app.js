let cart = [];

function showWarning(message, title = "Notice") {
    document.getElementById('warning-title').innerText = title;
    document.getElementById('warning-message').innerText = message;
    const warnModal = new bootstrap.Modal(document.getElementById('warningModal'));
    warnModal.show();
}

function loadData() {
    fetch('backend.php?action=get_products')
        .then(res => res.json())
        .then(data => {
            const pos = document.getElementById('pos-products');
            if (!pos) return;

            if (!data || data.length === 0) {
                pos.innerHTML = '<p style="text-align:center;color:#888;grid-column:1/-1;padding:40px;">No products available.</p>';
                return;
            }

            pos.innerHTML = data.map(p => {
                let stockText, stockClass;
                if (p.quantity <= 0) {
                    stockText = "Out of stock";
                    stockClass = "stock-out";
                } else if (p.quantity <= 5) {
                    stockText = "Low in stock";
                    stockClass = "stock-low";
                } else {
                    stockText = `${p.quantity} in stock`;
                    stockClass = "stock-in";
                }

                const imgSrc = p.product_image
                    ? `assets/images/products/${p.product_image}`
                    : `assets/images/products/default.png`;

                return `
                    <div class="product-card">
                        <img src="${imgSrc}" onerror="this.src='assets/images/products/default.png'">
                        <h3>${p.product_name}</h3>
                        <p class="price">PHP ${parseFloat(p.price).toFixed(2)}</p>
                        <div class="stock-badge ${stockClass}">${stockText}</div>
                        <button class="btn-green add-cart-btn"
                            onclick="addToCart(${p.product_id}, '${p.product_name.replace(/'/g,"\\'")}', ${p.price}, ${p.quantity})"
                            ${p.quantity <= 0 ? 'disabled style="opacity:0.5;cursor:not-allowed;"' : ''}>
                            Add to Cart
                        </button>
                    </div>`;
            }).join('');
        })
        .catch(err => {
            console.error('Failed to load products:', err);
        });
}

function addToCart(id, name, price, stock) {
    const item = cart.find(i => i.id === id);
    const currentQty = item ? item.qty : 0;

    if (stock <= 0) {
        showWarning(`${name} is out of stock.`, "Out of Stock");
        return;
    }
    if (currentQty + 1 > stock) {
        showWarning(`Sorry, only ${stock} unit(s) of "${name}" available.`, "Stock Limit");
        return;
    }

    if (item) {
        item.qty++;
    } else {
        cart.push({ id, name, price: parseFloat(price), qty: 1, stock });
    }
    renderCart();
}

function renderCart() {
    const list = document.getElementById('cart-list');
    if (!list) return;

    let subtotal = 0;

    if (cart.length === 0) {
        list.innerHTML = '<p style="color:#aaa;text-align:center;padding:20px;">Cart is empty</p>';
    } else {
        list.innerHTML = cart.map((i, index) => {
            subtotal += i.price * i.qty;
            return `
                <div class="cart-item">
                    <div>
                        <b>${i.name}</b><br>
                        PHP ${(i.price * i.qty).toFixed(2)}
                    </div>
                    <div class="qty-controls">
                        <button onclick="decreaseQty(${index})">−</button>
                        <span>${i.qty}</span>
                        <button onclick="increaseQty(${index})">+</button>
                    </div>
                    <button class="remove-btn" onclick="removeFromCart(${index})">Remove</button>
                </div>`;
        }).join('');
    }

    const tax   = subtotal * 0.12;
    const total = subtotal + tax;
    document.getElementById('subtotal-val').innerText = "PHP " + subtotal.toFixed(2);
    document.getElementById('tax-val').innerText      = "PHP " + tax.toFixed(2);
    document.getElementById('total-val').innerText    = "PHP " + total.toFixed(2);
}

function increaseQty(index) {
    const item = cart[index];
    if (item.qty < item.stock) {
        item.qty++;
        renderCart();
    } else {
        showWarning("Maximum available stock reached.", "Limit Reached");
    }
}

function decreaseQty(index) {
    if (cart[index].qty > 1) {
        cart[index].qty--;
    } else {
        cart.splice(index, 1);
    }
    renderCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function openModal() {
    if (cart.length === 0) {
        showWarning("Your cart is empty. Please add items first.", "Empty Cart");
        return;
    }

    const modal = document.getElementById('paymentModal');
    const list  = document.getElementById('modal-cart-list');

    let currentSubtotal = 0;
    list.innerHTML = cart.map(i => {
        currentSubtotal += i.price * i.qty;
        return `<div class="modal-item">${i.name} x${i.qty} — PHP ${(i.price * i.qty).toFixed(2)}</div>`;
    }).join('');

    const currentTotal = currentSubtotal * 1.12;
    document.getElementById('modal-total').innerText = "PHP " + currentTotal.toFixed(2);
    modal.style.display = "flex";
}

function closeModal() {
    document.getElementById('paymentModal').style.display = "none";
}

function confirmCheckout() {
    closeModal();
    checkout();
}

function checkout() {
    const name      = document.getElementById('cust_name').value.trim();
    const paidInput = document.getElementById('amt_paid').value;
    const paid      = parseFloat(paidInput);
    const totalText = document.getElementById('total-val').innerText.replace("PHP ", "");
    const total     = parseFloat(totalText);

    if (!name) {
        showWarning("Please enter a customer name.", "Missing Name");
        return;
    }
    if (paidInput === "" || isNaN(paid) || paid < 0) {
        showWarning("Please enter a valid payment amount.", "Invalid Payment");
        return;
    }
    if (paid < total) {
        showWarning(`Insufficient payment. Still needs PHP ${(total - paid).toFixed(2)}.`, "Insufficient Funds");
        return;
    }

    const subtotal = total / 1.12;
    const tax      = total - subtotal;
    const change   = paid - total;

    const formData = new FormData();
    formData.append('customer', name);
    formData.append('total',    total);
    formData.append('paid',     paid);
    formData.append('change',   change);
    formData.append('cart',     JSON.stringify(cart));

    fetch('backend.php?action=checkout', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(response => {
            if (response.trim() !== 'success') {
                showWarning("Transaction failed. Please try again.", "Error");
                return;
            }

            document.getElementById('receipt-products').innerHTML = cart.map(item =>
                `<p>${item.name} × ${item.qty} — PHP ${(item.price * item.qty).toFixed(2)}</p>`
            ).join('');

            document.getElementById('receipt-subtotal').innerText = `PHP ${subtotal.toFixed(2)}`;
            document.getElementById('receipt-tax').innerText      = `PHP ${tax.toFixed(2)}`;
            document.getElementById('receipt-total').innerText    = `PHP ${total.toFixed(2)}`;
            document.getElementById('receipt-paid').innerText     = `PHP ${paid.toFixed(2)}`;
            document.getElementById('receipt-change').innerText   = `PHP ${change.toFixed(2)}`;
            document.getElementById('receipt-customer').innerText = name;

            document.getElementById('receiptModal').style.display = "flex";

            cart = [];
            renderCart();
            loadData();
            document.getElementById('cust_name').value  = '';
            document.getElementById('amt_paid').value   = '';
        })
        .catch(() => {
            showWarning("Connection error. Please try again.", "System Error");
        });
}

function closeReceipt() {
    document.getElementById('receiptModal').style.display = "none";
}

function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('active');
}

document.addEventListener('click', function (e) {
    const sidebar   = document.getElementById('sidebar');
    const hamburger = document.querySelector('.hamburger');
    if (sidebar && hamburger && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
        sidebar.classList.remove('active');
    }
});

loadData();
renderCart();
