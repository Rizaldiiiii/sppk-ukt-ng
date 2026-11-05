// Scroll Progress Indicator (Motion.md §13.2)
const progressElement = document.querySelector(".scroll-progress");

if (progressElement) {
    window.addEventListener(
        "scroll",
        () => {
            const scrollTop = window.scrollY;
            const scrollHeight =
                document.documentElement.scrollHeight - window.innerHeight;
            const progress =
                scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;

            document.documentElement.style.setProperty(
                "--scroll-progress",
                `${progress}%`
            );
        },
        { passive: true }
    );
}

