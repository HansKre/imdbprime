<!--show cases usage of localStorage-->
if (!localStorage.pageLoadCount) {
    localStorage.pageLoadCount = 0;
}
localStorage.pageLoadCount = parseInt(localStorage.pageLoadCount) + 1;
document.getElementById('count').textContent = localStorage.pageLoadCount;

<!--show cases the usage of the browser inbuilt online detection-->
var statusElem  = document.getElementById('status'),
    state 		= document.getElementById('state');

function online(event) {
    statusElem.className = navigator.onLine ? 'online' : 'offline';
    statusElem.innerHTML = navigator.onLine ? 'online' : 'offline';
//                state.innerHTML += '<li>New event: ' + event.type + '</li>';
}

addEvent(window, 'online', online);
addEvent(window, 'offline', online);
online({ type: 'ready' });

<!--Just some messing around with geo location-->
var geoElem     = document.getElementById('geo');
function getGeoLocation() {
    if (navigator.geolocation){
        navigator.geolocation.getCurrentPosition(printPosition);
    } else {
        geoElem.innerHTML = "Geolocation not supported by your browser."
    }
}

function printPosition(position) {
    geoElem.innerHTML = position.coords.latitude + ' , ' + position.coords.longitude;
}
getGeoLocation();

<!--Just some messing around with cookie -->
    var listElem     = document.getElementById('list');
function areCookiesEnabled() {
    listElem.innerHTML += '<li>Cookies enabled: ' + navigator.cookieEnabled + '</li>';
}
areCookiesEnabled();

<!--Load Values from local storage-->
function preLoadFromLocalStorage() {
    var checkboxLocalStorageElem     = document.getElementById('checkboxLocalStorage');
    var textboxLocalStorageElem     = document.getElementById('textboxLocalStorage');
    if (localStorage){
        checkboxLocalStorageElem.checked = localStorage.checkboxChecked;
        textboxLocalStorageElem.value = localStorage.textName;
    } else {
        // handle error case
    }
}
preLoadFromLocalStorage();



// storage demo: https://html5demos.com/storage/
// extensive article: https://html.spec.whatwg.org/multipage/webstorage.html
// speeding up by bootstrapping: https://labs.ft.com/2012/08/basic-offline-html5-web-app/?mhq5j=e3
// offline apps PPT: https://www.w3l.de/de/fileadmin/user_upload/Offline_Apps_mit_HTML5_2014.pdf