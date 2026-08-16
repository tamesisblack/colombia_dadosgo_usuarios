@extends('layouts.app')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container mt-5">
    <div class="card shadow mtn-top-margin">
        <div class="card-header bg-primary text-white">
            <h4>{{trans('lang.mtn_momo')}}</h4>
        </div>

        <div class="card-body">

            <div id="step1" class="step">
                <h5>{{trans('lang.momo_number')}}</h5>
                <p class="text-muted">{{trans('lang.momo_number_subtitle')}}</p>

                <form id="mtnForm" method="POST" action="{{ route('payment.mtnmomo.request') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{trans('lang.phone_number_mtn')}}</label>
                        <input type="tel" name="phone" id="phone" class="form-control" placeholder="e.g. 25677xxxxxx" required>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('checkout') }}" class="btn btn-secondary">{{trans('lang.back')}}</a>
                        <button type="submit" class="btn btn-primary" id="submitPhone">
                            {{trans('lang.request_payment')}}
                        </button>
                    </div>
                </form>
            </div>

            <div id="step2" class="step d-none text-center mt-5">
                <h4>{{trans('lang.payment_request_sent')}}</h4>
                <p>{{trans('lang.please_approve_payment')}} <strong>{{ $formatted }}</strong></p>
                <div class="my-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
                <p class="text-muted">{{trans('lang.do_not_close_page')}}</p>
                <button class="btn btn-outline-danger mt-3" onclick="cancelPolling()">{{trans('lang.cancel')}}</button>
            </div>

            <div id="errorMessage" class="alert alert-danger d-none mt-3"></div>
            <div id="successMessage" class="alert alert-success d-none mt-3"></div>

        </div>
    </div>
</div>
    <!-- Ensure jQuery is here for this page only (temporary safety) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
    $(document).ready(function() {
        // Get CSRF token
        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        // Set up AJAX defaults
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        $('#mtnForm').submit(function(e) {
            e.preventDefault();
            e.stopPropagation();

            let phone = $('#phone').val();
            if (!phone) {
                alert("Please enter phone number");
                return;
            }

            // Show processing
            $('#errorMessage, #successMessage').addClass('d-none');
            $('#submitPhone')
                .prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm me-2"></span>Processing...');
            
            // IMPORTANT: Include _token in the JSON data
            $.ajax({
                url: '{{ route("payment.mtnmomo.request") }}',
                method: 'POST',
                data: JSON.stringify({
                    phone: phone,
                    _token: csrfToken  // ← Add this line
                }),
                dataType: 'json',
                contentType: 'application/json',
                success: function(response) {
                    if (response.success === true) {
                        $('#step1').addClass('d-none');
                        $('#step2').removeClass('d-none');
                        startPolling(response.reference || 'unknown');
                    } else {
                        $('#errorMessage')
                            .text(response.message || "Payment request failed")
                            .removeClass('d-none');
                    }
                },
                error: function(xhr) {
                    console.error("AJAX Error:", xhr.status, xhr.responseText);

                    let msg = "Something went wrong";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    } else if (xhr.status === 419) {
                        msg = "CSRF token mismatch - refresh page";
                        // Optionally refresh the page
                        // setTimeout(() => location.reload(), 2000);
                    } else if (xhr.status === 422) {
                        msg = "Validation error: " + (xhr.responseJSON?.errors?.phone?.[0] || "Invalid phone");
                    } else if (xhr.status === 500) {
                        msg = "Server error - check laravel.log";
                    }

                    $('#errorMessage').text(msg).removeClass('d-none');
                },
                complete: function() {
                    $('#submitPhone')
                        .prop('disabled', false)
                        .html('Request Payment');
                }
            });
        });

        let pollTimer = null;

        function startPolling(reference) {
            let attempts = 0;
            const max = 120;

            pollTimer = setInterval(function() {
                attempts++;
                
                if (attempts > max) {
                    clearInterval(pollTimer);
                    $('#errorMessage').text('Timed out - check MTN app').removeClass('d-none');
                    $('#step2').addClass('d-none');
                    $('#step1').removeClass('d-none');
                    return;
                }

                $.ajax({
                    url: '{{ route("payment.mtnmomo.check") }}',
                    method: 'POST',
                    data: JSON.stringify({
                        reference: reference,
                        _token: csrfToken  // ← Add this line too
                    }),
                    dataType: 'json',
                    contentType: 'application/json',
                    success: function(resp) {
                        if (resp.success && resp.status === 'SUCCESS') {
                            clearInterval(pollTimer);
                            $('#step2').addClass('d-none');
                            $('#successMessage').text('Success! Redirecting...').removeClass('d-none');
                            setTimeout(() => {
                                window.location.href = resp.redirect || '{{ route("success") }}';
                            }, 2000);
                        } else if (!resp.success) {
                            clearInterval(pollTimer);
                            $('#errorMessage').text(resp.message || 'Failed').removeClass('d-none');
                            $('#step2').addClass('d-none');
                            $('#step1').removeClass('d-none');
                        }
                    },
                    error: function(err) {
                        console.warn("Poll error (continuing):", err);
                        if (err.status === 419) {
                            clearInterval(pollTimer);
                            $('#errorMessage').text('Session expired - please refresh').removeClass('d-none');
                        }
                    }
                });
            }, 5000);
        }

        window.cancelPolling = function() {
            if (pollTimer) clearInterval(pollTimer);
            $('#step2').addClass('d-none');
            $('#step1').removeClass('d-none');
        };
    });
</script>