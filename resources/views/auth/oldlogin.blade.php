@extends('auth.layout.main')
@section('auth-main')
    <div style="position: fixed; top: 0%; width: 100%">
        <x-alert-notification />
    </div>
    <div class="main-wrapper">
        <div class="account-content">
            <div class="container">

                <!-- Account Logo -->
                <div class="account-logo">
                    <a href="{{ route('admin.dashboard') }}"><img src="{{ asset('assets/img/newlogo.png') }}"
                            style="background: black;
                        mix-blend-mode: difference; width: 200px;"
                            alt="Dreamguy's Technologies"></a>
                </div>
                <!-- /Account Logo -->

                <div class="account-box">
                    <div class="account-wrapper">
                        <h3 class="account-title">Login</h3>
                        <p class="account-subtitle">Access to our dashboard</p>

                        <!-- Account Form -->
                        <form action="{{ route('login') }}" method="post">
                            @csrf
                            @method('POST')
                            <div class="form-group">
                                <label>Email Address</label>
                                <input class="form-control" type="text" name="email" value="{{ old('email') }}">
                                <x-field-validation errorname="email" />
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col">
                                        <label>Password</label>
                                    </div>
                                    {{-- <div class="col-auto">
                                        <a class="text-muted" href="forgot-password.html">
                                            Forgot password?
                                        </a>
                                    </div> --}}
                                </div>
                                <div class="position-relative">
                                    <input class="form-control" type="password" name="password"
                                        value="{{ old('password') }}" id="password">
                                    <span class="fa fa-eye-slash" id="toggle-password"></span>
                                </div>
                                <x-field-validation errorname="password" />
                            </div>
                            <div class="form-group text-center">
                                <button class="btn btn-primary account-btn" type="submit">Login</button>
                            </div>
                            {{-- <div class="account-footer">
                                <p>Don't have an account yet? <a href="register.html">Register</a></p>
                            </div> --}}
                        </form>
                        <!-- /Account Form -->

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
