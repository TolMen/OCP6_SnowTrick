document.addEventListener("DOMContentLoaded", function () {
    // Vérifie si l'élément du modal de succès existe dans le document
    var successModalElement = document.getElementById("successModal");
    if (successModalElement) {
        // Crée une instance de modal Bootstrap en utilisant l'élément existant
        var successModal = new bootstrap.Modal(successModalElement);

        // Affiche le modal de succès
        successModal.show();
    }
});
