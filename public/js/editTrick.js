// Ajouter une nouvelle vidéo
document.querySelector(".add-video").addEventListener("click", function () {
    var videoInput = document.createElement("div");
    videoInput.classList.add("input-group", "mb-3");

    // Ajouter le champ input avec le bouton de suppression
    videoInput.innerHTML = `
        <input type="text" name="trick[videos][]" class="form-control" placeholder="Entrez l'URL de la vidéo ici..." required> 
        <button type="button" class="btn btn-danger remove-video">Supprimer</button>
    `;

    // Ajouter la nouvelle vidéo au DOM
    document.querySelector(".video-inputs").appendChild(videoInput);
});

// Gérer la suppression des vidéos
document.querySelector(".video-inputs").addEventListener("click", function (e) {
    if (e.target.closest(".remove-video")) {
        e.target.closest(".input-group").remove();
    }
});