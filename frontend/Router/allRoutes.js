import Route from "./Route.js";

//Définir ici vos routes
export const allRoutes = [
    // Routes publiques
    // Routes publiques
    new Route("/", "Accueil", "pages/home.html", []),
    new Route("/ourmenus", "Nos menus", "/pages/menu/ourmenus.html", []),
    new Route("/detailmenu", "Detail Menu", "/pages/menu/detailmenu.html", []),
    //new Route("/ourrentals", "Nos locations", "/pages/rental/ourrentals.html", []),
    //new Route("/detailrental", "Detail location", "/pages/rental/detailrental.html", []),
    new Route("/signin", "Connexion", "/pages/auth/signin.html", ["disconnected"], "/js/auth/signin.js"),
    new Route("/signup", "Inscription", "/pages/auth/signup.html", ["disconnected"], "/js/auth/signup.js"),
    new Route("/contact", "Contact", "/pages/contact.html", []),
    new Route("/mentionslegales", "Mentions Légales", "/pages/mentionslegales.html", []),

    // Routes utilisateurs connectés
    //new Route("/user/dashboard", "Mon espace", "/pages/user/dashboard.html", ["ROLE_USER"]),
    new Route("/user/orderform", "Commander", "/pages/user/orderform.html", ["ROLE_USER"], "/js/user/orderForm.js" ),
    //new Route("/user/orders", "Mes commandes", "/pages/user/orders.html", ["ROLE_USER"]),
    new Route("/user/profile", "Mon profil", "/pages/user/profile.html", ["ROLE_USER"]),
    
    
    // Routes employés
    new Route("/employee/dashboard", "Mon espace employee", "/pages/employee/dashboard.html", ["ROLE_EMPLOYEE"]),
    new Route("/employee/orders", "Commandes", "/pages/employee/orders.html", ["ROLE_EMPLOYEE"]),
    new Route("/employee/profile", "Mon profil employee", "/pages/employee/profile.html", ["ROLE_USER"]),
    
    // Routes administrateurs
    new Route("/admin/dashboard/stats", "Pages des ventes", "/pages/admin/stats.html", ["ROLE_ADMIN"], "/js/admin/stats.js"),
    new Route("/admin/users", "Gérer utilisateurs", "/pages/admin/users.html", ["ROLE_ADMIN"]),
    new Route("/admin/dashboard", "Mon espace admin", "/pages/admin/dashboard.html", ["ROLE_ADMIN"]),
    new Route("/admin/orders", "Commandes", "/pages/admin/orders.html", ["ROLE_ADMIN"]),
    new Route("/admin/profile", "Mon profile Admin", "/pages/admin/profile.html", ["ROLE_ADMIN"]),
    
];
//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "Vite et Gourmand"; 
