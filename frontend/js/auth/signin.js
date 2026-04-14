const signinForm = document.getElementById("signinForm");
let champSignin = getChampSignin();
signinForm.innerHTML = champSignin;
const btnSignin = document.getElementById("btnSignin");
const inputMail = document.getElementById("EmailInput");
const inputPassword = document.getElementById("PasswordInput");


btnSignin.addEventListener("click", CheckCredentials);

function getChampSignin(){
   let email = sanitizeHtml();
   let mdp = sanitizeHtml();
   return `<div class="mb-3">
      <p>pour profiter de tous nos services connectez-vous</p>
      <label for="EmailInput" class="form-label">Email</label>
      <input type="email" class="form-control" id="EmailInput" placeholder="test@mail.fr" value="${email}" name="email">
    </div>
    <div class="mb-3">
      <label for="PasswordInput" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="PasswordInput" value="${mdp}" name="mdp">
            <div class="invalid-feedback">
        Le mail et, ou le mot de passe ne correspondent pas.
      </div>
    </div >    
    <div class="text-center">
    <button type="button" class="btn btn-primary" id="btnSignin">connectez-vous</button>
    </div> `;

}

function CheckCredentials() {
    let dataForm = new FormData(signinForm);
    // On récupère les inputs pour gérer l'affichage d'erreur
    const mailInput = document.getElementById("EmailInput");
    const passwordInput = document.getElementById("PasswordInput");

    let myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    let raw = JSON.stringify({
        "email": dataForm.get("email"),
        "password": dataForm.get("mdp")
    });

    fetch(`${apiUrl}/login`, {
        method: "POST",
        headers: myHeaders,
        body: raw
    })
    .then((response) => {
        if (response.ok) return response.json();
        throw new Error("Identifiants incorrects");
    })
    .then((result) => {
        setToken(result.apiToken);

        if (result.roles && result.roles.length > 0) {
            let roleToStore = "ROLE_USER";
            if (result.roles.includes("ROLE_ADMIN")) roleToStore = "ROLE_ADMIN";
            else if (result.roles.includes("ROLE_EMPLOYEE")) roleToStore = "ROLE_EMPLOYEE";

            setCookie(RoleCookieName, roleToStore, 7);
        }

        alert(`Bienvenue ${result.user}`);
        window.location.href = "/";
    })
    .catch((error) => {
        console.error("Erreur :", error);
        // Utilisation des bonnes variables pour Bootstrap
        mailInput.classList.add("is-invalid");
        passwordInput.classList.add("is-invalid");
    });
}