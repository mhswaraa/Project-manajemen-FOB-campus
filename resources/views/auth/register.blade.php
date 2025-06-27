<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-t">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('fonts/icomoon/style.css') }}">

    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <title>Register</title>
  </head>
  <body>
  

  
  <div class="content">
    <div class="container">
      <div class="row">
        <div class="col-md-6 order-md-2">
          <img src="{{ asset('images/undraw_remotely_2j6y.svg') }}" alt="Image" class="img-fluid">
        </div>
        <div class="col-md-6 contents">
          <div class="row justify-content-center">
            <div class="col-md-8">
              <div class="mb-4">
              <h3>Daftar Akun Baru</h3>
              <p class="mb-4">Buat akun Anda untuk memulai mengelola proyek.</p>
            </div>
            
            {{-- FORM REGISTRASI YANG DISESUAIKAN DENGAN LARAVEL --}}
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                {{-- INPUT NAMA LENGKAP --}}
                <div class="form-group first">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                </div>
                @error('name')
                    <div class="text-danger mb-3" style="font-size: 14px;">{{ $message }}</div>
                @enderror

                {{-- INPUT EMAIL --}}
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                </div>
                @error('email')
                    <div class="text-danger mb-3" style="font-size: 14px;">{{ $message }}</div>
                @enderror

                {{-- INPUT PASSWORD --}}
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password">
                </div>
                @error('password')
                    <div class="text-danger mb-3" style="font-size: 14px;">{{ $message }}</div>
                @enderror

                {{-- INPUT KONFIRMASI PASSWORD --}}
                <div class="form-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
                </div>

                {{-- INPUT ROLE --}}
                <div class="form-group last mb-4">
                    <label for="role">Daftar sebagai</label>
                    <select class="form-control @error('role') is-invalid @enderror" id="role" name="role" required>
                        <option value="" disabled {{ old('role') ? '' : 'selected' }}></option>
                        <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                        <option value="ceo"      {{ old('role') === 'ceo'      ? 'selected' : '' }}>CEO</option>
                        <option value="investor" {{ old('role') === 'investor' ? 'selected' : '' }}>Investor</option>
                        <option value="penjahit" {{ old('role') === 'penjahit' ? 'selected' : '' }}>Penjahit Borongan</option>
                    </select>
                </div>
                @error('role')
                    <div class="text-danger mb-3" style="font-size: 14px;">{{ $message }}</div>
                @enderror

              <input type="submit" value="Daftar Akun" class="btn text-white btn-block btn-primary">

              {{-- LINK LOGIN --}}
               <span class="d-block text-center my-4 text-muted"> Sudah punya akun? <a href=""Masuk di sini</a></span>
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
