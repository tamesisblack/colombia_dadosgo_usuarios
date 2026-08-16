@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Foloosi Payment</h4>
                    <p class="mb-0">Amount: {{ $formatted_price }}</p>
                </div>
                <div class="card-body text-center">
                    @if(session('payment_error'))
                        <div class="alert alert-danger">{{ session('payment_error') }}</div>
                    @endif
                    
                    <div class="alert alert-info">
                        <strong>Redirecting to Foloosi Secure Checkout...</strong>
                    </div>
                    <div class="spinner-border text-primary my-4" role="status"></div>
                    <p class="text-muted">Please wait while we redirect you to the payment gateway.</p>
                    <p class="text-muted small">If you are not redirected automatically, <a href="javascript:void(0)" id="manualRedirect">click here</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="https://www.foloosi.com/js/foloosipay.v2.js"></script>
<script>
    (function() {
        var referenceToken = "{{ $reference_token ?? '' }}";
        
        if (!referenceToken) {
            alert('Invalid payment session. Please try again.');
            window.location.href = "{{ route('customize.giftcard') }}";
            return;
        }
        
        var options = {
            reference_token: referenceToken,
            redirect: true
        };

        try {
            var fp1 = new Foloosipay(options);
            fp1.open();
            
            // Fallback redirect after 5 seconds if Foloosi doesn't redirect
            setTimeout(function() {
                if (window.location.href.indexOf('foloosi_token') === -1) {
                    window.location.href = "{{ route('giftcard.success') }}?foloosi_token={{ Session::get('foloosi_payment_token') }}";
                }
            }, 10000);
            
        } catch (error) {
            console.error('Foloosi error:', error);
            alert('Failed to initialize Foloosi payment: ' + error.message);
            window.location.href = "{{ route('customize.giftcard') }}";
        }
    })();
</script>
@endsection