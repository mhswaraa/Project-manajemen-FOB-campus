<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <title>Login</title>
  </head>
  <body>
  

  
  <div class="content">
    <div class="container">
      <div class="row">
        <div class="col-md-6 order-md-2">
          <img src="{{ asset('images/undraw_file_sync_ot38.svg') }}" alt="Image" class="img-fluid">
        </div>
        <div class="col-md-6 contents">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="mb-4">
              <h3><strong>Project Management</strong></h3>
              <p class="mb-4">Platform terintegrasi untuk mengelola proyek fashion Anda.</p>
            </div>
            
            {{-- FORM LOGIN YANG DISESUAIKAN DENGAN LARAVEL --}}
            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Menampilkan pesan status (jika ada) --}}
                @if (session('status'))
                    <div class="alert alert-success mb-3">
                        {{ session('status') }}
                    </div>
                @endif
                
                {{-- INPUT EMAIL --}}
                <div class="form-group first">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                </div>
                @error('email')
                    <div class="text-danger mb-3" style="font-size: 14px;">{{ $message }}</div>
                @enderror

                {{-- INPUT PASSWORD --}}
                <div class="form-group last mb-4">
                    <label for="password">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="current-password">
                </div>
                 @error('password')
                    <div class="text-danger mb-3" style="font-size: 14px;">{{ $message }}</div>
                @enderror
              
              <div class="d-flex mb-5 align-items-center">
                {{-- REMEMBER ME --}}
                <label class="control control--checkbox mb-0"><span class="caption">Ingat saya</span>
                  <input type="checkbox" name="remember" id="remember_me"/>
                  <div class="control__indicator"></div>
                </label>

                {{-- LUPA PASSWORD --}}
                @if (Route::has('password.request'))
                    <span class="ml-auto"><a href="{{ route('password.request') }}" class="forgot-pass">Lupa Password</a></span> 
                @endif
              </div>

              <input type="submit" value="Log In" class="btn text-white btn-block btn-primary">

              {{-- LINK DAFTAR --}}
               @if (Route::has('register'))
                <span class="d-block text-center my-4 text-muted"> Belum punya akun? <a href="{{ route('register') }}">Daftar di sini</a></span>
               @endif
            </form>
            </div>
          </div>
          
        </div>
        
      </div>
    </div>
  </div>

  
    <script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('js/popper.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
  </body>
</html>
