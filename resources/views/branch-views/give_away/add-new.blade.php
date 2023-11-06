@extends('layouts.branch.app')

@section('title', translate('add_new_giveaway'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('add_new_giveaway')}}
                </span>
            </h2>
        </div>

        <!-- End Page Header -->


        <div class="row g-2">
            <div class="col-12">
                <form action="{{route('branch.give-away.add-new.store')}}" method="post">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 d-flex align-items-center gap-2 mb-0">
                                <i class="tio-user"></i>
                                {{translate('General_Information')}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Choose')}} {{translate('Product')}}</label>
                                        <select name="product_id" class="form-control js-select2-custom">
                                            <option value="" selected>---{{translate('select')}}---</option>
                                            @foreach($products as $product)
                                                <option value="{{$product['id']}}">{{$product['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Status')}}</label>
                                        <select name="status" class="form-control js-select2-custom" >
                                            <option value="">---{{translate('select')}}---</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inctive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group text-center">
                                        <button type="reset" id="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                                        <button type="submit" class="btn btn-primary">{{translate('Submit')}}</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>



<script>
    $(document).on('ready', function () {
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });
</script>
@endpush

