const formSignup          = document.getElementById("signupForm");
const inputNom            = document.getElementById("NomInput");
const inputPreNom         = document.getElementById("PrenomInput");
const inputMail           = document.getElementById("EmailInput");
const inputNuméro         = document.getElementById("NuméroInput");
const inputPassword       = document.getElementById("PasswordInput");
const inputValidPassword  = document.getElementById("ValidatePasswordInput");
const btnValidation       = document.getElementById("btn-validation-inscription");


inputNom.addEventListener("keyup",           () => { validateRequired(inputNom);                          updateBtn(); });
inputPreNom.addEventListener("keyup",        () => { validateRequired(inputPreNom);                       updateBtn(); });
inputMail.addEventListener("keyup",          () => { validateMail(inputMail);                             updateBtn(); });
inputNuméro.addEventListener("keyup",        () => { validateNuméro(inputNuméro);                         updateBtn(); });
inputPassword.addEventListener("keyup",      () => { validatePassword(inputPassword);                     updateBtn(); });
inputValidPassword.addEventListener("keyup", () => { validateConfirmationPassword(inputPassword, inputValidPassword); updateBtn(); });

btnValidation.addEventListener("click", signupUser);



function updateBtn() {
    
    const allValid =
        validateRequired(inputNom)    &&
        validateRequired(inputPreNom) &&
        validateMail(inputMail)       &&
        validateNuméro(inputNuméro)   &&
        validatePassword(inputPassword) &&
        validateConfirmationPassword(inputPassword, inputValidPassword);

    btnValidation.disabled = !allValid;
    return allValid; 
}



function validateRequired(input) {
    const ok = input.value.trim() !== '';
    input.classList.toggle("is-valid",   ok);
    input.classList.toggle("is-invalid", !ok);
    return ok;
}

function validateMail(input) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const ok = emailRegex.test(input.value);
    input.classList.toggle("is-valid",   ok);
    input.classList.toggle("is-invalid", !ok);
    return ok;
}

function validateNuméro(input) {
    const numéroRegex = /^(?:0|\+33 ?)[1-9]([-. ]?[0-9]{2}){4}$/;
    const ok = numéroRegex.test(input.value);
    input.classList.toggle("is-valid",   ok);
    input.classList.toggle("is-invalid", !ok);
    return ok;
}

function validatePassword(input) {
    const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{8,}$/;
    const ok = passwordRegex.test(input.value);
    input.classList.toggle("is-valid",   ok);
    input.classList.toggle("is-invalid", !ok);
    return ok;
}

function validateConfirmationPassword(inputPwd, inputConfirmPwd) {
    const ok = inputPwd.value === inputConfirmPwd.value && inputConfirmPwd.value !== '';
    inputConfirmPwd.classList.toggle("is-valid",   ok);
    inputConfirmPwd.classList.toggle("is-invalid", !ok);
    return ok;
}

// ─── Inscription ──────────────────────────────────────────────────────────────

async function signupUser() {
    
    if (!updateBtn()) return;

    setLoadingState(true);

    const dataForm = new FormData(formSignup);

    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    const raw = JSON.stringify({
        "email":     dataForm.get("email"),
        "password":  dataForm.get("mdp"),
        "numero":    dataForm.get("num"),
        "firstName": dataForm.get("nom"),
        "lastName":  dataForm.get("prenom")
    });

    try {
        const response = await fetch(`${apiUrl}/registration`, {
            method:   "POST",
            headers:  myHeaders,
            body:     raw,
            redirect: "follow"
        });

        if (!response.ok) throw new Error("Erreur lors de l'inscription");

        alert(`Bravo, ${dataForm.get("prenom")} ! Vous êtes inscrit, vous pouvez vous connecter.`);
        document.location.href = "/signin";

    } catch (error) {
        console.error("Erreur attrapée :", error);
        alert(error.message || "Une erreur de communication est survenue.");

    } finally {
        
        setLoadingState(false);
    }
}

function setLoadingState(isLoading) {
    btnValidation.disabled    = isLoading;
    btnValidation.textContent = isLoading ? "Inscription…" : "S'inscrire";
}