document.addEventListener("DOMContentLoaded", () => {
  const franceImage = document.getElementById("france-image");
  const audio = document.getElementById("background-music");

  // Lancer la musique au premier clic
  document.addEventListener(
    "click",
    () => {
      audio.volume = 0.3; // Volume à 30%
      audio.play().catch((error) => {
        console.log("Erreur lors de la lecture :", error);
      });
    },
    { once: true },
  ); // Une seule fois

  setInterval(() => {
    // Afficher
    franceImage.classList.add("show");

    // Masquer après 1.5s
    setTimeout(() => {
      franceImage.classList.remove("show");
    }, 1500);
  }, 3000); // Répète toutes les 3s (1.5s visible + 1.5s invisible)
});
