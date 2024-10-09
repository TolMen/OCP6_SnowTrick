// Ajoute un écouteur d'événement au bouton "Voir Plus"
document.getElementById("show-more-btn").addEventListener("click", function () {
    // Récupère l'élément contenant les tricks supplémentaires
    var extraTricks = document.getElementById("extra-tricks");

    // Vérifie si les tricks supplémentaires sont actuellement cachés
    if (extraTricks.classList.contains("d-none")) {
        // Affiche les tricks supplémentaires en retirant la classe "d-none"
        extraTricks.classList.remove("d-none");
        // Change le texte du bouton pour indiquer qu'on peut voir moins
        this.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Voir Moins';
    } else {
        // Cache les tricks supplémentaires en ajoutant la classe "d-none"
        extraTricks.classList.add("d-none");
        // Change le texte du bouton pour indiquer qu'on peut voir plus
        this.innerHTML = '<i class="fa-solid fa-eye"></i> Voir Plus';
    }
});
