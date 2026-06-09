document.addEventListener("DOMContentLoaded", function () {
    const contactForm = document.querySelector('.contact-form');
    if (!contactForm) return;

    const firstNameEl = document.getElementById('c_first_name');
    const lastNameEl  = document.getElementById('c_last_name');
    const emailEl     = document.getElementById('c_email');
    const phoneEl     = document.getElementById('c_phone');
    const subjectEl   = document.getElementById('c_subject');
    const messageEl   = document.getElementById('c_message');

    contactForm.addEventListener('submit', function (e) {
        e.preventDefault();

        let valid = true;

        [firstNameEl, lastNameEl, emailEl, phoneEl, subjectEl, messageEl]
            .forEach(el => el.classList.remove("invalid"));

        function validate(input, condition) {
            if (!condition) {
                input.classList.add("invalid");
                input.value = "";
                valid = false;
            }
        }

        validate(firstNameEl, /^[\p{L}\s'-]+$/u.test(firstNameEl.value.trim()));
        validate(lastNameEl,  /^[\p{L}\s'-]+$/u.test(lastNameEl.value.trim()));
        validate(emailEl,     /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailEl.value.trim()));
        validate(phoneEl,     /^[0-9]*$/.test(phoneEl.value.trim()));
        validate(subjectEl,   subjectEl.value.trim().length > 0);
        validate(messageEl,   messageEl.value.trim().length > 0);

        if (!valid) {
            showContactMessage("Popraw zaznaczone pola.", "error");
            return;
        }

        const payload = {
            c_first_name: firstNameEl.value.trim(),
            c_last_name:  lastNameEl.value.trim(),
            c_email:      emailEl.value.trim(),
            c_phone:      phoneEl.value.trim(),
            subject:      subjectEl.value.trim(),
            message:      messageEl.value.trim()
        };

        fetch('../../api/user/SaveContactMessage.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams(payload)
        })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                showContactMessage(json.message, "success");
                subjectEl.value = "";
                messageEl.value = "";
            } else {
                showContactMessage(json.message, "error");
            }
        })
        .catch(() => showContactMessage("Błąd serwera.", "error"));
    });

    function showContactMessage(text, type='info') {
        let box = document.getElementById('contact-msg-box');
        if (!box) {
            box = document.createElement('div');
            box.id = 'contact-msg-box';
            document.querySelector('.form-layout').before(box);
        }
        box.textContent = text;
        box.style.padding = "10px";
        box.style.borderRadius = "6px";

        if (type === "success") {
            box.style.background = "#c8f7d4";
            box.style.border = "1px solid #3aa556";
            box.style.marginBottom = "20px";
        } else if (type === "error") {
            box.style.background = "#ffd6d6";
            box.style.border = "1px solid #d14b4b";
            box.style.marginBottom = "20px";
        } else {
            box.style.background = "#eee";
            box.style.border = "1px solid #bbb";
            box.style.marginBottom = "20px";
        }
    }
});
