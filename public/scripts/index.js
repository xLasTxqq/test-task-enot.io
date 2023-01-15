const UNAUTHORIZED = 401;
const GUEST_ONLY = 302;
const BAD_REQUEST = 400;
const SUCCESS = 200;
const SERVER_ERROR = 500;
const PAGE_NOT_FOUND = 404;

const PROFILE_PAGE = "/backend/"
const LOGIN_PAGE = "/backend/login"
const REGISTR_PAGE = "/backend/register"
const LOGOUT_PAGE = "/backend/logout"

const ROUTES = {
    "/": "profile.html",
    "/login": "login.html",
    "/register": "register.html",
    "/logout": "",
}

let tegs_a_updated_in_dom = false;

function updatePage(page, url) {
    document.getElementById('app').innerHTML = page;
    update_document = document.querySelector("#app");
    if (!tegs_a_updated_in_dom) {
        update_document = document;
        tegs_a_updated_in_dom = true;
    }
    update_document.querySelectorAll("a").forEach((a) => a.addEventListener('click', (event) => {
        event.preventDefault();
        dispatchEvent(new CustomEvent("loading_page", { detail: { url: event.target.attributes.href.value } }))
    }));
    history.pushState({ document: page }, null, url);
    dispatchEvent(new CustomEvent("loaded_page", { detail: { url: url } }))
}

onpopstate = (event) => {
    document.getElementById('app').innerHTML = event.state.document
};

function router(url) {
    let page = '/views/' + ROUTES[url];
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            updatePage(this.responseText, url);
        }
    };
    xhttp.open('GET', page);
    xhttp.send();
}

dispatchEvent(new CustomEvent("loading_page", {
    detail: {
        url:
            (document.location.pathname in ROUTES ? document.location.pathname : "/login")
    }
}));

addEventListener("loaded_page", changeSelectedNavigation);

function changeSelectedNavigation() {
    let Hrefs = document.querySelectorAll("a");
    Hrefs.forEach(element => {
        if (element.attributes.href.value == document.location.pathname) {
            element.classList.remove("text-gray-300", "hover:bg-gray-700", "hover:text-white");
            element.classList.add("bg-gray-900", "text-white");
        } else {
            element.classList.add("text-gray-300", "hover:bg-gray-700", "hover:text-white");
            element.classList.remove("bg-gray-900", "text-white");
        }
    })
}

addEventListener("change_auth", changeNavigation)
dispatchEvent(new CustomEvent("change_auth", { detail: { auth: localStorage.getItem("auth") ? true : false } }));
function changeNavigation(data) {
    let auth = data.detail.auth;
    let arrayForGuest = [];
    let arrayForAuth = [];
    arrayForGuest.push(...document.querySelectorAll("a[href='/login']"));
    arrayForGuest.push(...document.querySelectorAll("a[href='/register']"));
    arrayForAuth.push(...document.querySelectorAll("a[href='/']"));
    arrayForAuth.push(...document.querySelectorAll("a[href='/logout']"));
    if (auth === true) {
        arrayForAuth.forEach((element) => {
            element.classList.remove("hidden");
        });
        arrayForGuest.forEach((element) => {
            element.classList.add("hidden");
        });
    } else {
        arrayForAuth.forEach((element) => {
            element.classList.add("hidden");
        });
        arrayForGuest.forEach((element) => {
            element.classList.remove("hidden");
        });
    }
}

document.querySelector("#mobile-menu-button").addEventListener('click', ()=>{
    document.querySelector("#mobile-menu").classList.toggle("hidden");
});