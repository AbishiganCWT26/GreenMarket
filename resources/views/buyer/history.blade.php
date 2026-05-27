@extends('buyer.layouts.buyer_master')

@section('title', 'Order History')
@section('page-title', 'Order History')

@section('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
:root {
    --primary-green: #10B981;
    --dark-green: #059669;
    --body-bg: #f6f8fa;
    --card-bg: #ffffff;
    --text-color: #0f1724;
    --muted: #6b7280;
    --accent-amber: #f59e0b;
    --blue: #3b82f6;
    --shadow-sm: 0 1px 3px rgba(15,23,36,0.04);
    --shadow-md: 0 7px 15px rgba(15,23,36,0.08);
    --shadow-lg: 0 15px 30px rgba(15,23,36,0.12);
}

body {
    background: var(--body-bg);
    min-height: 100vh;
}

.history-container {
    max-width: 2500px;
    margin: 0 auto;
    padding: 1.5rem 1rem;
}

.page-header {
    text-align: center;
    animation: slideDown 0.6s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.page-header h1 {
    color: var(--text-color);
    font-weight: 700;
    font-size: 1.8rem;
    margin-bottom: 0.25rem;
}

.page-header p {
    color: var(--muted);
    font-size: 0.95rem;
}

.stats-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: var(--card-bg);
    border-radius: 10px;
    padding: 1rem;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    text-align: center;
    border-left: 3px solid var(--primary-green);
    position: relative;
    overflow: hidden;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-green), var(--dark-green));
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
    color: white;
    font-size: 1rem;
}

.stat-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-color);
    margin-bottom: 0.25rem;
}

.stat-label {
    color: var(--muted);
    font-size: 0.8rem;
}

