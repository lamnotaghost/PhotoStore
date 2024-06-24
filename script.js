let cart = JSON.parse(localStorage.getItem('cart')) || [];

document.addEventListener('DOMContentLoaded', () => {
    // Загрузка корзины из localStorage
    console.log('Корзина при загрузке страницы:', cart); // Отладочное сообщение

    updateCart();

    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', (event) => {
            event.preventDefault();
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            const formData = new FormData(checkoutForm);

            if (cart.length === 0) {
                alert('Корзина пуста. Добавьте товары в корзину перед оформлением заказа.');
                return;
            }

            // Добавляем корзину в данные формы
            formData.append('cart', JSON.stringify(cart));

            fetch('process_order.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Заказ успешно оформлен!');
                    localStorage.removeItem('cart');
                    updateCart(); // Обновляем отображение корзины
                } else {
                    alert('Ошибка: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ошибка при оформлении заказа.');
            });
        });
    }

    const clearCartButton = document.getElementById('clear-cart-button');
    if (clearCartButton) {
        clearCartButton.addEventListener('click', () => {
            clearCart();
        });
    }
});

function loadProducts(category, containerId) {
    fetch('db.php?category=' + category)
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById(containerId);
            if (!container) return;
            container.innerHTML = '';
            if (products.error) {
                console.error('Ошибка при извлечении товаров:', products.error);
                container.innerHTML = `<p class="error">${products.error}</p>`;
                return;
            }
            products.forEach(product => {
                const productElement = document.createElement('div');
                productElement.className = 'product-card';
                productElement.dataset.productId = product.product_id;
                productElement.innerHTML = `
                    <img src="${product.image_name}" alt="${product.name}">
                    <h3>${product.name}</h3>
                    <p>${product.description}</p>
                    <p>Цена: ${product.price} руб.</p>
                    <input type="number" value="1" min="1">
                    <button onclick="addToCart('${product.product_id}')">Добавить в корзину</button>
                `;
                container.appendChild(productElement);
            });
        })
        .catch(error => {
            console.error('Ошибка при извлечении товаров:', error);
        });
}

function addToCart(productId) {
    const productCard = document.querySelector(`.product-card[data-product-id="${productId}"]`);
    if (!productCard) return;

    const quantity = parseInt(productCard.querySelector('input[type="number"]').value, 10);
    const priceText = productCard.querySelector('p:nth-child(4)').innerText;
    const price = parseFloat(priceText.replace('Цена: ', '').replace(' руб.', ''));

    if (isNaN(quantity) || isNaN(price)) {
        alert('Ошибка при добавлении товара в корзину. Проверьте корректность данных.');
        return;
    }

    const product = {
        id: productId,
        name: productCard.querySelector('h3').innerText,
        price: price,
        quantity: quantity
    };

    const cart = JSON.parse(localStorage.getItem('cart')) || [];
    const existingProduct = cart.find(item => item.id === product.id);

    if (existingProduct) {
        existingProduct.quantity += product.quantity;
    } else {
        cart.push(product);
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    alert('Товар добавлен в корзину!');
    console.log('Текущая корзина:', cart);
    updateCart();
}

function updateCart() {
    const cartItemsContainer = document.querySelector('.cart-items');
    if (!cartItemsContainer) return;

    cartItemsContainer.innerHTML = '';

    let totalPrice = 0;
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.forEach((item, index) => {
        if (item.price == null || item.quantity == null) {
            console.error('Invalid item in cart:', item);
            return;
        }

        const productTotalPrice = item.quantity * item.price;
        totalPrice += productTotalPrice;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.id}</td>
            <td>${item.name}</td>
            <td>${item.quantity}</td>
            <td>${item.price.toFixed(2)} руб.</td>
            <td>${productTotalPrice.toFixed(2)} руб.</td>
            <td><button onclick="removeFromCart(${index})">Удалить</button></td>
        `;
        cartItemsContainer.appendChild(row);
    });

    const totalPriceElement = document.querySelector('.total-price');
    if (totalPriceElement) {
        totalPriceElement.innerText = `Общая сумма: ${totalPrice.toFixed(2)} руб.`;
    }
}

function removeFromCart(index) {
    let cart = JSON.parse(localStorage.getItem('cart')) || [];
    cart.splice(index, 1);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCart();
}

function clearCart() {
    cart = [];
    localStorage.removeItem('cart');
    updateCart();
    alert('Корзина очищена.');
}
