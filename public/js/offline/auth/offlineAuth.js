const OfflineAuth = (() => {

    const STORE_NAME = OfflineDB.STORES.users;

    async function saveUser(user) {

        return OfflineDB.put(STORE_NAME, {
            ...user,
            email: user.email.toLowerCase()
        });
    }

    async function getUser(email) {

        return OfflineDB.get(
            STORE_NAME,
            email.toLowerCase()
        );
    }

    async function deleteUser(email) {

        return OfflineDB.remove(
            STORE_NAME,
            email.toLowerCase()
        );
    }

    async function getAllUsers() {

        return OfflineDB.getAll(STORE_NAME);
    }

    return {
        saveUser,
        getUser,
        deleteUser,
        getAllUsers
    };

})();