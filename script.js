const STORAGE_KEY = 'pdvCart';

// Funções de Ajuda
function getCart() {
    const cartJson = localStorage.getItem(STORAGE_KEY);
    return cartJson ? JSON.parse(cartJson) : {};
}

function saveCart(cart) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
    updateCartDisplay();
}

function clearCart() {
    localStorage.removeItem(STORAGE_KEY);
    updateCartDisplay();
}

// Lógica do Carrinho

/**
 * Adiciona um produto ao carrinho ou aumenta a quantidade.
 * @param {string} id
 * @param {string} name
 * @param {number} price
 * @param {number} stock
 */
function addToCart(id, name, price, stock) {
    const cart = getCart();
    
    // Converte para string para usar como chave
    const productId = String(id); 

    if (cart[productId]) {
        if (cart[productId].quantity < stock) {
            cart[productId].quantity++;
        } else {
            alert(`Estoque máximo para ${name} atingido! (${stock})`);
        }
    } else {
        cart[productId] = { id: productId, name, price, quantity: 1 };
    }

    saveCart(cart);
}

/**
 * Remove um item ou diminui a quantidade no carrinho.
 * @param {string} id
 */
function removeFromCart(id, all = false) {
    const cart = getCart();
    const productId = String(id);

    if (cart[productId]) {
        if (all || cart[productId].quantity <= 1) {
            delete cart[productId];
        } else {
            cart[productId].quantity--;
        }
    }
    saveCart(cart);
}

// Atualização da Interface

function updateCartDisplay() {
    const cart = getCart();
    const cartList = document.getElementById('cartList');
    const cartTotalElement = document.getElementById('cartTotal');
    const finalizeBtn = document.getElementById('finalizeSaleBtn');
    const clearBtn = document.getElementById('clearCartBtn');
    const cartDataInput = document.getElementById('cartDataInput');
    
    let total = 0;
    let html = '';

    for (const id in cart) {
        const item = cart[id];
        const subtotal = item.price * item.quantity;
        total += subtotal;

        html += `
            <div class="cart-item">
                <div>
                    <strong>${item.name}</strong>
                    <span class="text-muted small">${item.quantity} x R$ ${item.price.toFixed(2)}</span>
                </div>
                <div>
                    <span class="font-weight-bold">R$ ${subtotal.toFixed(2)}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" onclick="removeFromCart(${item.id}, true)">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
            </div>
        `;
    }

    cartList.innerHTML = html || '<div class="text-muted text-center p-3">Carrinho vazio.</div>';
    cartTotalElement.textContent = total.toFixed(2);
    
    const hasItems = Object.keys(cart).length > 0;
    finalizeBtn.disabled = !hasItems;
    clearBtn.disabled = !hasItems;
    
    // Prepara os dados para o POST PHP
    cartDataInput.value = JSON.stringify(cart); 
}

// Inicialização e Listeners
document.addEventListener('DOMContentLoaded', () => {
    updateCartDisplay(); // Carrega o carrinho na inicialização

    // Listener para Adicionar Produto ao Carrinho
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const stock = parseInt(this.dataset.stock);

            if (stock > 0) {
                 addToCart(id, name, price, stock);
            } else {
                alert(`O produto "${name}" está sem estoque.`);
            }
           
        });
    });

    // Listener para Limpar Carrinho
    document.getElementById('clearCartBtn').addEventListener('click', clearCart);

    // Listener para Finalizar Venda
    const finalizeForm = document.getElementById('finalizeSaleForm');
    if (finalizeForm) {
        finalizeForm.addEventListener('submit', function(event) {
            if (getCart() && Object.keys(getCart()).length > 0) {
                setTimeout(clearCart, 500); 
            } else {
                event.preventDefault(); // Impede o envio se estiver vazio
                alert('Carrinho vazio. Adicione produtos para finalizar.');
            }
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('msg') && urlParams.get('type') === 'success') {
         clearCart(); // Garante que o carrinho JS está limpo após uma venda bem sucedida.
    }
});