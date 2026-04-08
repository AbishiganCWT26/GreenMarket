<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lead Farmer @yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/Lead_Farmer/lead_farmer-master.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .swal2-image {
            margin: 0em auto 0em !important;
        }

        div:where(.swal2-container) h2:where(.swal2-title) {
            padding: 0em 0em 0 !important;
        }
    </style>
    @yield('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- animate.css (for subtle entrance) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Google Translate Compact Card CSS -->
    <style>
        .goog-te-gadget-simple {
            background-color: #f0f4f9 !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 20px !important;
            padding: 1px 6px 1px 4px !important;
            display: flex !important;
            align-items: center !important;
            gap: 1px !important;
            font-family: 'Inter', sans-serif !important;
            font-weight: 700 !important;
            font-size: 5pt !important;
            transition: all 0.2s ease !important;
            cursor: pointer !important;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
            line-height: 1 !important;
        }

        .translate-card {
            background-color: #ffffff !important;
            box-shadow: 0 2px 5px rgba(15, 23, 36, 0.04), 0 1px 2px rgba(15, 23, 36, 0.02) !important;
            border-radius: 14px !important;
            padding: 1px 3px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 1px !important;
            transition: all 0.25s ease !important;
            border: 1px solid rgba(16, 185, 129, 0.1) !important;
            backdrop-filter: blur(1px) !important;
        }

        .goog-te-gadget-simple:hover {
            background-color: #e9eef3 !important;
            border-color: #10B981 !important;
            box-shadow: 0 3px 6px rgba(16, 185, 129, 0.08) !important;
            transform: translateY(-0.5px);
        }

        .goog-te-gadget-simple:active {
            transform: scale(0.98) translateY(0.5px) !important;
            box-shadow: 0 1px 2px rgba(16, 185, 129, 0.1) !important;
        }

        .goog-te-menu-value {
            color: #0f1724 !important;
            font-size: 0.5rem !important;
            font-weight: 500 !important;
            letter-spacing: 0.01em !important;
            display: flex !important;
            align-items: center !important;
            gap: 0.5px !important;
        }

        .goog-te-gadget-icon {
            display: none !important;
        }

        .goog-te-menu-value img {
            display: none !important;
        }

        .goog-te-menu-value span:first-child {
            color: #0f1724 !important;
            font-weight: 500 !important;
            font-size: 0.45rem !important;
        }

        .goog-te-menu-value span:last-child {
            color: #10B981 !important;
            font-size: 0.55rem !important;
            margin-left: 0.5px !important;
            font-weight: 600 !important;
            opacity: 0.9;
            transition: transform 0.2s;
        }

        .goog-te-gadget-simple:hover .goog-te-menu-value span:last-child {
            transform: translateY(0.5px);
            color: #059669 !important;
        }

        .VIpgJd-ZVi9od-ORHb,
        .VIpgJd-ZVi9od-ORHb-OEVmcd,
        .goog-te-banner-frame.skiptranslate {
            display: none !important;
        }

        .goog-tooltip,
        .goog-tooltip:hover,
        .goog-text-highlight {
            display: none !important;
            background-color: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        iframe.goog-te-banner-frame,
        .goog-te-banner-frame,
        [class*="VIpgJd-ZVi9od-ORHb"] {
            display: none !important;
        }

        body {
            top: 0 !important;
        }

        .translate-icon {
            width: 30px !important;
            height: 25px !important;
        }

        @media screen and (min-width: 2560px) and (max-width: 5000px) {
            .goog-te-gadget-simple {
                border-radius: 24px !important;
                padding: 2px 8px 2px 6px !important;
                font-size: 12pt !important;
                gap: 2px !important;
            }

            .translate-card {
                border-radius: 16px !important;
                padding: 2px 4px !important;
                gap: 2px !important;
            }

            .goog-te-menu-value {
                font-size: 0.6rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.55rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.65rem !important;
            }
        }

        @media screen and (min-width: 1501px) and (max-width: 2559px) {
            .goog-te-gadget-simple {
                border-radius: 22px !important;
                padding: 2px 7px 2px 5px !important;
                font-size: 12pt !important;
            }

            .translate-card {
                border-radius: 15px !important;
                padding: 2px 4px !important;
            }

            .goog-te-menu-value {
                font-size: 0.55rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.5rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.6rem !important;
            }
        }

        @media screen and (min-width: 1400px) and (max-width: 1500px) {
            .goog-te-gadget-simple {
                border-radius: 21px !important;
                padding: 2px 6px 2px 5px !important;
                font-size: 12pt !important;
            }

            .translate-card {
                border-radius: 14px !important;
                padding: 1.5px 3px !important;
            }

            .goog-te-menu-value {
                font-size: 0.5rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.45rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.55rem !important;
            }
        }

        @media screen and (min-width: 1200px) and (max-width: 1399px) {
            .goog-te-gadget-simple {
                border-radius: 20px !important;
                padding: 1.5px 5px 1.5px 4px !important;
                font-size: 12pt !important;
            }

            .translate-card {
                border-radius: 13px !important;
                padding: 1px 3px !important;
            }

            .goog-te-menu-value {
                font-size: 0.45rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.4rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.5rem !important;
            }
        }

        @media screen and (min-width: 1001px) and (max-width: 1199px) {
            .goog-te-gadget-simple {
                border-radius: 19px !important;
                padding: 1px 5px 1px 4px !important;
                font-size: 10pt !important;
            }

            .translate-card {
                border-radius: 12px !important;
                padding: 1px 3px !important;
            }

            .goog-te-menu-value {
                font-size: 0.4rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.35rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.45rem !important;
            }
        }

        @media screen and (max-width: 1000px) {
            .goog-te-gadget-simple {
                border-radius: 18px !important;
                padding: 1px 4px 1px 3px !important;
                font-size: 10pt !important;
            }

            .translate-card {
                border-radius: 11px !important;
                padding: 1px 2px !important;
            }

            .goog-te-menu-value {
                font-size: 0.35rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.3rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.4rem !important;
            }
        }

        @media screen and (min-width: 992px) and (max-width: 999px) {
            .goog-te-gadget-simple {
                border-radius: 17px !important;
                padding: 1px 4px 1px 3px !important;
                font-size: 10pt !important;
                gap: 1px !important;
            }

            .translate-card {
                border-radius: 10px !important;
                padding: 1px 2px !important;
            }

            .goog-te-menu-value {
                font-size: 0.3rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.25rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.35rem !important;
            }
        }

        @media screen and (min-width: 768px) and (max-width: 991px) {
            .goog-te-gadget-simple {
                border-radius: 16px !important;
                padding: 1px 4px 1px 3px !important;
                font-size: 8pt !important;
            }

            .translate-card {
                border-radius: 9px !important;
                padding: 1px 2px !important;
            }

            .goog-te-menu-value {
                font-size: 0.3rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.25rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.35rem !important;
            }
        }

        @media screen and (min-width: 576px) and (max-width: 767px) {
            .goog-te-gadget-simple {
                border-radius: 15px !important;
                padding: 1px 3px 1px 2px !important;
                font-size: 7pt !important;
                gap: 0.5px !important;
            }

            .translate-card {
                border-radius: 8px !important;
                padding: 0.5px 2px !important;
            }

            .goog-te-menu-value {
                font-size: 0.3rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.25rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.35rem !important;
            }
        }

        @media screen and (min-width: 481px) and (max-width: 575px) {
            .goog-te-gadget-simple {
                border-radius: 14px !important;
                padding: 1px 3px 1px 2px !important;
                font-size: 4.5pt !important;
                gap: 0.5px !important;
            }

            .translate-card {
                border-radius: 7px !important;
                padding: 0.5px 2px !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.2rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.3rem !important;
            }
        }

        @media screen and (min-width: 380px) and (max-width: 480px) {
            .goog-te-gadget-simple {
                border-radius: 12px !important;
                padding: 1px 2px 1px 1px !important;
                font-size: 4.5pt !important;
                gap: 0.5px !important;
            }

            .translate-card {
                border-radius: 6px !important;
                padding: 0.5px 1px !important;
            }

            .goog-te-menu-value {
                font-size: 0.25rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.2rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.3rem !important;
            }
        }

        @media screen and (max-width: 379px) {
            .goog-te-gadget-simple {
                border-radius: 8px !important;
                padding: 0.5px 1px 0.5px 0.5px !important;
                font-size: 4.5pt !important;
                font-weight: 700 !important;
                gap: 0.2px !important;
            }

            .translate-card {
                border-radius: 4px !important;
                padding: 0.2px 0.5px !important;
            }

            .goog-te-menu-value {
                font-size: 0.2rem !important;
            }

            .goog-te-menu-value span:first-child {
                font-size: 0.15rem !important;
            }

            .goog-te-menu-value span:last-child {
                font-size: 0.25rem !important;
            }

            .translate-icon {
                width: 20px !important;
                height: 15px !important;
                font-size: 0.6rem !important;
            }
        }

        .swal-popup-compact {
            border-radius: 20px !important;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif !important;
            padding: 1rem !important;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.2) !important;
            backdrop-filter: blur(2px);
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
        }

        @media (min-width: 2560px) {
            .swal2-popup.swal-popup-compact {
                font-size: 1.8rem !important;
                padding: 2rem 2.5rem !important;
                min-width: 650px !important;
                border-radius: 48px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 2.2rem !important;
                margin-bottom: 1.2rem !important;
            }

            .swal2-popup.swal-popup-compact .swal2-html-container,
            .swal2-popup.swal-popup-compact .swal2-toast .swal2-title {
                font-size: 1.6rem !important;
            }

            .swal2-popup.swal-popup-compact .swal2-timer-progress-bar {
                height: 6px !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 580px !important;
                padding: 1.2rem 2rem !important;
                border-radius: 60px !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast .swal2-title {
                font-size: 1.7rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast .swal2-html-container {
                font-size: 1.4rem !important;
            }
        }

        @media (min-width: 1501px) and (max-width: 2559px) {
            .swal2-popup.swal-popup-compact {
                font-size: 1.4rem !important;
                padding: 1.6rem 2rem !important;
                border-radius: 36px !important;
                min-width: 480px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 1.8rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 480px !important;
                padding: 1rem 1.6rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast .swal2-title {
                font-size: 1.4rem !important;
            }
        }

        @media (min-width: 1400px) and (max-width: 1500px) {
            .swal2-popup.swal-popup-compact {
                font-size: 1.2rem !important;
                padding: 1.2rem 1.8rem !important;
                border-radius: 28px !important;
                min-width: 420px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 1.6rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 440px !important;
                padding: 0.9rem 1.5rem !important;
            }
        }

        @media (min-width: 1200px) and (max-width: 1399px) {
            .swal2-popup.swal-popup-compact {
                font-size: 1.1rem !important;
                padding: 1rem 1.5rem !important;
                border-radius: 26px !important;
                min-width: 380px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 1.5rem !important;
            }
        }

        @media (min-width: 1001px) and (max-width: 1199px) {
            .swal2-popup.swal-popup-compact {
                font-size: 1rem !important;
                padding: 0.9rem 1.4rem !important;
                border-radius: 24px !important;
                min-width: 340px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 1.4rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 380px !important;
            }
        }

        @media (width: 1000px) {
            .swal2-popup.swal-popup-compact {
                font-size: 1rem !important;
                padding: 0.9rem 1.4rem !important;
                border-radius: 24px !important;
            }
        }

        @media (min-width: 992px) and (max-width: 999px) {
            .swal2-popup.swal-popup-compact {
                font-size: 0.95rem !important;
                padding: 0.85rem 1.3rem !important;
                border-radius: 24px !important;
                min-width: 320px !important;
            }
        }

        @media (min-width: 768px) and (max-width: 991px) {
            .swal2-popup.swal-popup-compact {
                font-size: 0.9rem !important;
                padding: 0.8rem 1.2rem !important;
                border-radius: 22px !important;
                min-width: 300px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 1.25rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 340px !important;
                bottom: 20px !important;
                right: 20px !important;
            }
        }

        @media (min-width: 576px) and (max-width: 767px) {
            .swal2-popup.swal-popup-compact {
                font-size: 0.85rem !important;
                padding: 0.7rem 1rem !important;
                border-radius: 20px !important;
                min-width: 280px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 1.1rem !important;
                margin-bottom: 0.4rem !important;
            }

            .swal2-popup.swal-popup-compact .swal2-html-container {
                font-size: 0.85rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 300px !important;
                padding: 0.7rem 1rem !important;
                border-radius: 36px !important;
            }
        }

        @media (min-width: 481px) and (max-width: 575px) {
            .swal2-popup.swal-popup-compact {
                font-size: 0.8rem !important;
                padding: 0.6rem 0.9rem !important;
                border-radius: 18px !important;
                min-width: 260px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 1rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 270px !important;
                bottom: 16px !important;
                right: 16px !important;
            }
        }

        @media (min-width: 380px) and (max-width: 480px) {
            .swal2-popup.swal-popup-compact {
                font-size: 0.75rem !important;
                padding: 0.5rem 0.8rem !important;
                border-radius: 16px !important;
                min-width: 240px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 0.9rem !important;
                margin-bottom: 0.2rem !important;
            }

            .swal2-popup.swal-popup-compact .swal2-html-container {
                font-size: 0.75rem !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 250px !important;
                padding: 0.5rem 0.8rem !important;
                border-radius: 30px !important;
            }
        }

        @media (max-width: 379px) {
            .swal2-popup.swal-popup-compact {
                font-size: 0.7rem !important;
                padding: 0.4rem 0.7rem !important;
                border-radius: 14px !important;
                min-width: 210px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-title {
                font-size: 0.85rem !important;
                line-height: 1.2 !important;
            }

            .swal2-popup.swal-popup-compact .swal2-html-container {
                font-size: 0.7rem !important;
                line-height: 1.3 !important;
            }

            .swal2-popup.swal-popup-compact.swal2-toast {
                max-width: 220px !important;
                bottom: 12px !important;
                right: 12px !important;
                padding: 0.4rem 0.7rem !important;
                border-radius: 28px !important;
            }

            .swal2-popup.swal-popup-compact .swal2-icon {
                width: 2.2rem !important;
                height: 2.2rem !important;
                margin: 0.2rem auto 0.4rem !important;
            }
        }

        .swal2-toast {
            position: fixed !important;
            bottom: 24px !important;
            right: 24px !important;
            left: auto !important;
            top: auto !important;
        }

        @media (max-width: 767px) {
            .swal2-toast {
                bottom: 16px !important;
                right: 16px !important;
            }
        }

        @media (max-width: 480px) {
            .swal2-toast {
                bottom: 12px !important;
                right: 12px !important;
            }
        }

        @media (min-width: 2560px) {
            .swal2-toast {
                bottom: 48px !important;
                right: 48px !important;
            }
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @include('includes.loader')
    <div class="dashboard-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('assets/images/Logo Green Market.png') }}" class="logo" alt="Greenmarket">
                <h3>Lead Farmer Panel</h3>
                <button id="sidebar-close" class="sidebar-toggle">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>

            <nav class="sidebar-nav">
                <ul class="main-menu">
                    <li>
                        <a href="{{ route('lf.dashboard') }}"
                            class="menu-link {{ request()->routeIs('lf.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-gauge-high"></i><span>Dashboard</span>
                        </a>
                    </li>

                    <li class="menu-heading">FARMER MANAGEMENT</li>

                    <li>
                        <a href="{{ route('lf.registerFarmer') }}"
                            class="menu-link {{ request()->routeIs('lf.registerFarmer') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-plus"></i><span>Register Farmer</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('lf.manageFarmers') }}"
                            class="menu-link {{ request()->routeIs('lf.manageFarmers') ? 'active' : '' }}">
                            <i class="fa-solid fa-users"></i><span>Manage Farmers</span>
                        </a>
                    </li>

                    <li class="menu-heading">PRODUCT MANAGEMENT</li>

                    <li>
                        <a href="{{ route('lf.addProduct') }}"
                            class="menu-link {{ request()->routeIs('lf.addProduct') ? 'active' : '' }}">
                            <i class="fa-solid fa-plus-circle"></i><span>Add New Product</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('lf.manageProducts') }}"
                            class="menu-link {{ request()->routeIs('lf.manageProducts') ? 'active' : '' }}">
                            <i class="fa-solid fa-box-open"></i><span>Manage Products</span>
                        </a>
                    </li>

                    <li class="menu-heading">INVENTORY MANAGEMENT</li>

                    <li>
                        <a href="{{ route('lf.inventory.dashboard') }}"
                            class="menu-link {{ request()->routeIs('lf.inventory.dashboard') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-pie"></i><span>Inventory Dashboard</span>
                        </a>
                    </li>

                    <li class="menu-heading">ORDERS & SALES</li>

                    <li>
                        <a href="{{ route('lf.orders') }}"
                            class="menu-link {{ request()->routeIs('lf.orders') ? 'active' : '' }}">
                            <i class="fa-solid fa-shopping-cart"></i><span>View Orders</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('lf.reports.sales') }}"
                            class="menu-link {{ request()->routeIs('lf.reports.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-chart-line"></i><span>Sales Reports</span>
                        </a>
                    </li>

                    <li class="menu-heading">ACCOUNT</li>

                    <li>
                        <a href="{{ route('lf.profile') }}"
                            class="menu-link {{ request()->routeIs('lf.profile') ? 'active' : '' }}">
                            <i class="fa-solid fa-user"></i><span>My Profile</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('lf.profile.photo') }}"
                            class="menu-link {{ request()->routeIs('lf.profile.photo') ? 'active' : '' }}">
                            <i class="fa-solid fa-camera"></i><span>Profile Photo</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('lf.notifications') }}"
                            class="menu-link {{ request()->routeIs('lf.notifications') ? 'active' : '' }}">
                            <i class="fa-solid fa-bell"></i><span>Notifications</span>
                            @if(isset($sharedCounts['unreadNotifications']) && $sharedCounts['unreadNotifications'] > 0)
                                <span class="notif-dot"></span>
                            @endif
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="sidebar-footer">
                <a href="{{ route('logout.confirmation') }}" id="nav-logout-link" class="logout-link">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>

        <main class="main-content">
            <header class="navbar">
                <div class="left-header">
                    <button id="mobile-menu-btn" class="mobile-menu-btn">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h1 class="page-title">
                        <i class="fa-solid fa-seedling"></i>
                        @yield('page-title', 'Lead Farmer Dashboard')
                    </h1>
                </div>

                <div class="header-right-group">
                    <div class="translate-wrapper" style="display:flex;justify-content:center;margin:10px 0;">
                        <div class="translate-card"
                            style="background-color:#ffffff;box-shadow:0 7px 15px rgba(15,23,36,0.08),0 1px 3px rgba(15,23,36,0.04);border-radius:28px;padding:4px 10px;display:inline-flex;align-items:center;gap:8px;transition:all 0.25s ease;border:1px solid rgba(16,185,129,0.15);backdrop-filter:blur(2px);">
                            <div class="translate-icon"
                                style="background:linear-gradient(145deg,#10B981,#059669);width:28px;height:28px;border-radius:14px;display:flex;align-items:center;justify-content:center;color:white;font-size:1rem;box-shadow:0 6px 12px rgba(5,150,105,0.25);transition:0.2s ease;">
                                <i class="fas fa-language"></i>
                            </div>
                            <div id="google_translate_element"></div>
                        </div>
                    </div>
                    <div class="notif-wrapper">
                        <div class="notif-btn" id="notifBtn">
                            <i class="fa-regular fa-bell"></i>
                            @if(isset($sharedCounts['unreadNotifications']) && $sharedCounts['unreadNotifications'] > 0)
                                <span class="notif-dot"></span>
                            @endif
                        </div>

                        <div class="notif-dropdown" id="notifDropdown">
                            <div class="notif-header">
                                <span>Notifications</span>
                                <button class="mark-all-read" id="markAllRead">Mark all read</button>
                            </div>

                            <div class="notif-list">
                                @if(isset($recentNotifications) && count($recentNotifications) > 0)
                                    @foreach($recentNotifications as $notification)
                                        <div class="notif-item {{ $notification->is_read ? '' : 'unread' }}"
                                            data-id="{{ $notification->id }}">
                                            <div class="notif-icon">
                                                @if($notification->notification_type == 'order_payment' || $notification->notification_type == 'payment_received' || $notification->notification_type == 'payment_confirmation')
                                                    <i class="fa-solid fa-money-bill-wave text-success"></i>
                                                @elseif($notification->notification_type == 'admin_alert')
                                                    <i class="fa-solid fa-triangle-exclamation text-warning"></i>
                                                @else
                                                    <i class="fa-solid fa-info-circle text-info"></i>
                                                @endif
                                            </div>
                                            <div class="notif-content">
                                                <div class="notif-title">{{ $notification->title }}</div>
                                                <div class="notif-msg">{{ Str::limit($notification->message, 80) }}</div>
                                                <small
                                                    class="notif-time">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</small>
                                            </div>
                                            @if(!$notification->is_read)
                                                <div class="notif-actions">
                                                    <button class="mark-read-btn" data-id="{{ $notification->id }}"
                                                        title="Mark as read">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @else
                                    <div class="notif-empty">No notifications</div>
                                @endif
                            </div>

                            <div class="notif-footer">
                                <a href="{{ route('lf.notifications') }}" id="viewAllNotifications">View all
                                    notifications</a>
                            </div>
                        </div>
                    </div>

                    <div class="header-user-meta">
                        <span class="role">Lead Farmer</span>
                        <span class="username">
                            {{ Auth::user()->leadFarmer->name ?? Auth::user()->username ?? 'Lead Farmer' }}
                        </span>
                    </div>

                    <a href="{{ route('lf.profile') }}" class="profile-photo-link" id="headerProfilePhotoLink">
                        <img src="{{ asset('uploads/profile_pictures/' . (Auth::user()->profile_photo ?? 'lead-farmer-icon.svg')) }}"
                            class="profile-photo" onerror="this.src='{{ asset('assets/icons/lead-farmer-icon.svg') }}'">
                    </a>

                    <a href="{{ route('logout.confirmation') }}" class="logout-icon" id="header-logout-link"
                        title="Logout">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </a>
                </div>
            </header>

            <section class="dashboard-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        {{ session('warning') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </section>
        </main>
    </div>

    <div class="overlay" id="overlay"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @yield('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebarClose = document.getElementById('sidebar-close');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const notifBtn = document.getElementById('notifBtn');
            const notifDropdown = document.getElementById('notifDropdown');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function () {
                    sidebar.classList.add('open');
                    overlay.classList.add('active');
                });
            }

            if (sidebarClose) {
                sidebarClose.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                });
            }

            if (notifBtn && notifDropdown) {
                notifBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    notifDropdown.classList.toggle('show');
                });

                document.addEventListener('click', function (e) {
                    if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
                        notifDropdown.classList.remove('show');
                    }
                });
            }


            const markAllReadBtn = document.getElementById('markAllRead');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function () {
                    fetch('{{ route("lf.notifications.mark-all-read") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelectorAll('.notif-item.unread').forEach(item => {
                                    item.classList.remove('unread');
                                });
                                document.querySelectorAll('.mark-read-btn').forEach(btn => btn.remove());
                                const notifDot = document.querySelector('.notif-dot');
                                if (notifDot) notifDot.remove();
                                Swal.fire({
                                    @if(file_exists(public_path('assets/icons/Gif/mark as read1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/mark as read1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                                    title: 'Success',
                                    text: 'All notifications marked as read',
                                    timer: 1500,
                                    showConfirmButton: false,
                                    background: '#ffffff'
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                @if(file_exists(public_path('assets/icons/Gif/mark as read fail1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/mark as read fail1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                                title: 'Error',
                                text: 'Failed to mark notifications as read',
                                confirmButtonColor: '#10B981',
                                background: '#ffffff'
                            });
                        });
                });
            }

            document.querySelectorAll('.mark-read-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const notifId = this.dataset.id;
                    const notifItem = this.closest('.notif-item');

                    fetch(`/lead-farmer/notifications/${notifId}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                notifItem.classList.remove('unread');
                                this.remove();
                                Swal.fire({
                                    @if(file_exists(public_path('assets/icons/Gif/mark as read1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/mark as read1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                                    title: 'Marked as read',
                                    timer: 1000,
                                    showConfirmButton: false,
                                    background: '#ffffff'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                });
            });

            @if(session('success'))
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/success5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                    title: 'Success!',
                    text: '{{ session("success") }}',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#ffffff',
                    backdrop: 'rgba(16, 185, 129, 0.1)',
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                    title: 'Error!',
                    text: '{{ session("error") }}',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#ffffff',
                    backdrop: 'rgba(239, 68, 68, 0.1)',
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if(session('warning'))
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/alert1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/alert1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'warning' @endif,
                    title: 'Warning!',
                    text: '{{ session("warning") }}',
                    timer: 3000,
                    showConfirmButton: false,
                    background: '#ffffff',
                    backdrop: 'rgba(245, 158, 11, 0.1)',
                    toast: true,
                    position: 'top-end'
                });
            @endif

            @if($errors->any())
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/Validation Error1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Validation Error1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                    title: 'Validation Error',
                    html: '{!! implode("<br>", $errors->all()) !!}',
                    timer: 4000,
                    background: '#ffffff'
                });
            @endif

            setTimeout(() => {
                document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <script>
        window.welcomeShown = false;
        window.showWelcomeMessage = function () {
            if (!window.welcomeShown) {
                Swal.fire({
                    @if(file_exists(public_path('assets/icons/Gif/info1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/info1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'info' @endif,
                    title: 'translate ready',
                    text: 'choose language from the compact menu',
                    timer: 3000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'bottom-end',
                    background: '#ffffff',
                    iconColor: '#3b82f6',
                    customClass: { popup: 'swal-popup-compact' }
                });
                window.welcomeShown = true;
            }
        };
        window.googleTranslateElementInit = function () {
            new google.translate.TranslateElement({
                pageLanguage: 'en',
                includedLanguages: 'en,si,ta',
                layout: google.translate.TranslateElement.InlineLayout.SIMPLE
            }, 'google_translate_element');
            setTimeout(window.showWelcomeMessage, 600);
        };
        (function () {
            const script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = '//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
            script.async = true;
            document.head.appendChild(script);
            setTimeout(function () {
                const gadget = document.querySelector('.goog-te-gadget-simple');
                if (!gadget) return;
                let previousLangText = gadget.innerText || 'en';
                const observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mut) {
                        if (mut.type === 'characterData' || mut.type === 'childList') {
                            const currentText = gadget.innerText || '';
                            if (currentText !== previousLangText && currentText.trim() !== '') {
                                Swal.fire({
                                    @if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                                    title: 'language updated',
                                    text: 'page content will now appear in selected language.',
                                    timer: 3500,
                                    showConfirmButton: false,
                                    background: '#ffffff',
                                    iconColor: '#10B981',
                                    toast: true,
                                    position: 'bottom-end',
                                    showClass: { popup: 'animate__animated animate__fadeInUp' },
                                    hideClass: { popup: 'animate__animated animate__fadeOutDown' },
                                    customClass: { popup: 'swal-popup-compact' }
                                });
                                previousLangText = currentText;
                            }
                        }
                    });
                });
                if (gadget) {
                    observer.observe(gadget, { childList: true, subtree: true, characterData: true });
                }
            }, 800);
            const observerRetry = new MutationObserver(function (mutations, obs) {
                if (document.querySelector('.goog-te-gadget-simple')) {
                    obs.disconnect();
                    setTimeout(function () {
                        const gadget = document.querySelector('.goog-te-gadget-simple');
                        if (!gadget) return;
                        let previousLangText = gadget.innerText || 'en';
                        const observer = new MutationObserver(function (mutations) {
                            mutations.forEach(function (mut) {
                                if (mut.type === 'characterData' || mut.type === 'childList') {
                                    const currentText = gadget.innerText || '';
                                    if (currentText !== previousLangText && currentText.trim() !== '') {
                                        Swal.fire({
                                            @if(file_exists(public_path('assets/icons/Gif/success4.gif'))) imageUrl: '{{ asset('assets/icons/Gif/success4.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'success' @endif,
                                            title: 'language updated',
                                            text: 'page content will now appear in selected language.',
                                            timer: 3500,
                                            showConfirmButton: false,
                                            background: '#ffffff',
                                            iconColor: '#10B981',
                                            toast: true,
                                            position: 'bottom-end',
                                            showClass: { popup: 'animate__animated animate__fadeInUp' },
                                            hideClass: { popup: 'animate__animated animate__fadeOutDown' },
                                            customClass: { popup: 'swal-popup-compact' }
                                        });
                                        previousLangText = currentText;
                                    }
                                }
                            });
                        });
                        observer.observe(gadget, { childList: true, subtree: true, characterData: true });
                    }, 150);
                }
            });
            observerRetry.observe(document.body, { childList: true, subtree: true });
            window.addEventListener('error', function (e) {
                if (e.target && (e.target.src || '').includes('translate.google')) {
                    e.preventDefault();
                    Swal.fire({
                        @if(file_exists(public_path('assets/icons/Gif/error2.gif'))) imageUrl: '{{ asset('assets/icons/Gif/error2.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'error' @endif,
                        title: 'translation error',
                        text: 'google translate failed to load. please refresh.',
                        confirmButtonColor: '#059669',
                        background: '#ffffff',
                        iconColor: '#f59e0b'
                    });
                }
            }, true);
        })();
        (function () {
            if (document.body) {
                document.body.style.marginTop = '0px';
                document.body.style.position = 'static';
            }
            const bodyObserver = new MutationObserver(function () {
                if (document.body.style.marginTop !== '0px') {
                    document.body.style.marginTop = '0px';
                }
                if (document.body.style.position !== 'static') {
                    document.body.style.position = 'static';
                }
            });
            bodyObserver.observe(document.body, { attributes: true, attributeFilter: ['style'] });
        })();
    </script>
</body>

</html>