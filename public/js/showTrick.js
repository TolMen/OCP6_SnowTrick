// public/js/trick-show.js

document.addEventListener("DOMContentLoaded", function () {
    // Toggle visibility of media content on mobile
    document.getElementById("toggleMedia").onclick = function () {
        var mediaContent = document.getElementById("mediaContent");
        if (mediaContent.classList.contains("d-none")) {
            mediaContent.classList.remove("d-none");
            this.textContent = "Masquer les médias";
        } else {
            mediaContent.classList.add("d-none");
            this.textContent = "Voir les médias";
        }

        // Toggle visibility of additional images and videos
        var imageItems = document.querySelectorAll(".image-item");
        var videoItems = document.querySelectorAll(".video-item");
        imageItems.forEach((item) => item.classList.toggle("d-none"));
        videoItems.forEach((item) => item.classList.toggle("d-none"));
    };

    // Load more images
    document.getElementById("loadMoreImages").onclick = function () {
        // Logique pour charger plus d'images
        console.log("Load more images clicked");
        // Vous pouvez ici ajouter la logique pour afficher plus d'images
    };

    // Load more videos
    document.getElementById("loadMoreVideos").onclick = function () {
        // Logique pour charger plus de vidéos
        console.log("Load more videos clicked");
        // Vous pouvez ici ajouter la logique pour afficher plus de vidéos
    };
});
