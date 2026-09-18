document.addEventListener('DOMContentLoaded', function () {

    const editModal =
        document.getElementById('editModal');

    const editForm =
        document.getElementById('editBranchForm');

    const editBranchName =
        document.getElementById('edit_branch_name');

    const editBranchAddress =
        document.getElementById('edit_branch_address');


    document
        .querySelectorAll('.branch-edit-btn')
        .forEach(button => {

            button.addEventListener('click', function () {

                const id =
                    this.dataset.id;

                const name =
                    this.dataset.name || '';

                const address =
                    this.dataset.address || '';


                editForm.action =
                    `/admin/branches/${id}`;

                editBranchName.value =
                    name;

                editBranchAddress.value =
                    address;


                editModal.style.display =
                    'flex';

            });

        });


    editModal.addEventListener(
        'click',
        function (event) {

            if (event.target === editModal) {
                editModal.style.display =
                    'none';
            }

        }
    );

});