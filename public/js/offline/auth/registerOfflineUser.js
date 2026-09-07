document.addEventListener(
    'DOMContentLoaded',
    async function () {

        /*
        |--------------------------------------------------------------------------
        | Only register while online.
        |--------------------------------------------------------------------------
        */

        if (!navigator.onLine) {
            return;
        }


        try {

            const response =
                await fetch(
                    '/offline/register',
                    {
                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN':
                                document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    ?.getAttribute(
                                        'content'
                                    ),

                            'Accept':
                                'application/json'
                        }
                    }
                );


            if (!response.ok) {
                return;
            }


            const data =
                await response.json();


            if (!data.success) {
                return;
            }


            const email =
                data.user.email
                    .toLowerCase();


            /*
            |--------------------------------------------------------------------------
            | Check IndexedDB first.
            |--------------------------------------------------------------------------
            */

            const existingUser =
                await OfflineAuth.getUser(
                    email
                );


            if (existingUser) {
                await OfflineAuth.saveUser({
                    ...existingUser,
                    id: data.user.id,
                    name: data.user.name,
                    role: data.user.role,
                    branch_id: data.user.branch_id
                });

                console.log(
                    'Offline login details updated for:',
                    email
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Ask user to enable offline login.
            |--------------------------------------------------------------------------
            */

            const enableOffline =
                confirm(
                    'Enable offline login for this account?'
                );


            if (!enableOffline) {
                return;
            }


            const password =
                prompt(
                    'Enter your password to enable offline login:'
                );


            if (!password) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Hash password locally.
            |--------------------------------------------------------------------------
            */

            const salt =
                crypto.randomUUID();


            const passwordHash =
                await createPasswordHash(
                    password,
                    salt
                );


            /*
            |--------------------------------------------------------------------------
            | Store offline account.
            |--------------------------------------------------------------------------
            */

            await OfflineAuth.saveUser({

                id:
                    data.user.id,

                name:
                    data.user.name,

                email:
                    email,

                role:
                    data.user.role,

                branch_id:
                    data.user.branch_id,

                salt:
                    salt,

                password_hash:
                    passwordHash

            });


            alert(
                'Offline login has been enabled for this account.'
            );


        } catch (error) {

            console.error(
                'Offline registration failed:',
                error
            );

        }

    }
);