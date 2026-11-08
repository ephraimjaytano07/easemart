fetchProducts('');

document.getElementById('searchInput').addEventListener('input', function () {
    fetchProducts(this.value.trim());
});

function fetchProducts(query) {
    fetch('fetch_products.php?search=' + encodeURIComponent(query))
        .then(res => res.text())
        .then(html => {
            document.getElementById('productTableBody').innerHTML = html;
        })
        .catch(err => console.error('Error fetching products:', err));
}
