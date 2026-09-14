document.addEventListener('DOMContentLoaded', function () {

    const modal =
        document.getElementById('transactionDetailsModal');

    const closeButton =
        document.getElementById('closeTransactionDetails');

    const detailReceipt =
        document.getElementById('detail-receipt');

    const detailBranch =
        document.getElementById('detail-branch');

    const detailTotal =
        document.getElementById('detail-total');

    const detailPaymentMethod =
        document.getElementById('detail-payment-method');

    const detailPaymentStatus =
        document.getElementById('detail-payment-status');

    const detailDate =
        document.getElementById('detail-date');

    const detailItems =
        document.getElementById('detail-items');


    document
        .querySelectorAll('.transaction-details-btn')
        .forEach(button => {

            button.addEventListener('click', async function () {

                const transactionId =
                    this.dataset.id;


                modal.style.display = 'flex';

                detailItems.innerHTML =
                    '<p class="text-muted">Loading items...</p>';


                try {

                    const response = await fetch(
                        this.dataset.url,
                        {
                            headers: {
                                'Accept':
                                    'application/json'
                            }
                        }
                    );


                    const data =
                        await response.json();


                    if (!response.ok || !data.success) {

                        throw new Error(
                            data.message ||
                            'Failed to load transaction.'
                        );

                    }


                    const transaction =
                        data.transaction;


                    /*
                    |--------------------------------------------------------------------------
                    | Transaction details
                    |--------------------------------------------------------------------------
                    */

                    detailReceipt.textContent =
                        transaction.receipt_no;

                    detailBranch.textContent =
                        transaction.branch_name ||
                        'Unknown Branch';

                    detailTotal.textContent =
                        `₱${Number(transaction.total_amount).toFixed(2)}`;

                    detailPaymentMethod.textContent =
                        transaction.payment_method
                            ? transaction.payment_method
                                .replace(/\b\w/g, char =>
                                    char.toUpperCase()
                                )
                            : 'Unknown';

                    detailPaymentStatus.textContent =
                        transaction.payment_status
                            ? transaction.payment_status
                                .replace(/\b\w/g, char =>
                                    char.toUpperCase()
                                )
                            : 'Unknown';


                    const date =
                        new Date(transaction.created_at);


                    detailDate.textContent =
                        date.toLocaleString(
                            'en-US',
                            {
                                month: 'short',
                                day: 'numeric',
                                year: 'numeric',
                                hour: 'numeric',
                                minute: '2-digit',
                                hour12: true
                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Purchased items
                    |--------------------------------------------------------------------------
                    */

                    if (!data.items.length) {

                        detailItems.innerHTML =
                            '<p class="text-muted">No items found.</p>';

                        return;
                    }


                    detailItems.innerHTML = '';


                    data.items.forEach(item => {

                        const itemRow =
                            document.createElement('div');

                        itemRow.className =
                            'row mb-2';

                        const initial = item.name ? item.name.charAt(0).toUpperCase() : '?';

                        const imageContent = item.img_url ? `<img src="${item.img_url}" alt="${item.name}">` : `<span>${initial}</span>`;

                        itemRow.innerHTML = `
                            <div class="item-group">
                                <div class="item-image">
                                    ${imageContent}
                                </div>

                                <div>
                                    <div>
                                        <strong>${item.name}</strong>
                                    </div>

                                    <div class="text-muted">
                                        ${item.quantity} ×
                                        ₱${Number(item.price_at_time).toFixed(2)}
                                    </div>
                                </div>
                            </div>

                            <strong>
                                ₱${Number(item.subtotal).toFixed(2)}
                            </strong>
                        `;

                        detailItems.appendChild(itemRow);

                    });


                } catch (error) {

                    console.error(
                        'Transaction details error:',
                        error
                    );


                    detailItems.innerHTML =
                        '<p class="text-muted">Failed to load transaction details.</p>';

                }

            });

        });


    /*
    |--------------------------------------------------------------------------
    | Close button
    |--------------------------------------------------------------------------
    */

    closeButton.addEventListener(
        'click',
        function () {
            modal.style.display = 'none';
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Close when clicking outside modal
    |--------------------------------------------------------------------------
    */

    modal.addEventListener(
        'click',
        function (event) {

            if (event.target === modal) {
                modal.style.display = 'none';
            }

        }
    );

});