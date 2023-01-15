addEventListener("loading_page", (event) => {
    if (event.detail.url === "/")
        loading_page_profile()
});
addEventListener("loaded_page", (event) => {
    if (event.detail.url === "/")
        loaded_page_profile()
});

function loading_page_profile() {
    if (localStorage.getItem('auth')) {
        document.querySelector("#title").innerHTML = "Страница профиля"
        router("/");
    }
    else {
        dispatchEvent(new CustomEvent("loading_page", { detail: { url: "/login" } }));
    }
}

function loaded_page_profile() {
    let page = document.querySelector("#app");
    page.querySelector("#convert_from").addEventListener("change", conversion)
    page.querySelector("#convert_to").addEventListener("change", conversion)
    page.querySelector("#quantity").addEventListener("change", conversion)
    getCurrencies();
}

function conversion() {
    data = JSON.parse(localStorage.getItem('auth'));
    let convert_from = document.querySelector("#app").querySelector("#convert_from").value
    let convert_to = document.querySelector("#app").querySelector("#convert_to").value
    let quantity = document.querySelector("#app").querySelector("#quantity").value
    let result = document.querySelector("#app").querySelector("#result")
    if (convert_from in data && convert_to in data && !isNaN(parseFloat(quantity)) && isFinite(quantity)) {
        result.value = quantity / data[convert_from][2] * data[convert_to][2]
    }
}

async function getCurrencies() {
    let response = await fetch(PROFILE_PAGE, {
        method: 'GET',
    })
    if (response.ok) {
        let responseJson = await response.json();

        if (responseJson.status === SUCCESS) {
            fillSelects(responseJson["data"]);
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

function fillSelects(data) {
    data.push({ 0: 'RUR', 1: 'Рубль', 2: '1' })
    localStorage.setItem('auth', JSON.stringify(data));
    let convert_from = document.querySelector("#app").querySelector("#convert_from");
    let convert_to = document.querySelector("#app").querySelector("#convert_to");
    data.forEach((element, key) => {
        let option = document.createElement('option');
        option.value = key;
        option.innerText = element[0] + " \\ " + element[1];
        convert_from.appendChild(option.cloneNode(true));
        convert_to.appendChild(option.cloneNode(true));
    });
}