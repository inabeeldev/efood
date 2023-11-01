<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required Meta Tags Always Come First -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Title -->
    <title>{{translate('Branch')}} | {{translate('Membership')}}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="favicon.ico">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&amp;display=swap" rel="stylesheet">
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/style.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">

</head>
@php

    $premuimPlane = 0.0;

@endphp
<body>
<!-- ========== MAIN CONTENT ========== -->
<main id="content" role="main" class="main">
    <div class="auth-wrapper">
        <div class="" style="width: 0;
                        flex-grow: 1;
                        background: none;
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: center; margin-top:80px;">
            <div class="container text-center">
        <div class="row">

            @foreach($planes as $plane)
                <div class="col-lg-6 col-md-6 col-sm-10 pb-4 d-block m-auto">
                    @php
                        $selected_plan = "";
                        if(request('plan') == $plane->id){
                            $selected_plan = "box-shadow: 0px 0px 30px -7px rgba(0,0,0,0.29);";
                        }
                    @endphp
                    <div class="pricing-item" style="{{$selected_plan}}">
                        <!-- Indicator of subscription type -->
                        <div class="pt-4 pb-3" style="letter-spacing: 2px">
                            <h4>{{$plane->title}}</h4>
                        </div>
                        <!-- Price class -->
                        <div class="pricing-price pb-1 text-primary color-primary-text ">
                            <h1 style="font-weight: 1000; font-size: 3.5em;">
                                <span style="   font-size: 20px;">$</span>{{$plane->price}}
                            </h1>
                        </div>
                        <!-- Perks of said subscription -->
                        <div class="pricing-description">
                            <ul class="list-unstyled mt-3 mb-4">
                                <li class="pl-3 pr-3">Help desk support </li>
                                <li class="pl-3 pr-3">Emergency security </li>
                                <li class="pl-3 pr-3">Customer reservations </li>
                                <li class="pl-3 pr-3">Location management</li>
                                <li class="pl-3 pr-3">Order management  {{$plane->price <= 0 ?  "(UPGRADE)" : ""}} </li>
                                <li class="pl-3 pr-3">Food category & subcategory management</li>
                                <li class="pl-3 pr-3">Food variation & add-on management</li>
                                <li class="pl-3 pr-3">Food item & set menu management</li>
                                <li class="pl-3 pr-3">Chat with customer  {{$plane->price <= 0 ?  "(UPGRADE)" : ""}}</li>
                                <li class="pl-3 pr-3">Send custom notification  {{$plane->price <= 0 ?  "(UPGRADE)" : ""}}</li>
                                <li class="pl-3 pr-3">Coupon management  {{$plane->price <= 0 ?  "(UPGRADE)" : ""}}</li>
                                <li class="pl-3 pr-3">Customer management  {{$plane->price <= 0 ?  "(UPGRADE)" : ""}}</li>
                                <li class="pl-3 pr-3">Delivery man management  {{$plane->price <= 0 ?  "(UPGRADE)" : ""}}</li>
                                <li class="pl-3 pr-3">Rich Analytics & Reports {{$plane->price <= 0 ?  "(UPGRADE)" : ""}}</li>
                                <li class="pl-3 pr-3">Restaurant business settings</li>
                            </ul>
                        </div>
                        <!-- Button -->
                        <div class="pricing-button pb-4">
                            <input type="hidden" name="plane_name" value="{{$plane->title}}">
                            <input type="hidden" name="price" value="{{$plane->price*100}}">
                            <input type="hidden" name="plane" value="{{$plane->id}}">
                            @php
                                $premuimPlane = $plane->price <= 0 ? 0 : $plane->price;
                            @endphp
                            <a data-plan="{{$plane->id}}" href="#"  {{$plane->price <= 0 ?  "disabled" : ""}} type="button" class=" {{$plane->price <= 0 ?  "disabled" : "submit_form"}} btn btn-lg btn-select {{ $plane->price <= 0 ? 'btn-success' : 'btn-primary' }}  w-75"> {{$plane->price <= 0 ?  "SELECTED" : "SELECT"}}</a>
                        </div>
                    </div>
                </div>
            @endforeach




        </div>
    </div>
        </div>

        <!-- Content -->
        <!--<div class="auth-wrapper-right">-->



            <!-- Card -->
            <!--<div class="auth-wrapper-form">-->
            <!--        <div class="auth-header" style="text-align: -webkit-center;">-->
            <!--            <div class="" style="width:200px; height:auto;">-->
            <!--                <div class="auth-left-cont" style>-->
            <!--                    @php($restaurant_logo=\App\Model\BusinessSetting::where(['key'=>'logo'])->first()->value)-->
            <!--                    <img width="310" src="{{asset('storage/app/public/restaurant/'.$restaurant_logo)}}"-->
            <!--                         onerror="this.src='{{asset('public/assets/admin/img/logo.png')}}'">-->
            <!--                </div>-->
            <!--            </div>-->
                       
            <!--        </div>-->
                    
                    <form action="{{ route('branch.get_member_ship') }}" method="post" style="text-align:center;" id="stripe-form">
                        @csrf
                        <input type="hidden" name="selected_plane" id="selected_plane" value="{{request('plan')}}">
                        <!--<button type="submit" class="btn btn-primary btn-lg w-75" name="" id="" >Process</button>-->
                    </form>
            <!--</div>-->
            <!-- End Card -->
        <!--</div>-->
        <!-- End Content -->
    </div>
</main>
<!-- ========== END MAIN CONTENT ========== -->


<!-- JS Implementing Plugins -->
<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>

<!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>

{!! Toastr::message() !!}

@if ($errors->any())
    <script>
        @foreach($errors->all() as $error)
        toastr.error('{{$error}}', Error, {
            CloseButton: true,
            ProgressBar: true
        });
        @endforeach
    </script>
@endif

<!-- JS Plugins Init. -->
<script>
    $(document).on('ready', function () {
        $('.submit_form').on('click',function(e){
            $('#selected_plane').val($(this).data('plan'));
            $('#stripe-form').submit();
        })
        // $('.btn-select').on('click',function(e){
        // $('.btn-select').removeClass('btn-success');
        // $('.btn-select').addClass('btn-primary');
        // $(this).removeClass('btn-primary');
        // $(this).addClass('btn-success');
        // $('#stripe-form').data('amount',($(this).prev().prev().val()*100));
        // $('#stripe-form').data('name',$(this).prev().prev().prev().val());
        // $('#selected_plane').val($(this).prev().val());
        

        // })
    });
    
    $(function() {
   
    var $form = $(".require-validation");
   
    $('form.require-validation').bind('submit', function(e) {
        var $form     = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;
        $errorMessage.addClass('hide');
  
        $('.has-error').removeClass('has-error');
        $inputs.each(function(i, el) {
          var $input = $(el);
          if ($input.val() === '') {
            $input.parent().addClass('has-error');
            $errorMessage.removeClass('hide');
            e.preventDefault();
          }
        });
   
        if (!$form.data('cc-on-file')) {
          e.preventDefault();
          Stripe.setPublishableKey($form.data('stripe-publishable-key'));
          Stripe.createToken({
            number: $('.card-number').val(),
            cvc: $('.card-cvc').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val()
          }, stripeResponseHandler);
        }
  
  });
  
  function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            /* token contains id, last4, and card type */
            var token = response['id'];
               
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }
   
});
</script>



<!-- IE Support -->
<script>
    if (/MSIE \d|Trident.*rv:/.test(navigator.userAgent)) document.write('<script src="{{asset('public/assets/admin')}}/vendor/babel-polyfill/polyfill.min.js"><\/script>');
</script>
</body>
</html>
