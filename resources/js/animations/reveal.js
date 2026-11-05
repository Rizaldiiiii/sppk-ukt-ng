// Scroll Reveal dengan IntersectionObserver (Motion.md §3.3)
const revealElements = document.querySelectorAll("[data-reveal]");

if (revealElements.length > 0 && "IntersectionObserver" in window) {
    document.documentElement.classList.add("has-reveal");

    const observer = new IntersectionObserver(
        (entries, obs) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("is-visible");
                    obs.unobserve(entry.target);
                }
            });
        },
        {
            threshold: 0.12,
            rootMargin: "0px 0px -40px 0px",
        }
    );

    revealElements.forEach((element) => {
        observer.observe(element);
    });
}