.filter-section {
    background: var(--card-bg);
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    box-shadow: var(--shadow-sm);
    animation: fadeIn 0.5s ease 0.2s both;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

.filter-header {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: var(--text-color);
    font-weight: 600;
    font-size: 0.9rem;
}

.filter-header i {
    color: var(--primary-green);
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
}

.filter-group {
    margin-bottom: 0;
}

.filter-label {
    display: block;
    margin-bottom: 0.375rem;
    color: var(--text-color);
    font-weight: 500;
    font-size: 0.85rem;
}

.filter-select,
.filter-input {
    width: 100%;
    padding: 0.625rem;
    border: 1.5px solid #e2e8f0;
    border-radius: 6px;
    background: white;
    color: var(--text-color);
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.filter-select:focus,
.filter-input:focus {
    outline: none;
    border-color: var(--primary-green);
    box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.1);
}

.apply-btn {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    border: none;
    padding: 0.625rem 1.25rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 1rem;
}

.apply-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.orders-wrapper {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
    background: var(--card-bg);
    border-radius: 10px;
    box-shadow: var(--shadow-sm);
    animation: bounceIn 0.6s ease;
}

@keyframes bounceIn {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.empty-icon {
    font-size: 2.5rem;
    color: var(--muted);
    margin-bottom: 1rem;
    opacity: 0.5;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.order-card {
    background: var(--card-bg);
    border-radius: 10px;
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    transition: all 0.3s ease;
    animation: slideUp 0.5s ease;
    border: 1px solid #e2e8f0;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(15px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.order-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
    border-color: var(--primary-green);
}

.order-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.order-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.order-number {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.95rem;
}

.order-date {
    color: var(--muted);
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 15px;
    font-weight: 600;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    white-space: nowrap;
}

/* 1. Processing order - Amber */
.status-badge.Processing.order {
    background: #FEF3C7;
    color: #92400E;
    border: 1px solid #FDE68A;
}

/* 2. confirmed - Blue */
.status-badge.confirmed {
    background: #DBEAFE;
    color: #1E40AF;
    border: 1px solid #BFDBFE;
}

/* 3. paid - Green */
.status-badge.paid {
    background: #D1FAE5;
    color: #065F46;
    border: 1px solid #A7F3D0;
}

/* 4. ready_for_pickup - Teal Gradient */
.status-badge.ready_for_pickup {
    background: linear-gradient(135deg, #10B981, #059669);
    color: white;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

/* 5. completed - Cyan Gradient */
.status-badge.completed {
    background: linear-gradient(135deg, #06B6D4, #0891B2);
    color: white;
    box-shadow: 0 2px 4px rgba(6, 182, 212, 0.2);
}

/* 6. cancelled - Red */
.status-badge.cancelled {
    background: #FEE2E2;
    color: #991B1B;
    border: 1px solid #FECACA;
}

/* 7. refunded - Purple */
.status-badge.refunded {
    background: #F3E8FF;
    color: #6B21A8;
    border: 1px solid #E9D5FF;
}

/* 8. Payment Pending - Orange */
.status-badge.Payment.Pending {
    background: #FFEDD5;
    color: #9A3412;
    border: 1px solid #FED7AA;
}

/* 9. awaiting_verification - Indigo */
.status-badge.awaiting_verification {
    background: #E0E7FF;
    color: #3730A3;
    border: 1px solid #C7D2FE;
}

.order-body {
    padding: 1rem;
}

.rejection-alert {
    background: #FEF2F2;
    border: 1px solid #FEE2E2;
    border-radius: 12px;
    padding: 1.25rem;
    margin-top: 1.25rem;
    display: flex;
    gap: 1rem;
    animation: slideInDown 0.4s ease-out;
}

.rejection-icon {
    background: #FEE2E2;
    color: #EF4444;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.25rem;
}

.rejection-content h4 {
    color: #991B1B;
    font-size: 1rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.rejection-reason {
    color: #B91C1C;
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
    padding: 0.5rem 0.75rem;
    background: white;
    border-radius: 8px;
    border-left: 4px solid #EF4444;
}

.rejection-note {
    color: #7F1D1D;
    font-size: 0.85rem;
    line-height: 1.5;
}

@keyframes slideInDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Re-upload Modal Styling */
.reupload-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    backdrop-filter: blur(5px);
    overflow-y: auto;
    padding: 1.5rem 1rem;
}

.reupload-content {
    background: white;
    border-radius: 16px;
    max-width: 500px;
    margin: 2rem auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.reupload-header {
    background: #f8fafc;
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.reupload-body {
    padding: 1.5rem;
}

.drop-zone {
    border: 2px dashed #cbd5e0;
    border-radius: 12px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.drop-zone:hover {
    border-color: var(--primary-green);
    background: #f0fdf4;
}

.order-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    color: var(--muted);
    font-size: 0.8rem;
    margin-bottom: 0.25rem;
}

.info-value {
    color: var(--text-color);
    font-weight: 500;
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.farmer-info {
    background: #f8fafc;
    border-radius: 6px;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border-left: 3px solid var(--accent-amber);
}

.farmer-name {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.farmer-detail {
    color: var(--muted);
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    margin-bottom: 0.125rem;
}

.order-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e2e8f0;
}

.action-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    border: none;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
}

.btn-primary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
}

.btn-secondary {
    background: var(--blue);
    color: white;
}

.btn-secondary:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

/* Premium Responsive Pagination Styling - Image Match */
.pagination-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
    margin-top: 3rem;
    padding: 2rem 0;
    animation: fadeIn 0.8s ease;
}

.pagination-info {
    font-size: 0.95rem;
    color: #64748b;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.75rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

.pagination-btn {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 12px;
    color: #1e293b;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}

.pagination-btn:hover:not(:disabled) {
    border-color: var(--primary-green);
    color: var(--primary-green);
    transform: translateY(-1px);
}

.pagination-btn.active {
    background: #10B981;
    color: white;
    border-color: #10B981;
    box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3);
}

.pagination-nav-btn {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    width: auto;
    padding: 0 0.75rem;
    color: #0f172a;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.pagination-nav-btn i {
    font-size: 1.2rem;
    font-weight: 900;
}

.pagination-btn:disabled {
    opacity: 0.3;
    cursor: not-allowed;
}

/* Breakpoint-specific Responsive Adjustments */
@media (max-width: 767px) {
    .pagination { gap: 0.4rem; }
    .pagination-btn { width: 38px; height: 38px; font-size: 0.9rem; border-radius: 10px; }
    .pagination-nav-btn span { display: none; }
    .pagination-nav-btn { padding: 0 0.4rem; }
    .pagination-info { font-size: 0.85rem; }
}

@media (max-width: 480px) {
    .pagination-btn { width: 34px; height: 34px; font-size: 0.85rem; border-radius: 8px; }
    .pagination { gap: 0.25rem; }
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    backdrop-filter: blur(3px);
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255,255,255,0.3);
    border-top: 3px solid var(--primary-green);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.invoice-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 10000;
    backdrop-filter: blur(5px);
    animation: fadeIn 0.3s ease;
}

.invoice-content {
    background: white;
    border-radius: 12px;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    margin: 2rem auto;
    box-shadow: var(--shadow-lg);
    animation: slideUp 0.4s ease;
}

.invoice-header {
    background: linear-gradient(135deg, var(--primary-green), var(--dark-green));
    color: white;
    padding: 1.5rem;
    border-radius: 12px 12px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.invoice-title {
    font-size: 1.2rem;
    font-weight: 700;
}

.close-invoice {
    background: rgba(255,255,255,0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.close-invoice:hover {
    background: rgba(255,255,255,0.3);
    transform: rotate(90deg);
}

.invoice-body {
    padding: 0.3rem;
}

.invoice-header {
    padding-bottom: 5px !important;
    margin-bottom: 5px !important;
}

.invoice-company {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 2rem;
}

.invoice-logo {
    width: 100px;
    height: auto;
}

.company-info h3 {
    color: var(--text-color);
    margin-bottom: 0.25rem;
    font-size: 1.3rem;
}

.company-info p {
    color: var(--muted);
    font-size: 0.9rem;
    margin-bottom: 0.125rem;
}

.invoice-details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
}

.detail-item h4 {
    color: var(--text-color);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.detail-item p {
    color: var(--muted);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
}

.invoice-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 2rem;
}

.invoice-table th {
    background: #f1f5f9;
    padding: 0.75rem;
    text-align: left;
    color: var(--text-color);
    font-weight: 600;
    font-size: 0.9rem;
    border-bottom: 2px solid #e2e8f0;
}

.invoice-table td {
    padding: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
    color: var(--text-color);
    font-size: 0.9rem;
}

.invoice-table tr:hover {
    background: #f8fafc;
}

.invoice-totals {
    background: #f8fafc;
    padding: 1.5rem;
    border-radius: 8px;
    max-width: 300px;
    margin-left: auto;
}

.total-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.total-row:last-child {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-color);
    border-top: 2px solid #e2e8f0;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
}

.invoice-footer {
    display: flex;
    gap: 1rem;
    justify-content: center;
    padding: 1.5rem;
    background: #f8fafc;
    border-radius: 0 0 12px 12px;
}

@media (max-width: 1200px) {
    .history-container {
        max-width: 1000px;
    }
}

@media (max-width: 992px) {
    .history-container {
        max-width: 800px;
    }

    .stats-cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .invoice-content {
        margin: 1rem;
        max-height: 85vh;
    }
}

@media (max-width: 768px) {
    .history-container {
        padding: 1rem;
    }

    .page-header h1 {
        font-size: 1.5rem;
    }

    .stats-cards {
        grid-template-columns: 1fr;
    }

    .filter-grid {
        grid-template-columns: 1fr;
    }

    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }

    .order-info-grid {
        grid-template-columns: 1fr;
    }

    .order-actions {
        flex-direction: column;
    }

    .action-btn {
        width: 100%;
        justify-content: center;
    }

    .invoice-details-grid {
        grid-template-columns: 1fr;
    }

    .invoice-totals {
        max-width: 100%;
    }

    .invoice-footer {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .history-container {
        padding: 0.75rem;
    }

    .page-header h1 {
        font-size: 1.3rem;
    }

    .stat-card {
        padding: 0.75rem;
    }

    .stat-number {
        font-size: 1.2rem;
    }

    .filter-section {
        padding: 0.75rem;
    }

    .order-body {
        padding: 0.75rem;
    }

    .invoice-body {
        padding: 1rem;
    }
}
/* SweetAlert Custom Styling */
.pickup-swal-popup {
    border-radius: 15px !important;
    padding: 1.5rem !important;
}

.pickup-swal-title {
    color: var(--dark-green) !important;
    font-weight: 700 !important;
    font-size: 1.5rem !important;
}

.pickup-info-container {
    text-align: left;
    margin-top: 1rem;
}

.pickup-info-group {
    background: #f8fafc;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    border-left: 4px solid var(--primary-green);
}

.pickup-info-label {
    display: block;
    font-size: 0.8rem;
    color: var(--muted);
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.25rem;
}

.pickup-info-value {
    display: block;
    font-size: 1rem;
    color: var(--text-color);
    font-weight: 500;
}

.pickup-map-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--blue);
    color: white !important;
    padding: 0.75rem 1.25rem;
    border-radius: 6px;
    text-decoration: none !important;
    font-weight: 600;
    transition: all 0.3s ease;
    margin-top: 0.5rem;
    width: 100%;
    justify-content: center;
}

.pickup-map-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Order Tracking Styles */
:root {
  --tracking-primary: #10B981;
  --tracking-bg: #f8fafc;
  --tracking-text: #0f172a;
  --icon-card-size: 100px;
  --grid-gap: 40px;
}

.order-tracking-popup {
  border-radius: 2rem !important;
  padding: 2rem !important;
  background: white !important;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1) !important;
  max-width: 900px !important;
  width: 95% !important;
}

.custom-tracking-title {
  font-family: 'Plus Jakarta Sans', sans-serif !important;
  font-weight: 800 !important;
  color: var(--tracking-text) !important;
  margin-bottom: 2rem !important;
}

.tracking-wrapper {
  position: relative;
  width: 100%;
  padding: 20px;
  margin: 0 auto;
}

.tracking-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--grid-gap);
  position: relative;
  z-index: 2;
}

.icon-wrapper {
  background: white;
  border-radius: 1.25rem;
  width: var(--icon-card-size);
  height: var(--icon-card-size);
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  border: 1px solid #e2e8f0;
  transition: transform 0.3s ease;
  position: relative;
  z-index: 3;
}

.icon-wrapper:hover {
  transform: translateY(-5px);
}

.icon-wrapper img {
  width: 70%;
  height: 70%;
  object-fit: contain;
}

.icon-wrapper.active {
  border-color: var(--tracking-primary);
  background: #ecfdf5;
}

.icon-wrapper img.active-step {
  filter: invert(48%) sepia(79%) saturate(2476%) hue-rotate(86deg) brightness(118%) contrast(119%) !important;
}

.svg-connector {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 1;
  pointer-events: none;
}

.path-line {
  fill: none;
  stroke: #e2e8f0;
  stroke-width: 4;
  stroke-linecap: round;
  stroke-linejoin: round;
}

.path-line.active {
  stroke: var(--tracking-primary);
}

/* Breakpoints for Desktop S-curve */
@media (min-width: 992px) {
  .tracking-grid {
    grid-template-rows: repeat(2, 1fr);
  }
}

/* Breakpoints for Mobile Vertical View */
@media (max-width: 991px) {
  :root {
    --icon-card-size: 150px;
    --grid-gap: 30px;
  }
  .tracking-grid {
    grid-template-columns: 1fr;
    gap: 50px;
  }
  .icon-wrapper {
    grid-row: auto !important;
    grid-column: auto !important;
    margin: 0 auto;
  }
  .order-tracking-popup {
    max-width: 450px !important;
  }
}

@media (max-width: 575px) {
  :root {
    --icon-card-size: 150px;
    --grid-gap: 20px;
  }
}

@media (max-width: 379px) {
  :root {
    --icon-card-size: 130px;
    --grid-gap: 15px;
  }
}

@media (min-width: 2560px) {
  :root { --icon-card-size: 180px; --grid-gap: 120px; }
  .custom-tracking-title { font-size: 4rem !important; }
}

@media (min-width: 1501px) and (max-width: 2559px) {
  .custom-tracking-title { font-size: 3.5rem !important; }
}

@media (min-width: 1400px) and (max-width: 1500px) {
  :root { --icon-card-size: 120px; --grid-gap: 60px; }
  .custom-tracking-title { font-size: 3rem !important; }
}

@media (min-width: 1200px) and (max-width: 1399px) {
  :root { --icon-card-size: 110px; --grid-gap: 50px; }
  .custom-tracking-title { font-size: 2.5rem !important; }
}

@media (min-width: 1001px) and (max-width: 1199px) {
  .custom-tracking-title { font-size: 2.25rem !important; }
}

@media (width: 1000px) {
  .custom-tracking-title { font-size: 2rem !important; }
}

@media (min-width: 992px) and (max-width: 999px) {
  .custom-tracking-title { font-size: 1.85rem !important; }
}

@media (min-width: 768px) and (max-width: 991px) {
  .custom-tracking-title { font-size: 1.75rem !important; }
}

@media (min-width: 576px) and (max-width: 767px) {
  .custom-tracking-title { font-size: 1.5rem !important; }
}

@media (min-width: 481px) and (max-width: 575px) {
  .custom-tracking-title { font-size: 1.35rem !important; }
}

@media (min-width: 380px) and (max-width: 480px) {
  .custom-tracking-title { font-size: 1.25rem !important; }
}

@media (max-width: 379px) {
  :root { --icon-card-size: 130px; --grid-gap: 15px; }
  .custom-tracking-title { font-size: 1.1rem !important; }
}

</style>
@endsection

@section('content')
<div class="history-container">
    <div class="page-header">
        <h1><i class="fas fa-shopping-bag me-2"></i>Order History</h1>
        <p>View and manage your past purchases</p>
    </div>

    @if(isset($orders) && count($orders) > 0)
    <div class="stats-cards">
        @php
            $totalOrders = count($orders);
            $totalAmount = $orders->sum('total_amount');
            $completedOrders = $orders->where('order_status', 'completed')->count();
            $pendingOrders = $orders->whereIn('order_status', ['Processing order', 'confirmed', 'paid', 'ready_for_pickup'])->count();
        @endphp

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-box"></i>
            </div>
            <div class="stat-number">{{ $totalOrders }}</div>
            <div class="stat-label">Total Orders</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-rupee-sign"></i>
            </div>
            <div class="stat-number">Rs. {{ number_format($totalAmount, 2) }}</div>
            <div class="stat-label">Total Spent</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-number">{{ $completedOrders }}</div>
            <div class="stat-label">Completed</div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-number">{{ $pendingOrders }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>

    <div class="filter-section">
        <div class="filter-header">
            <i class="fas fa-filter"></i>
            Filter Orders
        </div>
        <div class="filter-grid">
            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select class="filter-select" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="Processing order">Processing Order</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="paid">Paid</option>
                    <option value="ready_for_pickup">Ready for Pickup</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="refunded">Refunded</option>
                    <option value="Payment Pending">Payment Pending</option>
                    <option value="awaiting_verification">Awaiting Verification</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">From Date</label>
                <input type="text" class="filter-input" id="fromDate" placeholder="Select date">
            </div>

            <div class="filter-group">
                <label class="filter-label">To Date</label>
                <input type="text" class="filter-input" id="toDate" placeholder="Select date">
            </div>

            <div class="filter-group">
                <label class="filter-label">Sort By</label>
                <select class="filter-select" id="sortFilter">
                    <option value="newest">Newest First</option>
                    <option value="oldest">Oldest First</option>
                    <option value="amount_high">Amount (High to Low)</option>
                    <option value="amount_low">Amount (Low to High)</option>
                </select>
            </div>
        </div>

        <button class="apply-btn" id="applyFilters">
            <i class="fas fa-check"></i>
            Apply Filters
        </button>
    </div>

    <div class="orders-wrapper" id="ordersContainer">
        @foreach($orders as $order)
            <div class="order-card" data-status="{{ $order->order_status }}" data-date="{{ $order->created_at }}" data-amount="{{ $order->total_amount }}">
                <div class="order-header">
                    <div class="order-title">
                        <div class="order-number">
                            <i class="fas fa-hashtag me-1"></i>
                            {{ $order->order_number }}
                        </div>
                        <div class="order-date">
                            <i class="far fa-calendar me-1"></i>
                            {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                        </div>
                    </div>

                    <div class="status-badge {{ $order->order_status }}">
                        @switch($order->order_status)
                            @case('Processing order')
                                <i class="fas fa-clock"></i>
                                @break
                            @case('confirmed')
                                <i class="fas fa-check-circle"></i>
                                @break
                            @case('paid')
                                <i class="fas fa-credit-card"></i>
                                @break
                            @case('ready_for_pickup')
                                <i class="fas fa-truck-loading"></i>
                                @break
                            @case('completed')
                                <i class="fas fa-check-double"></i>
                                @break
                            @case('cancelled')
                                <i class="fas fa-times-circle"></i>
                                @break
                            @case('refunded')
                                <i class="fas fa-undo"></i>
                                @break
                            @case('Payment Pending')
                                <i class="fas fa-hourglass-half"></i>
                                @break
                            @case('awaiting_verification')
                                <i class="fas fa-user-check"></i>
                                @break
                        @endswitch
                        {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}
                    </div>
                </div>

                <div class="order-body">
                    <div class="order-info-grid">
                        <div class="info-item">
                            <span class="info-label">Total Amount</span>
                            <span class="info-value">
                                <i class="fas fa-rupee-sign"></i>
                                Rs. {{ number_format($order->total_amount, 2) }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Order Date</span>
                            <span class="info-value">
                                <i class="far fa-calendar-alt"></i>
                                {{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}
                            </span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Order Type</span>
                            <span class="info-value">
                                @if(($order->order_type ?? 'Pickup') == 'Delivery')
                                    <i class="fas fa-truck"></i>
                                @else
                                    <i class="fas fa-store"></i>
                                @endif
                                {{ $order->order_type ?? 'Pickup' }}
                            </span>
                        </div>

                        @if($order->paid_date)
                        <div class="info-item">
                            <span class="info-label">Paid On</span>
                            <span class="info-value">
                                <i class="fas fa-calendar-check"></i>
                                {{ \Carbon\Carbon::parse($order->paid_date)->format('M d, Y') }}
                            </span>
                        </div>
                        @endif
                    </div>

                    <div class="farmer-info">
                        <div class="farmer-name">
                            <i class="fas fa-user-tie"></i>
                            Seller: {{ $order->lead_farmer_name }}
                        </div>
                        <div class="farmer-detail">
                            <i class="fas fa-user"></i>
                            Seller Phone No: {{ $order->lead_farmer_contact }}
                        </div>
                    </div>

                    @if(($order->delivery_payment_status ?? '') === 'rejected')
                        <div class="rejection-alert">
                            <div class="rejection-icon">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="rejection-content">
                                <h4>Payment Slip Rejected</h4>
                                <div class="rejection-reason">
                                    <strong>Reason:</strong> {{ $order->delivery_rejection_reason }}
                                </div>
                                <p class="rejection-note">
                                    Please re-upload the payment slip or contact the seller if needed. Your order will continue after the payment is approved.
                                    <br>
                                    Also, the system could not find the old slip. If the old slip has any previous records, please merge the slips and resend them using iLovePDF or PDF24.

                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="order-actions">
                        @if(($order->delivery_payment_status ?? '') === 'rejected')
                            <button class="action-btn btn-primary re-upload-btn" 
                                    data-order-id="{{ $order->id }}"
                                    data-order-number="{{ $order->order_number }}"
                                    data-amount="{{ $order->total_amount }}">
                                <i class="fas fa-upload"></i>
                                Re-upload Payment Slip
                            </button>
                        @else
                            <button class="action-btn btn-primary view-invoice-btn" data-order-id="{{ $order->id }}">
                                <i class="fas fa-file-invoice"></i>
                                View Invoice
                            </button>

                            @if(($order->order_type ?? 'Pickup') == 'Delivery' && !in_array($order->order_status, ['ready_for_pickup', 'cancelled', 'refunded', 'Payment Pending']))
                                <button class="action-btn btn-secondary track-order-btn" 
                                        data-order-id="{{ $order->id }}" 
                                        data-order-status="{{ $order->order_status }}"
                                        data-order-number="{{ $order->order_number }}">
                                    <i class="fas fa-truck"></i>
                                    Track Order
                                </button>
                            @endif

                            @if($order->order_status == 'ready_for_pickup')
                                <button class="action-btn btn-secondary track-pickup-btn" data-order-id="{{ $order->id }}">
                                    <i class="fas fa-map-marker-alt"></i>
                                    Track Pickup
                                </button>
                            @endif

                            @if($order->order_status == 'completed')
                                <button class="action-btn btn-secondary feedback-btn" data-order-id="{{ $order->id }}">
                                    <i class="fas fa-star"></i>
                                    Rate Order
                                </button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="pagination-section" id="paginationContainer"></div>

    @else
    <div class="empty-state">
        <div class="empty-icon">
            <i class="fas fa-shopping-basket"></i>
        </div>
        <h3 style="color: var(--text-color); margin-bottom: 0.75rem; font-size: 1.2rem;">No Orders Found</h3>
        <p style="color: var(--muted); margin-bottom: 1rem; font-size: 0.9rem;">You haven't placed any orders yet.</p>
        <a href="{{ route('buyer.browseProducts') }}" class="action-btn btn-primary" style="width: auto; display: inline-flex;">
            <i class="fas fa-store me-2"></i>
            Start Shopping
        </a>
    </div>
    @endif
</div>

<div class="reupload-modal" id="reUploadModal">
    <div class="reupload-content">
        <div class="reupload-header">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-cloud-upload-alt me-2 text-primary"></i>
                Re-upload Payment Slip
            </h5>
            <button class="close-reupload" id="closeReUpload" style="background: none; border: none; font-size: 1.25rem; cursor: pointer;">
                <i class="fas fa-times text-muted"></i>
            </button>
        </div>
        <form id="reUploadForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="order_id" id="reupload_order_id">
            <div class="reupload-body">
                <div class="alert alert-primary mb-4" style="border-radius: 12px; background: #eff6ff; border: 1px solid #dbeafe; color: #1e40af; font-size: 0.9rem;">
                    <i class="fas fa-info-circle me-2"></i> 
                    Order: <strong><span id="reupload_order_number"></span></strong><br>
                    Amount: <strong>Rs. <span id="reupload_amount"></span></strong>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block;">Transaction ID / Reference</label>
                    <input type="text" name="transaction_id" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;" placeholder="New bank reference number" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label class="form-label" style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block;">Date</label>
                        <input type="date" name="transaction_date" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;" required max="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="form-label" style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block;">Time</label>
                        <input type="time" name="transaction_time" class="form-control" style="width: 100%; padding: 0.75rem; border: 1px solid #e2e8f0; border-radius: 8px;" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block;">Payment Slip Image</label>
                    <div class="drop-zone" id="reUploadDropZone">
                        <i class="fas fa-image" style="font-size: 2rem; color: #cbd5e0; margin-bottom: 1rem;"></i>
                        <p id="reUploadDropZoneText" style="margin-bottom: 0.25rem;">Click or Drag & Drop here</p>
                        <small class="text-muted">JPG, PNG, or PDF (Max 5MB)</small>
                        <input type="file" name="payment_slip" id="reUploadSlipInput" accept="image/*,.pdf" style="display: none" required>
                    </div>
                    <div id="reUploadPreview" class="mt-3 text-center" style="display: none">
                        <img id="reUploadPreviewImg" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; object-fit: contain; border-radius: 10px; border: 1px solid #e2e8f0;">
                    </div>
                </div>
            </div>
            <div style="background: #f8fafc; padding: 1.25rem 1.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 1rem;">
                <button type="button" class="action-btn btn-secondary" id="cancelReUpload" style="width: auto;">Cancel</button>
                <button type="submit" class="action-btn btn-primary" style="width: auto;" id="reSubmitBtn">
                    <i class="fas fa-paper-plane me-2"></i>Submit Payment
                </button>
            </div>
        </form>
    </div>
</div>

<div class="loading-overlay" id="loadingOverlay" style="display: none;">
    <div class="loading-spinner"></div>
</div>

<div class="invoice-modal" id="invoiceModal">
    <div class="invoice-content">
        <div class="invoice-header">
            <div class="invoice-title">
                <i class="fas fa-file-invoice me-2"></i>
                Order Invoice
            </div>
            <button class="close-invoice" id="closeInvoice">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="invoice-body" id="invoiceBody">
        </div>
        <div class="invoice-footer">
            <button class="action-btn btn-secondary" id="downloadInvoice">
                <i class="fas fa-download me-2"></i>
                Download PDF
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const reUploadModal = document.getElementById('reUploadModal');
    const reUploadForm = document.getElementById('reUploadForm');
    const closeReUpload = document.getElementById('closeReUpload');
    const cancelReUpload = document.getElementById('cancelReUpload');
    const reUploadDropZone = document.getElementById('reUploadDropZone');
    const reUploadSlipInput = document.getElementById('reUploadSlipInput');

    // Use Event Delegation for Open Buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.re-upload-btn');
        if (btn) {
            document.getElementById('reupload_order_id').value = btn.dataset.orderId;
            document.getElementById('reupload_order_number').innerText = btn.dataset.orderNumber;
            document.getElementById('reupload_amount').innerText = parseFloat(btn.dataset.amount).toLocaleString('en-US', {minimumFractionDigits: 2});
            
            reUploadForm.reset();
            document.getElementById('reUploadPreview').style.display = 'none';
            document.getElementById('reUploadDropZoneText').innerText = 'Click or Drag & Drop here';
            
            reUploadModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    });

    // Close Modal via Buttons and Backdrop (using delegation and direct listeners)
    document.addEventListener('click', function(e) {
        // Close via Buttons
        if (e.target.closest('#closeReUpload') || e.target.closest('#cancelReUpload')) {
            closeReUploadModal();
        }
        // Close via Backdrop
        if (e.target === reUploadModal) {
            closeReUploadModal();
        }
    });

    // Explicitly handle the function to ensure it's defined
    function closeReUploadModal() {
        if (reUploadModal) {
            reUploadModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }

    // File Upload Handlers
    if(reUploadDropZone) {
        reUploadDropZone.onclick = () => reUploadSlipInput.click();
    }

    if(reUploadSlipInput) {
        reUploadSlipInput.onchange = (e) => {
            const file = e.target.files[0];
            if (file) {
                document.getElementById('reUploadDropZoneText').innerText = file.name;
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        document.getElementById('reUploadPreviewImg').src = e.target.result;
                        document.getElementById('reUploadPreview').style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    document.getElementById('reUploadPreview').style.display = 'none';
                }
            }
        };
    }

    // Submit Form
    if(reUploadForm) {
        reUploadForm.onsubmit = async function(e) {
            e.preventDefault();
            const submitBtn = document.getElementById('reSubmitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';

            const formData = new FormData(this);
            try {
                const response = await fetch('{{ route("buyer.resubmitPaymentSlip") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    }
                });

                const data = await response.json();
                if (data.success) {
                    closeReUploadModal();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#10B981'
                    }).then(() => {
                        window.location.href = data.redirect_url;
                    });
                } else {
                    closeReUploadModal();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Resubmission failed',
                        confirmButtonColor: '#EF4444'
                    });
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Payment';
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'System Error',
                    text: 'An unexpected error occurred.',
                    confirmButtonColor: '#EF4444'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit Payment';
            }
        };
    }
    const loadingOverlay = document.getElementById('loadingOverlay');
    const applyFiltersBtn = document.getElementById('applyFilters');
    const ordersContainer = document.getElementById('ordersContainer');
    const statusFilter = document.getElementById('statusFilter');
    const sortFilter = document.getElementById('sortFilter');
    const fromDateInput = document.getElementById('fromDate');
    const toDateInput = document.getElementById('toDate');
    const invoiceModal = document.getElementById('invoiceModal');
    const invoiceBody = document.getElementById('invoiceBody');
    const closeInvoiceBtn = document.getElementById('closeInvoice');
    const downloadInvoiceBtn = document.getElementById('downloadInvoice');

    // Initialize date pickers
    if (fromDateInput) {
        flatpickr(fromDateInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            onChange: function(selectedDates, dateStr) {
                if (dateStr && toDateInput._flatpickr) {
                    toDateInput._flatpickr.set("minDate", dateStr);
                }
            }
        });
    }

    if (toDateInput) {
        flatpickr(toDateInput, {
            dateFormat: "Y-m-d",
            maxDate: "today",
            onChange: function(selectedDates, dateStr) {
                if (dateStr && fromDateInput._flatpickr) {
                    fromDateInput._flatpickr.set("maxDate", dateStr);
                }
            }
        });
    }

    function showToast(message, type = 'success') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        Toast.fire({
            icon: type,
            title: message,
            background: type === 'success' ? '#d1fae5' :
                        type === 'error' ? '#fee2e2' :
                        type === 'warning' ? '#fef3c7' : '#f1f5f9',
            color: type === 'success' ? '#065f46' :
                    type === 'error' ? '#7f1d1d' :
                    type === 'warning' ? '#92400e' : '#374151'
        });
    }

    let currentPage = 1;
    let filteredCards = [];
    const allOrderCards = Array.from(document.querySelectorAll('.order-card'));

    function getItemsPerPage() {
        const w = window.innerWidth;
        
        if (w >= 2560) return 8;
        if (w >= 1500) return 8;
        if (w >= 1200) return 8;
        if (w >= 992) return 8;
        if (w >= 768) return 6;
        return 5;
    }

    function renderPagination(totalItems) {
        const itemsPerPage = getItemsPerPage();
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const paginationContainer = document.getElementById('paginationContainer');
        
        if (!paginationContainer) return;
        if (totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        let html = `
            <div class="pagination-info">
                Showing ${currentPage === 1 ? 1 : (currentPage - 1) * itemsPerPage + 1} to ${Math.min(currentPage * itemsPerPage, totalItems)} of ${totalItems} orders
            </div>
            <div class="pagination">
                <button class="pagination-btn pagination-nav-btn" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i> <span>Previous</span>
                </button>
        `;

        // Smart pagination logic (show current, neighbors, and first/last)
        let startPage = Math.max(1, currentPage - 1);
        let endPage = Math.min(totalPages, currentPage + 1);

        if (startPage > 1) {
            html += `<button class="pagination-btn" onclick="changePage(1)">1</button>`;
            if (startPage > 2) html += `<button class="pagination-btn" disabled>...</button>`;
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) html += `<button class="pagination-btn" disabled>...</button>`;
            html += `<button class="pagination-btn" onclick="changePage(${totalPages})">${totalPages}</button>`;
        }

        html += `
                <button class="pagination-btn pagination-nav-btn" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                    <span>Next</span> <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        `;

        paginationContainer.innerHTML = html;
    }

    window.changePage = function(page) {
        currentPage = page;
        displayOrders();
        window.scrollTo({ top: ordersContainer.offsetTop - 100, behavior: 'smooth' });
    }

    function displayOrders() {
        if (!ordersContainer) return;
        
        const itemsPerPage = getItemsPerPage();
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageCards = filteredCards.slice(start, end);

        ordersContainer.innerHTML = '';

        if (filteredCards.length === 0) {
            ordersContainer.innerHTML = `
                <div class="empty-state" style="margin-top: 1rem;">
                    <div class="empty-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 style="color: var(--text-color); margin-bottom: 0.75rem; font-size: 1.2rem;">No Orders Found</h3>
                    <p style="color: var(--muted);">Try adjusting your filters</p>
                </div>
            `;
            const paginationContainer = document.getElementById('paginationContainer');
            if (paginationContainer) paginationContainer.innerHTML = '';
        } else {
            pageCards.forEach((card, index) => {
                card.style.animation = 'slideUp 0.4s ease';
                card.style.animationDelay = `${index * 0.05}s`;
                ordersContainer.appendChild(card);
            });
            renderPagination(filteredCards.length);
        }
    }

    function filterOrders() {
        const status = statusFilter ? statusFilter.value : 'all';
        const sort = sortFilter ? sortFilter.value : 'newest';
        const fromDate = fromDateInput ? fromDateInput.value : '';
        const toDate = toDateInput ? toDateInput.value : '';

        // Capture filtered results from the original set
        filteredCards = [...allOrderCards];

        if (status !== 'all') {
            filteredCards = filteredCards.filter(card => card.dataset.status === status);
        }

        if (fromDate) {
            const from = new Date(fromDate);
            filteredCards = filteredCards.filter(card => {
                const orderDate = new Date(card.dataset.date);
                return orderDate >= from;
            });
        }

        if (toDate) {
            const to = new Date(toDate);
            to.setHours(23, 59, 59, 999);
            filteredCards = filteredCards.filter(card => {
                const orderDate = new Date(card.dataset.date);
                return orderDate <= to;
            });
        }

        filteredCards.sort((a, b) => {
            switch (sort) {
                case 'oldest':
                    return new Date(a.dataset.date) - new Date(b.dataset.date);
                case 'amount_high':
                    return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
                case 'amount_low':
                    return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
                case 'newest':
                default:
                    return new Date(b.dataset.date) - new Date(a.dataset.date);
            }
        });

        currentPage = 1;
        displayOrders();
        
        if (status !== 'all' || fromDate || toDate || sort !== 'newest') {
            showToast('Filters applied', 'success');
        }
    }

    // Handle window resize for dynamic items per page
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            displayOrders();
        }, 250);
    });

    // Initial call to set up view
    filterOrders();

    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', filterOrders);
    }

    async function loadInvoice(orderId) {
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
        }

        try {
            // Get CSRF token from meta tag or input
            let csrfToken = '';
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                csrfToken = metaToken.getAttribute('content');
            } else {
                const inputToken = document.querySelector('input[name="_token"]');
                if (inputToken) {
                    csrfToken = inputToken.value;
                }
            }

            const response = await fetch(`/buyer/invoice/data/${orderId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Failed to load invoice');
            }

            const itemsHtml = data.items.map(item => `
                <tr>
                    <td>${item.product_name}</td>
                    <td>${item.quantity} ${item.unit_of_measure || ''}</td>
                    <td>Rs. ${item.unit_price}</td>
                    <td>Rs. ${item.total}</td>
                </tr>
            `).join('');

            // Create pickup information HTML
            let pickupInfoHtml = '';
            if (data.products_pickup_address && data.order_type !== 'Delivery') {
                pickupInfoHtml = `
                    <div style="background: #e8f5e9; border-radius: 6px; padding: 10px; margin: 10px 0; border-left: 3px solid #4CAF50;">
                        <div style="font-weight: 600; color: #2e7d32; margin-bottom: 5px; font-size: 0.95rem;">
                            <i class="fas fa-map-marker-alt" style="margin-right: 5px;"></i>
                            Pickup Information
                        </div>
                        <div style="color: #555; font-size: 0.85rem; line-height: 1.4;">
                            <div style="margin-bottom: 3px;"><strong>Address:</strong> ${data.products_pickup_address}</div>
                            ${data.products_pickup_map_link ? `<div><strong>Map Link:</strong> <a href="${data.products_pickup_map_link}" target="_blank" style="color: #3b82f6; text-decoration: none;">${data.products_pickup_map_link}</a></div>` : ''}
                        </div>
                    </div>
                `;
            }

            // Create payment details HTML
            let paymentDetailsHtml = '';
            if (data.paid_date || data.order_type === 'Delivery') {
                paymentDetailsHtml = `
                    <div class="detail-item">
                        <h4 style="color: #0f1724; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">Payment Details</h4>
                        <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Payment Method:</strong> ${data.payment_method || 'Credit Card'}</p>
                        ${data.paid_date ? `<p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Paid Date:</strong> ${data.paid_date}</p>` : ''}
                        ${data.transaction_id ? `<p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Transaction ID:</strong> ${data.transaction_id}</p>` : ''}
                        <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Status:</strong> ${data.payment_status}</p>
                    </div>
                `;
            }

            const invoiceHtml = `
                <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 11px; line-height: 1.4; color: #0f1724; background-color: #f1f5f9; margin: 0; padding: 5px;">
                    <div style="background: #e9f1dc; border-radius: 8px; padding: 10px; max-width: 1100px; margin: 0 auto;">
                        <!-- ================= HEADER ================= -->
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 10px;">
                            <tr>
                                <td style="width: 65%; vertical-align: middle;">
                                    <div style="display: table;">
                                        <div style="display: table-cell; width: 140px; height: 80px; background-color: #4d7c32; border-radius: 40px; text-align: center; vertical-align: middle;">
                                            <img src="/assets/images/Logo Green Market.png" alt="GreenMarket Logo" style="max-width: 135px; max-height: 75px;" onerror="this.src='https://via.placeholder.com/135x75?text=Logo'">
                                        </div>
                                        <span style="display: table-cell; padding-left: 18px; font-size: 28px; font-weight: 900; color: #3e7033; vertical-align: middle; font-family: 'Georgia', serif;">
                                            GreenMarket
                                        </span>
                                    </div>
                                </td>

                                <td style="width: 20%; vertical-align: middle; text-align: left; font-size: 13px;">
                                    <div><strong>Invoice No.</strong> : ${data.invoice_number}</div>
                                    <div><strong>Order No.</strong> : ${data.order_number}</div>
                                    <div><strong>Order Type</strong> : ${data.order_type}</div>
                                    <div><strong>Date</strong> : ${data.order_date}</div>
                                </td>
                            </tr>
                        </table>

                        <div style="height: 5px; background-color: #4d7c32; margin-bottom: 25px;"></div>

                        <!-- ================= INVOICE DETAILS ================= -->
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; background: #f8fafc; padding: 1.5rem; border-radius: 8px;">
                            <div class="detail-item">
                                <h4 style="color: #0f1724; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">Buyer Details</h4>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Name:</strong> ${data.buyer_name}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Contact:</strong> ${data.buyer_contact}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Address:</strong> ${data.buyer_address || 'Not provided'}</p>
                            </div>

                            <div class="detail-item">
                                <h4 style="color: #0f1724; font-size: 0.9rem; margin-bottom: 0.5rem; font-weight: 600;">Seller Details</h4>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Name:</strong> ${data.lead_farmer_name}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Contact:</strong> ${data.lead_farmer_contact}</p>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.25rem;"><strong>Grama Niladari Division:</strong> ${data.lead_farmer_GNdivision || 'Not provided'}</p>
                            </div>

                            ${paymentDetailsHtml}
                        </div>

                        <!-- ================= PICKUP INFORMATION ================= -->
			            ${pickupInfoHtml}

                        <!-- ================= ORDER ITEMS TABLE ================= -->
                        <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
                            <thead>
                                <tr>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Product</th>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Quantity</th>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Unit Price</th>
                                    <th style="background: #f1f5f9; padding: 0.75rem; text-align: left; color: #0f1724; font-weight: 600; font-size: 0.9rem; border-bottom: 2px solid #e2e8f0;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>

                        <!-- ================= TOTALS ================= -->
                        <div style="background: #f8fafc; padding: 1.5rem; border-radius: 8px; max-width: 300px; margin-left: auto;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span>Subtotal:</span>
                                <span>Rs. ${data.subtotal}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-weight: 700; font-size: 1.1rem; color: #0f1724; border-top: 2px solid #e2e8f0; padding-top: 0.75rem; margin-top: 0.75rem;">
                                <span>Grand Total:</span>
                                <span>Rs. ${data.total_amount}</span>
                            </div>
                        </div>

                        <!-- ================= FOOTER NOTES ================= -->
                        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; color: #6b7280; font-size: 0.85rem;">
                            <p><strong>Notes:</strong> ${data.order_type === 'Delivery' && data.order_status === 'Awaiting verification' ? 'Contact seller for Quick Payment verification.' : 'Thank you for your purchase! Please contact the seller for pickup arrangements.'}</p>
                        </div>
                    </div>
                </div>
            `;

            if (invoiceBody) {
                invoiceBody.innerHTML = invoiceHtml;
            }

            if (invoiceModal) {
                invoiceModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

        } catch (error) {
            console.error('Error loading invoice:', error);
            showToast('Failed to load invoice. Please try again.', 'error');
        } finally {
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }
    }

    // Attach event listeners to view invoice buttons
    document.querySelectorAll('.view-invoice-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            if (orderId) {
                loadInvoice(orderId);
            } else {
                showToast('Invalid order ID', 'error');
            }
        });
    });

    if (closeInvoiceBtn) {
        closeInvoiceBtn.addEventListener('click', function() {
            if (invoiceModal) {
                invoiceModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }

    if (invoiceModal) {
        invoiceModal.addEventListener('click', function(e) {
            if (e.target === invoiceModal) {
                invoiceModal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });
    }


    if (downloadInvoiceBtn) {
        downloadInvoiceBtn.addEventListener('click', async function() {
            if (!invoiceBody || !invoiceBody.innerHTML.trim()) {
                showToast('No invoice content to download', 'error');
                return;
            }

            showToast('Preparing PDF download...', 'info');

            try {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                });

                const canvas = await html2canvas(invoiceBody, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff'
                });

                const imgData = canvas.toDataURL('image/png');
                const imgWidth = 200;
                const pageHeight = 280;
                const imgHeight = canvas.height * imgWidth / canvas.width;

                doc.addImage(imgData, 'PNG', 5, 5, imgWidth, imgHeight);

                // Add page number if content is too long
                let heightLeft = imgHeight;
                let position = 0;

                while (heightLeft >= pageHeight) {
                    position = heightLeft - pageHeight;
                    doc.addPage();
                    doc.addImage(imgData, 'PNG', 10, -position, imgWidth, imgHeight);
                    heightLeft -= pageHeight;
                }

                const fileName = `invoice-${Date.now()}.pdf`;
                doc.save(fileName);
                showToast('PDF downloaded successfully!', 'success');
            } catch (error) {
                console.error('Error generating PDF:', error);
                showToast('Failed to generate PDF. Please try printing instead.', 'error');
            }
        });
    }

    // Feedback button functionality
    document.querySelectorAll('.feedback-btn').forEach(button => {
        button.addEventListener('click', function() {
            const orderId = this.getAttribute('data-order-id');
            if (!orderId) {
                showToast('Invalid order ID', 'error');
                return;
            }

            Swal.fire({
                title: 'Rate Your Order',
                html: `
                    <div style="text-align: center;">
                        <div style="display: flex; gap: 0.5rem; justify-content: center; margin: 1rem 0 2rem;" id="ratingStars">
                            ${[1,2,3,4,5].map(star => `
                                <i class="fas fa-star" data-value="${star}" style="font-size: 2rem; color: #e2e8f0; cursor: pointer; transition: color 0.2s;"></i>
                            `).join('')}
                        </div>
                        <input type="hidden" id="ratingValue" value="5">
                        <textarea id="feedbackComment" rows="3" style="width: 100%; padding: 0.75rem; border: 1.5px solid #e2e8f0; border-radius: 6px; font-size: 0.9rem;" placeholder="Share your experience (optional)"></textarea>
                    </div>
                `,
                @if(file_exists(public_path('assets/icons/Gif/Rate Order1.gif'))) imageUrl: '{{ asset('assets/icons/Gif/Rate Order1.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
                showCancelButton: true,
                confirmButtonText: 'Submit Rating',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#10B981',
                cancelButtonColor: '#6b7280',
                reverseButtons: true,
                preConfirm: () => {
                    const rating = document.getElementById('ratingValue').value;
                    const comment = document.getElementById('feedbackComment').value;

                    if (!rating) {
                        Swal.showValidationMessage('Please select a rating');
                        return false;
                    }

                    return { rating: parseInt(rating), comment: comment };
                },
                didOpen: () => {
                    const stars = document.querySelectorAll('#ratingStars .fa-star');
                    const ratingValue = document.getElementById('ratingValue');

                    stars.forEach(star => {
                        star.addEventListener('mouseenter', function() {
                            const value = parseInt(this.getAttribute('data-value'));
                            stars.forEach((s, index) => {
                                s.style.color = index < value ? '#f59e0b' : '#e2e8f0';
                            });
                        });

                        star.addEventListener('click', function() {
                            const value = parseInt(this.getAttribute('data-value'));
                            ratingValue.value = value;
                            stars.forEach((s, index) => {
                                s.style.color = index < value ? '#f59e0b' : '#e2e8f0';
                            });
                        });
                    });

                    // Set default rating to 5 stars
                    ratingValue.value = '5';
                    stars.forEach((star, index) => {
                        star.style.color = index < 5 ? '#f59e0b' : '#e2e8f0';
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit feedback via AJAX
                    fetch(`/buyer/order/${orderId}/feedback`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(result.value)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showToast(data.message || 'Thank you for your feedback!', 'success');
                        } else {
                            showToast(data.message || 'Failed to submit feedback', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error submitting feedback:', error);
                        showToast('Failed to submit feedback. Please try again.', 'error');
                    });
                }
            });
        });
    });

    // Track Pickup functionality
    document.querySelectorAll('.track-pickup-btn').forEach(button => {
        button.addEventListener('click', async function() {
            const orderId = this.getAttribute('data-order-id');
            
            if (loadingOverlay) loadingOverlay.style.display = 'flex';
            
            try {
                const response = await fetch(`/buyer/invoice/data/${orderId}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (!response.ok) throw new Error('Failed to fetch data');
                
                const data = await response.json();
                
                if (!data.success) throw new Error(data.message || 'Failed to load details');

                Swal.fire({
                    title: '<i class="fas fa-truck-loading me-2"></i>Pickup Details',
                    html: `
                        <div class="pickup-info-container">
                            <div class="pickup-info-group">
                                <span class="pickup-info-label">Seller Name</span>
                                <span class="pickup-info-value">${data.lead_farmer_name}</span>
                            </div>
                            <div class="pickup-info-group">
                                <span class="pickup-info-label">Seller Phone No</span>
                                <span class="pickup-info-value">
                                    <a href="tel:${data.lead_farmer_contact}" style="color: var(--primary-green); text-decoration: none;">
                                        <i class="fas fa-phone-alt me-1"></i> ${data.lead_farmer_contact}
                                    </a>
                                </span>
                            </div>
                            <div class="pickup-info-group">
                                <span class="pickup-info-label">Pickup Address</span>
                                <span class="pickup-info-value">${data.products_pickup_address}</span>
                            </div>
                            ${data.products_pickup_map_link ? `
                                <a href="${data.products_pickup_map_link}" target="_blank" class="pickup-map-btn">
                                    <i class="fas fa-map-marked-alt"></i> Open in Google Maps
                                </a>
                            ` : ''}
                        </div>
                    `,
                    @if(file_exists(public_path('assets/icons/Gif/info5.gif'))) imageUrl: '{{ asset('assets/icons/Gif/info5.gif') }}', imageWidth: 60, imageHeight: 60 @else icon: 'question' @endif,
                    showCloseButton: true,
                    showConfirmButton: false,
                    customClass: {
                        popup: 'pickup-swal-popup',
                        title: 'pickup-swal-title'
                    },
                    width: '450px'
                });

            } catch (error) {
                console.error('Error:', error);
                showToast(error.message || 'Error loading pickup information', 'error');
            } finally {
                if (loadingOverlay) loadingOverlay.style.display = 'none';
            }
        });
    });

    // Track Order functionality
    const trackingSteps = [
        { file: 'Order placed-1.svg', label: 'Order Placed' },
        { file: 'processing order-1.svg', label: 'Processing' },
        { file: 'Awaiting payment verification.svg', label: 'Awaiting Verification' },
        { file: 'order paid.svg', label: 'Paid' },
        { file: 'Dispatched.svg', label: 'Dispatched' },
        { file: 'Arrived to District.svg', label: 'District' },
        { file: 'Order is on the way.svg', label: 'On the Way' },
        { file: 'Order completed.svg', label: 'Completed' }
    ];

    window.getStepNumber = function(status) {
        switch(status) {
            case 'Processing order':
                return 2;
            case 'awaiting_verification':
                return 3;
            case 'confirmed':
            case 'paid':
                return 4;
            case 'Dispatched':
                return 5;
            case 'arrived_to_district':
                return 6;
            case 'Order_is_on_the_way':
                return 7;
            case 'completed':
                return 8;
            default:
                return 1;
        }
    }

    window.buildTrackingHTML = function(currentStatus) {
        const activeStep = getStepNumber(currentStatus);
        const steps = [
            { file: 'Order placed-1.svg', row: 1, col: 1 },
            { file: 'processing order-1.svg', row: 1, col: 2 },
            { file: 'Awaiting payment verification.svg', row: 1, col: 3 },
            { file: 'order paid.svg', row: 1, col: 4 },
            { file: 'Dispatched.svg', row: 2, col: 4 },
            { file: 'Arrived to District.svg', row: 2, col: 3 },
            { file: 'Order is on the way.svg', row: 2, col: 2 },
            { file: 'Order completed.svg', row: 2, col: 1 }
        ];

        let html = `
            <div class="tracking-wrapper">
                <svg class="svg-connector" id="trackingSvg" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <marker id="arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#e2e8f0" class="marker-fill" />
                        </marker>
                        <marker id="arrowhead-active" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                            <polygon points="0 0, 10 3.5, 0 7" fill="#10B981" />
                        </marker>
                    </defs>
                    <path id="journeyPathBase" class="path-line" marker-end="url(#arrowhead)" />
                    <path id="journeyPathActive" class="path-line active" marker-end="url(#arrowhead-active)" />
                </svg>
                <div class="tracking-grid">
        `;

        steps.forEach((step, index) => {
            const stepNum = index + 1;
            const isActive = stepNum <= activeStep;
            const gridStyle = `grid-row: ${step.row}; grid-column: ${step.col};`;
            
            html += `
                <div class="icon-wrapper ${isActive ? 'active' : ''}" style="${gridStyle}" data-step="${stepNum}">
                    <img src="/assets/icons/Orders icons/${step.file}" 
                         class="${isActive ? 'active-step' : ''}"
                         onerror="this.src='https://cdn-icons-png.flaticon.com/512/649/649730.png'; this.style.opacity='0.5'">
                </div>
            `;
        });

        html += `
                </div>
            </div>
            <script>
                setTimeout(drawTrackingPath, 100);
            <\/script>
        `;
        return html;
    }

    window.drawTrackingPath = function() {
        const wrapper = document.querySelector('.tracking-wrapper');
        const svg = document.getElementById('trackingSvg');
        const pathBase = document.getElementById('journeyPathBase');
        const pathActive = document.getElementById('journeyPathActive');
        if (!wrapper || !svg) return;

        const rect = wrapper.getBoundingClientRect();
        svg.setAttribute('viewBox', `0 0 ${rect.width} ${rect.height}`);

        const getCenter = (stepNum) => {
            const el = document.querySelector(`.icon-wrapper[data-step="${stepNum}"]`);
            if (!el) return null;
            const r = el.getBoundingClientRect();
            return {
                x: r.left - rect.left + r.width / 2,
                y: r.top - rect.top + r.height / 2
            };
        };

        const activeStep = getStepNumber(window.currentTrackingStatus);
        const stepsNum = [1, 2, 3, 4, 5, 6, 7, 8];
        const pts = stepsNum.map(getCenter).filter(p => p !== null);

        if (pts.length < 2) return;

        let d = `M ${pts[0].x} ${pts[0].y}`;
        let dActive = `M ${pts[0].x} ${pts[0].y}`;

        const isDesktop = window.innerWidth >= 992;

        for (let i = 0; i < pts.length - 1; i++) {
            const p1 = pts[i];
            const p2 = pts[i+1];
            let segment = '';

            if (isDesktop) {
                if (i === 3) { // 4 to 5: Curve from end of Row 1 to start of Row 2
                    const cp1x = p1.x + 80;
                    const cp1y = p1.y + 50;
                    const cp2x = p2.x + 80;
                    const cp2y = p2.y - 50;
                    segment = ` C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${p2.x} ${p2.y}`;
                } else {
                    segment = ` L ${p2.x} ${p2.y}`;
                }
            } else {
                // Mobile vertical line
                segment = ` L ${p2.x} ${p2.y}`;
            }

            d += segment;
            if (i < activeStep - 1) {
                dActive += segment;
            }
        }

        pathBase.setAttribute('d', d);
        pathActive.setAttribute('d', dActive);
    }

    document.querySelectorAll('.track-order-btn').forEach(button => {
        button.addEventListener('click', function() {
            const status = this.getAttribute('data-order-status');
            const orderNumber = this.getAttribute('data-order-number');
            window.currentTrackingStatus = status;

            Swal.fire({
                title: 'Delivery Tracking: ' + orderNumber,
                html: buildTrackingHTML(status),
                customClass: {
                    popup: 'order-tracking-popup',
                    title: 'custom-tracking-title'
                },
                showCloseButton: true,
                showConfirmButton: false,
                background: 'transparent',
                width: 'auto',
                allowOutsideClick: true,
                didOpen: () => {
                    // Re-draw path on window resize
                    window.addEventListener('resize', drawTrackingPath);
                    // Initial draw
                    setTimeout(drawTrackingPath, 150);
                },
                willClose: () => {
                    window.removeEventListener('resize', drawTrackingPath);
                }
            });
        });
    });

    // Add animation delay to order cards
    document.querySelectorAll('.order-card').forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
    });

    // Check for URL parameters to auto-apply filters
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    if (statusParam && statusFilter) {
        statusFilter.value = statusParam;
        setTimeout(() => filterOrders(), 100);
    }
});
</script>

