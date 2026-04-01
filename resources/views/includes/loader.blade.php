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
					<img src="{{ asset('assets/images/Logo Green Market.png') }}"
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

		<div class="loading-gif">
			<img src="{{ asset('assets/icons/Gif/loading2.gif') }}" alt="loading">
		</div>
	</div>
</div>

<style>
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
	background: #034410ff;
	border-radius: 0 100% 0 100%;
	top: 50%;
	left: 50%;
	filter: drop-shadow(0 0 3px rgba(121, 236, 150, 0.4));
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

.loader-container {
	position: relative;
	z-index: 2;
	text-align: center;
	display: flex;
	flex-direction: column;
	align-items: center;
}

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

.spinner-ring {
	position: absolute;
	width: 100%;
	height: 100%;
	border: 3px solid transparent;
	border-top: 3px solid #11b94cff;
	border-radius: 50%;
	animation: spin 1.5s linear infinite;
}

.spinner-ring-2 {
	width: 130%;
	height: 130%;
	top: -15%;
	left: -15%;
	border-top: 3px solid #0b8d39ff;
	animation: spin 2s linear infinite reverse;
	opacity: 0.7;
}

.spinner-ring-3 {
	width: 80%;
	height: 80%;
	top: 10%;
	left: 10%;
	border-top: 3px solid #11b94cff;
	animation: spin 1s linear infinite;
	opacity: 0.5;
}

@keyframes spin {
	to { transform: rotate(360deg); }
}

