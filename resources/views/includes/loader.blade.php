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

        <div class="logo-wrapper">
            <img src="{{ asset('assets/images/Logo-4.png') }}" class="logo-base" alt="GreenMarket Logo">
            <img src="{{ asset('assets/images/Logo-4.png') }}" class="logo-reveal" alt="GreenMarket Logo">
        </div>

        <div class="loading-text">GREENMARKET</div>

        <div class="progress-line-container">
            <div class="progress-line"></div>
        </div>

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
    background: rgb(34, 197, 94);
    border-radius: 0 100% 0 100%;
    top: 50%;
    left: 50%;
    filter: drop-shadow(0 0 3px rgba(34, 197, 94, 0.3));
    animation: orbit 5s linear infinite;
}

.p1 { animation-delay: 0s; }
.p2 { animation-delay: -1s; }
.p3 { animation-delay: -2s; }
.p4 { animation-delay: -3s; }
.p5 { animation-delay: -4s; }

@keyframes orbit {
    0%   { transform: rotate(0deg) translateX(160px) rotate(0deg); opacity: 0.3; }
    50%  { transform: rotate(180deg) translateX(160px) rotate(-180deg); opacity: 0.4; }
    100% { transform: rotate(360deg) translateX(160px) rotate(-360deg); opacity: 0.3; }
}

/* Loader container */
.loader-container {
    position: relative;
    z-index: 2;
    text-align: center;
}

/* Logo reveal */
.logo-wrapper {
    position: relative;
    width: 200px;
    height: 150px;
    margin-bottom: 30px;
}

.logo-base,
.logo-reveal {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.logo-base {
    filter: grayscale(100%) opacity(0.15);
    transform: scale(1.05);
}

.logo-reveal {
    clip-path: polygon(0% 100%,100% 100%,100% 100%,0% 100%);
    animation: growUp 10s cubic-bezier(0.4,0,0.2,1) forwards;
    filter: drop-shadow(0 0 10px rgba(34, 197, 94, 0.2));
}

@keyframes growUp {
    0%   { clip-path: polygon(0% 100%,100% 100%,100% 100%,0% 100%); opacity: 0; }
    15%  { clip-path: polygon(0% 0%,100% 0%,100% 100%,0% 100%); opacity: 1; }
    85%  { opacity: 1; }
    100% { opacity: 0; transform: scale(0.95); }
}

/* Loading text */
.loading-text {
    margin-top: 20px;
    font-weight: 600;
    letter-spacing: 3px;
    font-size: 1.3rem;
    color: rgba(0,0,0,0.7);
    opacity: 0;
    animation: textFade 10s ease-in-out forwards;
}

@keyframes textFade {
    0%   { opacity: 0; transform: translateY(10px); }
    15%  { opacity: 1; transform: translateY(0); }
    85%  { opacity: 1; }
    100% { opacity: 0; transform: translateY(-10px); }
}

/* Progress bar */
.progress-line-container {
    width: 220px;
    height: 2px;
    background: rgba(0,0,0,0.1);
    margin: 25px auto 0;
    overflow: hidden;
}

.progress-line {
    width: 0%;
    height: 100%;
    background: linear-gradient(90deg,
        rgba(34,197,94,0.6),
        rgba(134,239,172,0.6),
        rgba(34,197,94,0.6));
    animation: lineGrow 10s linear forwards;
}

@keyframes lineGrow {
    from { width: 0%; }
    to   { width: 100%; }
}

/* Percentage */
.percentage {
    margin-top: 15px;
    font-family: monospace;
    font-size: 0.85rem;
    color: rgba(0,0,0,0.4);
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

    let percentage = 0;

    const interval = setInterval(() => {
        percentage++;
        percentageEl.textContent = percentage + "%";

        if (percentage >= 100) {
            clearInterval(interval);

            setTimeout(() => {
                loader.style.transition = "opacity 0.8s ease";
                loader.style.opacity = "0";
            }, 300);

            setTimeout(() => {
                loader.remove();
            }, 1200);
        }
    }, 30);
});
</script>
