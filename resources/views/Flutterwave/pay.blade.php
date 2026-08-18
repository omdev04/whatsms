<script src="https://checkout.flutterwave.com/v3.js"></script>

<script>
    var tx_ref_id = new Date().toISOString().replace(/[-:.]/g, "") + '-' + Math
        .floor((Math.random() * 1000000000)) + '-fltwp-' +
        new Date().toISOString().split('T')[0];

    var data = @json($data);
    var Checkout = FlutterwaveCheckout({
        public_key: data.public_key,
        tx_ref: tx_ref_id,
        amount: data.price,
        currency: data.currency,
        redirect_url: data.redirect_url,
        customer: {
            email: data.email,
            name: data.name,
        },
        meta: {
            meta: data.meta ?? '',
        },
        customizations: {
            title: data.title ?? null,
            logo: data.logo ?? null,
        },
        // callback: function(payment) {},
    });
</script>

</html>
