@include('layouts.app')
@include('layouts.header')

@php
$cityToCountry = json_decode(file_get_contents(public_path('tz-cities-to-countries.json')), true);
$countriesJs = [];
foreach ($cityToCountry as $key => $value) {
    $countriesJs[$key] = $value;
}
@endphp

<div class="siddhi-checkout">
    <div class="container position-relative">
        <div class="py-5 row">
            <div class="col-md-12 mb-3">
                <div>
                    <div class="siddhi-cart-item mb-3 rounded shadow-sm bg-white overflow-hidden">
                        <div class="siddhi-cart-item-profile bg-white p-3">
                            <div class="card card-default">
                                <?php $authorName = @$user_wallet['user']['name']; ?>
                                @if($message = Session::get('success'))
                                    <div class="py-5 linus-coming-soon d-flex justify-content-center align-items-center">
                                        <div class="col-md-6">
                                            <div class="text-center pb-3">
                                                <h1 class="font-weight-bold">
                                                    <?php if (@$authorName) echo @$authorName . ","; ?> 
                                                    {{trans('lang.your_transaction_has_been_successful')}}
                                                </h1>
                                                <p>
                                                    {{trans('lang.check_your_transaction_status_in')}} 
                                                    <a href="{{route('transactions')}}" class="font-weight-bold text-decoration-none text-primary">
                                                        {{trans('lang.my_transactions')}} 
                                                    </a> {{trans('lang.about_next_steps_information')}}
                                                </p>
                                            </div>
                                            <div class="bg-white rounded text-center p-4 shadow-sm">
                                                <h1 class="display-1 mb-4">🎉</h1>
                                                <p class="small text-muted">{{trans('lang.wallet_amount_credit_msg')}}</p>
                                                <a href="{{route('transactions')}}" class="btn rounded btn-primary btn-lg btn-block remove_hover">
                                                    {{trans('lang.transactions')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="data_table_processing_order" class="dataTables_processing panel panel-default" style="display: none;">
    {{trans('lang.Processing')}}
</div>

@include('layouts.footer')
@include('layouts.nav')

@if($message = Session::get('success'))
    <script src="https://unpkg.com/geofirestore/dist/geofirestore.js"></script>
    <script type="text/javascript">
        cityToCountry = JSON.parse('<?php echo json_encode($countriesJs); ?>');
        var userTimeZone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        var userCity = userTimeZone.split('/')[1];
        var userCountry = cityToCountry[userCity];
        var fcmToken = '';
        var id_order = "<?php echo uniqid(); ?>";

        var razorpaySettings = database.collection('settings').doc('razorpaySettings');
        
        // Check if payment_status is set to 1 OR specifically check for PhonePe success
        <?php if ((isset($user_wallet['payment_status']) && $user_wallet['payment_status'] == 1) || 
                  (isset($user_wallet['data']['payment_status']) && $user_wallet['data']['payment_status'] == true)): ?>
            $("#data_table_processing_order").show();
            var final_wallet_balance = 0.0;
            var amount = Number({{$user_wallet['data']['amount'] ?? 0}}); 
            var wallet_amount = Number(@json($user_wallet['user']['wallet_amount'] ?? 0)); 
            var final_wallet_balance = amount + wallet_amount; 
            
            database.collection('users').doc(user_uuid).update({
                'wallet_amount': final_wallet_balance.toString(),
            }).then(function (result) {
                $("#data_table_processing_order").hide();
            }).catch(function(error) {
                console.error("Error updating wallet amount:", error);
                $("#data_table_processing_order").hide();
            });

            var createdAt = firebase.firestore.FieldValue.serverTimestamp();
            var id = "{{uniqid()}}";
            var paymentMethod = "{{$user_wallet['data']['payment_method'] ?? ''}}";
            var transactionId = "{{$user_wallet['transaction_id'] ?? ''}}";
            
            database.collection('wallet').doc(id).set({
                'amount': amount.toString(),
                'date': createdAt,
                'isTopUp': true,
                'note': "Wallet Topup",
                'payment_method': paymentMethod,
                'payment_status': "success",
                'transactionUser': "user",
                'transactionId': transactionId,
                'id': id,
                'user_id': user_uuid
            }).then(function (result) {
                $("#data_table_processing_order").hide();
            }).catch(function(error) {
                console.error("Error creating wallet entry:", error);
                $("#data_table_processing_order").hide();
            });
        <?php else: ?>
            console.log("Payment status not set correctly.");
        <?php endif; ?>
    </script>
@endif
