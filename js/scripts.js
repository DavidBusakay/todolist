document.addEventListener("DOMContentLoaded", () => {
    // Controler le scroll vers le bas du container de la liste des collections
    const collectionsList = document.getElementById("collectionsList");
    collectionsList.scrollTo(0, collectionsList.scrollHeight);

    // Mettre à jour l'heure
    function updateTime() {
        const now = new Date();
        const timeFormatter = new Intl.DateTimeFormat("fr-CD", {
            hour: "numeric",
            minute: "numeric",
            second: "numeric",
            timeZone: "Africa/Kinshasa"
        });
        const currentTimeString = timeFormatter.format(now);
        document.getElementById("currentTime").textContent = currentTimeString;
    }
    setInterval(updateTime, 1000);
    updateTime();
});