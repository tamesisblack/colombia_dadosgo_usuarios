@extends('layouts.app')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Wallet Top-up - PayMongo</h4>
                    <p class="mb-0">Amount: {{ $formatted_price }}</p>
                </div>
                <div class="card-body">
                    @if(session('payment_error'))
                        <div class="alert alert-danger">{{ session('payment_error') }}</div>
                    @endif
                    
                    <form id="paymongo-card-form">
                        @csrf
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

<script src="https://js.paymongo.com/v2/"></script>
<script>
    (function() {
        var paymongo = new PayMongo('{{ $public_key }}');
        var payButton = document.getElementById('pay-button');
        var form = document.getElementById('paymongo-card-form');
        
        // Format card number input
        document.getElementById('card_number').addEventListener('input', function(e) {
            var value = e.target.value.replace(/\s/g, '');
            var formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            var cardNumber = document.getElementById('card_number').value.replace(/\s/g, '');
            var expMonth = document.getElementById('exp_month').value;
            var expYear = document.getElementById('exp_year').value;
            var cvc = document.getElementById('cvc').value;
            var cardName = document.getElementById('card_name').value;
            
            if (!cardNumber || !expMonth || !expYear || !cvc || !cardName) {
                alert('Please fill in all card details');
                return;
            }
            
            payButton.disabled = true;
            payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            
            // Create payment method
            paymongo.createPaymentMethod({
                data: {
                    attributes: {
                        type: 'card',
                        details: {
                            card_number: cardNumber,
                            exp_month: parseInt(expMonth),
                            exp_year: parseInt(expYear),
                            cvc: cvc
                        },
                        billing: {
                            name: cardName,
                            email: '{{ $email }}'
                        }
                    }
                }
            }).then(function(paymentMethod) {
                // Create payment
                return paymongo.createPayment({
                    data: {
                        attributes: {
                            amount: {{ $amount }} * 100,
                            currency: '{{ $currency }}',
                            description: 'Wallet Top-up',
                            payment_method: paymentMethod.data.id,
                            statement_descriptor: 'WALLET TOPUP'
                        }
                    }
                });
            }).then(function(payment) {
                // Handle payment success
                window.location.href = "{{ route('wallet-success') }}";
            }).catch(function(error) {
                console.error('PayMongo error:', error);
                alert('Payment failed: ' + (error.message || 'Please try again'));
                payButton.disabled = false;
                payButton.innerHTML = 'Pay {{ $formatted_price }}';
            });
        });
    })();
</script>
@endsection
