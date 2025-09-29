<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Flexpaie</title>
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

        #start-payment-button:disabled  {
            background:rgb(54, 211, 51);
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
        <img src="{{ asset('assets/img/flexpaie.png')}}" class="img-fluid">
        <h1>{{ $amount }} {{ $currency_code }}</h1>
        <form id="paymentForm">
            @csrf
            <input type="hidden" id="amount" name="amount" value="{{ $amount }}">
            <input type="hidden" name="currency" value="{{ $currency_code }}">
            <input type="hidden" name="user_id" value="{{ $user_id }}">
            <input type="hidden" name="plan_id" value="{{ $plan_id }}">
            <input type="hidden" name="request_id" value="{{ $request_id }}">
            <input type="hidden" name="payment_for" value="{{ $payment_for }}">
            </br>

            @if($payment_for=="wallet")
            <button type="submit" id="start-payment-button">Add To Wallet</button>
            @else
            <button type="submit" id="start-payment-button">Pay</button>
            @endif
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
        $("#paymentForm").on('submit',function(e) {
            $('#start-payment-button').attr('disabled', true);
            e.preventDefault();
            $.ajax({
                url: "{{ route('flexpaie.pay') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (response) {
                    $('#start-payment-button').attr('disabled',false);
                    if(response.status == 200){
                        const message = response.body.message;
                        swal("Success", message, "success")
                        .then(() => {
                            window.location.href = "{{ route('flexpaie.checkout') }}?amount={{ $amount }}&currency_code={{ $currency_code }}&payment_for={{ $payment_for }}&user_id={{ $user_id }}&plan_id={{ $plan_id }}&request_id={{ $request_id }}";

                        });
                    }else{
                        swal('Failed', response.message, "warning")
                        .then(() => {
                            window.location.href = "{{ route('flexpaie.checkout') }}";
                        });
                    }
                },
                error: function (error) {
                    $('#start-payment-button').attr('disabled',false);
                    console.error(error);
                    swal('Failed', error.responseJSON.body.message, "warning")
                    .then(() => {
                        window.location.href = "{{ route('failure') }}";
                    });
                    
                }
            });
        })
    </script>
</body>
</html>
