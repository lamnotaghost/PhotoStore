<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Магазин Фототехники</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#cameras">Фотокамеры</a></li>
                <li><a href="#lenses">Объективы</a></li>
                <li><a href="cart.html">Корзина</a></li>
                <li><a href="login.php">Админ</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section id="cameras" class="product-section">
            <h2>Фотокамеры</h2>
            <div id="products-container-cameras" class="products-grid"></div>
        </section>

        <section id="lenses" class="product-section">
            <h2>Объективы</h2>
            <div id="products-container-lenses" class="products-grid"></div>
        </section>
    </main>

    <script>
        // Функция загрузки продуктов
        function loadProducts(category, containerId) {
            fetch('db.php?category=' + category)
                .then(response => response.json())
                .then(products => {
                    const container = document.getElementById(containerId);
                    container.innerHTML = '';
                    products.forEach(product => {
                        const productElement = document.createElement('div');
                        productElement.className = 'product-card';
                        productElement.dataset.productId = product.id;
                        productElement.innerHTML = `
                            <img src="${product.image_name}" alt="${product.name}">
                            <h3>${product.name}</h3>
                            <p>${product.description}</p>
                            <p>Цена: ${product.price} руб.</p>
                            <input type="number" value="1" min="1">
                            <button onclick="addToCart('${product.id}')">Добавить в корзину</button>
                        `;
                        container.appendChild(productElement);
                    });
                })
                .catch(error => {
                    console.error('Ошибка при извлечении товаров:', error);
                });
        }

        // Загрузка продуктов по категориям
        document.addEventListener('DOMContentLoaded', () => {
            loadProducts('camera', 'products-container-cameras');
            loadProducts('lens', 'products-container-lenses');
        });
    </script>
</body>
</html>
