let button = document.getElementById("btn-go-to-top");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    scrollToTop();
};

function scrollToTop() {
    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        button.style.display = "block";
    } else {
        button.style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
function goToTop() {
    document.body.scrollTop = 0;
    document.documentElement.scrollTop = 0;
}
