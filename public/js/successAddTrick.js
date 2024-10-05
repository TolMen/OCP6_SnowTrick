document.addEventListener("DOMContentLoaded", function () {
    // Vérifie si le message de succès est présent dans les données de session
    if (document.getElementById("successModal")) {
        var successModal = new bootstrap.Modal(
            document.getElementById("successModal")
        );
        successModal.show();
    }
});
