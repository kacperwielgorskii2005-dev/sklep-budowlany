let isEditing = false;
let dataLoaded = false;

document.addEventListener("DOMContentLoaded", loadUserData);

function loadUserData() {
    fetch('../../api/user/LoadUserData.php')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert("Błąd ładowania danych: " + data.message);
                return;
            }

            const user = data.data;

            document.getElementById('u_login').value = user.login;
            document.getElementById('u_email').value = user.email;
            document.getElementById('u_password').value = "••••••••";

            document.getElementById('u_first_name').value = user.first_name;
            document.getElementById('u_last_name').value = user.last_name;
            document.getElementById('u_phone').value = user.phone;
            document.getElementById('u_address').value = user.address;
            document.getElementById('u_postal').value = user.postal_code;
            document.getElementById('u_city').value = user.city;
            document.getElementById('u_country').value = user.country;

            document.getElementById('c_first_name').value = user.first_name;
            document.getElementById('c_last_name').value = user.last_name;
            document.getElementById('c_email').value = user.email;
            document.getElementById('c_phone').value = user.phone;

            dataLoaded = true;
        });
}

function toggleEdit() {
    const inputs = document.querySelectorAll('#billing-grid input');
    const editBtn = document.querySelector('.edit-btn');

    if (isEditing) {

        if (!validateFields()) {
            alert("Popraw błędne dane przed zapisaniem.");
            return;
        }

        saveData();
        return;
    }

    isEditing = true;
    inputs.forEach(input => input.disabled = false);
    editBtn.textContent = 'Zapisz';
}

function validateFields() {
    if (!dataLoaded) return true;

    const first_name = document.getElementById('u_first_name');
    const last_name = document.getElementById('u_last_name');
    const phone = document.getElementById('u_phone');
    const city = document.getElementById('u_city');
    const country = document.getElementById('u_country');

    let valid = true;

    const nameRegex = /^[\p{L}]+$/u;
    const cityRegex = /^[\p{L}\s'\-]+$/u;
    const countryRegex = /^[\p{L}\s'\-]+$/u;
    const phoneRegex = /^[0-9]+$/;

    function mark(input, condition) {
        if (!condition) {
            input.classList.add("invalid");
            valid = false;
        } else {
            input.classList.remove("invalid");
        }
    }

    mark(first_name, first_name.value.trim() === "" || nameRegex.test(first_name.value.trim()));
    mark(last_name, last_name.value.trim() === "" || nameRegex.test(last_name.value.trim()));
    mark(phone, phone.value.trim() === "" || phoneRegex.test(phone.value.trim()));
    mark(city, city.value.trim() === "" || cityRegex.test(city.value.trim()));
    mark(country, country.value.trim() === "" || countryRegex.test(country.value.trim()));

    return valid;
}


function saveData() {
    const data = {
        first_name: document.getElementById('u_first_name').value,
        last_name: document.getElementById('u_last_name').value,
        phone: document.getElementById('u_phone').value,
        address: document.getElementById('u_address').value,
        city: document.getElementById('u_city').value,
        country: document.getElementById('u_country').value,
        postal_code: document.getElementById('u_postal').value
    };

    fetch('../../api/user/UpdateUserData.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: new URLSearchParams(data)
    })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                document.querySelectorAll('#billing-grid input').forEach(input => input.disabled = true);
                document.querySelector('.edit-btn').textContent = 'Edytuj';
                isEditing = false;
                location.reload();
            } else {
                alert('Błąd: ' + res.message);
                isEditing = true;
            }
        });
}

function cancelEdit() {
    location.reload();
}
