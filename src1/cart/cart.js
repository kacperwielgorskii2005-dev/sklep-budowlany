const FREE_DELIVERY_THRESHOLD = 500.00;
const DELIVERY_COST = 35.00;
const DISCOUNT = 50.00;

async function updateCartBackend(productId, quantity) {
    try {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        
        const response = await fetch('../../api/cart/UpdateCartPage.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('Cart update failed:', data.message);
            alert('Błąd podczas aktualizacji koszyka');
        }
        
        return data.success;
    } catch (error) {
        console.error('Error updating cart:', error);
        alert('Błąd połączenia z serwerem');
        return false;
    }
}

async function removeItemBackend(productId) {
    try {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('product_id', productId);
        
        const response = await fetch('../../api/cart/UpdateCartPage.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('Remove failed:', data.message);
            alert('Błąd podczas usuwania produktu');
        }
        
        return data.success;
    } catch (error) {
        console.error('Error removing item:', error);
        alert('Błąd połączenia z serwerem');
        return false;
    }
}

async function clearCartBackend() {
    try {
        const formData = new FormData();
        formData.append('action', 'clear');
        
        const response = await fetch('../../api/cart/UpdateCartPage.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            console.error('Clear cart failed:', data.message);
            alert('Błąd podczas czyszczenia koszyka');
        }
        
        return data.success;
    } catch (error) {
        console.error('Error clearing cart:', error);
        alert('Błąd połączenia z serwerem');
        return false;
    }
}

async function increaseQty(button) {
    const cartItem = button.closest('.cart-item');
    const productId = cartItem.dataset.productId;
    const input = button.parentElement.querySelector('.qty-input');
    const currentValue = parseInt(input.value);
    
    if (currentValue < 99) {
        const newValue = currentValue + 1;
        
        const success = await updateCartBackend(productId, newValue);
        
        if (success) {
            input.value = newValue;
            updateItemTotal(button);
            updateCartSummary();
        }
    }
}

async function decreaseQty(button) {
    const cartItem = button.closest('.cart-item');
    const productId = cartItem.dataset.productId;
    const input = button.parentElement.querySelector('.qty-input');
    const currentValue = parseInt(input.value);
    
    if (currentValue > 1) {
        const newValue = currentValue - 1;
        
        const success = await updateCartBackend(productId, newValue);
        
        if (success) {
            input.value = newValue;
            updateItemTotal(button);
            updateCartSummary();
        }
    }
}

function updateItemTotal(button) {
    const cartItem = button.closest('.cart-item');
    const qtyInput = cartItem.querySelector('.qty-input');
    const quantity = parseInt(qtyInput.value);
    const priceValue = parseFloat(qtyInput.dataset.price);
    const total = quantity * priceValue;
    
    cartItem.querySelector('.price-total').textContent = formatPrice(total);
}

async function removeItem(button) {
    if (confirm('Czy na pewno chcesz usunąć ten produkt z koszyka?')) {
        const cartItem = button.closest('.cart-item');
        const productId = cartItem.dataset.productId;
        
        const success = await removeItemBackend(productId);
        
        if (success) {
            cartItem.style.animation = 'slideOut 0.3s ease';
            
            setTimeout(() => {
                cartItem.remove();
                updateCartSummary();
                checkEmptyCart();
                
                console.log('Usunięto produkt ID:', productId);
            }, 300);
        }
    }
}

function updateCartSummary() {
    const cartItems = document.querySelectorAll('.cart-item');
    let subtotal = 0;
    let productCount = cartItems.length;

    cartItems.forEach(item => {
        const qtyInput = item.querySelector('.qty-input');
        const quantity = parseInt(qtyInput.value);
        const price = parseFloat(qtyInput.dataset.price);
        
        subtotal += price * quantity;
    });

    const itemsCountEl = document.querySelector('.items-count');
    if (itemsCountEl) {
        const word = productCount === 1 ? 'produkt' : (productCount < 5 ? 'produkty' : 'produktów');
        itemsCountEl.textContent = `${productCount} ${word}`;
    }

    const subtotalEl = document.getElementById('subtotal-value');
    if (subtotalEl) {
        subtotalEl.textContent = formatPrice(subtotal);
    }

    const deliveryCost = subtotal >= FREE_DELIVERY_THRESHOLD ? 0 : DELIVERY_COST;
    
    const summaryRows = document.querySelectorAll('.summary-row');
    summaryRows.forEach(row => {
        const firstSpan = row.querySelector('span:first-child');
        if (firstSpan && firstSpan.textContent.includes('Dostawa')) {
            const deliverySpan = row.querySelector('span:last-child');
            if (deliverySpan) {
                if (deliveryCost === 0) {
                    deliverySpan.innerHTML = '<span style="color: #4CAF50; font-weight: 600;">GRATIS</span>';
                } else {
                    deliverySpan.textContent = formatPrice(deliveryCost);
                }
            }
        }
    });

    const finalTotal = subtotal + deliveryCost - DISCOUNT;
    const totalEl = document.getElementById('total-value');
    if (totalEl) {
        totalEl.textContent = formatPrice(finalTotal);
    }

    updateFreeDeliveryMessage(subtotal);
}

