addEventListener("loading_page", (event) => {
    if (event.detail.url === "/login")
        loading_page_login()
});
addEventListener("loaded_page", (event) => {
    if (event.detail.url === "/login")
        loaded_page_login()
});

function loading_page_login() {
    if (localStorage.getItem('auth'))
        dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/" } }));
    else {
        document.querySelector("#title").innerHTML = "Страница логина"
        router("/login");
    }
}

function loaded_page_login() {
    let page = document.querySelector("#app");
    page.querySelector("#login_button").addEventListener("click", login_form_submit)
}

function addErrorsToLoginForm(erorrs) {
    errorsString = "";
    erorrs.forEach(error => {
        errorsString += error.charAt(0).toUpperCase() + error.slice(1) + "<br>";
    });
    document.querySelector("#login_errors").innerHTML = errorsString;
}

async function login_form_submit() {
    let formData = new FormData();
    formData.set('email', document.querySelector("#login_email").value)
    formData.set('password', document.querySelector("#login_password").value)
    let response = await fetch(LOGIN_PAGE, {
        method: 'POST',
        body: formData,
    })

    if (response.ok) {
        let responseJson = await response.json();
        if (responseJson.status === BAD_REQUEST) {
            addErrorsToLoginForm(responseJson["errors"])

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