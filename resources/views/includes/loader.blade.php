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
                    <img src="{{ asset('assets/images/Logo-4-loader.png') }}" class="logo-image" alt="GreenMarket Logo">
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

/* Floating particles (optional future use) */
.particle {
    position: absolute;
    background: rgba(34, 197, 94, 0.05);
    border-radius: 50%;
    animation: floatParticle 15s infinite linear;
}

@keyframes floatParticle {
    0% { transform: translate(0,0) scale(1); opacity: 0; }
    10%,90% { opacity: 0.1; }
    100% { transform: translate(var(--tx,100px), var(--ty,-100px)) scale(0.5); opacity: 0; }
}

/* Orbit leaves - Changed to dark green */
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
    background: #059669; /* Dark green color */
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
    justify-content: center;
}

/* Logo spinner - Fixed alignment */
.logo-wrapper1 {
    position: relative;
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
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 4;
}

.logo-image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.3));
    animation: logoPulse 2s ease-in-out infinite;
}

@keyframes logoPulse {
    0%, 100% { 
        transform: scale(1);
        filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.2));
    }
    50% { 
        transform: scale(1.05);
        filter: drop-shadow(0 0 15px rgba(34, 197, 94, 0.4));
    }
}

.spinner-ring {
    position: absolute;
    width: 100%;
    height: 100%;
    border: 3px solid transparent;
    border-top: 3px solid #10B981;
    border-radius: 50%;
    animation: spin 1.5s linear infinite;
    z-index: 2;
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
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Loading text */
.loading-text {
    margin-top: 10px;
    font-weight: 600;
    letter-spacing: 3px;
    font-size: 1.3rem;
    color: rgba(0,0,0,0.7);
    opacity: 0.8;
    animation: textFade 2s ease-in-out infinite;
    text-align: center;
    width: 100%;
}

@keyframes textFade {
    0%, 100% { 
        opacity: 0.6;
        transform: translateY(0);
        letter-spacing: 2px;
    }
    50% { 
        opacity: 1;
        transform: translateY(-3px);
        letter-spacing: 4px;
        color: #10B981;
    }
}

/* Percentage */
.percentage {
    margin-top: 10px;
    font-family: monospace;
    font-size: 0.9rem;
    color: rgba(0,0,0,0.6);
    font-weight: 500;
    text-align: center;
    width: 100%;
}

/* Glow */
.glow-effect {
    position: absolute;
    inset: 0;
    border-radius: 20px;
    box-shadow:
        0 0 50px rgba(34,197,94,0.1),
        inset 0 0 50px rgba(34,197,94,0.05);
    animation: glowPulse 3s infinite alternate;
    pointer-events: none;
}

@keyframes glowPulse {
    from { opacity: 0.3; }
    to   { opacity: 0.6; }
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("page-loader");
    const percentageEl = document.getElementById("percentage");

    // Check if all images are loaded
    function checkAllImagesLoaded() {
        const images = document.querySelectorAll('img');
        let loadedCount = 0;
        const totalImages = images.length;
        
        if (totalImages === 0) {
            // No images found, complete loader immediately
            completeLoader();
            return;
        }

        images.forEach(img => {
            if (img.complete) {
                loadedCount++;
                updatePercentage(loadedCount, totalImages);
            } else {
                img.addEventListener('load', () => {
                    loadedCount++;
                    updatePercentage(loadedCount, totalImages);
                    if (loadedCount === totalImages) {
                        completeLoader();
                    }
                });
                img.addEventListener('error', () => {
                    loadedCount++;
                    updatePercentage(loadedCount, totalImages);
                    if (loadedCount === totalImages) {
                        completeLoader();
                    }
                });
            }
        });

        // If all images were already loaded
        if (loadedCount === totalImages) {
            completeLoader();
        }
    }

    function updatePercentage(loaded, total) {
        const percentage = Math.round((loaded / total) * 100);
        percentageEl.textContent = percentage + "%";
    }

    function completeLoader() {
        // Ensure we show 100%
        percentageEl.textContent = "100%";
        
        // Wait a moment for user to see 100%
        setTimeout(() => {
            // Add fade out animation to loader
            loader.style.transition = "opacity 0.8s ease, transform 0.8s ease";
            loader.style.opacity = "0";
            loader.style.transform = "scale(0.95)";

            setTimeout(() => {
                loader.remove();
            }, 800);
        }, 300);
    }

    // Start checking images after a short delay to ensure DOM is ready
    setTimeout(() => {
        checkAllImagesLoaded();
    }, 100);

    // Fallback: If loader takes too long, force complete after 5 seconds
    setTimeout(() => {
        if (loader && loader.parentNode) {
            completeLoader();
        }
    }, 5000);
});
</script>