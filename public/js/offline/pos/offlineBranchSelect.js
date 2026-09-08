document.addEventListener(
    'DOMContentLoaded',
    async function () {

        /*
        |--------------------------------------------------------------------------
        | Offline authentication
        |--------------------------------------------------------------------------
        */

        let auth = null;

        try {

            auth = JSON.parse(
                sessionStorage.getItem(
                    'offline_auth'
                )
            );

        } catch (error) {

            auth = null;

        }


        if (
            !auth ||
            auth.authenticated !== true ||
            auth.offline !== true
        ) {

            window.location.href =
                '/login';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Only Admin / Dev / Owner can select branch
        |--------------------------------------------------------------------------
        */

        if (
            ![
                'admin',
                'dev',
                'owner'
            ].includes(auth.role)
        ) {

            window.location.href =
                '/offline-pos';

            return;
        }


        const userInfo =
            document.getElementById(
                'offlineUserInfo'
            );


        if (userInfo) {

            userInfo.textContent =
                `${auth.name} • Offline Mode`;

        }


        const container =
            document.getElementById(
                'offlineBranchContainer'
            );


        try {

            const branches =
                await OfflineDB.getAll(
                    OfflineDB.STORES.branches
                );


            if (!branches.length) {

                container.innerHTML = `

                    <p class="text-muted">
                        No branches are cached for
                        offline use.
                    </p>

                    <p class="text-muted">
                        Open branch selection while
                        online first.
                    </p>

                `;

                return;
            }


            container.innerHTML = '';


            branches
                .sort(
                    (a, b) =>
                        String(a.name)
                            .localeCompare(
                                String(b.name)
                            )
                )
                .forEach(branch => {

                    const card =
                        document.createElement('a');


                    card.href =
                        `/offline-pos?branch=${encodeURIComponent(
                            branch.id
                        )}`;


                    card.innerHTML = `

                        <div class="branch-card">

                            <span class="icon-wrapper">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    height="24px"
                                    viewBox="0 -960 960 960"
                                    width="24px"
                                    fill="#e3e3e3"
                                >
                                    <path d="M200-120q-33 0-56.5-23.5T120-200v-200h-7q-19 0-31-14.5t-8-33.5l40-200q3-14 14-23t25-9h574q14 0 25 9t14 23l40 200q4 19-8 33.5T800-400v200q0 33-23.5 56.5T720-120H200Z"/>
                                </svg>
                            </span>

                            <h2>
                                ${escapeHtml(branch.name)}
                            </h2>

                            <p class="text-muted">
                                ${escapeHtml(
                                    branch.address ||
                                    'No address provided'
                                )}
                            </p>

                        </div>

                    `;


                    container.appendChild(card);

                });


        } catch (error) {

            console.error(
                'Offline branch loading failed:',
                error
            );

            container.innerHTML = `

                <p class="text-danger">
                    Failed to load cached branches.
                </p>

            `;

        }

    }
);


function escapeHtml(value) {

    const element =
        document.createElement('div');

    element.textContent =
        value ?? '';

    return element.innerHTML;
}