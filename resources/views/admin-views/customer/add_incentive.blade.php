@extends('layouts.admin.app')

@section('title', translate('Withdraw Funds'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4">
            <h2 class="h1 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/deliveryman.png')}}" alt="">
                <span class="page-header-title">
                    {{translate('Withdraw Funds')}}
                </span>
            </h2>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center mb-4 justify-content-between">
            <h3 class="h3 mb-0 d-flex align-items-center gap-2">
                <img width="20" class="avatar-img" src="{{asset('public/assets/admin/img/icons/salary.png')}}" alt="">
                <span>{{translate('wallet_balance')}} =></span>
            </h3>
            <span class="balance1 h2">${{ auth('branch')->user()->wallet_amount }}</span>
        </div>
        <!-- End Page Header -->


        <div class="row g-2">
            <div class="col-12">
                <form action="{{route('branch.funds.request-withdraw')}}" method="post" >
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ auth('branch')->id() }}">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0 d-flex align-items-center gap-2 mb-0">
                                <i class="tio-user"></i>
                                {{translate('General_Information')}}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Account Title')}}</label>
                                        <input value="{{old('account_title')}}" type="text" name="account_title" class="form-control" placeholder="{{translate('Account Title')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Bank Name')}}</label>
                                        <input value="{{old('bank_name')}}" type="text" name="bank_name" class="form-control" placeholder="{{translate('Bank Name')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Account Number')}}</label>
                                        <input value="{{old('account_number')}}" type="text" name="account_no" class="form-control" placeholder="{{translate('Account Number')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Routing Number')}}</label>
                                        <input value="{{old('routing_number')}}" type="text" name="routing_number" class="form-control" placeholder="{{translate('Routing Number')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label">{{translate('Amount to Withdraw')}}</label>
                                        <input value="{{old('withdraw_amount')}}" type="number" name="amount" class="form-control" placeholder="{{translate('Amount to Withdraw')}}" required>
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

    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '230px',
                groupClassName: 'col-6 col-lg-4',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('{{\App\CentralLogics\translate("Please only input png or jpg type file")}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{\App\CentralLogics\translate("File size too big")}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>

    <script>
        // INITIALIZATION OF SHOW PASSWORD
        // =======================================================
        $('.js-toggle-password').each(function () {
            new HSTogglePassword(this).init()
        });
    </script>
@endpush

