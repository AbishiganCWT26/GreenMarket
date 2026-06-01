
document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("page-loader");
    const percentageEl = document.getElementById("percentage");

    const images = Array.from(document.images);
    const totalImages = images.length;
    let loadedImages = 0;
    let startTime = Date.now();
    let isFinished = false;

    // Maximum loading time fallback (5 seconds)
    setTimeout(() => {
        finishLoading();
    }, 5000);

    if (totalImages === 0) {
        finishLoading();
        return;
    }

    function markLoaded(img) {
        if (img.dataset.counted) return; // prevent double counting
        img.dataset.counted = "true";

        loadedImages++;
        updateProgress();
    }

    function updateProgress() {
        const percent = Math.round((loadedImages / totalImages) * 100);
        percentageEl.textContent = percent + "%";

        if (loadedImages >= totalImages && !isFinished) {
            // Wait at least 1 second before hiding the loader
            const elapsedTime = Date.now() - startTime;
            const remainingTime = Math.max(0, 1000 - elapsedTime);
            
            if (remainingTime > 0) {
                // Wait for the remaining time before finishing
                setTimeout(() => {
                    finishLoading();
                }, remainingTime);
            } else {
                finishLoading();
            }
        }
    }

    images.forEach(img => {
        // Already loaded or already failed
        if (img.complete) {
            markLoaded(img);
        } else {
            img.addEventListener("load", () => markLoaded(img), { once: true });
            img.addEventListener("error", () => markLoaded(img), { once: true });
        }
    });

    function finishLoading() {
        if (isFinished) return;
        isFinished = true;
        
        percentageEl.textContent = "100%";

        loader.style.transition = "opacity 0.8s ease, transform 0.8s ease";
        loader.style.opacity = "0";
        loader.style.transform = "scale(0.95)";

        setTimeout(() => {
            loader.remove();
        }, 800);
    }
});
