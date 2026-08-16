@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mt-5">
    <div class="card shadow mtn-top-margin">
        <div class="card-header bg-primary text-white">
            <h4>Wallet Top-up - MTN MoMo</h4>
        </div>

        <div class="card-body">
            <!-- Display amount information -->
            <div class="alert alert-info mb-4">
                <strong>Amount to pay:</strong> {{ $formatted_price ?? '€' . number_format($amount ?? 0, 2) }}
            </div>

            <div id="step1" class="step">
                <h5>MTN MoMo Number</h5>
                <p class="text-muted">Enter your MTN MoMo phone number to receive payment request</p>

                <form id="mtnForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" name="phone" id="phone" class="form-control" placeholder="e.g. 25677xxxxxx" required>
                        <small class="form-text text-muted">Enter without country code (e.g., 25677xxxxxx)</small>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('transactions') }}" class="btn btn-secondary">Back</a>
                        <button type="button" class="btn btn-primary" id="submitPhone">
                            Request Payment
                        </button>
                    </div>
                </form>
            </div>

            <div id="step2" class="step d-none text-center mt-5">
                <h4>Payment Request Sent!</h4>
                <p>Please approve payment of <strong>{{ $formatted_price ?? '€' . number_format($amount ?? 0, 2) }}</strong> on your MTN MoMo app</p>
                <div class="my-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <p class="text-muted">Do not close this page while waiting for confirmation</p>
                <button type="button" class="btn btn-outline-danger mt-3" onclick="cancelPolling()">Cancel</button>
            </div>

            <div id="errorMessage" class="alert alert-danger d-none mt-3"></div>
            <div id="successMessage" class="alert alert-success d-none mt-3"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    let pollTimer = null;
    let reference = '';

    $(document).ready(function() {
        // Store the amount and settings from PHP
        const amount = {{ $amount ?? 0 }};
        
        $('#submitPhone').on('click', function(e) {
            e.preventDefault();
            
            const phone = $('#phone').val().trim();
            if (!phone) {
                showError('Please enter your phone number');
                return;
            }

            // Validate phone number format (basic)
            if (!/^\d{8,15}$/.test(phone)) {
                showError('Please enter a valid phone number (8-15 digits)');
                return;
            }

            // Show processing
            hideMessages();
            $('#submitPhone')
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2"></span> Processing...');
            
            $.ajax({
                url: '{{ route("wallet.mtnmomo.request") }}',
                method: 'POST',
                data: JSON.stringify({
                    phone: phone,
                    amount: amount,
                    _token: '{{ csrf_token() }}'
                }),
                dataType: 'json',
                contentType: 'application/json',
                success: function(response) {
                    if (response.success === true) {
                        reference = response.reference || 'unknown';
                        $('#step1').addClass('d-none');
                        $('#step2').removeClass('d-none');
                        startPolling();
                    } else {
                        showError(response.message || "Payment request failed");
                        $('#submitPhone')
                            .prop('disabled', false)
                            .html('Request Payment');
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.status, xhr.responseText);

                    let msg = "Something went wrong";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        msg = "CSRF token mismatch - refresh page";
                    } else if (xhr.status === 422) {
                        msg = "Validation error: " + (xhr.responseJSON?.errors?.phone?.[0] || "Invalid phone");
                    } else if (xhr.status === 500) {
                        msg = "Server error - please try again";
                    }

                    showError(msg);
                    $('#submitPhone')
                        .prop('disabled', false)
                        .html('Request Payment');
                }
            });
        });
    });

    function startPolling() {
        let attempts = 0;
        const max = 120;

        if (pollTimer) {
            clearInterval(pollTimer);
        }

        pollTimer = setInterval(function() {
            attempts++;
            
            if (attempts > max) {
                clearInterval(pollTimer);
                showError('Payment timed out - please check your MTN MoMo app');
                $('#step2').addClass('d-none');
                $('#step1').removeClass('d-none');
                return;
            }

            $.ajax({
                url: '{{ route("wallet.mtnmomo.check") }}',
                method: 'POST',
                data: JSON.stringify({
                    reference: reference,
                    _token: '{{ csrf_token() }}'
                }),
                dataType: 'json',
                contentType: 'application/json',
                success: function(resp) {
                    if (resp.success === true) {
                        clearInterval(pollTimer);
                        $('#step2').addClass('d-none');
                        showSuccess('Payment successful! Redirecting...');
                        setTimeout(() => {
                            window.location.href = resp.redirect || '{{ route("wallet-success") }}';
                        }, 2000);
                    } else if (resp.success === false && resp.message !== 'Payment still processing') {
                        clearInterval(pollTimer);
                        showError(resp.message || 'Payment failed');
                        $('#step2').addClass('d-none');
                        $('#step1').removeClass('d-none');
                    }
                },
                error: function(err) {
                    console.warn("Poll error (continuing):", err);
                    if (err.status === 419) {
                        clearInterval(pollTimer);
                        showError('Session expired - refresh page');
                        $('#step2').addClass('d-none');
                        $('#step1').removeClass('d-none');
                    }
                }
            });
        }, 3000); // Poll every 3 seconds
    }

    function cancelPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
        $('#step2').addClass('d-none');
        $('#step1').removeClass('d-none');
        hideMessages();
    }

    function showError(message) {
        $('#errorMessage').text(message).removeClass('d-none');
        $('#successMessage').addClass('d-none');
        setTimeout(() => {
            $('#errorMessage').addClass('d-none');
        }, 5000);
    }

    function showSuccess(message) {
        $('#successMessage').text(message).removeClass('d-none');
        $('#errorMessage').addClass('d-none');
    }

    function hideMessages() {
        $('#errorMessage, #successMessage').addClass('d-none');
    }
</script>