document.addEventListener('DOMContentLoaded', async function () {
    const rowsElement = document.getElementById('offlineTransactionRows');
    const emptyElement = document.getElementById('offlineTransactionEmpty');
    const searchElement = document.getElementById('offlineTransactionSearch');
    const paymentElement = document.getElementById('offlineTransactionPayment');
    const sortElement = document.getElementById('offlineTransactionSort');

    let orders = [];
    let branches = [];

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    }

    function formatDate(value) {
        const date = new Date(value);
        return Number.isNaN(date.getTime())
            ? 'Unknown date'
            : date.toLocaleString([], {
                month: 'short',
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit'
            });
    }

    function formatAmount(value) {
        return `PHP ${Number(value || 0).toFixed(2)}`;
    }

    function render() {
        const query = searchElement.value.trim().toLowerCase();
        const payment = paymentElement.value;
        const sort = sortElement.value;
        const branchMap = new Map(branches.map(branch => [String(branch.id), branch.name]));

        const filtered = orders
            .filter(order => {
                const branchName = branchMap.get(String(order.branch_id)) || 'Unknown Branch';
                const searchable = `${order.receipt_no} ${branchName} ${order.payment_method}`.toLowerCase();
                return (!query || searchable.includes(query)) &&
                    (payment === 'all' || order.payment_method === payment);
            })
            .sort((a, b) => {
                const difference = new Date(a.created_at) - new Date(b.created_at);
                return sort === 'oldest' ? difference : -difference;
            });

        rowsElement.innerHTML = filtered.map(order => {
            const branchName = branchMap.get(String(order.branch_id)) || 'Unknown Branch';
            return `
                <tr class="transaction-row">
                    <td>${escapeHtml(order.receipt_no)}</td>
                    <td>${escapeHtml(branchName)}</td>
                    <td>${formatAmount(order.total_amount)}</td>
                    <td>${escapeHtml(order.payment_method || 'Unknown')}</td>
                    <td>Pending sync</td>
                    <td>${escapeHtml(formatDate(order.created_at))}</td>
                </tr>
            `;
        }).join('');

        emptyElement.textContent = filtered.length
            ? ''
            : 'No offline transactions found.';
        emptyElement.style.display = filtered.length ? 'none' : 'block';
    }

    try {
        [orders, branches] = await Promise.all([
            OfflineDB.getAll(OfflineDB.STORES.orders),
            OfflineDB.getAll(OfflineDB.STORES.branches)
        ]);
        render();
    } catch (error) {
        console.error('Offline transactions failed to load:', error);
        emptyElement.textContent = 'Offline transactions could not be loaded.';
    }

    searchElement.addEventListener('input', render);
    paymentElement.addEventListener('change', render);
    sortElement.addEventListener('change', render);
});
