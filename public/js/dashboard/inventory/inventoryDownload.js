document.addEventListener('DOMContentLoaded', function () {

    const downloadButton =
        document.getElementById('inventoryDownloadBtn');

    if (!downloadButton) {
        return;
    }

    function escapeCsv(value) {

        const text =
            String(value ?? '')
                .replace(/\r?\n|\r/g, ' ')
                .trim();

        return `"${text.replace(/"/g, '""')}"`;
    }

    function getNumericValue(value) {

        const cleaned =
            String(value ?? '')
                .replace(/[^0-9.-]/g, '');

        return parseFloat(cleaned) || 0;
    }

    function getBranchName() {

        const branchSelect =
            document.getElementById('filter-branch');

        if (!branchSelect) {
            return 'Global View';
        }

        const selectedOption =
            branchSelect.options[
                branchSelect.selectedIndex
            ];

        return selectedOption
            ? selectedOption.textContent.trim()
            : 'Global View';
    }

    downloadButton.addEventListener('click', function () {

        const branchName =
            getBranchName();

        const rows =
            Array.from(
                document.querySelectorAll('.inventory-row')
            ).filter(row => row.style.display !== 'none');

        if (rows.length === 0) {

            alert('There are no inventory items to download.');

            return;
        }

        const headers = [
            'Item Name',
            'Item Code',
            'Category',
            'Purchase Price',
            'Unit',
            'Quantity',
            'Stock Status',
            'Alert Threshold',
            'Inventory Value',
            'Last Updated',
            'View'
        ];

        const csvRows = [
            headers.map(escapeCsv).join(',')
        ];

        rows.forEach(row => {

            const nameElement =
                row.querySelector('[data-cell="name"] .item-data');

            const codeElement =
                row.querySelector('[data-cell="code"] .item-data');

            const categoryElement =
                row.querySelector('[data-cell="category"] .item-data');

            const priceElement =
                row.querySelector('.display-price');

            const unitElement =
                row.querySelector('.display-unit');

            const quantityElement =
                row.querySelector('.display-qty');

            const lastUpdateElement =
                row.querySelector('[data-cell="last update"] .item-data');


            const itemName =
                nameElement?.textContent.trim() || '';

            const itemCode =
                codeElement?.textContent.trim() || '--';

            const category =
                categoryElement?.textContent.trim() || '';

            const purchasePrice =
                getNumericValue(
                    priceElement?.textContent
                );

            const unit =
                unitElement?.textContent.trim() || '';

            const quantityText =
                quantityElement?.textContent.trim() || '';

            const quantity =
                getNumericValue(quantityText);

            const alertThreshold =
                getNumericValue(
                    row.getAttribute('data-threshold')
                );

            let stockStatus = 'In Stock';

            if (quantity <= 0) {
                stockStatus = 'Out of Stock';
            } else if (quantity < alertThreshold) {
                stockStatus = 'Low Stock';
            }

            const inventoryValue =
                quantity * purchasePrice;

            const lastUpdated =
                lastUpdateElement?.textContent.trim() || '--';

            const viewType =
                branchName === 'Global View'
                    ? 'Global'
                    : branchName;


            csvRows.push([
                itemName,
                itemCode,
                category,
                purchasePrice.toFixed(2),
                unit,
                quantity.toFixed(2),
                stockStatus,
                alertThreshold.toFixed(2),
                inventoryValue.toFixed(2),
                lastUpdated,
                viewType
            ].map(escapeCsv).join(','));

        });


        /*
        |--------------------------------------------------------------------------
        | UTF-8 BOM
        |--------------------------------------------------------------------------
        | Helps Microsoft Excel correctly recognize UTF-8 CSV files.
        */

        const csvContent =
            '\uFEFF' + csvRows.join('\r\n');


        const blob =
            new Blob(
                [csvContent],
                {
                    type: 'text/csv;charset=utf-8;'
                }
            );


        const url =
            URL.createObjectURL(blob);


        const link =
            document.createElement('a');

        link.href = url;


        const safeBranchName =
            branchName
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');


        const date =
            new Date()
                .toISOString()
                .slice(0, 10);


        link.download =
            `inventory-${safeBranchName}-${date}.csv`;


        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);

        URL.revokeObjectURL(url);

    });

});