.loading-text {
		font-family: 'Pacifico', cursive;
		font-size: 2.5rem;
		background:
			linear-gradient(90deg, #023604ff, #11b94cff, #023604ff);
		-webkit-background-clip: text;
		background-clip: text;
		color: transparent;
		filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.3));
		position: relative;
	}

	.loading-text::after {
		content: "";
		position: absolute;
		width: 100%;
		height: 10px;
		background: linear-gradient(90deg, #023604ff, #11b94cff);
		bottom: -5px;
		left: 0;
		opacity: 0.5;
		border-radius: 50%;
	}


.percentage {
	margin-top: 10px;
	font-family: monospace;
	font-size: 0.9rem;
	color: rgba(0,0,0,0.6);
}

.glow-effect {
	position: absolute;
	inset: 0;
	border-radius: 20px;
	box-shadow:
		0 0 50px rgba(34,197,94,0.1),
		inset 0 0 50px rgba(34,197,94,0.05);
}

.loading-gif {
	margin-top: 20px;
}

.loading-gif img {
	width: 100px;
	height: auto;
}

@media (min-width: 2560px) {
	.orbit-container {
		width: 600px;
		height: 600px;
	}
	.leaf-particle {
		width: 22px;
		height: 22px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(240px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(240px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(240px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 280px;
		height: 210px;
		margin-bottom: 28px;
	}
	.logo-spinner {
		width: 170px;
		height: 170px;
	}
	.logo-image-container {
		width: 110px;
		height: 110px;
	}
	.loading-text {
		font-size: 1.9rem;
		letter-spacing: 5px;
	}
	.percentage {
		font-size: 1.2rem;
		margin-top: 14px;
	}
	.loading-gif {
		margin-top: 28px;
	}
	.loading-gif img {
		width: 100px;
	}
}

@media (min-width: 1501px) and (max-width: 2559px) {
	.orbit-container {
		width: 500px;
		height: 500px;
	}
	.leaf-particle {
		width: 18px;
		height: 18px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(200px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(200px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(200px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 240px;
		height: 180px;
		margin-bottom: 24px;
	}
	.logo-spinner {
		width: 150px;
		height: 150px;
	}
	.logo-image-container {
		width: 100px;
		height: 100px;
	}
	.loading-text {
		font-size: 1.6rem;
		letter-spacing: 4px;
	}
	.percentage {
		font-size: 1rem;
		margin-top: 12px;
	}
	.loading-gif {
		margin-top: 24px;
	}
	.loading-gif img {
		width: 60px;
	}
}

@media (min-width: 1400px) and (max-width: 1500px) {
	.orbit-container {
		width: 450px;
		height: 450px;
	}
	.leaf-particle {
		width: 16px;
		height: 16px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(180px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(180px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(180px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 220px;
		height: 165px;
		margin-bottom: 22px;
	}
	.logo-spinner {
		width: 135px;
		height: 135px;
	}
	.logo-image-container {
		width: 90px;
		height: 90px;
	}
	.loading-text {
		font-size: 1.45rem;
		letter-spacing: 3.5px;
	}
	.percentage {
		font-size: 0.95rem;
		margin-top: 11px;
	}
	.loading-gif {
		margin-top: 22px;
	}
	.loading-gif img {
		width: 55px;
	}
}

@media (min-width: 1200px) and (max-width: 1399px) {
	.orbit-container {
		width: 400px;
		height: 400px;
	}
	.leaf-particle {
		width: 14px;
		height: 14px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(160px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(160px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(160px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 200px;
		height: 150px;
		margin-bottom: 20px;
	}
	.logo-spinner {
		width: 120px;
		height: 120px;
	}
	.logo-image-container {
		width: 80px;
		height: 80px;
	}
	.loading-text {
		font-size: 1.3rem;
		letter-spacing: 3px;
	}
	.percentage {
		font-size: 0.9rem;
		margin-top: 10px;
	}
	.loading-gif {
		margin-top: 20px;
	}
	.loading-gif img {
		width: 50px;
	}
}

@media (min-width: 1001px) and (max-width: 1199px) {
	.orbit-container {
		width: 380px;
		height: 380px;
	}
	.leaf-particle {
		width: 13px;
		height: 13px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(150px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(150px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(150px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 190px;
		height: 140px;
		margin-bottom: 18px;
	}
	.logo-spinner {
		width: 110px;
		height: 110px;
	}
	.logo-image-container {
		width: 75px;
		height: 75px;
	}
	.loading-text {
		font-size: 1.2rem;
		letter-spacing: 2.8px;
	}
	.percentage {
		font-size: 0.85rem;
		margin-top: 8px;
	}
	.loading-gif {
		margin-top: 18px;
	}
	.loading-gif img {
		width: 100px;
	}
}

@media (max-width: 1000px) {
	.orbit-container {
		width: 360px;
		height: 360px;
	}
	.leaf-particle {
		width: 12px;
		height: 12px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(140px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(140px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(140px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 180px;
		height: 135px;
		margin-bottom: 16px;
	}
	.logo-spinner {
		width: 100px;
		height: 100px;
	}
	.logo-image-container {
		width: 70px;
		height: 70px;
	}
	.loading-text {
		font-size: 1.1rem;
		letter-spacing: 2.5px;
	}
	.percentage {
		font-size: 0.8rem;
		margin-top: 8px;
	}
	.loading-gif {
		margin-top: 16px;
	}
	.loading-gif img {
		width: 42px;
	}
}

@media (min-width: 992px) and (max-width: 999px) {
	.orbit-container {
		width: 340px;
		height: 340px;
	}
	.leaf-particle {
		width: 11px;
		height: 11px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(135px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(135px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(135px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 170px;
		height: 130px;
		margin-bottom: 15px;
	}
	.logo-spinner {
		width: 95px;
		height: 95px;
	}
	.logo-image-container {
		width: 65px;
		height: 65px;
	}
	.loading-text {
		font-size: 1rem;
		letter-spacing: 2.2px;
	}
	.percentage {
		font-size: 0.75rem;
		margin-top: 7px;
	}
	.loading-gif {
		margin-top: 15px;
	}
	.loading-gif img {
		width: 40px;
	}
}

@media (min-width: 768px) and (max-width: 991px) {
	.orbit-container {
		width: 320px;
		height: 320px;
	}
	.leaf-particle {
		width: 10px;
		height: 10px;
	}
	@keyframes orbit {
		0%   { transform: rotate(0deg) translateX(125px) rotate(0deg); opacity: 0.3; }
		50%  { transform: rotate(180deg) translateX(125px) rotate(-180deg); opacity: 0.6; }
		100% { transform: rotate(360deg) translateX(125px) rotate(-360deg); opacity: 0.3; }
	}
	.logo-wrapper1 {
		width: 160px;
		height: 120px;
		margin-bottom: 14px;
	}
	.logo-spinner {
		width: 90px;
		height: 90px;
	}
	.logo-image-container {
		width: 60px;
		height: 60px;
	}
	.loading-text {
		font-size: 0.95rem;
		letter-spacing: 2px;
	}
	.percentage {
		font-size: 0.7rem;
		margin-top: 6px;
	}
	.loading-gif {
		margin-top: 14px;
	}
	.loading-gif img {
		width: 38px;
	}
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const loader = document.getElementById("page-loader");
    const percentageEl = document.getElementById("percentage");

    const images = Array.from(document.images);
    const totalImages = images.length;
    let loadedImages = 0;
    let startTime = Date.now();
    let isFinished = false;

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
            // Check if 40 seconds have passed
            const elapsedTime = Date.now() - startTime;
            const remainingTime = Math.max(0, 2500 - elapsedTime);
            
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
</script>