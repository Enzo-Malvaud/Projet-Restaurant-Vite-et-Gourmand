const signinForm    = document.getElementById("signinForm");
const mailInput     = document.getElementById("EmailInput");
const passwordInput = document.getElementById("PasswordInput");
const btnSignin     = document.getElementById("btnSignin");

btnSignin.addEventListener("click", CheckCredentials);

async function CheckCredentials() {
   
    mailInput.classList.remove("is-invalid");
    passwordInput.classList.remove("is-invalid");

    
    btnSignin.disabled = true;

    let dataForm = new FormData(signinForm);

    let myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    let raw = JSON.stringify({
        "email": dataForm.get("email"),
        "password": dataForm.get("mdp")
    });

   
    try {
        const response = await fetch(`${apiUrl}/login`, {
            method: "POST",
            headers: myHeaders,
            body: raw
        });

        if (!response.ok) throw new Error("Identifiants incorrects");

        const result = await response.json();

        setToken(result.apiToken);

        if (result.roles && result.roles.length > 0) {
            let roleToStore = "ROLE_USER";
            if (result.roles.includes("ROLE_ADMIN")) roleToStore = "ROLE_ADMIN";
            else if (result.roles.includes("ROLE_EMPLOYEE")) roleToStore = "ROLE_EMPLOYEE";

            setCookie(RoleCookieName, roleToStore, 7);
        }

        alert(`Bienvenue ${result.user}`);
        window.location.href = "/";

    } catch (error) {
        console.error("Erreur :", error);
        mailInput.classList.add("is-invalid");
        passwordInput.classList.add("is-invalid");

    } finally {
        
        btnSignin.disabled = false;
    }
}