

// // document.addEventListener("DOMContentLoaded", function () {
// //     // Toggle visibility of media content on mobile
// //     document.getElementById("toggleMedia").onclick = function () {
// //         var mediaContent = document.getElementById("mediaContent");
// //         if (mediaContent.classList.contains("d-none")) {
// //             mediaContent.classList.remove("d-none");
// //             this.textContent = "Masquer les médias";
// //         } else {
// //             mediaContent.classList.add("d-none");
// //             this.textContent = "Voir les médias";
// //         }

// //         // Toggle visibility of additional images and videos
// //         var imageItems = document.querySelectorAll(".image-item");
// //         var videoItems = document.querySelectorAll(".video-item");
// //         imageItems.forEach((item) => item.classList.toggle("d-none"));
// //         videoItems.forEach((item) => item.classList.toggle("d-none"));
// //     };

// //     // Load more images
// //     document.getElementById("loadMoreImages").onclick = function () {
// //         // Logique pour charger plus d'images
// //         console.log("Load more images clicked");
// //         // Vous pouvez ici ajouter la logique pour afficher plus d'images
// //     };

// //     // Load more videos
// //     document.getElementById("loadMoreVideos").onclick = function () {
// //         // Logique pour charger plus de vidéos
// //         console.log("Load more videos clicked");
// //         // Vous pouvez ici ajouter la logique pour afficher plus de vidéos
// //     };
// // });



// let currentOffset = 10; // Offset pour les commentaires déjà chargés
// const trickSlug = "{{ trick.slug }}"; // Récupérer le slug du trick

// document
//     .getElementById("loadMoreComments")
//     .addEventListener("click", function () {
//         fetch(`/trick/${trickSlug}/comments?offset=${currentOffset}`)
//             .then((response) => response.json())
//             .then((data) => {
//                 // Ajouter les nouveaux commentaires au DOM
//                 data.comments.forEach((comment) => {
//                     const commentDiv = document.createElement("div");
//                     commentDiv.className = "comment mb-3 border p-3 rounded";
//                     commentDiv.innerHTML = `
//                     <div class="d-flex align-items-start">
//                         <img src="{{ asset('uploads/images/imgProfilDefault.jpg') }}" alt="default-user" class="rounded-circle me-3" style="height: 50px; width: 50px;">
//                         <p class="mb-0">${comment.message}</p>
//                         <div class="d-flex justify-content-between">
//                             <p class="mb-0"><strong>${
//                                 comment.username
//                             }</strong></p>
//                             <p class="small text-muted">${new Date(
//                                 comment.dateCreated
//                             ).toLocaleDateString()}</p>
//                         </div>
//                     </div>
//                 `;
//                     document
//                         .querySelector(".comments-section")
//                         .appendChild(commentDiv);
//                 });
//                 currentOffset += 10; // Mettre à jour l'offset
//             });
//     });
