<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-dark text-light">
    <div class="container py-5">
        <h1 class="mb-2">LPG Point</h1>
        <p class="text-secondary mb-4">Sprint 1 — Project Setup &amp; Environment</p>

        <div class="card bg-body-tertiary mb-4" style="max-width: 30rem;">
            <div class="card-body">
                <h5 class="card-title d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    Bootstrap 5 is wired up
                </h5>
                <p class="card-text">Icons, buttons, cards and JS components are all working with no CDN.</p>
                <p class="card-text">
                    Sample invoice no. <span style="font-family: 'JetBrains Mono', monospace;">INV-00001</span>
                </p>
                <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#demoModal">
                    Open modal
                </button>
                <button type="button" class="btn btn-outline-secondary" id="toastBtn">
                    Show toast
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="demoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bootstrap modal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    This is Bootstrap's own Modal JS component, no hand-rolled JS.
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="demoToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <strong class="me-auto">LPG Point</strong>
            </div>
            <div class="toast-body">Bootstrap's Toast JS component works too.</div>
        </div>
    </div>

    <script>
        document.getElementById('toastBtn').addEventListener('click', function () {
            new bootstrap.Toast(document.getElementById('demoToast')).show();
        });
    </script>
</body>
</html>
