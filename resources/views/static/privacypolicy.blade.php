@include('layouts.app')

@include('layouts.header')

<div class="container">
	<div class="privacy_policy mt-5 mb-5" id="privacy_policy"></div>
</div>

@include('layouts.footer')

<script type="text/javascript">

		jQuery("#data-table_processing").show();

		var privacyPolicyRef = database.collection('settings').doc('privacyPolicy');
		privacyPolicyRef.get().then(async function (privacyPolicySnapshots) {
	        var privacyPolicyData = privacyPolicySnapshots.data();
			$('#privacy_policy').html(privacyPolicyData.privacy_policy);
			jQuery("#data-table_processing").hide();
		});	
			
</script>