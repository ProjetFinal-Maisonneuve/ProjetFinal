const cellarToggleActionBtn = document.getElementById("setting-btn");
const cellarBoxes = document.querySelectorAll(".cellar-box");
if (cellarToggleActionBtn) {
    let clicked = false;
    cellarToggleActionBtn.addEventListener("click", () => {
        clicked = !clicked;
        if (clicked) {
            cellarToggleActionBtn.classList.add(
                "bg-focus",
                "border-muted",
                "border"
            );
            cellarBoxes.forEach((box) => {
                box.classList.add("animate-shake");
                box.querySelector(".cellar-action-btns").classList.remove(
                    "hidden"
                );
            });
        } else {
            cellarToggleActionBtn.classList.remove(
                "bg-focus",
                "border-muted",
                "border"
            );
            cellarBoxes.forEach((box) => {
                box.classList.remove("animate-shake");
                box.querySelector(".cellar-action-btns").classList.add(
                    "hidden"
                );
            });
        }
    });
}
