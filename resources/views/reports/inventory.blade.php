@extends('layouts.butcher')

@push('page-styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    :root {
        --primary-color: #8B0000;
        --primary-dark: #5a0000;
        --primary-light: #b84d4d;
        --secondary-color: #4A0404;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #17a2b8;
        --light-bg: #f8f9fa;
        --dark-text: #212529;
        --muted-text: #6c757d;
        --border-color: #dee2e6;
        --card-shadow: 0 4px 12px rgba(0,0,0,0.08);
        --transition-speed: 0.3s;
    }
    
    .card.stat-card {
        border-left: 4px solid var(--primary-color);
        transition: all var(--transition-speed) ease;
        height: 100%;
        border-radius: 12px;
        box-shadow: var(--card-shadow);
        background: white;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }
    
    .card.stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255,255,255,0.3), transparent);
        transform: translateX(-100%);
        transition: transform 0.6s ease;
    }
    
    .card.stat-card:hover::before {
        transform: translateX(100%);
    }
    
    .card.stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .card.stat-card.success {
        border-left-color: var(--success-color);
        background: linear-gradient(135deg, #f8fff9 0%, #ffffff 100%);
    }
    
    .card.stat-card.warning {
        border-left-color: var(--warning-color);
        background: linear-gradient(135deg, #fffdf8 0%, #ffffff 100%);
    }
    
    .card.stat-card.danger {
        border-left-color: var(--danger-color);
        background: linear-gradient(135deg, #fdf8f8 0%, #ffffff 100%);
    }
    
    .card.stat-card.info {
        border-left-color: var(--info-color);
        background: linear-gradient(135deg, #f8fcfd 0%, #ffffff 100%);
    }
    
    .card.stat-card.purple {
        border-left-color: #6f42c1;
        background: linear-gradient(135deg, #fbf9fd 0%, #ffffff 100%);
    }
    
    .card.stat-card.orange {
        border-left-color: #fd7e14;
        background: linear-gradient(135deg, #fdf9f7 0%, #ffffff 100%);
    }
    
    .expiring-badge {
        animation: pulse 2s infinite;
        position: relative;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(1.05); }
    }
    
    .activity-item {
        border-left: 3px solid var(--light-bg);
        padding-left: 1rem;
        margin-bottom: 0.5rem;
        padding: 0.5rem;
        background: white;
        border-radius: 0 5px 5px 0;
        transition: all 0.2s ease;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-size: 0.85rem;
    }
    
    .activity-item:hover {
        background-color: rgba(0,0,0,0.02);
        transform: translateX(3px);
    }
    
    .activity-item.created { 
        border-left-color: var(--success-color); 
        background-color: rgba(40, 167, 69, 0.05);
    }
    
    .activity-item.updated { 
        border-left-color: var(--info-color); 
        background-color: rgba(23, 162, 184, 0.05);
    }
    
    .activity-item.deleted { 
        border-left-color: var(--danger-color); 
        background-color: rgba(220, 53, 69, 0.05);
    }
    
    /* Activity timeline improvements */
    .activity-timeline {
        position: relative;
    }
    
    .activity-timeline::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 36px;
        width: 2px;
        background-color: var(--border-color);
        transform: translateX(-50%);
    }
    
    .timeline-badge {
        z-index: 2;
    }
    
    .real-time-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        background-color: var(--success-color);
        border-radius: 50%;
        animation: blink 1.5s infinite;
        box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
    }
    
    @keyframes blink {
        0%, 100% { opacity: 1; box-shadow: 0 0 5px rgba(40, 167, 69, 0.5); }
        50% { opacity: 0.5; box-shadow: 0 0 2px rgba(40, 167, 69, 0.3); }
    }
    
    /* New styles for improved UI */
    .dashboard-section {
        margin-bottom: 2rem;
        padding: 1.75rem;
        background: white;
        border-radius: 15px;
        box-shadow: var(--card-shadow);
        border: 1px solid var(--border-color);
        transition: all var(--transition-speed) ease;
    }
    
    .dashboard-section:hover {
        box-shadow: 0 6px 16px rgba(0,0,0,0.1);
    }
    
    .section-title {
        border-bottom: 3px solid var(--primary-color);
        padding-bottom: 0.85rem;
        margin-bottom: 1.75rem;
        color: var(--primary-dark);
        font-weight: 700;
        font-size: 1.6rem;
        display: flex;
        align-items: center;
        letter-spacing: -0.02em;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 1.5rem;
    }
    
    .filter-card {
        background: var(--light-bg);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(0,0,0,0.05);
    }
    
    .batch-header {
        background-color: #e9ecef;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        margin: 1.25rem 0 0.75rem;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    
    .product-table th {
        background-color: var(--light-bg);
        font-weight: 600;
        color: var(--dark-text);
    }
    
    .status-badge {
        font-size: 0.85rem;
        padding: 0.4em 0.6em;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .avatar-sm {
        width: 32px;
        height: 32px;
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Improved table styling */
    .table-hover tbody tr:hover {
        background-color: rgba(139, 0, 0, 0.03);
    }
    
    /* Card header improvements */
    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.08);
        font-weight: 600;
        padding: 1rem 1.25rem;
    }
    
    /* Button enhancements */
    .btn {
        border-radius: 6px;
        font-weight: 500;
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
        border: 1px solid transparent;
    }
    
    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .btn:active {
        transform: translateY(0);
    }
    
    .btn:focus {
        outline: 0;
        box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.25);
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(139, 0, 0, 0.25);
    }
    
    .btn-outline-secondary {
        border-color: var(--border-color);
        color: var(--dark-text);
    }
    
    .btn-outline-secondary:hover {
        background-color: var(--light-bg);
        border-color: #adb5bd;
    }
    
    /* Form control improvements */
    .form-control, .form-select {
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(139, 0, 0, 0.15);
        outline: 0;
    }
    
    .form-label {
        font-weight: 500;
        color: var(--dark-text);
        margin-bottom: 0.3rem;
    }
    
    /* Badge improvements */
    .badge {
        padding: 0.4em 0.7em;
        font-weight: 500;
        border-radius: 20px;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .dashboard-section {
            padding: 1rem;
        }
        
        .section-title {
            font-size: 1.3rem;
        }
        
        .chart-container {
            height: 250px;
        }
        
        .filter-card {
            padding: 1rem;
        }
        
        .batch-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .batch-header .float-end {
            margin-top: 0.5rem;
            align-self: flex-end;
        }
        
        /* Adjust dashboard cards for tablets */
        .col-xl-2.col-lg-3.col-md-4 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        /* Adjust table cells for better mobile viewing */
        .product-name-cell {
            min-width: 150px;
        }
        
        .updated-by-cell, .last-update-cell {
            min-width: 120px;
        }
    }
    
    @media (max-width: 576px) {
        .chart-container {
            height: 200px;
        }
        
        .section-title {
            font-size: 1.2rem;
        }
        
        .dashboard-section {
            padding: 0.75rem;
        }
        
        /* Stack dashboard cards on mobile */
        .col-xl-2.col-lg-3.col-md-4.col-sm-6 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        /* Simplify table for mobile */
        .product-table th, .product-table td {
            padding: 0.5rem;
        }
        
        .product-name-cell {
            min-width: 120px;
        }
        
        .updated-by-cell, .last-update-cell, .expiration-cell {
            min-width: 100px;
        }
        
        /* Adjust filter form for mobile */
        .filter-card .col-lg-2, .filter-card .col-lg-4 {
            flex: 0 0 100%;
            max-width: 100%;
            margin-bottom: 1rem;
        }
        
        .filter-card .d-flex {
            flex-direction: column;
        }
        
        .filter-card .btn {
            margin-bottom: 0.5rem;
            width: 100%;
        }
        
        /* Adjust batch header for mobile */
        .batch-header {
            padding: 0.5rem;
        }
        
        .batch-header .badge {
            margin-top: 0.5rem;
        }
    }
    
    /* Extra small devices (phones, less than 400px) */
    @media (max-width: 400px) {
        .section-title {
            font-size: 1.1rem;
        }
        
        .chart-container {
            height: 180px;
        }
        
        /* Further simplify table for very small screens */
        .product-table th, .product-table td {
            padding: 0.25rem;
            font-size: 0.85rem;
        }
        
        .product-name-cell {
            min-width: 100px;
        }
        
        .hide-on-xs {
            display: none;
        }
    }
    
    /* Empty state improvements */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--muted-text);
    }
    
    .empty-state h4 {
        margin-bottom: 0.5rem;
        color: var(--dark-text);
    }
    
    /* Progress bar improvements */
    .progress {
        height: 6px;
        border-radius: 3px;
        background-color: rgba(0,0,0,0.08);
        margin-top: 0.75rem;
        overflow: visible;
    }
    
    .progress-bar {
        border-radius: 3px;
        position: relative;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        right: 0;
        top: -2px;
        width: 6px;
        height: 10px;
        background: inherit;
        border-radius: 2px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    
    /* Stat value improvements */
    .stat-value {
        font-weight: 700;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover .stat-value {
        transform: scale(1.05);
    }
    
    .stat-label {
        font-size: 0.9rem;
        color: var(--muted-text);
        font-weight: 500;
    }
    
    /* Product table improvements */
    .product-table {
        font-size: 0.95rem;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .product-table th {
        font-weight: 600;
        color: var(--dark-text);
        background-color: var(--light-bg);
        border-top: none;
        border-bottom: 2px solid var(--border-color);
        padding: 0.5rem;
        vertical-align: middle;
        font-size: 0.85rem;
    }
    
    .product-table td {
        vertical-align: middle;
        border-color: var(--border-color);
        padding: 0.5rem;
        transition: background-color 0.2s ease;
        font-size: 0.85rem;
    }
    
    /* Adjust column widths for better printing */
    @media print {
        .product-table .product-name-cell {
            width: 20% !important;
            min-width: 150px !important;
        }
        
        .product-table .code-cell {
            width: 10% !important;
            min-width: 80px !important;
        }
        
        .product-table .price-cell {
            width: 8% !important;
        }
        
        .product-table .quantity-cell {
            width: 10% !important;
        }
        
        .product-table .expiration-cell {
            width: 12% !important;
        }
        
        .product-table .last-update-cell {
            width: 15% !important;
        }
    }
    
    .product-table tr:hover td {
        background-color: rgba(139, 0, 0, 0.03);
    }
    
    .product-table .batch-header {
        background-color: rgba(139, 0, 0, 0.05);
        font-weight: 600;
        border-left: 4px solid var(--primary-color);
        padding: 0.5rem; /* Match table cell padding */
        padding-left: calc(0.5rem + 4px); /* Account for border width */
        margin: 0;
        display: table-cell;
        width: 100%;
        font-size: 0.9rem;
        line-height: 1.2;
    }
    
    /* Adjust activity timeline for printing */
    @media print {
        .activity-timeline {
            padding: 0 !important;
        }
        
        .activity-item {
            padding: 0.3rem 0.5rem 0.3rem 3rem !important;
            margin-bottom: 0.2rem !important;
        }
        
        .timeline-badge {
            width: 20px !important;
            height: 20px !important;
            margin-top: 0.5rem !important;
            margin-left: 10px !important;
        }
    }
    
    .product-name-cell {
        min-width: 200px;
    }
    
    .product-code {
        font-family: monospace;
        font-size: 0.8em;
        background-color: rgba(139, 0, 0, 0.08);
        padding: 0.15rem 0.3rem;
        border-radius: 4px;
        color: var(--primary-dark);
        display: inline-block;
        min-width: 100px;
        max-width: 120px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .animal-type-badge {
        min-width: 70px;
        text-align: center;
        padding: 0.25em 0.5em;
        font-weight: 500;
        font-size: 0.75rem;
    }
    
    .price-cell {
        font-weight: 600;
        color: var(--primary-dark);
        font-size: 1.05rem;
    }
    
    .quantity-cell {
        font-weight: 500;
    }
    
    .expiration-cell {
        min-width: 120px;
    }
    
    .updated-by-cell {
        min-width: 150px;
    }
    
    .code-cell {
        width: 120px;
        min-width: 120px;
        max-width: 120px;
    }
    
    /* Status badge improvements */
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25em 0.6em;
        border-radius: 20px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.2rem;
        transition: all 0.2s ease;
        line-height: 1.2;
    }
    
    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    
    /* Card header improvements */
    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0,0,0,0.08);
        font-weight: 600;
        padding: 1rem 1.25rem;
    }
    
    /* Empty state improvements */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: var(--muted-text);
    }
    
    .empty-state h4 {
        margin-bottom: 0.5rem;
        color: var(--dark-text);
    }
    
    /* Print styles */
    @media print {
        /* Hide non-essential elements */
        .btn, .filter-card, .no-print, .real-time-indicator {
            display: none !important;
        }
        
        /* Show print-specific title */
        .print-title {
            display: block !important;
        }
        
        /* Ensure the page fills properly */
        body {
            background: white;
            font-size: 12pt;
            margin: 0;
            padding: 0;
        }
        
        .container-fluid {
            padding: 0.25in;
            max-width: 100% !important;
            overflow-x: visible !important;
        }
        
        /* Handle wide tables for printing */
        .table-responsive {
            overflow-x: visible !important;
        }
        
        .product-table, .activity-timeline {
            width: 100% !important;
            max-width: none !important;
            overflow-x: visible !important;
        }
        
        /* Improve table readability */
        .product-table th,
        .product-table td {
            padding: 0.2rem !important;
            font-size: 9pt;
            white-space: normal !important;
        }
        
        /* Ensure charts are visible */
        canvas {
            max-width: 100% !important;
            height: auto !important;
        }
        
        /* Remove shadows and borders for cleaner print */
        .card, .dashboard-section, .stat-card {
            box-shadow: none !important;
            border: 1px solid #ccc !important;
        }
        
        /* Ensure batch headers are visible */
        .batch-header {
            background-color: #f0f0f0 !important;
            border: 1px solid #ccc !important;
        }
        
        /* Ensure badges are visible */
        .badge {
            color: black !important;
            border: 1px solid #999 !important;
            background: transparent !important;
        }
        
        /* Hide animated elements */
        .expiring-badge, .real-time-indicator {
            animation: none !important;
        }
        
        /* Prevent page breaks inside tables */
        .product-table, .product-table thead, .product-table tbody, .product-table tr {
            page-break-inside: avoid;
        }
        
        .product-table th, .product-table td {
            page-break-inside: avoid;
        }
        
        /* Prevent page breaks in batch headers */
        .batch-header {
            page-break-inside: avoid;
            display: table-row;
        }
        
        /* Ensure charts render properly */
        .chart-container {
            page-break-inside: avoid;
        }
        
        /* Force background colors to print */
        * {
            -webkit-print-color-adjust: exact !important;
            color-adjust: exact !important;
        }
        
        /* Landscape orientation for print */
        @page {
            size: landscape;
            margin: 0.5in;
        }
        
        /* Improved table styling for print */
        .product-table {
            font-size: 9pt !important;
            width: 100% !important;
        }
        
        /* Compact table cells for printing */
        .product-table th,
        .product-table td {
            padding: 0.2rem !important;
            word-wrap: break-word;
        }
        
        /* Ensure all content prints */
        .container-fluid {
            padding: 0.25in !important;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Page Header with Real-time Indicator -->
    <div class="row mb-4 align-items-md-center">
        <div class="col-md-8 col-12 mb-3 mb-md-0">
            <h1 class="page-title mb-2 mb-md-0">
                <i class="fas fa-warehouse me-2"></i>Inventory Analytics
                <span class="badge bg-success ms-2">
                    <span class="real-time-indicator me-1"></span>Live
                </span>
            </h1>
            <h1 class="print-title mb-2 mb-md-0" style="display: none;">
                <i class="fas fa-warehouse me-2"></i>Inventory Analytics Report
            </h1>
            <p class="text-muted mb-0">Real-time inventory insights, stock levels, and expiration tracking</p>
        </div>
        <div class="col-md-4 col-12">
            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary flex-fill flex-md-grow-0">
                    <i class="fas fa-arrow-left me-1"></i>
                    Back to Reports
                </a>
                <button onclick="printReport()" class="btn btn-primary flex-fill flex-md-grow-0" style="background-color: #007bff; border-color: #007bff;">
                    <i class="fas fa-print me-1"></i>
                    Print Report
                </button>
                <a href="{{ route('products.index') }}" class="btn btn-secondary flex-fill flex-md-grow-0">
                    <i class="fas fa-box me-1"></i>
                    View Products
                </a>
            </div>
        </div>
    </div>

    <x-alert/>

    <!-- Enhanced Inventory Overview Cards - Row 1 -->
    <div class="dashboard-section">
        <h2 class="section-title">
            <i class="fas fa-chart-bar me-2"></i>Inventory Overview
        </h2>
        <div class="row">
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <span class="bg-primary text-white avatar" style="width: 60px; height: 60px; font-size: 1.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-box-open"></i>
                            </span>
                        </div>
                        <div class="stat-value h3 mb-1">{{ $totalProducts }}</div>
                        <div class="stat-label text-muted small">Total Products</div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card stat-card success h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <span class="bg-success text-white avatar" style="width: 60px; height: 60px; font-size: 1.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <div class="stat-value h3 mb-1 text-success">{{ $inStockItems }}</div>
                        <div class="stat-label text-muted small">In Stock</div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ ($inStockItems / max(1, $totalProducts)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card stat-card warning h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <span class="bg-warning text-white avatar" style="width: 60px; height: 60px; font-size: 1.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-exclamation-triangle"></i>
                            </span>
                        </div>
                        <div class="stat-value h3 mb-1 text-warning">{{ $lowStockItems }}</div>
                        <div class="stat-label text-muted small">Low Stock</div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($lowStockItems / max(1, $totalProducts)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card stat-card danger h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <span class="bg-danger text-white avatar" style="width: 60px; height: 60px; font-size: 1.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-times-circle"></i>
                            </span>
                        </div>
                        <div class="stat-value h3 mb-1 text-danger">{{ $outOfStockItems }}</div>
                        <div class="stat-label text-muted small">Out of Stock</div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ ($outOfStockItems / max(1, $totalProducts)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card stat-card orange h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <span class="bg-warning text-white avatar expiring-badge" style="width: 60px; height: 60px; font-size: 1.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <div class="stat-value h3 mb-1 text-warning expiring-badge">{{ $expiringItems }}</div>
                        <div class="stat-label text-muted small">Expiring Soon</div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ ($expiringItems / max(1, $totalProducts)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12 mb-4">
                <div class="card stat-card info h-100">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <span class="bg-info text-white avatar" style="width: 60px; height: 60px; font-size: 1.5rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                <i class="fas fa-dollar-sign"></i>
                            </span>
                        </div>
                        <div class="stat-value h4 mb-1 text-info">₱{{ number_format($totalStockValue, 0) }}</div>
                        <div class="stat-label text-muted small">Stock Value</div>
                        <div class="progress mt-2" style="height: 5px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Selling Products Section -->
    <div class="dashboard-section">
        <h2 class="section-title">
            <i class="fas fa-fire me-2"></i>Top Selling Products
        </h2>
        <div class="row g-4">
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card stat-card h-100">
                    <div class="card-header bg-light">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-sun me-2"></i>Today
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSellingDaily as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ $item->total_qty }}</td>
                                        <td class="text-end">₱{{ number_format($item->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-6 col-12">
                <div class="card stat-card h-100">
                    <div class="card-header bg-light">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-calendar-alt me-2"></i>This Month
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSellingMonthly as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ $item->total_qty }}</td>
                                        <td class="text-end">₱{{ number_format($item->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4 col-md-12 col-12">
                <div class="card stat-card h-100">
                    <div class="card-header bg-light">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>This Year
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSellingYearly as $item)
                                    <tr>
                                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ $item->total_qty }}</td>
                                        <td class="text-end">₱{{ number_format($item->revenue, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-3">No data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts Row -->
    <div class="dashboard-section">
        <h2 class="section-title">
            <i class="fas fa-chart-pie me-2"></i>Inventory Analytics
        </h2>
        <div class="row">
            <!-- Product Distribution Pie Chart -->
            <div class="col-xl-4 col-md-6 col-12 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>
                            Product Distribution
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="productDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Level Distribution -->
            <div class="col-xl-4 col-md-6 col-12 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-layer-group me-2"></i>
                            Stock Level Status
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="stockLevelChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Value by Category -->
            <div class="col-xl-4 col-md-12 col-12 mb-4">
                <div class="card stat-card h-100">
                    <div class="card-header">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>
                            Top 5 Categories by Value
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="stockValueChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Products Alert -->
    <div class="dashboard-section">
        <h2 class="section-title">
            <i class="fas fa-hourglass-half me-2"></i>Expiring Soon
        </h2>
        <div class="row">
            <div class="col-12">
                <div class="card stat-card">
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($expiringProducts as $product)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap">
                                        <div class="mb-2 mb-sm-0">
                                            <strong>{{ $product->name }}</strong>
                                            <div class="small text-muted">{{ $product->meatCut->name ?? 'N/A' }}</div>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-danger me-2">
                                                {{ \Carbon\Carbon::parse($product->expiration_date)->diffForHumans() }}
                                            </span>
                                            <span class="badge bg-warning">
                                                {{ \Carbon\Carbon::parse($product->expiration_date)->format('M d, Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-center text-muted py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-check-circle"></i>
                                        <h4>No Expiring Products</h4>
                                        <p class="text-muted mb-0">All products are fresh and within expiration date</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Expired Products History -->
    <div class="dashboard-section">
        <h2 class="section-title">
            <i class="fas fa-skull-crossbones me-2"></i>Expired Products History
        </h2>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-skull-crossbones me-2"></i>Expired Products
                        </h3>
                        <button class="btn btn-outline-primary btn-sm" onclick="exportExpiredProducts()">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Product</th>
                                        <th>Animal Type</th>
                                        <th>Cut</th>
                                        <th class="text-end">Quantity</th>
                                        <th>Expired On</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($expiredProducts as $product)
                                    <tr class="table-dark">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-2">
                                                    <i class="fas fa-drumstick-bite text-dark"></i>
                                                </div>
                                                <div>
                                                    <strong>{{ $product->name }}</strong>
                                                    <div class="small text-muted">{{ $product->category->name ?? 'No Category' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $product->meatCut->animal_type ?? 'N/A' }}</td>
                                        <td>{{ $product->meatCut->name ?? 'N/A' }}</td>
                                        <td class="text-end">{{ $product->quantity }} {{ $product->unit->name ?? 'pcs' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($product->expiration_date)->format('M d, Y') }}</td>
                                        <td><span class="badge bg-dark"><i class="fas fa-times me-1"></i>Expired</span></td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <div class="empty-state">
                                                <i class="fas fa-check-circle"></i>
                                                <h4>No Expired Products</h4>
                                                <p class="text-muted mb-0">No products have expired yet</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table with Filters -->
    <div class="dashboard-section">
        <h2 class="section-title">
            <i class="fas fa-list me-2"></i>Product Inventory Details
        </h2>
        
        <div class="filter-card shadow-sm border-0 mb-4">
            <form method="GET" action="{{ route('reports.inventory') }}">
                <div class="row align-items-end">
                    <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                        <label class="form-label mb-1">Animal Type</label>
                        <select name="animal_type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($animalTypes as $type)
                                <option value="{{ $type }}" {{ request('animal_type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                        <label class="form-label mb-1">Stock Status</label>
                        <select name="stock_status" class="form-select">
                            <option value="">All Status</option>
                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                            <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                            <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                        <label class="form-label mb-1">Date From</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-lg-2 col-md-3 col-sm-6 mb-3">
                        <label class="form-label mb-1">Date To</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-lg-4 col-md-12 mb-3">
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary flex-fill flex-md-grow-0">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                            <a href="{{ route('reports.inventory') }}" class="btn btn-outline-secondary flex-fill flex-md-grow-0">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card stat-card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h3 class="mb-0 d-flex align-items-center">
                            <i class="fas fa-boxes me-2 text-primary"></i>
                            Inventory Items
                        </h3>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-primary rounded-pill">{{ $products->count() }} items</span>
                            <button class="btn btn-outline-primary btn-sm" onclick="exportTableToCSV('inventory-items.csv')">
                                <i class="fas fa-download me-1"></i>Export
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover product-table card-table mb-0">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th class="product-name-cell">Product Name</th>
                                        <th class="code-cell hide-on-xs">Code</th>
                                        <th>Animal Type</th>
                                        <th class="hide-on-xs">Cut Type</th>
                                        <th class="price-cell text-end">Price/kg</th>
                                        <th class="quantity-cell text-center">Quantity</th>
                                        <th class="text-center">Status</th>
                                        <th class="expiration-cell hide-on-xs">Expiration</th>
                                        <th class="updated-by-cell hide-on-xs">Last Updated By</th>
                                        <th class="last-update-cell hide-on-xs text-end">Last Update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($productsByBatch as $batchDate => $batchProducts)
                                        <tr class="table-secondary">
                                            <td colspan="10" class="batch-header fw-bold">
                                                <div class="d-flex align-items-center justify-content-between flex-wrap">
                                                    <span>
                                                        <i class="fas fa-calendar me-2"></i>Batch: {{ $batchDate ?? 'Unknown' }}
                                                    </span>
                                                    <span class="badge bg-secondary">{{ $batchProducts->count() }} items</span>
                                                </div>
                                            </td>
                                        </tr>
                                        @foreach($batchProducts as $product)
                                        <tr class="align-middle">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="fas fa-drumstick-bite text-primary fs-4"></i>
                                                    </div>
                                                    <div>
                                                        <strong class="d-block mb-1">{{ $product->name }}</strong>
                                                        <small class="text-muted">{{ $product->category->name ?? 'No Category' }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="code-cell align-middle text-center">
                                                <code class="product-code">{{ $product->code }}</code>
                                            </td>
                                            <td class="align-middle">
                                                <span class="badge bg-primary animal-type-badge">{{ $product->meatCut->animal_type ?? 'N/A' }}</span>
                                            </td>
                                            <td>{{ $product->meatCut->name ?? 'N/A' }}</td>
                                            <td class="price-cell text-end fw-bold">₱{{ number_format($product->price_per_kg, 2) }}</td>
                                            <td class="quantity-cell align-middle text-center">
                                                <span class="badge bg-{{ $product->quantity > ($product->quantity_alert ?? 10) ? 'success' : ($product->quantity > 0 ? 'warning' : 'danger') }} status-badge">
                                                    <i class="fas fa-weight-hanging me-1"></i>{{ $product->quantity }} {{ $product->unit->name ?? 'pcs' }}
                                                </span>
                                            </td>
                                            <td class="align-middle text-center">
                                                @if($product->quantity <= 0)
                                                    <span class="badge bg-danger status-badge">
                                                        <i class="fas fa-times-circle me-1"></i>Out of Stock
                                                    </span>
                                                @elseif($product->quantity <= ($product->quantity_alert ?? 10))
                                                    <span class="badge bg-warning status-badge">
                                                        <i class="fas fa-exclamation-triangle me-1"></i>Low Stock
                                                    </span>
                                                @else
                                                    <span class="badge bg-success status-badge">
                                                        <i class="fas fa-check-circle me-1"></i>In Stock
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="align-middle">
                                                @if($product->expiration_date)
                                                    @php
                                                        $expirationDate = \Carbon\Carbon::parse($product->expiration_date);
                                                        $daysUntilExpiry = now()->diffInDays($expirationDate, false);
                                                    @endphp
                                                    @if($daysUntilExpiry < 0)
                                                        <span class="badge bg-dark status-badge">
                                                            <i class="fas fa-skull-crossbones me-1"></i>Expired
                                                        </span>
                                                    @elseif($daysUntilExpiry <= 7)
                                                        <span class="badge bg-danger expiring-badge status-badge">
                                                            <i class="fas fa-clock me-1"></i>{{ ceil($daysUntilExpiry) }}d left
                                                        </span>
                                                    @else
                                                        <span class="badge bg-info status-badge">
                                                            <i class="fas fa-calendar me-1"></i>{{ $expirationDate->format('M d, Y') }}
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($product->updatedByUser)
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-sm me-2" style="background-color: var(--primary-color); color: white; width: 32px; height: 32px; font-size: 0.75rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                                            {{ strtoupper(substr($product->updatedByUser->name, 0, 2)) }}
                                                        </span>
                                                        <div>
                                                            <strong>{{ $product->updatedByUser->name }}</strong>
                                                            <div class="small text-muted">{{ ucfirst($product->updatedByUser->role) }}</div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">System</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                <div>
                                                    <span class="small">{{ $product->updated_at->format('M d, Y') }}</span>
                                                </div>
                                                <div class="small text-muted">{{ $product->updated_at->format('h:i A') }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5">
                                                <div class="empty-state">
                                                    <i class="fas fa-box-open"></i>
                                                    <h4>No Products Found</h4>
                                                    <p class="text-muted">No products match your current filter criteria</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Stock Activity -->
    <div class="dashboard-section">
        <h2 class="section-title">
            <i class="fas fa-history me-2"></i>Recent Stock Activity (Last 7 Days)
        </h2>
        <div class="row">
            <div class="col-12">
                <div class="card stat-card shadow-sm border-0">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-stream me-2 text-info"></i>Activity Timeline
                        </h3>
                        <span class="badge bg-info rounded-pill">{{ $recentActivity->count() }} events</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="activity-timeline" style="padding: 0.5rem;">
                            @forelse($recentActivity as $activity)
                                <div class="activity-item {{ $activity->action }} border-start-0 position-relative ps-5 py-2">
                                    <div class="timeline-badge position-absolute top-0 start-0 translate-middle rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 28px; height: 28px; margin-top: 1rem; margin-left: 14px; background-color: 
                                         {{ $activity->action === 'created' ? 'var(--success-color)' : ($activity->action === 'updated' ? 'var(--info-color)' : 'var(--danger-color)') }}; 
                                         color: white; box-shadow: 0 1px 4px rgba(0,0,0,0.15);">
                                        <i class="fas {{ $activity->action === 'created' ? 'fa-plus' : ($activity->action === 'updated' ? 'fa-edit' : 'fa-trash') }}" style="font-size: 0.7rem;"></i>
                                    </div>
                                    <div class="d-flex justify-content-between flex-wrap mb-1">
                                        <div class="mb-1 mb-sm-0">
                                            <h5 class="mb-1 small">{{ $activity->product->name ?? 'Unknown Product' }}</h5>
                                            <span class="badge bg-{{ $activity->action === 'created' ? 'success' : ($activity->action === 'updated' ? 'info' : 'danger') }} rounded-pill" style="font-size: 0.7rem; padding: 0.25em 0.5em;">
                                                {{ ucfirst($activity->action) }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <div class="small">{{ $activity->created_at->format('M d, Y') }}</div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">{{ $activity->created_at->format('h:i A') }}</div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between flex-wrap">
                                        <div class="d-flex align-items-center">
                                            @if($activity->staff)
                                                <span class="avatar avatar-sm me-2" style="background-color: var(--primary-color); color: white; width: 24px; height: 24px; font-size: 0.6rem; display: inline-flex; align-items: center; justify-content: center; border-radius: 50%;">
                                                    {{ strtoupper(substr($activity->staff->name, 0, 2)) }}
                                                </span>
                                                <div>
                                                    <strong class="small">{{ $activity->staff->name }}</strong>
                                                    <div class="small text-muted">{{ ucfirst($activity->staff->role ?? 'Staff') }}</div>
                                                </div>
                                            @else
                                                <span class="text-muted">System</span>
                                            @endif
                                        </div>
                                        <div class="mt-1 mt-sm-0">
                                            @if($activity->details)
                                                <small class="text-muted">{{ $activity->details }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center text-muted py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h4>No Recent Activity</h4>
                                        <p class="text-muted mb-0">No stock updates in the last 7 days</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
    // Product Distribution Pie Chart
    const distributionCtx = document.getElementById('productDistributionChart').getContext('2d');
    const productDistributionChart = new Chart(distributionCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($productDistribution->keys()) !!},
            datasets: [{
                data: {!! json_encode($productDistribution->values()) !!},
                backgroundColor: [
                    'rgba(139, 0, 0, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                ],
                borderColor: '#fff',
                borderWidth: 3,
                hoverOffset: 15,
                hoverBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 13,
                            family: 'sans-serif'
                        },
                        boxWidth: 12,
                        boxHeight: 12
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.9)',
                    titleFont: {
                        size: 15,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    cornerRadius: 8,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} products (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 1500,
                easing: 'easeOutQuart'
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Stock Level Distribution Chart
    const stockLevelCtx = document.getElementById('stockLevelChart').getContext('2d');
    const stockLevelChart = new Chart(stockLevelCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode(array_keys($stockLevelDistribution)) !!},
            datasets: [{
                data: {!! json_encode(array_values($stockLevelDistribution)) !!},
                backgroundColor: [
                    'rgba(40, 167, 69, 0.8)',   // Green - In Stock
                    'rgba(255, 193, 7, 0.8)',    // Yellow - Low Stock
                    'rgba(220, 53, 69, 0.8)'     // Red - Out of Stock
                ],
                borderColor: '#fff',
                borderWidth: 2,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true,
                        font: {
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} items (${percentage}%)`;
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true
            }
        }
    });

    // Stock Value by Category Chart
    const stockValueCtx = document.getElementById('stockValueChart').getContext('2d');
    const stockValueChart = new Chart(stockValueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($stockValueByCategory->keys()) !!},
            datasets: [{
                label: 'Stock Value',
                data: {!! json_encode($stockValueByCategory->values()) !!},
                backgroundColor: [
                    'rgba(139, 0, 0, 0.8)',
                    'rgba(139, 0, 0, 0.7)',
                    'rgba(139, 0, 0, 0.6)',
                    'rgba(139, 0, 0, 0.5)',
                    'rgba(139, 0, 0, 0.4)'
                ],
                borderColor: [
                    'rgb(139, 0, 0)',
                    'rgb(139, 0, 0)',
                    'rgb(139, 0, 0)',
                    'rgb(139, 0, 0)',
                    'rgb(139, 0, 0)'
                ],
                borderWidth: 1,
                borderRadius: 6,
                hoverBackgroundColor: 'rgba(139, 0, 0, 1)',
                hoverBorderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '₱' + (value >= 1000 ? (value/1000).toFixed(1) + 'k' : value);
                        },
                        font: {
                            size: 11
                        }
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            },
            plugins: {
                legend: { 
                    display: false 
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    callbacks: {
                        label: function(context) {
                            return 'Stock Value: ₱' + context.parsed.x.toLocaleString();
                        }
                    }
                }
            },
            animation: {
                duration: 1000,
                easing: 'easeInOutQuart'
            }
        }
    });


    // Auto-refresh analytics every 30 seconds
    setInterval(function() {
        fetch('{{ route("reports.inventory.analytics") }}')
            .then(response => response.json())
            .then(data => {
                console.log('Analytics updated:', data.last_updated);
                // Update charts with new data if needed
            });
    }, 30000);
    
    // Export table to CSV function
    function exportTableToCSV(filename) {
        const csv = [];
        const rows = document.querySelectorAll('.product-table tr');
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                // Clean up the text content
                let cellText = cols[j].innerText.replace(/\n/g, ' ').trim();
                
                // Fix peso symbol encoding issue
                cellText = cellText.replace(/â‚±/g, '₱');
                
                // Escape double quotes
                cellText = '"' + cellText.replace(/"/g, '""') + '"';
                row.push(cellText);
            }
            
            csv.push(row.join(','));
        }
        
        // Download CSV file with proper UTF-8 encoding for Excel
        const csvString = '\uFEFF' + csv.join('\n'); // Add BOM for Excel
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', filename);
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    
    // Export expired products table to CSV
    function exportExpiredProducts() {
        const csv = [];
        const rows = document.querySelectorAll('.dashboard-section:nth-child(3) table tr'); // Expired Products table
        
        for (let i = 0; i < rows.length; i++) {
            const row = [], cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length; j++) {
                // Clean up the text content
                let cellText = cols[j].innerText.replace(/\n/g, ' ').trim();
                
                // Fix peso symbol encoding issue
                cellText = cellText.replace(/â‚±/g, '₱');
                
                // Escape double quotes
                cellText = '"' + cellText.replace(/"/g, '""') + '"';
                row.push(cellText);
            }
            
            csv.push(row.join(','));
        }
        
        // Download CSV file with proper UTF-8 encoding for Excel
        const csvString = '\uFEFF' + csv.join('\n'); // Add BOM for Excel
        const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'expired-products.csv');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    
    // Custom print function to avoid PDF issues
    function printReport() {
        // Temporarily disable animations
        const style = document.createElement('style');
        style.innerHTML = `
            *, *::before, *::after { 
                animation-duration: 0s !important; 
                transition-duration: 0s !important; 
            }
            
            /* Force landscape orientation for printing */
            @media print {
                @page {
                    size: landscape;
                }
                body {
                    writing-mode: horizontal-tb;
                }
                
                /* Ensure all content fits on page */
                .container-fluid {
                    max-width: 100% !important;
                    width: 100% !important;
                    padding: 0.25in !important;
                }
                
                /* Scale content if needed */
                .dashboard-section, .card {
                    transform-origin: top left;
                }
                
                /* Ensure tables use full width */
                .table-responsive {
                    overflow-x: visible !important;
                }
                
                /* Adjust table for better printing */
                .product-table {
                    font-size: 9pt !important;
                    width: 100% !important;
                    table-layout: auto !important;
                }
                
                /* Compact table cells for printing */
                .product-table th,
                .product-table td {
                    padding: 0.2rem !important;
                }
                
                /* Ensure charts are visible */
                canvas {
                    max-width: 100% !important;
                    height: auto !important;
                }
                
                /* Ensure batch headers print correctly */
                .batch-header {
                    display: table-cell !important;
                    page-break-inside: avoid !important;
                }
            }
        `;
        document.head.appendChild(style);
        
        // Small delay to ensure styles are applied
        setTimeout(() => {
            window.print();
            // Remove the temporary style after printing
            setTimeout(() => {
                document.head.removeChild(style);
            }, 1000);
        }, 100);
    }
</script>
@endpush
@endsection