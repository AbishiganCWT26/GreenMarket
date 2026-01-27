{{-- Page Loader --}}
<div id="page-loader">
    <div class="particles-container"></div>

    <div class="orbit-container">
        <div class="leaf-particle p1"></div>
        <div class="leaf-particle p2"></div>
        <div class="leaf-particle p3"></div>
        <div class="leaf-particle p4"></div>
        <div class="leaf-particle p5"></div>
    </div>

    <div class="loader-container">
        <div class="glow-effect"></div>

        <div class="logo-wrapper1">
            <div class="logo-spinner">
                <div class="logo-image-container">
                    <img src="{{ asset('assets/images/Logo-4-loader.png') }}"
                         class="logo-image"
                         alt="GreenMarket Logo">
                </div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring spinner-ring-2"></div>
                <div class="spinner-ring spinner-ring-3"></div>
            </div>
        </div>

        <div class="loading-text">GREENMARKET</div>
        <div class="percentage" id="percentage">0%</div>
    </div>
</div>

<style>
/* =======================
   PAGE LOADER STYLES
======================= */

#page-loader {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: hidden;
}

/* Orbit leaves */
.orbit-container {
    position: absolute;
    width: 400px;
    height: 400px;
    z-index: 1;
}

.leaf-particle {
    position: absolute;
    width: 14px;
    height: 14px;
    background: #059669;
    border-radius: 0 100% 0 100%;
    top: 50%;
    left: 50%;
    filter: drop-shadow(0 0 3px rgba(5, 150, 105, 0.4));
    animation: orbit 5s linear infinite;
}

.p1 { animation-delay: 0s; }
.p2 { animation-delay: -1s; }
.p3 { animation-delay: -2s; }
.p4 { animation-delay: -3s; }
.p5 { animation-delay: -4s; }

@keyframes orbit {
    0%   { transform: rotate(0deg) translateX(160px) rotate(0deg); opacity: 0.3; }
    50%  { transform: rotate(180deg) translateX(160px) rotate(-180deg); opacity: 0.6; }
    100% { transform: rotate(360deg) translateX(160px) rotate(-360deg); opacity: 0.3; }
}

/* Loader container */
.loader-container {
    position: relative;
    z-index: 2;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
}

/* Logo */
.logo-wrapper1 {
    width: 200px;
    height: 150px;
    margin-bottom: 20px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.logo-spinner {
    position: relative;
    width: 120px;
    height: 120px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.logo-image-container {
    position: absolute;
    width: 80px;
    height: 80px;
    z-index: 4;
}

.logo-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    animation: logoPulse 2s ease-in-out infinite;
}

@keyframes logoPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Rings */
.spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid transparent;
    border-top: 3px solid #10B981;
    border-radius: 50%;
    animation: spin 1.5s linear infinite;
}

.spinner-ring-2 {
    width: 130%;
    height: 130%;
    top: -15%;
    left: -15%;
    border-top: 3px solid #34D399;
    animation: spin 2s linear infinite reverse;
    opacity: 0.7;
}

.spinner-ring-3 {
    width: 80%;
    height: 80%;
    top: 10%;
    left: 10%;
    border-top: 3px solid #059669;
    animation: spin 1s linear infinite;
    opacity: 0.5;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Text */
.loading-text {
    font-weight: 600;
    letter-spacing: 3px;
    font-size: 1.3rem;
    color: rgba(0,0,0,0.7);
}

.percentage {
    margin-top: 10px;
    font-family: monospace;
    font-size: 0.9rem;
    color: rgba(0,0,0,0.6);
}

/* Glow */
.glow-effect {
    position: absolute;
    inset: 0;
    border-radius: 20px;
    box-shadow:
        0 0 50px rgba(34,197,94,0.1),
        inset 0 0 50px rgba(34,197,94,0.05);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("page-loader");
    const percentageEl = document.getElementById("percentage");

    const images = Array.from(document.images);
    const totalImages = images.length;
    let loadedImages = 0;

    if (totalImages === 0) {
        finishLoading();
        return;
    }

    function updateProgress() {
        const percent = Math.round((loadedImages / totalImages) * 100);
        percentageEl.textContent = percent + "%";

        if (loadedImages === totalImages) {
            finishLoading();
        }
    }

    function imageLoaded() {
        loadedImages++;
        updateProgress();
    }

    images.forEach(img => {
        if (img.complete && img.naturalHeight !== 0) {
            imageLoaded();
        } else {
            img.addEventListener("load", imageLoaded, { once: true });
            img.addEventListener("error", imageLoaded, { once: true });
        }
    });

    function finishLoading() {
        percentageEl.textContent = "100%";

        loader.style.transition = "opacity 0.8s ease, transform 0.8s ease";
        loader.style.opacity = "0";
        loader.style.transform = "scale(0.95)";

        setTimeout(() => {
            loader.remove();
        }, 800);
    }
});
</script>
