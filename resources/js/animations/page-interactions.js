// Page Interactions: proteksi klik ganda form & fallback interaktivitas ringan (Motion.md §5.2 & §16)
document.addEventListener("DOMContentLoaded", () => {
    // Cegah pengiriman berulang saat form disubmit
    document.querySelectorAll("form[data-prevent-double-submit]").forEach((form) => {
        form.addEventListener("submit", () => {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                const defaultLabel = submitBtn.querySelector("[data-label-default]");
                const loadingLabel = submitBtn.querySelector("[data-label-loading]");
                if (defaultLabel && loadingLabel) {
                    defaultLabel.classList.add("hidden");
                    loadingLabel.classList.remove("hidden");
                    loadingLabel.classList.add("inline-flex");
                }
            }
        });
    });
});

