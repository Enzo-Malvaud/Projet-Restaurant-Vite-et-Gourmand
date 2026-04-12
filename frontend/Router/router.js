import Route from "./Route.js";
import { allRoutes, websiteName } from "./allRoutes.js";

const route404 = new Route("404", "Page introuvable", "/pages/404.html", []);

const getRouteByUrl = (url) => {
  let currentRoute = allRoutes.find(element => element.url == url);
  return currentRoute || route404;
};

const LoadContentPage = async () => {
  const path = window.location.pathname;
  const actualRoute = getRouteByUrl(path);

  // --- VÉRIFICATION DES DROITS (HIÉRARCHIE) ---
  const allRolesArray = actualRoute.authorize; // Ex: ["ROLE_EMPLOYEE"]

  if (allRolesArray.length > 0) {
    const userConnected = isConnected();
    const userRole = getRole() || "disconnected";
    
    // Récupération du poids de l'utilisateur (ex: ADMIN = 3)
    const userWeight = ROLES_HIERARCHY[userRole] ?? 0;

    if (allRolesArray.includes("disconnected")) {
      if (userConnected) {
        window.location.replace("/");
        return;
      }
    } else {
      // On vérifie si l'utilisateur a un poids supérieur ou égal 
      // au rôle minimum requis par la route
      const hasAccess = allRolesArray.some(roleRequired => {
        const requiredWeight = ROLES_HIERARCHY[roleRequired] ?? 0;
        return userWeight >= requiredWeight;
      });

      if (!hasAccess) {
        window.location.replace("/");
        return;
      }
    }
  }

  // --- CHARGEMENT DU CONTENU ---
  const html = await fetch(actualRoute.pathHtml).then((data) => data.text());
  document.getElementById("main-page").innerHTML = html;

  if (actualRoute.pathJS != "") {
    var scriptTag = document.createElement("script");
    scriptTag.setAttribute("type", "text/javascript");
    scriptTag.setAttribute("src", actualRoute.pathJS);
    document.querySelector("body").appendChild(scriptTag);
  }

  document.title = actualRoute.title + " - " + websiteName;

  // Mise à jour visuelle des éléments (boutons, liens)
  showAndHideElementsForRoles();
};

window.onpopstate = LoadContentPage;
window.route = (event) => {
  event = event || window.event;
  event.preventDefault();
  window.history.pushState({}, "", event.target.href);
  LoadContentPage();
};

LoadContentPage();

function getUserRoles() {
    const role = getRole();
    return role ? [role] : [];
}