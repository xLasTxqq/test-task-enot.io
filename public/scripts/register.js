addEventListener("loading_page", (event) => {
    if (event.detail.url === "/register")
        loading_page_register()
});
addEventListener("loaded_page", (event) => {
    if (event.detail.url === "/register")
        loaded_page_register()
});

function loading_page_register() {
    if (localStorage.getItem('auth'))
        dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/" } }));
    else {
        document.querySelector("#title").innerHTML = "Страница регистрации"
        router("/register");
    }
}

function loaded_page_register() {
    let page = document.querySelector("#app");
    page.querySelector("#register_button").addEventListener("click", register_form_submit)
}

function addErrorsToRegisterForm(erorrs) {
    errorsString = "";
    erorrs.forEach(error => {
        errorsString += error.charAt(0).toUpperCase() + error.slice(1) + "<br>";
    });
    document.querySelector("#register_errors").innerHTML = errorsString;
}

async function register_form_submit() {
    let formData = new FormData();
    formData.set('name', document.querySelector("#register_name").value)
    formData.set('email', document.querySelector("#register_email").value)
    formData.set('password', document.querySelector("#register_password").value)
    formData.set('password_confirmation', document.querySelector("#register_password_confirmation").value)
    let response = await fetch(REGISTR_PAGE, {
        method: 'POST',
        body: formData,
    })

    if (response.ok) {
        let responseJson = await response.json();
        if (responseJson.status === BAD_REQUEST) {
            addErrorsToRegisterForm(responseJson["errors"])
        } else if (responseJson.status === SUCCESS) {
            localStorage.setItem('auth', true);
            dispatchEvent(new CustomEvent("change_auth", { detail: { auth: true } }));
            dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/" } }));
        }
        else if (responseJson.status === GUEST_ONLY) {
            localStorage.setItem('auth', true);
            dispatchEvent(new CustomEvent("change_auth", { detail: { auth: true } }));
            dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/" } }));
        }

    } else {
        alert("Ошибка: " + response.status);
    }
}