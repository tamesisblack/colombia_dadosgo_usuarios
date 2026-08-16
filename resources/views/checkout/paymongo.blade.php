@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4>PayMongo - Wallet Top Up</h4>
                    <p class="mb-0">Amount: {{ $formatted_price }}</p>
                </div>
                <div class="card-body">
                    <form id="paymongo-card-form">
                        <div class="mb-3">
                            <label class="form-label">Card Number</label>
                            <input type="text" class="form-control" id="card_number" placeholder="4242 4242 4242 4242" required maxlength="19">
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">MM</label>
                                <input type="text" class="form-control" id="exp_month" placeholder="MM" required maxlength="2">
                            </div>
                            <div class="col-4">
                                <label class="form-label">YYYY</label>
                                <input type="text" class="form-control" id="exp_year" placeholder="YYYY" required maxlength="4">
                            </div>
                            <div class="col-4">
                                <label class="form-label">CVC</label>
                                <input type="text" class="form-control" id="cvc" placeholder="123" required maxlength="4">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Name on Card</label>
                            <input type="text" class="form-control" id="card_name" value="{{ $authorName }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="pay-button">
                            Pay {{ $formatted_price }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('paymongo-card-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('pay-button');
    btn.disabled = true;
    btn.innerHTML = 'Processing...';

    const formData = {
        card_number: document.getElementById('card_number').value.replace(/\s+/g, ''),
        exp_month:   document.getElementById('exp_month').value,
        exp_year:    document.getElementById('exp_year').value,
        cvc:         document.getElementById('cvc').value,
        name:        document.getElementById('card_name').value
    };

    try {
        const response = await fetch("{{ route('wallet.paymongo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.status) {
            if (result.redirect) {
                window.location.href = result.redirect;
            } else {
                window.location.href = "{{ route('success') }}";
            }
        } else {
            alert(result.message || 'Payment failed. Please try again.');
            btn.disabled = false;
            btn.innerHTML = 'Pay {{ $formatted_price }}';
        }
    } catch (err) {
        alert('Network error: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = 'Pay {{ $formatted_price }}';
    }
});
</script>
@endsection