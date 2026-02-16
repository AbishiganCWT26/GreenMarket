@extends('public_master')

@section('title', 'How It Works - GreenMarket')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/how-it-works.css') }}">
@endsection

@section('content')
@php
$howItWorksConfigs = DB::table('system_config')
->where('config_group', 'how_it_works')
->where('is_public', true)
->pluck('config_value', 'config_key')
->toArray();

function getHowItWorksConfig($key, $configs) {
return $configs[$key] ?? '';
}
@endphp

<div class="hw-container">
    <div class="hw-header-section animate-fade-up">
        <h1>How GreenMarket Works</h1>
        <p>Simple. Transparent. Direct from Farm to Table.</p>
    </div>

    <div class="hw-content-wrapper">
        <div class="hw-roles-grid">
            <div class="hw-role-card animate-fade-up" style="animation-delay:0.1s">
                <div class="hw-role-tag">
                    <i class="fas fa-shopping-cart"></i>
                    <h3><i class="fas fa-user-tag" style="margin-right:4px;"></i>For Buyers</h3>
                </div>

                <div class="hw-media-box">
                    @php $buyerImage = getHowItWorksConfig('How_Works_For_Buyers_image', $howItWorksConfigs); @endphp
                    <img src="{{ asset('assets/images/' . $buyerImage) }}" alt="Buyer process" onerror="this.style.display='none'">
                    <div class="hw-media-label">Buyer Process Flow</div>
                </div>

                @php
                $buyerInstructions = getHowItWorksConfig('How_Works_For_Buyers_para', $howItWorksConfigs);
                $buyerParagraphs = explode("\n\n", $buyerInstructions);
                @endphp

                @foreach($buyerParagraphs as $paragraph)
                @if(trim($paragraph))
                <p class="hw-description-text">
                    <i class="fas fa-circle" style="font-size:6px;"></i>
                    {{ trim($paragraph) }}
                </p>
                @endif
                @endforeach
            </div>

            <div class="hw-role-card animate-fade-up" style="animation-delay:0.2s">
                <div class="hw-role-tag">
                    <i class="fas fa-seedling"></i>
                    <h3><i class="fas fa-tractor" style="margin-right:4px;"></i>For Farmers</h3>
                </div>

                <div class="hw-media-box">
                    @php $farmerImage = getHowItWorksConfig('How_Works_For_Farmer_image', $howItWorksConfigs); @endphp
                    <img src="{{ asset('assets/images/' . $farmerImage) }}" alt="Farmer process" onerror="this.style.display='none'">
                    <div class="hw-media-label">Farmer Process Flow</div>
                </div>

                @php
                $farmerInstructions = getHowItWorksConfig('How_Works_For_Farmers_para', $howItWorksConfigs);
                $farmerParagraphs = explode("\n\n", $farmerInstructions);
                @endphp

                @foreach($farmerParagraphs as $paragraph)
                @if(trim($paragraph))
                <p class="hw-description-text">
                    <i class="fas fa-circle" style="font-size:6px;"></i>
                    {{ trim($paragraph) }}
                </p>
                @endif
                @endforeach
            </div>
        </div>

        <div class="hw-statistics-panel">
            <div class="hw-stat-item animate-fade-up" style="animation-delay:0.3s">
                <div class="hw-stat-value">{{ $stats['total_categories'] ?? 0 }}+</div>
                <div class="hw-stat-label">Categories</div>
            </div>
            <div class="hw-stat-item animate-fade-up" style="animation-delay:0.4s">
                <div class="hw-stat-value">{{ $stats['total_products'] ?? 0 }}+</div>
                <div class="hw-stat-label">Products</div>
            </div>
            <div class="hw-stat-item animate-fade-up" style="animation-delay:0.5s">
                <div class="hw-stat-value">{{ $stats['active_farmers'] ?? 0 }}+</div>
                <div class="hw-stat-label">Farmers</div>
            </div>
            <div class="hw-stat-item animate-fade-up" style="animation-delay:0.6s">
                <div class="hw-stat-value">{{ $stats['total_buyers'] ?? 0 }}+</div>
                <div class="hw-stat-label">Buyers</div>
            </div>
        </div>

        <div class="hw-features-area">
            <div class="hw-section-header">
                <i class="fas fa-crown"></i>
                <h2>Key Features</h2>
            </div>
            <div class="hw-features-grid">
                <div class="hw-feature-block animate-fade-up" style="animation-delay:0.7s">
                    <i class="fas fa-user-shield"></i>
                    <h4>Secure Registration</h4>
                    <p>Verified accounts</p>
                </div>
                <div class="hw-feature-block animate-fade-up" style="animation-delay:0.8s">
                    <i class="fas fa-bell"></i>
                    <h4>Real-time Updates</h4>
                    <p>SMS & email</p>
                </div>
                <div class="hw-feature-block animate-fade-up" style="animation-delay:0.9s">
                    <i class="fas fa-eye"></i>
                    <h4>Transparent Process</h4>
                    <p>Order tracking</p>
                </div>
                <div class="hw-feature-block animate-fade-up" style="animation-delay:1.0s">
                    <i class="fas fa-comments"></i>
                    <h4>Direct Communication</h4>
                    <p>Connect directly</p>
                </div>
                <div class="hw-feature-block animate-fade-up" style="animation-delay:1.1s">
                    <i class="fas fa-star"></i>
                    <h4>Feedback System</h4>
                    <p>Rate & review</p>
                </div>
                <div class="hw-feature-block animate-fade-up" style="animation-delay:1.2s">
                    <i class="fas fa-shield-alt"></i>
                    <h4>Secure Payments</h4>
                    <p>Safe processing</p>
                </div>
            </div>
        </div>

        <div class="hw-advantages-section">
            <div class="hw-section-header">
                <i class="fas fa-heart"></i>
                <h2>Why Choose Us?</h2>
            </div>
            <div class="hw-advantages-grid">
                <div class="hw-advantage-card animate-fade-up" style="animation-delay:1.3s">
                    <i class="fas fa-check-circle"></i>
                    <div class="hw-advantage-content">
                        <h4>Fresh & Local</h4>
                        <p>Direct from home gardens</p>
                    </div>
                </div>
                <div class="hw-advantage-card animate-fade-up" style="animation-delay:1.4s">
                    <i class="fas fa-check-circle"></i>
                    <div class="hw-advantage-content">
                        <h4>Fair Pricing</h4>
                        <p>Transparent pricing</p>
                    </div>
                </div>
                <div class="hw-advantage-card animate-fade-up" style="animation-delay:1.5s">
                    <i class="fas fa-check-circle"></i>
                    <div class="hw-advantage-content">
                        <h4>Easy Process</h4>
                        <p>Simple steps</p>
                    </div>
                </div>
                <div class="hw-advantage-card animate-fade-up" style="animation-delay:1.6s">
                    <i class="fas fa-check-circle"></i>
                    <div class="hw-advantage-content">
                        <h4>Community Focus</h4>
                        <p>Support local farmers</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="hw-support-panel animate-fade-up" style="animation-delay:1.7s">
            <h3><i class="fas fa-headset"></i> Need Help Getting Started?</h3>
            <p>Our support team is here to help you! Contact us for assistance.</p>
            <div class="hw-action-group">
                <a href="{{ route('contact.form') }}" class="hw-button button-secondary">
                    <i class="fas fa-headset"></i> Contact Us
                </a>
                <a href="#" class="hw-button button-secondary" id="registerTrigger">
                    <i class="fas fa-user-plus"></i> Register
                </a>
                <a href="{{ route('login') }}" class="hw-button button-secondary">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statValues = document.querySelectorAll('.hw-stat-value');
    statValues.forEach(stat => {
        const text = stat.textContent;
        const number = parseInt(text);
        if (!isNaN(number) && number > 0) {
            animateCounter(stat, number);
        }
    });

    function animateCounter(element, finalNumber) {
        let currentNumber = 0;
        const increment = finalNumber / 25;
        const timer = setInterval(() => {
            currentNumber += increment;
            if (currentNumber >= finalNumber) {
                currentNumber = finalNumber;
                clearInterval(timer);
            }
            element.textContent = Math.floor(currentNumber) + '+';
        }, 30);
    }

    const registerTrigger = document.getElementById('registerTrigger');
    if (registerTrigger) {
        registerTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Ready to Join?',
                html: `
                    <div style="text-align:center;">
                        <i class="fas fa-user-plus" style="font-size:2rem; color:#10B981; margin-bottom:8px;"></i>
                        <h3 style="color:#0f1724; margin-bottom:6px;">Choose Your Role</h3>
                        <p style="color:#6b7280; margin-bottom:12px;">Select how you want to use GreenMarket</p>
                        <div style="display:flex; flex-direction:column; gap:6px;">
                            <button onclick="window.location.href='{{ route('buyer.register') }}'" style="background:#10B981; color:white; border:none; padding:10px; border-radius:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;">
                                <i class="fas fa-shopping-cart"></i> As Buyer
                            </button>
                            <button onclick="showFarmerInfo()" style="background:white; color:#0f1724; border:1px solid #10B981; padding:10px; border-radius:12px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:5px;">
                                <i class="fas fa-seedling"></i> As Farmer
                            </button>
                        </div>
                    </div>
                `,
                showConfirmButton: false,
                showCloseButton: true,
                background: '#ffffff',
                width: '320px',
                customClass: {
                    popup: 'compact-popup'
                }
            });
        });
    }

    window.showFarmerInfo = function() {
        Swal.fire({
            title: 'Farmer Registration',
            html: `
                <div style="text-align:left;">
                    <i class="fas fa-seedling" style="font-size:2rem; color:#10B981; display:block; text-align:center; margin-bottom:8px;"></i>
                    <div style="background:#f6f8fa; padding:12px; border-radius:14px;">
                        <p style="margin-bottom:4px;"><strong>Step 1:</strong> Contact Lead Farmer</p>
                        <p style="margin-bottom:4px;"><strong>Step 2:</strong> Provide product details</p>
                        <p><strong>Step 3:</strong> Lead Farmer registers you</p>
                    </div>
                    <p style="color:#6b7280; font-size:0.75rem; margin-top:8px;">Don't know Lead Farmer? Contact Grama Sevakar.</p>
                </div>
            `,
            confirmButtonText: 'Got It',
            confirmButtonColor: '#10B981',
            background: '#ffffff',
            width: '300px'
        });
    };

    document.querySelectorAll('.hw-media-box img').forEach(img => {
        img.addEventListener('error', function() {
            this.style.display = 'none';
            const container = this.closest('.hw-media-box');
            if (container) {
                const caption = container.querySelector('.hw-media-label');
                if (caption) {
                    caption.textContent = 'Image not available';
                    caption.style.background = 'rgba(239,68,68,0.8)';
                }
            }
        });

        img.addEventListener('load', function() {
            this.style.opacity = '0';
            setTimeout(() => {
                this.style.transition = 'opacity 0.3s ease';
                this.style.opacity = '1';
            }, 10);
        });
    });

    if (window.location.hash === '#register') {
        setTimeout(() => {
            if (registerTrigger) {
                registerTrigger.click();
            }
        }, 300);
    }

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-up');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.hw-stat-item, .hw-feature-block, .hw-advantage-card').forEach(el => {
        observer.observe(el);
    });

    const successMessage = sessionStorage.getItem('successMessage');
    if (successMessage) {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: successMessage,
            timer: 2000,
            showConfirmButton: false,
            background: '#ffffff',
            customClass: {
                popup: 'compact-popup'
            }
        });
        sessionStorage.removeItem('successMessage');
    }

    const errorMessage = sessionStorage.getItem('errorMessage');
    if (errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: errorMessage,
            background: '#ffffff',
            customClass: {
                popup: 'compact-popup'
            }
        });
        sessionStorage.removeItem('errorMessage');
    }
});
</script>

<style>
.compact-popup {
    border-radius: 20px !important;
    font-size: 12px !important;
    padding: 4px !important;
}
</style>
@endsection