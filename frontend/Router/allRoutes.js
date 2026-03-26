import Route from "./Route.js";

//Définir ici vos routes
export const allRoutes = [
    new Route("/", "Accueil", "pages/home.html", []),
    new Route("/ourmenus", "Nos menus", "/pages/menu/ourmenus.html", []),
    new Route("/detailmenu", "Detail Menu", "/pages/menu/detailmenu.html", []),
    new Route("/ourrentals", "Nos locations", "/pages/rental/ourrentals.html", []),
    new Route("/detailrental", "Detail location", "/pages/rental/detailrental.html", []),
    
    // Routes utilisateurs connectés
    new Route("/ordermenu", "Order Menu", "/pages/order/ordermenu.html", ["ROLE_USER"]),
    new Route("/userprofile", "Mon profil", "/pages/user/profile.html", ["ROLE_USER"]),
    
    // Routes employés
    new Route("/employee/dashboard", "Dashboard", "/pages/employee/dashboard.html", ["ROLE_EMPLOYEE"]),
    new Route("/employee/orders", "Commandes", "/pages/employee/orders.html", ["ROLE_EMPLOYEE"]),
    
    // Routes administrateurs
    new Route("/admin/dashboard", "Admin", "/pages/admin/dashboard.html", ["ROLE_ADMIN"]),
    new Route("/admin/users", "Gérer utilisateurs", "/pages/admin/users.html", ["ROLE_ADMIN"]),
    
    // Routes publiques
    new Route("/contact", "Contact", "/pages/contact.html", []),
    new Route("/mentionslegales", "Mentions Légales", "/pages/mentionslegales.html", []),
    new Route("/signin", "Connexion", "/pages/auth/signin.html", ["disconnected"], "/js/auth/signin.js"),
    new Route("/signup", "Inscription", "/pages/auth/signup.html", ["disconnected"], "/js/auth/signup.js"),
];

//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Vite et Gourmand"; 

