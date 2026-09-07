async function createPasswordHash(password, salt) {

    const encoder = new TextEncoder();

    const keyMaterial =
        await crypto.subtle.importKey(
            'raw',
            encoder.encode(password),
            'PBKDF2',
            false,
            ['deriveBits']
        );

    const derivedBits =
        await crypto.subtle.deriveBits(
            {
                name: 'PBKDF2',
                salt: encoder.encode(salt),
                iterations: 100000,
                hash: 'SHA-256'
            },
            keyMaterial,
            256
        );

    return Array.from(
        new Uint8Array(derivedBits)
    )
    .map(byte => byte.toString(16).padStart(2, '0'))
    .join('');
}

async function verifyPassword(
    password,
    salt,
    storedHash
) {

    const hash =
        await createPasswordHash(
            password,
            salt
        );

    return hash === storedHash;
}