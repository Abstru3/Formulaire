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

  // Explosions 5 fois par seconde (200ms)
  const explosionsContainer = document.getElementById("explosions-container");

  setInterval(() => {
    const explosion = document.createElement("img");
    explosion.src = "boom-explosion.gif";
    explosion.style.position = "absolute";
    explosion.style.width = "300px";
    explosion.style.height = "300px";
    explosion.style.left = Math.random() * 100 + "%";
    explosion.style.top = Math.random() * 100 + "%";
    explosion.style.transform = "translate(-50%, -50%)";

    explosionsContainer.appendChild(explosion);

    // Supprimer l'explosion après 2s (laisse le gif se terminer)
    setTimeout(() => {
      explosion.remove();
    }, 2000);
  }, 200); // 200ms = 5 fois par seconde
});
