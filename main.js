
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('item-form');
    const saveBtn = document.getElementById('saveBtn');
    const retrieveBtn = document.getElementById('retrieveBtn');
    const updateBtn = document.getElementById('updateBtn');
    const deleteBtn = document.getElementById('deleteBtn');
    const exitBtn = document.getElementById('exitBtn');
    const tbody = document.getElementById('inventory-list');

    let items = [];
    let selectedId = null;

    function getFormData() {
        return {
            name: document.getElementById('name').value.trim(),
            manufacture: document.getElementById('manufacture').value.trim(),
            type: document.getElementById('type').value.trim(),
            grams: document.getElementById('grams').value ? parseInt(document.getElementById('grams').value) : null,
            price: document.getElementById('price').value ? parseFloat(document.getElementById('price').value) : null,
            expiration: document.getElementById('expiration').value,
            madeDate: document.getElementById('madeDate').value,
            availability: document.getElementById('availability').value ? parseInt(document.getElementById('availability').value) : null
        };
    }

    function setFormData(item) {
        document.getElementById('name').value = item.name || '';
        document.getElementById('manufacture').value = item.manufacture || '';
        document.getElementById('type').value = item.type || '';
        document.getElementById('grams').value = item.grams || '';
        document.getElementById('price').value = item.price || '';
        document.getElementById('expiration').value = item.expiration || '';
        document.getElementById('madeDate').value = item.made_date || '';
        document.getElementById('availability').value = item.availability || '';
    }

    function clearForm() {
        form.reset();
        selectedId = null;
        clearSelectionHighlight();
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        return dateStr;
    }

    function renderTable() {
        tbody.innerHTML = '';
        if (items.length === 0) {
            const tr = document.createElement('tr');
            tr.innerHTML = '<td colspan="8" style="text-align: center; padding: 20px; color: #999;">No items yet. Add one to get started.</td>';
            tbody.appendChild(tr);
            return;
        }

        items.forEach((it) => {
            const tr = document.createElement('tr');
            tr.dataset.id = it.id;
            tr.innerHTML = `
                <td>${escapeHtml(it.name)}</td>
                <td>${escapeHtml(it.manufacture)}</td>
                <td>${escapeHtml(it.type)}</td>
                <td>${escapeHtml(it.grams)}</td>
                <td>${escapeHtml(it.price)}</td>
                <td>${formatDate(it.expiration)}</td>
                <td>${formatDate(it.made_date)}</td>
                <td>${escapeHtml(it.availability)}</td>
            `;
            tr.addEventListener('click', () => selectRow(it.id, it, tr));
            tbody.appendChild(tr);
        });
    }

    function selectRow(id, item, tr) {
        clearSelectionHighlight();
        tr.classList.add('selected');
        selectedId = id;
        setFormData(item);
    }

    function clearSelectionHighlight() {
        const prev = tbody.querySelector('tr.selected');
        if (prev) prev.classList.remove('selected');
    }

    async function fetchItems() {
        try {
            const res = await fetch('api/items.php');
            if (res.status === 401) {
                window.location = 'login.php';
                return;
            }
            if (!res.ok) {
                throw new Error(`HTTP error! status: ${res.status}`);
            }
            const data = await res.json();
            items = Array.isArray(data) ? data : [];
            renderTable();
        } catch (error) {
            console.error('Failed to fetch items:', error);
            alert('Failed to load items. Make sure the backend is running.');
            tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: red; padding: 20px;">Failed to connect to server</td></tr>';
        }
    }

    async function saveItem() {
        const data = getFormData();
        if (!data.name) {
            alert('Item name is required');
            return;
        }

        try {
            const res = await fetch('api/items.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (res.status === 401) {
                window.location = 'login.php';
                return;
            }
            if (res.ok) {
                const result = await res.json();
                alert('Item saved successfully!');
                await fetchItems();
                clearForm();
            } else {
                const error = await res.json();
                alert(`Failed to save: ${error.error || 'Unknown error'}`);
            }
        } catch (error) {
            console.error('Save error:', error);
            alert('Failed to save item. Check your connection.');
        }
    }

    async function updateItem() {
        if (!selectedId) {
            alert('Please select an item to update');
            return;
        }

        const data = getFormData();
        if (!data.name) {
            alert('Item name is required');
            return;
        }

        try {
            const res = await fetch(`api/items.php?id=${selectedId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            if (res.status === 401) {
                window.location = 'login.php';
                return;
            }
            if (res.ok) {
                const result = await res.json();
                alert('Item updated successfully!');
                await fetchItems();
                clearForm();
            } else {
                const error = await res.json();
                alert(`Failed to update: ${error.error || 'Unknown error'}`);
            }
        } catch (error) {
            console.error('Update error:', error);
            alert('Failed to update item. Check your connection.');
        }
    }

    async function deleteItem() {
        if (!selectedId) {
            alert('Please select an item to delete');
            return;
        }

        if (!confirm('Are you sure you want to delete this item?')) {
            return;
        }

        try {
            const res = await fetch(`api/items.php?id=${selectedId}`, {
                method: 'DELETE'
            });

            if (res.status === 401) {
                window.location = 'login.php';
                return;
            }
            if (res.ok) {
                const result = await res.json();
                alert('Item deleted successfully!');
                await fetchItems();
                clearForm();
            } else {
                const error = await res.json();
                alert(`Failed to delete: ${error.error || 'Unknown error'}`);
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('Failed to delete item. Check your connection.');
        }
    }

    function exitAction() {
        clearForm();
    }

    // wire buttons
    saveBtn.addEventListener('click', saveItem);
    retrieveBtn.addEventListener('click', fetchItems);
    updateBtn.addEventListener('click', updateItem);
    deleteBtn.addEventListener('click', deleteItem);
    exitBtn.addEventListener('click', exitAction);

    // logout button (if present)
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            await fetch('api/auth.php', { method: 'DELETE' });
            window.location = 'login.php';
        });
    }

    // initial load
    fetchItems();
});
