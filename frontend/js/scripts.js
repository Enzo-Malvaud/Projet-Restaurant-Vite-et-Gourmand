const tokenCookieName = "accesstoken";
const RoleCookieName = "role";
const signoutBtn = document.getElementById("signout-btn");
//url à modifier avec l'api déployer
const base = window.location.origin;
const apiUrl = `${base}/api`;

const ROLES_HIERARCHY = {
    "ROLE_ADMIN": 3,
    "ROLE_EMPLOYEE": 2,
    "ROLE_USER": 1,
    "connected": 1,
    "disconnected": 0
};

if (signoutBtn) {
    signoutBtn.addEventListener("click", signout);
}


//retourne le cookie role
function getRole() {
    return getCookie(RoleCookieName);
}

//function effacé cookie
function signout(){
    //écrase nom du token
    eraseCookie(tokenCookieName);
    //écrase role 
    eraseCookie(RoleCookieName);
    // redirige page
    window.location.reload();
}


 

//function placer token en cookie
function setToken(token){
    //valeur 1 nom du cookie
    //valeur 2 on précise valeur token
    //valeur 3 durée temps cookie 7j
    setCookie(tokenCookieName, token, 7);
}

//function return token en cookie
function getToken(){
    return getCookie(tokenCookieName);
}


//function placer cookie
function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Strict";
}
//function récuperer cookie
function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i].trim();
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
//function ecraser cookie
function eraseCookie(name) {   
    document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
}

//function permettant de savoir si je suis connecté ou non
function isConnected(){
    if(getToken() == null || getToken() == undefined){
        return false;
    }
    else{
        return true;
    }
}



/**
 * disconnected
 * connected (admin ou client)
 * admin
 * client
 */

function showAndHideElementsForRoles() {
    const userConnected = isConnected();
    const userRole = getRole() || "disconnected"; // "disconnected" si pas de cookie
    
    // On récupère le poids du rôle actuel de l'utilisateur
    const userWeight = userConnected ? (ROLES_HIERARCHY[userRole] || 1) : 0;

    document.querySelectorAll('[data-show]').forEach(element => {
        const requirement = element.dataset.show;
        element.classList.add("d-none"); // On cache par défaut

        if (requirement === 'disconnected') {
            if (!userConnected) element.classList.remove("d-none");
        } 
        else if (requirement === 'connected') {
            if (userConnected) element.classList.remove("d-none");
        } 
        else {
            // Logique de hiérarchie : si le poids de l'user >= poids requis
            const requiredWeight = ROLES_HIERARCHY[requirement] || 0;
            if (userConnected && userWeight >= requiredWeight) {
                element.classList.remove("d-none");
            }
        }
    });
}

//function permettant de sécuriser entrer utilisareur 
function sanitizeHtml(text){
    const tempHtml = document.createElement('div');
    tempHtml.textContent = text;
    return tempHtml.innerHTML;
}

/*function getInfosUser(){
   
    let myHeaders = new Headers();
    myHeaders.append("X-AUTH-TOKEN", getToken());

    
    let requestOptions = {
        method: "GET",
        headers: myHeaders,
        redirect: "follow"
    };

    fetch(apiUrl + "account/me", requestOptions)
    .then(response => {
        if(response.ok){
            return response.json();
        }
        else{
            console.log("Impossible de récupérer les informations utilisateurs")
        }
    })
    .then(result => {
        return result;
    })
    .catch((error)=>console.error("erreur lors de la récupérations des données utilisateurs", error));

}*/