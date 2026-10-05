<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <style>
        body {
            margin: 0;
            padding: 2rem;
            color: #1f2937;
            background: #f3f4f6;
            font-family: Arial, sans-serif;
        }

        main {
            max-width: 1100px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        h1 {
            margin: 0;
        }

        .header-actions,
        .row-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        button {
            padding: 0.55rem 0.8rem;
            border: 0;
            border-radius: 5px;
            color: #fff;
            background: #2563eb;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        button.secondary {
            color: #1f2937;
            background: #e5e7eb;
        }

        button.secondary:hover {
            background: #d1d5db;
        }

        button.danger {
            background: #dc2626;
        }

        button.danger:hover {
            background: #b91c1c;
        }

        .logout {
            color: #1d4ed8;
        }

        .table-wrap {
            overflow-x: auto;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: top;
        }

        th {
            color: #374151;
            background: #f9fafb;
            white-space: nowrap;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        .empty {
            color: #6b7280;
            text-align: center;
        }

        #notice {
            margin: 0 0 1rem;
        }

        #notice.error {
            color: #b91c1c;
        }

        #notice.success {
            color: #15803d;
        }

        dialog {
            width: min(440px, calc(100% - 2rem));
            padding: 1.25rem;
            border: 0;
            border-radius: 8px;
            box-shadow: 0 20px 50px rgb(0 0 0 / 25%);
        }

        dialog::backdrop {
            background: rgb(15 23 42 / 50%);
        }

        dialog h2 {
            margin: 0 0 1rem;
        }

        .field {
            display: grid;
            gap: 0.35rem;
            margin-bottom: 0.9rem;
        }

        .field input,
        .field textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 0.6rem;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            font: inherit;
        }

        .field textarea {
            min-height: 5rem;
            resize: vertical;
        }

        .dialog-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
            margin-top: 1.25rem;
        }

        @media (max-width: 600px) {
            body {
                padding: 1rem;
            }

            th,
            td {
                padding: 0.65rem;
            }
        }
    </style>
</head>
<body>
<main>
    <div class="page-header">
        <h1>Products</h1>
        <div class="header-actions">
            <button type="button" id="add-product">Add product</button>
            <a class="logout" href="/logout">Log out</a>
        </div>
    </div>

    <p id="notice" role="status" aria-live="polite"></p>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Product</th>
                    <th scope="col">Description</th>
                    <th scope="col">Price</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Created</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td class="empty" colspan="7">No products found.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                        $product_id = (string) ($product['id'] ?? '');
                        $product_name = (string) ($product['product_name'] ?? '');
                        $description = (string) ($product['description'] ?? '');
                        $price = (string) ($product['price'] ?? '');
                        $quantity = (string) ($product['quantity'] ?? '');
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) ($product['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <div class="row-actions">
                                    <button
                                        type="button"
                                        class="edit-product secondary"
                                        data-id="<?= htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8') ?>"
                                        data-name="<?= htmlspecialchars($product_name, ENT_QUOTES, 'UTF-8') ?>"
                                        data-description="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>"
                                        data-price="<?= htmlspecialchars($price, ENT_QUOTES, 'UTF-8') ?>"
                                        data-quantity="<?= htmlspecialchars($quantity, ENT_QUOTES, 'UTF-8') ?>"
                                    >Edit</button>
                                    <button
                                        type="button"
                                        class="delete-product danger"
                                        data-id="<?= htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8') ?>"
                                    >Delete</button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<dialog id="product-dialog">
    <form id="product-form">
        <h2 id="dialog-title">Add product</h2>
        <input type="hidden" name="id" id="product-id">

        <label class="field">
            <span>Product name</span>
            <input type="text" name="product_name" maxlength="100" required>
        </label>

        <label class="field">
            <span>Description</span>
            <textarea name="description"></textarea>
        </label>

        <label class="field">
            <span>Price</span>
            <input type="number" name="price" min="0" step="0.01" required>
        </label>

        <label class="field">
            <span>Quantity</span>
            <input type="number" name="quantity" min="0" step="1" required>
        </label>

        <div class="dialog-actions">
            <button type="button" class="secondary" id="cancel-product">Cancel</button>
            <button type="submit" id="save-product">Save product</button>
        </div>
    </form>
</dialog>

<script>
    const dialog = document.getElementById('product-dialog');
    const form = document.getElementById('product-form');
    const notice = document.getElementById('notice');
    const idField = document.getElementById('product-id');
    const title = document.getElementById('dialog-title');
    const saveButton = document.getElementById('save-product');

    function showNotice(message, isError = false) {
        notice.textContent = message;
        notice.className = message ? (isError ? 'error' : 'success') : '';
    }

    function openProductDialog(product = null) {
        form.reset();
        idField.value = product ? product.id : '';
        title.textContent = product ? 'Edit product' : 'Add product';
        saveButton.textContent = product ? 'Save changes' : 'Add product';

        if (product) {
            form.elements.product_name.value = product.name;
            form.elements.description.value = product.description;
            form.elements.price.value = product.price;
            form.elements.quantity.value = product.quantity;
        }

        dialog.showModal();
    }

    async function sendProductRequest(url, method, formData = null) {
        const options = { method, credentials: 'same-origin' };
        if (formData) {
            options.headers = { 'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8' };
            options.body = new URLSearchParams(formData).toString();
        }

        const response = await fetch(url, options);
        const result = await response.json();
        if (!response.ok) {
            throw new Error(result.error || result.message || 'The request failed.');
        }
        return result;
    }

    document.getElementById('add-product').addEventListener('click', () => openProductDialog());
    document.getElementById('cancel-product').addEventListener('click', () => dialog.close());

    document.querySelectorAll('.edit-product').forEach((button) => {
        button.addEventListener('click', () => {
            openProductDialog({
                id: button.dataset.id,
                name: button.dataset.name,
                description: button.dataset.description,
                price: button.dataset.price,
                quantity: button.dataset.quantity
            });
        });
    });

    document.querySelectorAll('.delete-product').forEach((button) => {
        button.addEventListener('click', async () => {
            if (!window.confirm('Delete this product? This cannot be undone.')) {
                return;
            }

            button.disabled = true;
            showNotice('');
            try {
                await sendProductRequest(`/products/${encodeURIComponent(button.dataset.id)}`, 'DELETE');
                window.location.reload();
            } catch (error) {
                showNotice(error.message, true);
                button.disabled = false;
            }
        });
    });

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        saveButton.disabled = true;
        showNotice('');

        const formData = new FormData(form);
        const id = idField.value;
        formData.delete('id');

        try {
            await sendProductRequest(
                id ? `/products/${encodeURIComponent(id)}` : '/products',
                id ? 'PUT' : 'POST',
                formData
            );
            dialog.close();
            window.location.reload();
        } catch (error) {
            showNotice(error.message, true);
            saveButton.disabled = false;
        }
    });
</script>
<script src="/js/frontend.js"></script>

</body>
</html>
