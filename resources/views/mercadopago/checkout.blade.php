
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Mercadopago-Checkout</title>

</head>
<style>
	  body {
        text-align: center;
        padding: 40px 0;
        /* background: #EBF0F5; */
      }
	  .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
      }
              .center{
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
</style>
<body>
   <div class="center">
    <img src="{{ asset('assets/img/mercadepago.png')}}" class="img-fluid" width="500px">

		<div class="amount-display text-center"><h1> {{ "$currency" }} <?php echo $amount; ?></h1></div>
		<div class="button"></div>
	</div>
	
	
	<script src="https://sdk.mercadopago.com/js/v2"></script>

	<script>
		var public_key='<?php echo $public_key; ?>';

		var payment_for='<?php echo $payment_for; ?>';


	    // Check payment type and set label accordingly
	    if (payment_for=='wallet') {
	        label = 'Add Money To Wallet';
	    } else {
	        label = 'Pay Now';
	    } 


		const mp = new MercadoPago(public_key,{
			
		});

		const checkout = mp.checkout({

			preference:{
				id:'<?php echo $preference->id; ?>'
			},
			render:{
				container:'.button',
				label: label,
			}
		})
	</script>
</body>
</html>