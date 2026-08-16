@include('layouts.app')
@include('layouts.header')
<div class="siddhi-popular">
    <div class="container">
        <div class="transactions-banner p-4 rounded">
            <div class="row align-items-center text-center">
                <h3 class="font-weight-bold h4 text-light">{{ trans('lang.cashback_list') }}</h3>
            </div>
        </div>
        <div class="text-center py-5" style="display:none">
            <p class="h4 mb-4"><i class="feather-search bg-primary rounded p-2"></i></p>
            <p class="font-weight-bold text-dark h5">{{ trans('lang.nothing_found') }}</p>
            <p>{{ trans('lang.please_try_again') }}</p>
        </div>
        <div id="cashback_list" class="res-search-list"></div>
        <div class="row fu-loadmore-btn">
            <a class="page-link loadmore-btn" href="javascript:void(0);" id="loadmore" onclick="moreload()" data-dt-idx="0" tabindex="0">{{ trans('lang.load_more') }}</a>
        </div>
    </div>
</div>
@include('layouts.footer')
@include('layouts.nav')
<script type="text/javascript">
    var newdate = new Date();
    var todaydate = new Date(newdate.setHours(23, 59, 59, 999));
    var ref = database.collection('cashback').where('isEnabled', '==', true).where('startDate', '<=', newdate);
    var pagesize = 10;
    var offest = 1;
    var end = null;
    var endarray = [];
    var start = null;
    var cashback_list = '';

    var currentCurrency = '';
    var currencyAtRight = false;
    var decimal_digits = 0;
    var refCurrency = database.collection('currencies').where('isActive', '==', true);
    refCurrency.get().then(async function(snapshots) {
        var currencyData = snapshots.docs[0].data();
        currentCurrency = currencyData.symbol;
        currencyAtRight = currencyData.symbolAtRight;
        decimal_digits = currencyData.decimal_degits;
    });

    $(document).ready(function() {
        jQuery("#loadmore").hide();
        $("#data-table_processing").show();
        setTimeout(function() {
            getCashbackList();
        }, 3000);
    })
    async function getCashbackList() {
        cashback_list = document.getElementById('cashback_list');
        cashback_list.innerHTML = '';
        var html = '';

        ref.limit(pagesize).get().then(async function(snapshots) {
            if (snapshots.docs.length > 0) {
                html = buildHTML(snapshots);
                cashback_list.innerHTML = html;
                start = snapshots.docs[snapshots.docs.length - 1];
                endarray.push(snapshots.docs[0]);
                if (snapshots.docs.length < pagesize) {
                    jQuery("#loadmore").hide();
                } else {
                    jQuery("#loadmore").show();
                }
            } else {
                html = html + "<h5 class='font-weight-bold text-center mx-auto p-3'>{{ trans('lang.no_results') }}</h5>";
                cashback_list.innerHTML = html;
            }
        });

        $("#data-table_processing").hide();
    }

    function buildHTML(snapshots) {
        var html = '';
        var alldata = [];
        var number = [];
        <?php $id = null;
        if (Auth::user()) {
            $id = Auth::user()->getvendorId();
        } ?>
        var userId = "{{ $id }}";
        snapshots.docs.forEach((listval) => {
            var datas = listval.data();
            const isActive = datas?.endDate?.toDate && datas.endDate.toDate().getTime() >= newdate.getTime();
            const isAllCustomer = datas?.allCustomer === true;
            const allowedIds = datas?.customerIds || [];
            if (isActive) {
                if (!userId) {
                    if (isAllCustomer) {
                        alldata.push(datas);
                    }
                } else {

                    if (isAllCustomer || allowedIds.includes(userId)) {
                        alldata.push(datas);
                    }
                }
            }

        });
        alldata.forEach((listval) => {
            var val = listval;

            var endDate = '';
            var endTime = '';

            if (val.hasOwnProperty('endDate') && val.endDate) {
                try {
                    endDate = val.endDate.toDate().toDateString();
                    endTime = val.endDate.toDate().toLocaleTimeString('en-US');
                } catch (err) {}
            }
            var price_val = '';

            if (val.cashbackType == 'Percent') {
                price_val = val.cashbackAmount + "%";
            } else {
                if (currencyAtRight) {
                    price_val = parseFloat(val.cashbackAmount).toFixed(decimal_digits) + "" + currentCurrency;
                } else {
                    price_val = currentCurrency + "" + parseFloat(val.cashbackAmount).toFixed(decimal_digits);
                }

            }
            var minimumPurchase = '';
            var maximumDiscount='';
            if (currencyAtRight) {
                minimumPurchase = parseFloat(val.minumumPurchaseAmount).toFixed(decimal_digits) + "" + currentCurrency;
                maximumDiscount = parseFloat(val.maximumDiscount).toFixed(decimal_digits) + "" + currentCurrency;

            } else {
                minimumPurchase = currentCurrency + "" + parseFloat(val.minumumPurchaseAmount).toFixed(decimal_digits);
                maximumDiscount = currentCurrency + "" +parseFloat(val.maximumDiscount).toFixed(decimal_digits);

            }

            html = html + '<div class="transactions-list-wrap mt-4"><div class="bg-white px-4 py-3 border rounded-lg mb-3 transactions-list-view shadow-sm"><div class="gold-members d-flex align-items-center transactions-list">';

            html = html + '<div class="media-body"><h6 class="date">' + val.title + '</h6>';
            //<p class="text-dark offer-des mt-2"> {{ trans('lang.minimum_spent') }} ' + minimumPurchase + '</p>';

            html = html + "<p class='text-dark mb-0 offer-address'>{{ trans('lang.minimum_spent') }} " + minimumPurchase + " | {{ trans('lang.valid_till') }} " + endDate + "</p><p class='text-danger'>{{trans('lang.maximum_discount_up_to')}} "+maximumDiscount+"</p>";
            html = html + '</div></div>';
            html = html + '<div class="float-right ml-auto"><span class="price font-weight-bold h4">' + price_val + '</span>';
            html = html + '</div> </div></div></div>  ';
        });
        return html;
    }

    async function moreload() {
        if (start != undefined || start != null) {
            jQuery("#data-table_processing").hide();
            listener = ref.startAfter(start).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html = '';
                html = await buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    cashback_list.innerHTML += html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    if (endarray.indexOf(snapshots.docs[0]) != -1) {
                        endarray.splice(endarray.indexOf(snapshots.docs[0]), 1);
                    }
                    endarray.push(snapshots.docs[0]);
                    if (snapshots.docs.length < pagesize) {
                        jQuery("#loadmore").hide();
                    } else {
                        jQuery("#loadmore").show();
                    }
                }
            });
        }
    }

    async function prev() {
        if (endarray.length == 1) {
            return false;
        }
        end = endarray[endarray.length - 2];
        if (end != undefined || end != null) {
            jQuery("#data-table_processing").show();
            listener = ref.startAt(end).limit(pagesize).get();
            listener.then(async (snapshots) => {
                html = '';
                html = await buildHTML(snapshots);
                jQuery("#data-table_processing").hide();
                if (html != '') {
                    cashback_list.innerHTML = html;
                    start = snapshots.docs[snapshots.docs.length - 1];
                    endarray.splice(endarray.indexOf(endarray[endarray.length - 1]), 1);
                    if (snapshots.docs.length < pagesize) {
                        jQuery("#users_table_previous_btn").hide();
                    }
                }
            });
        }
    }
</script>