function updateFreeDeliveryMessage(subtotal) {
    const summaryDiv = document.querySelector('.cart-summary');
    if (!summaryDiv) return;

    const existingMessage = document.getElementById('free-delivery-message');
    if (existingMessage) {
        existingMessage.remove();
    }

    if (subtotal < FREE_DELIVERY_THRESHOLD && subtotal > 0) {
        const remaining = FREE_DELIVERY_THRESHOLD - subtotal;
        const message = document.createElement('div');
        message.id = 'free-delivery-message';
        message.style.cssText = `
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 16px;
            font-size: 0.9rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        `;
        message.innerHTML = `
            📦 Dodaj jeszcze <strong>${formatPrice(remaining)}</strong> do koszyka,<br>
            aby otrzymać <strong>darmową dostawę</strong>!
        `;
        
        const checkoutBtn = summaryDiv.querySelector('.checkout-btn');
        if (checkoutBtn) {
            summaryDiv.insertBefore(message, checkoutBtn);
        }
    } else if (subtotal >= FREE_DELIVERY_THRESHOLD) {
        const message = document.createElement('div');
        message.id = 'free-delivery-message';
        message.style.cssText = `
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            margin-top: 16px;
            font-size: 0.9rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(17, 153, 142, 0.3);
        `;
        message.innerHTML = `
            ✅ Gratulacje! Otrzymujesz <strong>darmową dostawę</strong>!
        `;
        
        const checkoutBtn = summaryDiv.querySelector('.checkout-btn');
        if (checkoutBtn) {
            summaryDiv.insertBefore(message, checkoutBtn);
        }
    }
}

function checkEmptyCart() {
    const cartItems = document.querySelectorAll('.cart-item');
    
    if (cartItems.length === 0) {
        const cartContent = document.querySelector('.cart-content');
        const cartSummaryTop = document.querySelector('.cart-summary-top');
        
        if (cartContent) cartContent.remove();
        if (cartSummaryTop) cartSummaryTop.remove();
        
        const container = document.querySelector('.cart-container');
        const emptyCartHTML = `
            <div class="empty-cart">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ccc">
                    <path d="M15.55 13c.75 0 1.41-.41 1.75-1.03l3.58-6.49A.996.996 0 0 0 20.01 4H5.21l-.94-2H1v2h2l3.6 7.59-1.35 2.44C4.52 15.37 5.48 17 7 17h12v-2H7l1.1-2h7.45zM6.16 6h12.15l-2.76 5H8.53L6.16 6zM7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zm10 0c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                </svg>
                <h3>Twój koszyk jest pusty</h3>
                <p>Dodaj produkty do koszyka, aby kontynuować zakupy</p>
                <a href="../main/index.php" class="btn-back-shop">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
                        <path d="M20 6h-2.18c.11-.31.18-.65.18-1 0-1.66-1.34-3-3-3-1.05 0-1.96.54-2.5 1.35l-.5.67-.5-.68C10.96 2.54 10.05 2 9 2 7.34 2 6 3.34 6 5c0 .35.07.69.18 1H4c-1.11 0-1.99.89-1.99 2L2 19c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V8c0-1.11-.89-2-2-2z"/>
                    </svg>
                    Wróć do sklepu
                </a>
            </div>
        `;
        
        container.innerHTML += emptyCartHTML;
    }
}

async function clearCart() {
    if (confirm('Czy na pewno chcesz wyczyścić cały koszyk?')) {
        const success = await clearCartBackend();
        
        if (success) {
            const cartItems = document.querySelectorAll('.cart-item');
            
            cartItems.forEach((item, index) => {
                setTimeout(() => {
                    item.style.animation = 'slideOut 0.3s ease';
                    setTimeout(() => {
                        item.remove();
                        if (index === cartItems.length - 1) {
                            checkEmptyCart();
                        }
                    }, 300);
                }, index * 100);
            });
            
            console.log('Wyczyszczono cały koszyk');
        }
    }
}

function goToCheckout() {
    const cartItems = document.querySelectorAll('.cart-item');
    
    if (cartItems.length === 0) {
        alert('Twój koszyk jest pusty!');
        return;
    }
    
    let hasUnavailable = false;
    cartItems.forEach(item => {
        const status = item.querySelector('.cart-item-status');
        if (status && status.classList.contains('unavailable')) {
            hasUnavailable = true;
        }
    });
    
    if (hasUnavailable) {
        if (!confirm('Niektóre produkty są niedostępne. Czy chcesz kontynuować bez nich?')) {
            return;
        }
    }
    
    window.location.href = '../../src/order-submit/index.php';
}

function formatPrice(price) {
    const num = typeof price === 'string' ? parseFloat(price) : price;
    
    const formatted = num.toFixed(2)
        .replace(/\B(?=(\d{3})+(?!\d))/g, ' ')
        .replace('.', ',');
    
    return formatted + ' zł';
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
        }
        to {
            opacity: 0;
            transform: translateX(100px);
        }
    }
`;
document.head.appendChild(style);

document.addEventListener('DOMContentLoaded', function() {
    console.log('Koszyk załadowany');
    updateCartSummary();
});