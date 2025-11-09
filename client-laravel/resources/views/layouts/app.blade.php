<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'User Management')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .main-container {
            padding: 2rem 0;
        }
        
        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: #007bff;
            color: white;
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 1.5rem;
            font-weight: 600;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .family-member {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #f8f9fa;
        }
        
        .btn-add-family {
            color: #28a745;
            text-decoration: none;
            border: none;
            background: none;
            font-size: 1rem;
            padding: 0.5rem 1rem;
            font-weight: 500;
        }
        
        .btn-add-family:hover {
            color: #1e7e34;
            text-decoration: none;
        }
        
        .family-member {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        
        .family-label {
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
        }
        
        .family-input {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .family-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .family-remove-btn {
            width: 100%;
            padding: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .family-member .row {
                margin-bottom: 0.5rem;
            }
            
            .family-label {
                margin-bottom: 0.5rem;
                margin-top: 0.5rem;
            }
            
            .family-remove-btn {
                margin-top: 0.5rem;
            }
        }
        
        @media (min-width: 992px) {
            .family-member .row {
                align-items: end;
            }
        }
        
        .form-control {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 0.75rem;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #495057;
        }
        
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #004085;
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #545b62;
            border-color: #4e555b;
        }
        
        .table thead th {
            background-color: #007bff;
            color: white;
            border: none;
        }
        
        .section-title {
            color: #495057;
            font-weight: 600;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>
    <div class="container-fluid main-container">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>