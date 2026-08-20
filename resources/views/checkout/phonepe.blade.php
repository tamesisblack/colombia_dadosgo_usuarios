@extends('layouts.app')
@section('content')
<div style="text-align: center; padding: 50px; min-height: 200px;">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Initiating PhonePe payment...</span>
    </div>
    <p class="mt-3">Redirecting to PhonePe...</p>
</div>

<script>

(function() {
    const apiUrl = '{{ $phonepe_url }}';
    const payload = JSON.parse('{{ $phonepe_payload }}');
    const bearerToken = '{{ $phonepe_bearer }}';

 

    fetch(apiUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'Authorization': 'O-Bearer ' + bearerToken   // ← space after O- is required
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
      

        // OFFICIAL v2 RESPONSE: redirectUrl is at ROOT level
        if (data.redirectUrl) {
            window.location.href = data.redirectUrl;
        } 
        else if (data.data && data.data.redirectUrl) {   // fallback for some responses
            window.location.href = data.data.redirectUrl;
        } 
        else {
            console.error('No redirectUrl in response:', data);
            alert('No se pudo iniciar el pago: ' + (data.message || JSON.stringify(data)));
            window.location.href = '{{ route("success") }}';
        }
    })
    .catch(error => {
        console.error('Fetch Error:', error);
        alert('Error de red al iniciar PhonePe');
        window.location.href = '{{ route("checkout") }}';
    });
})();

</script>
@endsection
