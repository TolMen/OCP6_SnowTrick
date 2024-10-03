document.getElementById("show-more-btn").addEventListener("click", function () {
    var extraTricks = document.getElementById("extra-tricks");
    extraTricks.classList.remove("d-none"); // Affiche les tricks supplémentaires
    this.style.display = "none"; // Masque le bouton "Voir plus"
});
