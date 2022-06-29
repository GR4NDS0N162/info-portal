const password_form = document.getElementById('password-form');
const password_current_field = document.getElementById('password-current-field');
const password_current_error = document.getElementById('password-current-error');
const password_new_field = document.getElementById('password-new-field');
const password_new_error = document.getElementById('password-new-error');
const password_check_field = document.getElementById('password-check-field');
const password_check_error = document.getElementById('password-check-error');

const validationEventType = 'input';

password_current_field.addEventListener(validationEventType, function (event)
{
    updateError(password_current_field, password_current_error,
        password_current_field.validity.valueMissing, 'Необходимо ввести текущий пароль.');
});

password_new_field.addEventListener(validationEventType, function (event)
{
    password_check_field.setAttribute('pattern', password_new_field.value);
    updateError(password_new_field, password_new_error,
        password_new_field.validity.valueMissing, 'Необходимо ввести новый пароль.',
        password_new_field.validity.patternMismatch, 'Введённый пароль слишком лёгкий.<br>' +
        'Введённый пароль должен удовлетворять условиям:<br>' +
        '- Цифра должна встречаться хотя бы один раз<br>' +
        '- Строчная буква должна встречаться хотя бы один раз<br>' +
        '- Заглавная буква должна встречаться хотя бы один раз<br>' +
        '- Специальный символ должен встречаться хотя бы один раз (@#$%^&+=)<br>' +
        '- Не допускаются пробелы<br>' +
        '- Количество символов - от 8 до 32)');
    updateError(password_check_field, password_check_error,
        !password_new_field.validity.valueMissing &&
        password_check_field.validity.patternMismatch, 'Пароли не совпадают.');
});

password_check_field.addEventListener(validationEventType, function (event)
{
    password_check_field.setAttribute('pattern', password_new_field.value);
    updateError(password_check_field, password_check_error,
        password_check_field.validity.valueMissing, 'Необходимо подтвердить пароль.',
        password_check_field.validity.patternMismatch, 'Пароли не совпадают.');
});

password_form.addEventListener('submit', function (event)
{
    validateForm(event, password_form,
        password_current_field, password_new_field, password_check_field);
});

const new_phone_form = document.getElementById('new-phone-form');
const new_phone_input = document.getElementById('new-phone-input');
const new_phone_submit = document.getElementById('new-phone-submit');
const new_phone_error = document.getElementById('new-phone-error');
const phones = document.getElementById('phones');

const phonePattern = '^\\+7([0-9]){10}$';
new_phone_input.setAttribute("pattern", phonePattern);

new_phone_input.addEventListener(validationEventType, function (event)
{
    updateError(new_phone_input, new_phone_error,
        new_phone_input.validity.valueMissing, 'Необходимо ввести телефонный номер.',
        new_phone_input.validity.patternMismatch, 'Введённая строка - не телефон');
});

new_phone_form.addEventListener('submit', function (event)
{
    event.preventDefault();
    new_phone_submit.blur();
});
