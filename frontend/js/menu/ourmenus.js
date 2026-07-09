
const buttonFilter = document.getElementById("buttonFilter");
const filterMenu = document.getElementById("filterMenu");

//fonction d'affichage filtre
buttonFilter.addEventListener('click', ()=> {
    if (filterMenu.style.display === "none") {
    filterMenu.style.display = "block";
  } else {
    filterMenu.style.display = "none";
  }
})

// Handle button click event
// déclaration tableaux des catégories active
let activeCategories = [];
// selectionne tous les bouton catégory de ma page.
document.querySelectorAll(".category-button").forEach(button => {
//pour chaque bouton de catégory ajoute un écouteur dévenement au click
  button.addEventListener("click", () => {
/* la constante category est definit et permet de récupérer 
le contenu textuel du boutton, pour y definir la catégory */
    const category = button.innerText.trim();
/* ajoute la class "active bouton" aux buttons  */ 
    button.classList.toggle("active-button");
    if (button.classList.contains("active-button")) {
/* si le button contient la class bouton-active , il pousse la category dans 
le tableau */
      activeCategories.push(category);
    } else {
/*parcours le tableau et ne garde que les catégories qui sont 
différentes de celle sur laquelle on vient de cliquer.*/
      activeCategories = activeCategories.filter(cat => cat !== category);
    }
   filterCards();
  });
});
// Filter cards based on active categories
function filterCards() {
  document.querySelectorAll(".card").forEach(card => {
    const cardCategories = card.getAttribute("data-category").split(",");
    const match = activeCategories.every(cat => cardCategories.includes(cat));
    card.style.display = match ? "block" : "none";
  });
}