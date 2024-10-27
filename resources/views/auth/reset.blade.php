@extends('auth.layout.main')
@section('auth-main')
    <style>
        @font-face {
            font-family: 'circulestd';
            /* Choose a name for your font */
            src: url('{{ asset('assets/fonts/CircularStd-Medium.woff') }}') format('woff');
            /* Specify the path to your TTF file and its format */
        }

        .loginField {
            border-right: none;
            font-size: 20px;
        }

        .fieldIcon {
            border-left: none;
            background: #ffffff;
            border-color: #e3e3e3;
            color: #018be3;
            font-size: 20px;
        }

        .authBG {
            background: url('{{ asset('images/auth/bg1.png') }}');
            height: 100vh;
            background-position: center;
            background-size: cover;
        }

        .form-center {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .authLogo {
            width: 200px;
        }

        .loginHead {
            font-family: circulestd;
            letter-spacing: 02px;
        }

        .authBtn {
            font-family: circulestd;
            font-size: 15px;
        }

        .authLoginBtn {
            font-family: circulestd;
        }

        .authlogombdiv {
            display: none;
        }

        .second_img_div {
            display: none;
        }

        @media only screen and (max-width: 980px) {

            .authLogo {
                /* width: 500px !important; */
                display: none;
            }

            .d-none-mb {
                display: none;
            }



            .loginHead {
                letter-spacing: 02px;
                font-size: 18px;
                margin-bottom: 25px !important;
            }

            /* .authBtn {} */

            /* .authLoginBtn {
                                                                                                                                                                                                                                                                                                                font-size: 16px !important;
                                                                                                                                                                                                                                                                                                                padding: 4px;
                                                                                                                                                                                                                                                                                                            } */

            .loginField {
                font-size: 16px;
            }

            .authBG {
                background: none !important;
                /* background: white !important; */
            }

            .login_mb_res {
                background: white;
                padding: 50px 30px;
                border-radius: 10px;
                box-shadow: 0px 0px 10px 0px #e0e0e0;
                margin: 0 10px;
            }

            .authlogombdiv {
                display: block;
                text-align: center;
                margin-bottom: 30px;
            }

            .authlogmb {
                width: 200px;
            }

            .second_img_div {
                display: block;
                text-align: center;
            }

            .second_img {
                width: 80%;
            }
        }
    </style>
    <div style="position: fixed; top: 0%; width: 100%">
        <x-alert-notification />
    </div>
    <div class="container-fluid authBG">
        <!-- Account Logo -->
        <div class="account-logo mt-3">
            <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/auth/newlogo.png') }}" class="authLogo"
                    alt="Dreamguy's Technologies"></a>
        </div>
        <!-- /Account Logo -->
        <div class="container form-center">
            <div class="row align-items-center login_mb_res">
                <div class="col-lg-5 col-12">
                    <div>

                        <div class="authlogombdiv">
                            <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('images/auth/newlogo.png') }}"
                                    class="authlogmb" alt="Dreamguy's Technologies"></a>
                        </div>
                        <h4 class="text-center text-uppercase mb-5 text-primary loginHead">Reset Password</h4>

                        <form action="{{ route('reset.password') }}" method="post">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="remember_token" value="{{ request()->token ?? '' }}">
                            <div class="form-group mb-4">
                                <div class="input-group">
                                    <input class="form-control loginField" type="password" name="confirm_password"
                                        id="password" placeholder="Password" value="{{ old('password') }}">
                                    <span class="input-group-text fieldIcon">
                                        <i class="fa fa-eye-slash" id="toggle-password"></i>
                                    </span>
                                </div>
                                <x-field-validation errorname="confirm_password" />
                            </div>
                            <div class="form-group mb-4">
                                <div class="input-group">
                                    <input class="form-control loginField" type="password" name="password" id="password"
                                        placeholder="Confirm Password" value="{{ old('password') }}">
                                    <span class="input-group-text fieldIcon">
                                        <i class="fa fa-eye-slash" id="toggle-password"></i>
                                    </span>
                                </div>
                                <x-field-validation errorname="password" />
                            </div>
                            <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <button class="btn btn-primary account-btn w-100 authLoginBtn"
                                        type="submit">Login</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="second_img_div">
                        <img src="{{ asset('images/auth/img1.png') }}" alt="" class="second_img">
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection