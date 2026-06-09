function openOrderPopup(orderId) {
    document.getElementById('orderPopup').classList.add('active');
    document.body.style.overflow = 'hidden';

    document.getElementById('popupOrderId').textContent = '#' + orderId;
    document.getElementById('popupProducts').innerHTML = '<div style="text-align: center; padding: 20px;">Ładowanie...</div>';

    fetch('../../api/user/GetOrderDetails.php?order_id=' + orderId)
        .then(res => res.json())
        .then(res => {
            if (!res.success) {
                alert('Błąd: ' + res.message);
                closeOrderPopup();
                return;
            }

            const data = res.data;
            const hasAssignedCustomer = data.customer_id !== null && data.customer_id !== undefined;

            const notes = (data.notes || "").toString();

            const extIdMatch = notes.match(/ExtOrderID:\s*([^\s]+)/i);
            if (extIdMatch) {
                data.notes = extIdMatch[1].trim();
            } else {
                data.notes = data.notes ? data.notes.toString().trim() : "";
            }

            if (!hasAssignedCustomer) {
                const nameMatch = notes.match(/Klient:\s*([^,]+)/i);
                const emailMatch = notes.match(/Email:\s*([^,]+)/i);
                const phoneMatch = notes.match(/Tel:\s*([^,]+)/i);
                const addressMatch = notes.match(/Adres:\s*([^\.]+)/i);

                let street = "";
                let city = "";

                if (addressMatch) {
                    const addr = addressMatch[1].trim();
                    const addrParts = addr.split(",");
                    street = (addrParts[0] || "").trim();
                    city   = (addrParts.slice(1).join(",") || "").trim();
                }

                data.customer_name = nameMatch ? nameMatch[1].trim() : (data.customer_name || "Brak danych");
                data.email         = emailMatch ? emailMatch[1].trim() : (data.email || "");
                data.phone         = phoneMatch ? phoneMatch[1].trim() : (data.phone || "");
                data.address       = street || (data.address || "");
                data.city          = city || (data.city || "");
            }


            document.getElementById('popupOrderId').textContent = '#' + data.order_id;
            document.getElementById('popupCustomer').textContent = data.customer_name || 'Brak danych';
            document.getElementById('popupDate').textContent = data.order_date;
            document.getElementById('popupEmail').textContent = data.email;
            document.getElementById('popupPhone').textContent = data.phone;
            document.getElementById('popupPayment').textContent = data.payment_method;
            document.getElementById('popupStreet').textContent = data.address;
            document.getElementById('popupCity').textContent = data.city;
            document.getElementById('popupNotes').textContent = data.notes;
            document.getElementById('popupSubtotal').textContent = data.subtotal;
            document.getElementById('popupShipping').textContent = data.shipping;
            document.getElementById('popupTotal').textContent = data.total;


            const statusBadge = getStatusBadge(data.status);
            document.getElementById('popupStatus').innerHTML = statusBadge;

            const cancelBtn = document.getElementById('btnCancelOrder');
            if ((data.status !== "Cancelled") && (data.status !== "Completed")) {
                cancelBtn.style.display = "flex";
            } else {
                cancelBtn.style.display = "none";
            }

            if (data.items && data.items.length > 0) {
                document.getElementById('popupProducts').innerHTML = data.items.map(item => `
                            <div class="product-row">
                                <div class="product-info">
                                    <div class="product-name-popup">${item.name}</div>
                                    <div class="product-details">${item.quantity} szt. × ${item.price_each}</div>
                                </div>
                                <div class="product-price">${item.total}</div>
                            </div>
                        `).join('');
            } else {
                document.getElementById('popupProducts').innerHTML = '<div style="text-align: center; padding: 20px;">Brak produktów</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Wystąpił błąd podczas pobierania danych zamówienia');
            closeOrderPopup();
        });
}

async function cancelOrder() {
    const orderId = document.getElementById('popupOrderId').textContent.replace('#', '');
    
    if (!confirm('Czy na pewno chcesz anulować zamówienie #' + orderId + '?\nTej operacji nie można cofnąć!')) {
        return;
    }
    
    try {
        const response = await fetch('../../api/panel-administrator/cancel_order.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'order_id=' + encodeURIComponent(orderId)
        });
        
        const text = await response.text();
        console.log('Cancel order response:', text);
        
        const result = JSON.parse(text);
        
        if (result.success) {
            alert('Zamówienie zostało anulowane');
            closeOrderPopup();
            location.reload();
        } else {
            alert('Błąd: ' + result.message);
        }
    } catch (error) {
        console.error('Error cancelling order:', error);
        alert('Wystąpił błąd podczas anulowania zamówienia: ' + error.message);
    }
}

function closeOrderPopup() {
    const overlay = document.getElementById('orderPopup');
    overlay.style.display = 'none';
    document.getElementById('popupProducts').innerHTML = '';
}

function formatCurrency(val) {
    const num = Number(val) || 0;
    return num.toLocaleString('pl-PL', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' zł';
}

function calculateSubtotal(order) {
    if (!order.products) return 0;
    return order.products.reduce((sum, p) => sum + (Number(p.total) || (Number(p.quantity || 0) * Number(p.price || 0) ) ), 0);
}

function escapeHtml(s) {
    if (s === null || s === undefined) return '';
    return String(s)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function acceptOrder() { alert('Przyjmij do realizacji (stub)'); }
function printOrder()  { window.print(); }
function changeOrderStatus() { alert('Zmień status (stub)'); }
function cancelOrder() { if (confirm('Czy na pewno anulować zamówienie?')) { alert('Anulowano (stub)'); } }
