//Implémenter le JS de ma page
const inputMail = document.getElementById("EmailInput");
const inputPassword = document.getElementById("PasswordInput");
const formSignin = document.getElementById("signinForm");

//Ajout évennement submit au form d'Inscription
formSignin.addEventListener("submit", validateForm );


//Function permettant de valider tout le formulaire
function validateForm(eventsignin){
    eventsignin.preventDefault()
    validateMail(inputMail);
    validatePassword(inputPassword);
}



// Function  permettant de verifier le champ mail via un regex
function validateMail(input){
    //Définir mon regex
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

// Function  permettant de verifier le champ mdp via un regex
function validatePassword(input){
    //Définir mon regex
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

