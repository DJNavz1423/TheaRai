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
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M200-800h560q17 0 28.5 11.5T800-760q0 17-11.5 28.5T760-720H200q-17 0-28.5-11.5T160-760q0-17 11.5-28.5T200-800Zm0 640q-17 0-28.5-11.5T160-200v-200h-7q-19 0-31-14.5t-8-33.5l40-200q3-14 14-23t25-9h574q14 0 25 9t14 23l40 200q4 19-8 33.5T807-400h-7v200q0 17-11.5 28.5T760-160q-17 0-28.5-11.5T720-200v-200H560v200q0 17-11.5 28.5T520-160H200Zm40-80h240v-160H240v160Z"/></svg>
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