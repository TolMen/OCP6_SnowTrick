document.addEventListener("DOMContentLoaded", function () {
    // Fonction pour gérer le clic sur les boutons de suppression des vidéos
    document.querySelectorAll(".remove-video").forEach(function (button) {
        button.addEventListener("click", function () {
            const videoNumber = this.getAttribute("data-video"); // Récupérer le numéro de la vidéo
            const videoInput = document.querySelector(`#trick_edit_video${videoNumber}`); // Utiliser l'ID correct

            // Vider le champ et masquer l'input
            if (videoInput) {
                videoInput.value = ""; // Vider la valeur de l'input
                videoInput.closest(".input-group").style.display = "none"; // Cacher l'input
            }

            // Ajouter un champ caché pour indiquer la suppression côté serveur
            const form = document.querySelector("form");
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = `remove_video_${videoNumber}`; // Crée un nom pour la suppression côté serveur
            hiddenInput.value = "1";
            form.appendChild(hiddenInput);
        });
    });

    // Fonction pour gérer le clic sur les boutons de suppression des images
    document.querySelectorAll(".remove-image").forEach(function (button) {
        button.addEventListener("click", function () {
            const imageNumber = this.getAttribute("data-image"); // Récupérer le numéro de l'image
            const imageInput = document.querySelector(`#trick_edit_image${imageNumber}`); // Utiliser l'ID correct

            // Vider le champ et masquer l'input
            if (imageInput) {
                imageInput.value = ""; // Vider la valeur de l'input
                imageInput.closest(".input-group").style.display = "none"; // Cacher l'input
            }

            // Ajouter un champ caché pour indiquer la suppression côté serveur
            const form = document.querySelector("form");
            const hiddenInput = document.createElement("input");
            hiddenInput.type = "hidden";
            hiddenInput.name = `remove_image_${imageNumber}`; // Crée un nom pour la suppression côté serveur
            hiddenInput.value = "1";
            form.appendChild(hiddenInput);
        });
    });
});
