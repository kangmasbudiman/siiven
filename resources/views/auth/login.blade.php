<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login - {{ Site('nama_toko') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/bootstrap/dist/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('sufee-admin/vendors/font-awesome/css/font-awesome.min.css') }}">

    <style>
        body {
            background: linear-gradient(135deg, #d7b18a, #b98a63);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: "Open Sans", sans-serif;
        }

        .login-wrapper {
            width: 420px;
            background: #ffffff;
            border-radius: 18px;
            padding: 35px 38px;
            box-shadow: 0px 12px 45px rgba(0, 0, 0, 0.08);
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .login-logo {
            width: 65px;
            height: 65px;
            background: #f1e5d1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin: auto;
            margin-bottom: 18px;
            color: #8f6f48;
        }

        .title {
            font-weight: 700;
            text-align: center;
            font-size: 22px;
            margin-bottom: 6px;
            color: #3b3b3b;
        }

        .subtitle {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-control {
            border-radius: 10px;
            height: 45px;
            border: 1.5px solid #e4dfdf;
        }

        .form-control:focus {
            border-color: #c9b08b;
            box-shadow: 0 0 0 0.15rem rgba(201, 176, 139, 0.35);
        }

        .btn-login {
            background: linear-gradient(135deg, #6340ed, #b98a63);
            border: none;
            border-radius: 10px;
            width: 100%;
            height: 48px;
            color: white;
            font-weight: 600;
            margin-top: 10px;
        }

        .btn-login:hover {
            opacity: 0.9;
        }

        .extras {
            text-align: center;
            margin-top: 18px;
            color: #888;
            font-size: 14px;
        }

        .extras a {
            color: #b98a63;
            font-weight: 600;
            text-decoration: none;
        }

        .extras a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body>

    <div class="login-wrapper">

        <div class="login-logo">
            😊
        </div>

        <h3 class="title">Selamat datang kembali</h3>
         <a href="{{ route('login') }}">
<h3 class="text-black font-weight-bold text-center">{{ Site('nama_toko') }}</h3>
                       
                    </a>
        <p class="subtitle">Silahkan login untuk melanjutkan</p>
 
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible py-2">
                {{ session('error') }}
                <button class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <label class="small mb-1">Username</label>
            <input type="text" name="username"
                   value="{{ old('username') }}"
                   class="form-control mb-3 @error('username') is-invalid @enderror"
                   placeholder="Enter your username" autofocus>
            @error('username') <small class="text-danger">{{ $message }}</small> @enderror

            <label class="small mb-1">Password</label>
            <input type="password" name="password"
                   class="form-control mb-2 @error('password') is-invalid @enderror"
                   placeholder="Enter your password">
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror

           

            <button type="submit" class="btn-login">Sign in</button>
        </form>

    
    </div>

<script src="{{ asset('sufee-admin/vendors/jquery/dist/jquery.min.js') }}"></script>
<script src="{{ asset('sufee-admin/vendors/bootstrap/dist/js/bootstrap.min.js') }}"></script>

</body>
</html>
