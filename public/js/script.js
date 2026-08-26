function preventEmptyInput(input, isEmail = false) {

    if (!input) return;

    const originalValue = input.value;

    input.addEventListener('blur', function () {

        const value = this.value.trim();

        let isEmpty = value === '';

        if (isEmail) {
            isEmpty =
                value === '' ||
                value.toLowerCase() === '@thearai.com.ph';
        }

        if (isEmpty && originalValue.trim() !== '') {
            this.value = originalValue;
        }

    });
}