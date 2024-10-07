document.getElementById("show-more-btn").addEventListener("click", function () {
    var extraTricks = document.getElementById("extra-tricks");

    if (extraTricks.classList.contains("d-none")) {
        extraTricks.classList.remove("d-none"); // Affiche les tricks supplémentaires
        this.innerHTML = '<i class="fa-solid fa-eye-slash"></i> Voir Moins'; // Change le texte du bouton
    } else {
        extraTricks.classList.add("d-none"); // Cache les tricks supplémentaires
        this.innerHTML = '<i class="fa-solid fa-eye"></i> Voir Plus'; // Change le texte du bouton
    }
});
