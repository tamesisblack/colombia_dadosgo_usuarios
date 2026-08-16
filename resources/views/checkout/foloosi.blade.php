@extends('layouts.app')

@section('content')
<div class="container text-center py-5">
    <h3>Redirecting to Foloosi Secure Checkout...</h3>
    <p>Amount: <strong>{{ $formatted_price }}</strong></p>
    <div class="spinner-border text-primary" role="status"></div>
</div>

<script type="text/javascript" src="https://www.foloosi.com/js/foloosipay.v2.js"></script>
<script>
    (function() {
        var options = {
            reference_token: "{{ $reference_token }}"
            // Foloosi will use the token you created server-side
        };

        var fp1 = new Foloosipay(options);
        fp1.open();   // Opens the popup (or redirects if you set redirect:true in options)

        // Optional: handler (recommended by Foloosi docs)
        foloosiHandler(null, function(e) {
            if (e.data.status === 'success') {
                window.location.href = "{{ route('success') }}?foloosi_token={{ Session::get('foloosi_payment_token') }}";
            } else if (e.data.status === 'error') {
                window.location.href = "{{ route('checkout') }}";
            }
        });
    })();
</script>
@endsection