document.addEventListener('DOMContentLoaded', function () {

    const downloadButton =
        document.getElementById('stockAuditDownloadBtn');

    if (!downloadButton) {
        return;
    }

    downloadButton.addEventListener('click', function () {

        const downloadUrl =
            downloadButton.dataset.downloadUrl;

        if (!downloadUrl) {
            alert('Download URL is missing.');
            return;
        }

        const url = new URL(
            downloadUrl,
            window.location.origin
        );

        const branchSelect =
            document.getElementById('branch_id');

        const sourceSelect =
            document.getElementById('source_type');

        const searchInput =
            document.getElementById('search');

        const dateFrom =
            document.getElementById('date_from');

        const dateTo =
            document.getElementById('date_to');


        /*
         * Branch filter
         */
        if (
            branchSelect &&
            branchSelect.value &&
            branchSelect.value !== 'all'
        ) {
            url.searchParams.set(
                'branch_id',
                branchSelect.value
            );
        }


        /*
         * Movement filter
         */
        if (
            sourceSelect &&
            sourceSelect.value &&
            sourceSelect.value !== 'all'
        ) {
            url.searchParams.set(
                'source_type',
                sourceSelect.value
            );
        }


        /*
         * Search
         */
        if (
            searchInput &&
            searchInput.value.trim()
        ) {
            url.searchParams.set(
                'search',
                searchInput.value.trim()
            );
        }


        /*
         * Date range
         */
        if (
            dateFrom &&
            dateFrom.value
        ) {
            url.searchParams.set(
                'date_from',
                dateFrom.value
            );
        }

        if (
            dateTo &&
            dateTo.value
        ) {
            url.searchParams.set(
                'date_to',
                dateTo.value
            );
        }


        /*
         * Open the Laravel download endpoint.
         *
         * This endpoint uses get(), not paginate(),
         * so all matching records are downloaded.
         */
        window.location.href =
            url.toString();

    });

});