
        document.addEventListener("DOMContentLoaded", () => {
            const btn = document.querySelector(".user-btn");
            const drop = document.querySelector(".user-dropdown");

            if (btn) {
                btn.addEventListener("click", (e) => {
                    e.stopPropagation();
                    drop.classList.toggle("open");
                });
            }

            document.addEventListener("click", () => {
                drop?.classList.remove("open");
            });
        });
