<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EasyPaisa</title>
    <style>
        body {
            font-size: 14px;
            font-family: "Moderat","Inter",sans-serif;
            font-weight: 400;
            color: #333;
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
        #start-payment-button {
            background: #0a8708;
            color: #ffffff;
            padding: 10px;
            font-size:16px;
            border: 1px solid #0a8708;
            border-radius: 10px;
        }
    </style>


</head>
<body>
    <div class="center">
        <img src="{{ asset('assets/img/easypaisa.png')}}" width="100%" class="img-fluid">
        <h1>{{ $amount }} {{ $currency_code }}</h1>
        <form id="paymentForm">
            <?php $token =csrf_token(); ?>
        <div class="form-submit">
            <input type="hidden" id="storeId" name="storeId" value="{{ $storeId }}">
            <input type="hidden" id="amount" name="amount" value="{{ $amount }}">
            <input type="hidden" id="orderId" name="orderId" value="{{ $orderId }}">
            <input type="hidden" id="token" name="token" value="{{ $token }}">
            <input type="hidden" id="encryptedHashRequest" name="encryptedHashRequest" value="{{ $encryptedHashRequest }}">
            <input type="hidden" id="timeStamp" name="timeStamp" value="{{ $timeStamp }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="currency" value="{{ $currency_code }}">
            <input type="hidden" name="plan_id" value="{{ $plan_id }}">
            <input type="hidden" name="request_id" value="{{ $request_id }}">
            <input type="hidden" name="payment_for" value="{{ $payment_for }}">
        </br>
             @if($payment_for=="wallet")
            <button type="submit" name="pay" class="btn btn-primary" id="submitPaymentMethod" onClick="loadIframe()">Add To Wallet</button>
            @else
            <button type="submit" name="pay" class="btn btn-primary" id="submitPaymentMethod" onClick="loadIframe()">Pay</button>
            @endif
        </div>
    </form>
</div>
<div>
    <iframe id="easypay-iframe" name="easypay-iframe" src="about:blank" width="100%" height="500px"></iframe>
</div>
    <script>

        const url = "{{$url}}";
        function loadIframe(iframeName, url) {
            var storeID = document.getElementById("storeId").value;
            var amount = document.getElementById("amount").value;
            var orderID = document.getElementById("orderId").value;
            var token = document.getElementById("token").value;
            var encryptedHashRequest = document.getElementById("encryptedHashRequest").value;
            var params = {
                storeId: storeID,
                orderId: orderID,
                transactionAmount: amount,
                transactionType: "InitialRequest",
                tokenExpiry: token,
                encryptedHashRequest: encryptedHashRequest,
            };
            var $iframe = $('#easypay-iframe');
            if ( $iframe.length ) {
                if(params.storeId != "" && params.orderId !="") {
                    var str = jQuery.param( params);
                    $iframe.attr('src',url+'?'+str); // here you can change src
                }
                return false;
            }
            return true;
        }
        $( "#submitPaymentMethod" ).click(function() {
            $("#iframe-class").addClass("show-iframe");
            return loadIframe('easypay-iframe','http://localhost:3000/:0');
        });
    </script>
</body>
</html>
