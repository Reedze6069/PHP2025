// Fields and error elements
const form = document.getElementById('contactForm');
const fields = [
    { id: 'firstName', min: 2, errorId: 'firstNameError', message: '✖ First Name must be at least 2 characters long.' },
    { id: 'surname',   min: 2, errorId: 'surnameError',   message: '✖ Surname must be at least 2 characters long.' },
    { id: 'subject',   min: 10, errorId: 'subjectError',  message: '✖ Subject must be at least 10 characters long.' }
];

function validateField(field) {
    const input = document.getElementById(field.id);
    const errorEl = document.getElementById(field.errorId);
    const value = input.value.trim();

    if (value.length < field.min) {
        input.classList.add('input-error');
        input.classList.remove('input-success');
        errorEl.textContent = field.message;
        return false;
    } else {
        input.classList.remove('input-error');
        input.classList.add('input-success');
        errorEl.textContent = '';
        return true;
    }
}

// Live validation
fields.forEach(field => {
    const input = document.getElementById(field.id);
    input.addEventListener('input', () => validateField(field));
});

// Submit validation
form.addEventListener('submit', function(e) {
    let allValid = true;
    fields.forEach(field => {
        if (!validateField(field)) {
            allValid = false;
        }
    });

    if (!allValid) {
        e.preventDefault();
    } else {
        e.preventDefault(); // prevent default for now so we can show a thank-you message
        form.reset(); // clear the form
        fields.forEach(f => {
            document.getElementById(f.id).classList.remove('input-success');
        });
        document.getElementById('formSuccess').textContent = '✅ Thank you! Your message has been sent.';
        setTimeout(() => {
            document.getElementById('formSuccess').textContent = '';
        }, 4000);
    }
});
