@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">PayMongo - Card Payment</h4>
                    <p class="mb-0">Amount: {{ $formatted_price }}</p>
                </div>
                <div class="card-body">
                    @if(session('payment_error'))
                        <div class="alert alert-danger">{{ session('payment_error') }}</div>
                    @endif
                    
                    <form id="paymongo-card-form">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Número de tarjeta</label>
                            <input type="text" class="form-control" id="card_number" placeholder="4242 4242 4242 4242" required maxlength="19">
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label">MM</label>
                                <input type="text" class="form-control" id="exp_month" placeholder="MM" required maxlength="2">
                            </div>
                            <div class="col-4">
                                <label class="form-label">AAAA</label>
                                <input type="text" class="form-control" id="exp_year" placeholder="AAAA" required maxlength="4">
                            </div>
                            <div class="col-4">
                                <label class="form-label">CVC</label>
                                <input type="text" class="form-control" id="cvc" placeholder="123" required maxlength="4">
                            </div>
                        </div>
                        <div class="mb-3 mt-3">
                            <label class="form-label">Nombre en la tarjeta</label>
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
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

    const formData = {
        card_number: document.getElementById('card_number').value.replace(/\s+/g, ''),
        exp_month:   document.getElementById('exp_month').value,
        exp_year:    document.getElementById('exp_year').value,
        cvc:         document.getElementById('cvc').value,
        name:        document.getElementById('card_name').value,
        _token:      '{{ csrf_token() }}'
    };

    if (!formData.card_number || formData.card_number.length < 15) {
        alert('Ingresa un número de tarjeta válido');
        btn.disabled = false;
        btn.innerHTML = 'Pay {{ $formatted_price }}';
        return;
    }

    if (!formData.exp_month || !formData.exp_year) {
        alert('Ingresa la fecha de vencimiento');
        btn.disabled = false;
        btn.innerHTML = 'Pay {{ $formatted_price }}';
        return;
    }

    if (!formData.cvc) {
        alert('Ingresa el CVC');
        btn.disabled = false;
        btn.innerHTML = 'Pay {{ $formatted_price }}';
        return;
    }

    try {
        const response = await fetch("{{ route('giftcard.wallet.paymongo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();

        if (result.status) {
            window.location.href = result.redirect || "{{ route('giftcard.success') }}";
        } else {
            alert(result.message || 'El pago falló. Inténtalo de nuevo.');
            btn.disabled = false;
            btn.innerHTML = 'Pay {{ $formatted_price }}';
        }
    } catch (err) {
        alert('Error de red: ' + err.message);
        btn.disabled = false;
        btn.innerHTML = 'Pay {{ $formatted_price }}';
    }
});

// Card number formatting
document.getElementById('card_number').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 16) value = value.slice(0, 16);
    value = value.replace(/(\d{4})/g, '$1 ').trim();
    e.target.value = value;
});

// Month formatting
document.getElementById('exp_month').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 2) value = value.slice(0, 2);
    if (parseInt(value) > 12) value = '12';
    e.target.value = value;
});
</script>
@endsection