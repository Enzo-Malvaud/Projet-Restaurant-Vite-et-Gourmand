//Implémenter le JS de ma page



const btnValidation = document.getElementById("btn-validation-inscription");
const formSignup = document.getElementById("signupForm");
let champSignup = getChampSignup();
formSignup.innerHTML = champSignup;
const inputNom = document.getElementById("NomInput");
const inputPreNom = document.getElementById("PrenomInput");
const inputMail = document.getElementById("EmailInput");
const inputNuméro = document.getElementById("NuméroInput");
const inputPassword = document.getElementById("PasswordInput");
const inputValidationPassword = document.getElementById("ValidatePasswordInput");

inputNom.addEventListener("keyup", validateForm);
inputPreNom.addEventListener("keyup", validateForm);
inputMail.addEventListener("keyup", validateForm);
inputPassword.addEventListener("keyup", validateForm);
inputValidationPassword.addEventListener("keyup", validateForm);
btnValidation.addEventListener("click", InscrireUtilisateur );

function getChampSignup(){
   let nom = sanitizeHtml();
   let prenom = sanitizeHtml();
   let email = sanitizeHtml();
   let numéro = sanitizeHtml();
   let mdp = sanitizeHtml();
   let confirmMdp = sanitizeHtml();
   return `
    <p>pour profiter de tous nos services inscrivez-vous</p>
    <div class="mb-3">
      <label for="NomInput" class="form-label">Nom</label>
      <input type="text" class="form-control" id="NomInput" value="${nom}" placeholder="Votre nom" name="nom">
    <div class="invalid-feedback">
      Le nom est requis
    </div>
    </div>
    <div class="mb-3">
      <label for="PrenomInput" class="form-label">Prénom</label>
      <input type="text" class="form-control" id="PrenomInput" value="${prenom}" placeholder="Votre prénom" name="prenom">
    <div class="invalid-feedback">
      Le prénom est requis
    </div>
    </div>
    <div class="mb-3">
      <label for="EmailInput" class="form-label">Email</label>
      <input type="email" class="form-control" id="EmailInput" value="${email}" placeholder="test@mail.fr" name="email">
    <div class="invalid-feedback">
      Le mail n'est pas au bon format
    </div>
  </div>
    <div class="mb-3">
      <label for="NuméroInput" class="form-label">Numéro</label>
      <input type="text" class="form-control" id="NuméroInput" value="${numéro}" placeholder="num" name="num">
      <div class="invalid-feedback">
        le numéro n'est pas valide
      </div>
    </div>
    <div class="mb-3">
      <label for="PasswordInput" class="form-label">Mot de passe</label>
      <input type="password" class="form-control" id="PasswordInput" value="${mdp}" name="mdp">
      <div class="invalid-feedback">
        Le mot de passe n'est pas assez robuste : Au moins 8 caractères, comprenant au moins 1 lettre majuscule, 1 minuscule, 1 chiffre, et 1 caractère spéciale
      </div>
      <div class="valid-feedback">
        Le mot de passe est robuste
      </div>
    </div >
    <div class="mb-3">
      <label for="ValidatePasswordInput" class="form-label">Confirmez votre Mot de passe</label>
      <input type="password" class="form-control" id="ValidatePasswordInput" value="${confirmMdp}"/>
    <div class="invalid-feedback">
      La confirmation n'est pas indentique au mot de passe ou et vide
    </div>
    </div>
    <div class="text-center">
    <button type="button" class="btn btn-primary" id="btn-validation-inscription">Inscrivez-vous</button>
    </div>  
`;

}



function validateForm(){
    const nomOk = validateRequired(inputNom);
    const prenomOk = validateRequired(inputPreNom);
    const mailOk = validateMail(inputMail);
    const numéroOk= validateNuméro(inputNuméro);
    const passwordOk = validatePassword(inputPassword);
    const passwordConfirmOk = validateConfirmationPassword(inputPassword, inputValidationPassword);


    if(nomOk && prenomOk && mailOk && numéroOk && passwordOk && passwordConfirmOk){
        btnValidation.disabled = false ;
    }
    else{
        btnValidation.disabled = true;
    }
}



function validateRequired(input){
    if(input.value != ''){
        input.classList.add("is-valid");
        input.classList.remove("is-invalid"); 
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }
}
function validateMail(input){
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const mailUser = input.value;
    if(mailUser.match(emailRegex)){
        input.classList.add("is-valid");
        input.classList.remove("is-invalid"); 
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }
}

function validateNuméro(input) {
    const numéroRegex = /^(06|07)[0-9]{8}$/;
    const numéroUser = input.value;
    if(numéroUser.match(numéroRegex)){
        input.classList.add("is-valid");
        input.classList.remove("is-invalid"); 
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }

    
}


function validatePassword(input){

    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/;
    const passwordUser = input.value;
    if(passwordUser.match(passwordRegex)){
        input.classList.add("is-valid");
        input.classList.remove("is-invalid"); 
        return true;
    }
    else{
        input.classList.remove("is-valid");
        input.classList.add("is-invalid");
        return false;
    }
}

function validateConfirmationPassword(inputPwd, inputConfirmPwd){
    if(inputPwd.value == inputConfirmPwd.value && inputConfirmPwd.value != ''){
        inputConfirmPwd.classList.add("is-valid");
        inputConfirmPwd.classList.remove("is-invalid");
        return true;
    }
    else{
        inputConfirmPwd.classList.add("is-invalid");
        inputConfirmPwd.classList.remove("is-valid");
        return false;
    }
}

function InscrireUtilisateur(){
    let dataForm = new FormData(formSignup);

    let myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    let raw = JSON.stringify({
    "firstName": dataForm.get("nom"),
    "lastName": dataForm.get("prenom"),
    "email": dataForm.get("email"),
    "numéro": dataForm.get("num"),
    "password": dataForm.get("mdp")
    });

    let requestOptions = {
        method: "POST",
        headers: myHeaders,
        body: raw,
        redirect: "follow"
    };

    fetch(apiUrl+"registration", requestOptions)
    .then((response) => {
        if(response.ok){
            return response.json();
        }
        else{
            alert("Erreur lors de l'inscription");
        }

    })
    .then((result) => {
        alert("Bravo, "+dataForm.get("prenom")+" vous êtes maintenant inscrit, vous pouvez vous connecter.")
        document.location.href="/signin"
    })
    .catch((error) => console.error(error));
}