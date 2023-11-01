@extends('layouts.admin.app')

@section('title', translate('Edit Package'))

@push('css_or_js')
{{--    <link href="{{asset('public/assets/back-end')}}/css/select2.min.css" rel="stylesheet"/>--}}
{{--    <meta name="csrf-token" content="{{ csrf_token() }}">--}}
@endpush

@section('content')
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
        <h2 class="h1 mb-0 d-flex align-items-center gap-2">
            <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/employee.png')}}" alt="">
            <span class="page-header-title">
                {{translate('edit_package')}}
            </span>
        </h2>
    </div>
    <!-- End Page Header -->

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <form action="{{route('admin.package.update',[$package['id']])}}" method="post" enctype="multipart/form-data"
                    style="text-align: {{Session::get('direction') === "rtl" ? 'right' : 'left'}};">
                @csrf
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0 d-flex align-items-center gap-2"><span class="tio-user"></span> {{translate('general_Information')}}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="title">{{translate('title')}}</label>
                                    <input type="text" name="title" class="form-control" id="title"
                                        placeholder="{{translate('Ex')}} : {{translate('Permium')}}" value="{{$package['title']}}" required>
                                </div>
                            </div>
                            {{-- <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">{{translate('promo_price')}}</label>
                                    <input type="number" name="promo_price" value="{{$package['promo_price']}}" class="form-control" id="promo_price"
                                        placeholder="{{translate('Ex')}} : 10" required>
                                </div>
                            </div> --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price">{{translate('price')}}</label>
                                    <input type="number" name="price" value="{{$package['price']}}" class="form-control" id="price"
                                        placeholder="{{translate('Ex')}} : 20" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="plan_period">{{translate('package_period')}}</label>
                                    <select name="plan_period" class="form-control" id="plan_period">
                                        <option {{$package['package_type'] == 'daily' ? 'selected' : ''}} value="daily">Daily</option>
                                        <option {{$package['package_type'] == 'weekly' ? 'selected' : ''}} value="weekly">Weekly</option>
                                        <option {{$package['package_type'] == 'monthly' ? 'selected' : ''}} value="monthly" >Monthly</option>
                                        <option {{$package['package_type'] == 'anually' ? 'selected' : ''}} value="anually">Anually</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="limit">{{translate('limit')}}</label>
                                    <input type="number" name="limit" value="{{$package['limit'] }}" class="form-control" id="limit"
                                        placeholder="{{translate('Ex')}} : 1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="order_limit">{{translate('order_limit')}}</label>
                                    <input type="number" name="order_limit" value="{{$package['order_limit'] }}" class="form-control" id="order_limit"
                                        placeholder="{{translate('Ex')}} : 1" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="trial_period">{{translate('trial_period')}}</label>
                                    <input type="number" name="trial_period" value="{{$package['trial_period'] }}" class="form-control" id="trial_period"
                                        placeholder="{{translate('Ex')}} : 1" required>
                                </div>
                            </div>
                            <div class="col-sm-8 mt-1">
                                <div class="d-flex justify-content-between align-items-center border rounded mb-2 px-3 py-2">
                                    <h5 class="mb-0 c1">
                                        {{translate('Status')}}
                                    </h5>
        
                                    <label class="switcher ml-auto mb-0">
                                        <input {{$package['status'] == 1 ? 'checked' : ''}} type="checkbox" name="status" class="switcher_input">
                                        <span class="switcher_control"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">{{translate('Description')}}</label>
                                    <textarea required class="ckeditor form-control" id="description" rows="10" name="description">{{$package['description']}}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3">
                    <button type="reset" id="reset" class="btn btn-secondary">{{translate('reset')}}</button>
                    <button type="submit" class="btn btn-primary">{{translate('submit')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <script src="//cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.ckeditor').ckeditor();
        });
    </script>
@endpush
