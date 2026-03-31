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




function CheckCredentials(){

    let dataForm = new FormData(signinForm);

    let myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    let raw = JSON.stringify({
    "username": dataForm.get("email"),
    "password": dataForm.get("mdp")
    
    });

    let requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: raw,
        redirect: "follow"
    };

    fetch(`${apiUrl}/login`, requestOptions)
    .then((response) => {
        if(response.ok){
            return response.json();
        }
        else{

            throw new Error("Erreur lors de la connexion");
        }

    })
    .then((result) => {

        localStorage.setItem('apiToken', result.apiToken);
        alert("Bravo, "+ result.user +" vous êtes maintenant connecté.");
        document.location.href="/home";
    })
    .catch((error) => {
        
        console.error("Erreur attrapée :", error);
        alert(error.message || "Une erreur de communication est survenue.");
    });
}