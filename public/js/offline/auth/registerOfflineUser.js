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


            const registerButton = document.createElement('button');
            registerButton.type = 'button';
            registerButton.textContent = 'Enable offline login';
            registerButton.style.cssText = [
                'position: fixed',
                'right: 1rem',
                'bottom: 1rem',
                'z-index: 1000',
                'padding: .75rem 1rem',
                'border: 0',
                'border-radius: .35rem',
                'background: #a90d13',
                'color: #fff',
                'cursor: pointer'
            ].join(';');

            registerButton.addEventListener('click', async function () {
                const enableOffline = confirm(
                    'Enable offline login for this account?'
                );

                if (!enableOffline) {
                    return;
                }

                const password = prompt(
                    'Enter your password to enable offline login:'
                );

                if (!password) {
                    return;
                }

                registerButton.disabled = true;
                registerButton.textContent = 'Saving offline login...';

                try {
                    const salt = crypto.randomUUID();
                    const passwordHash = await createPasswordHash(password, salt);

                    await OfflineAuth.saveUser({
                        id: data.user.id,
                        name: data.user.name,
                        email,
                        role: data.user.role,
                        branch_id: data.user.branch_id,
                        salt,
                        password_hash: passwordHash
                    });

                    registerButton.remove();
                } catch (error) {
                    console.error('Offline registration failed:', error);
                    registerButton.disabled = false;
                    registerButton.textContent = 'Enable offline login';
                }
            });

            document.body.appendChild(registerButton);


        } catch (error) {

            console.error(
                'Offline registration failed:',
                error
            );

        }

    }
);