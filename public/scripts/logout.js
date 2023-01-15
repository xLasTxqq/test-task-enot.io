addEventListener("loading_page", (event) => {
    if (event.detail.url === "/logout")
        loading_page_logout()
});

function loading_page_logout() {
    if (localStorage.getItem('auth')) {
        document.querySelector("#title").innerHTML = "Страница выхода"
        logout_form_submit();
    }
    else {
        dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/login" } }));
    }
}

async function logout_form_submit() {
    let response = await fetch(LOGOUT_PAGE, {
        method: 'DELETE',
        body: new FormData(),
    })
    if (response.ok) {
        let responseJson = await response.json();
        if (responseJson.status === SUCCESS) {
            localStorage.removeItem('auth');
            dispatchEvent(new CustomEvent("change_auth", { detail: { auth: false } }));
            dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/login" } }));
        }
        else if (responseJson.status === UNAUTHORIZED) {
            localStorage.removeItem('auth');
            dispatchEvent(new CustomEvent("change_auth", { detail: { auth: false } }));
            dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/login" } }));
        }

    } else {
        alert("Ошибка: " + response.status);
    }
}