document.addEventListener('DOMContentLoaded', () => {
    loadUsers();
    loadOrders();
    loadProducts();
});

function loadProducts() {
    fetch('get_products.php')
        .then(response => response.json())
        .then(products => {
            const productsTable = document.querySelector('#products-table tbody');
            productsTable.innerHTML = '';

            products.forEach(product => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${product.product_id}</td>
                    <td>${product.name}</td>
                    <td>${product.description}</td>
                    <td>${product.price} руб.</td>
                    <td>${product.category}</td>
                    <td><img src="${product.image_name}" alt="${product.name}" width="50"></td>
                    <td>
                        <button onclick="deleteProduct(${product.product_id})">Удалить</button>
                        <button onclick="showUpdateForm(${product.product_id}, '${product.name}', '${product.description}', ${product.price}, '${product.category}', '${product.image_name}')">Изменить</button>
                    </td>
                `;
                productsTable.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Ошибка при загрузке продуктов:', error);
        });
}

function loadUsers() {
    fetch('get_users.php')
        .then(response => response.json())
        .then(users => {
            const usersTable = document.querySelector('#users-table tbody');
            usersTable.innerHTML = '';

            users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.user_id}</td>
                    <td>${user.first_name}</td>
                    <td>${user.last_name}</td>
                    <td>${user.email}</td>
                    <td>${user.address}</td>
                    <td>${user.password}</td>
                `;
                usersTable.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Ошибка при загрузке пользователей:', error);
        });
}

function loadOrders() {
    fetch('get_orders.php')
        .then(response => response.json())
        .then(orders => {
            const ordersTable = document.querySelector('#orders-table tbody');
            ordersTable.innerHTML = '';

            orders.forEach(order => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${order.order_id}</td>
                    <td>${order.order_date}</td>
                    <td>${order.first_name} ${order.last_name}</td>
                    <td>${order.total_price} руб.</td>
                `;
                ordersTable.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Ошибка при загрузке заказов:', error);
        });
}

function deleteProduct(productId) {
    if (!confirm('Вы уверены, что хотите удалить этот товар?')) return;

    fetch('delete_product.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.text())
    .then(result => {
        if (result === 'success') {
            alert('Товар успешно удален!');
            loadProducts();  // Перезагрузить список продуктов
        } else {
            alert('Ошибка при удалении товара.');
            console.error('Ошибка:', result);
        }
    })
    .catch(error => {
        console.error('Ошибка при удалении товара:', error);
        alert('Ошибка при удалении товара.');
    });
}

function showUpdateForm(productId, name, description, price, category, imageName) {
    const updateFormContainer = document.getElementById('update-form-container');
    updateFormContainer.innerHTML = `
        <h3>Изменить товар</h3>
        <form id="update-product-form">
            <input type="hidden" name="product_id" value="${productId}">
            <label for="update-name">Название:</label>
            <input type="text" id="update-name" name="name" value="${name}" required><br>
            <label for="update-description">Описание:</label>
            <textarea id="update-description" name="description" required>${description}</textarea><br>
            <label for="update-price">Цена:</label>
            <input type="number" id="update-price" name="price" value="${price}" step="0.01" required><br>
            <label for="update-category">Категория:</label>
            <input type="text" id="update-category" name="category" value="${category}" required><br>
            <label for="update-image-name">Путь изображения:</label>
            <input type="text" id="update-image-name" name="image_name" value="${imageName}" required><br>
            <button type="submit">Сохранить изменения</button>
        </form>
    `;

    const updateForm = document.getElementById('update-product-form');
    updateForm.addEventListener('submit', (event) => {
        event.preventDefault();

        const formData = new FormData(updateForm);
        const data = Object.fromEntries(formData.entries());

        fetch('update_product.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.text())
        .then(result => {
            if (result === 'success') {
                alert('Товар успешно обновлен!');
                updateFormContainer.innerHTML = ''; // Очистить форму
                loadProducts(); // Перезагрузить список продуктов
            } else {
                alert('Ошибка при обновлении товара.');
                console.error('Ошибка:', result);
            }
        })
        .catch(error => {
            console.error('Ошибка при обновлении товара:', error);
            alert('Ошибка при обновлении товара.');
        });
    });
}