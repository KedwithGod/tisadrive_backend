<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payphone</title>
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


    <link rel="stylesheet" href="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.css"> 
    <script type="module" src="https://cdn.payphonetodoesposible.com/box/v1.1/payphone-payment-box.js"></script> 
</head>
<body>
    <div class="center">
        <form id="paymentForm">
            @php
            $transaction_id = str_random(10);
            @endphp
            <div class="form-submit">
                <input type="hidden" name="amount" id="amount" value="{{ $amount * 100 }}">
                <input type="hidden" name="currency" value="{{ $currency_code }}">
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <input type="hidden" name="plan_id" value="{{ $plan_id }}">
                <input type="hidden" name="request_id" value="{{ $request_id }}">
                <input type="hidden" name="payment_for" value="{{ $payment_for }}">
            </br>
                <div id="pp-button"></div>

            </div>
        </form>
    </div>
    <script>
       window.addEventListener('DOMContentLoaded',()=>{

            var payable_amount = document.getElementById('amount').value;
            ppb = new PPaymentButtonBox({
                token: "{{$token}}",
                clientTransactionId: '{{$transaction_id}}',
                amount: payable_amount,
                amountWithoutTax: payable_amount,
                currency: "{{ $currency_code }}",
                storeId:"{{$store_id}}",
                reference:"{{ $transaction_id }}"
            }).render('pp-button');
        })
    </script>
</body>
</html>
