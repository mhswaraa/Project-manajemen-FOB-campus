<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Project Management - Solusi Kolaborasi Fashion</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
        
        <!-- AOS Animation Library -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

        <style>
            /* Custom styles untuk gradien dan efek tambahan */
            .hero-gradient {
                background: linear-gradient(to top, rgba(255,255,255,1) 5%, rgba(255,255,255,0) 50%);
            }
            .cta-gradient {
                 background: linear-gradient(45deg, #4f46e5, #7c3aed);
            }
        </style>
    </head>
    <body class="antialiased font-sans bg-slate-50 text-slate-800">
        <div x-data="{ open: false }" @keydown.window.escape="open = false">
            <!-- Header -->
            <header class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-lg shadow-sm">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center justify-between h-16">
                        <!-- Logo -->
                        <div class="flex-shrink-0">
                             <a href="/" class="flex items-center gap-2">
                                <x-application-logo class="w-8 h-8 text-indigo-600" />
                                <span class="text-xl font-bold">ProjekMan</span>
                            </a>
                        </div>

                        <!-- Navigasi Desktop -->
                        <nav class="hidden md:flex md:items-center md:space-x-8">
                            <a href="#fitur" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Fitur</a>
                            <a href="#alur-kerja" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Cara Kerja</a>
                            <a href="#peran" class="text-sm font-medium text-slate-600 hover:text-indigo-600 transition-colors">Untuk Siapa</a>
                        </nav>

                        <!-- Tombol Autentikasi Desktop -->
                        <div class="hidden md:flex items-center space-x-2">
                             @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 transition-transform hover:scale-105">Dashboard</a>
                            @else
                                <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 transition-transform hover:scale-105">Daftar Sekarang</a>
                                @endif
                            @endauth
                        </div>

                        <!-- Tombol Hamburger Mobile -->
                        <div class="md:hidden">
                            <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                <span class="sr-only">Buka menu utama</span>
                                <svg x-show="!open" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                                <svg x-show="open" class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Menu Mobile -->
                <div x-show="open" x-transition class="md:hidden">
                    <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                        <a href="#fitur" @click="open = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-50">Fitur</a>
                        <a href="#alur-kerja" @click="open = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-50">Cara Kerja</a>
                        <a href="#peran" @click="open = false" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-50">Untuk Siapa</a>
                    </div>
                    <div class="pt-4 pb-3 border-t border-slate-200">
                        @auth
                            <div class="px-2 space-y-1">
                                <a href="{{ url('/dashboard') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">Dashboard</a>
                            </div>
                        @else
                            <div class="px-2 space-y-1">
                                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-slate-900 hover:bg-slate-50">Masuk</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-white bg-indigo-600 hover:bg-indigo-700">Daftar Sekarang</a>
                                @endif
                            </div>
                        @endauth
                    </div>
                </div>
            </header>

            <main class="pt-16">
                <!-- Hero Section -->
                <section class="relative py-20 sm:py-28 lg:py-32 bg-white overflow-hidden">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="grid lg:grid-cols-2 gap-12 items-center">
                            <div class="text-center lg:text-left" data-aos="fade-right">
                                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight">
                                    Kolaborasi Produksi <span class="text-indigo-600">Fashion</span> Generasi Baru
                                </h1>
                                <p class="mt-6 text-lg text-slate-600 max-w-xl mx-auto lg:mx-0">
                                    Satukan Investor, Manajer Proyek, dan Penjahit dalam satu platform terintegrasi. Wujudkan proyek fashion Anda dengan transparansi dan efisiensi penuh.
                                </p>
                                <div class="mt-8 flex gap-4 justify-center lg:justify-start" data-aos="fade-up" data-aos-delay="200">
                                    <a href="{{ route('register') }}" class="inline-block px-6 py-3 text-base font-medium text-white bg-indigo-600 rounded-lg shadow-lg hover:bg-indigo-700 transform hover:scale-105 transition-all duration-300">Mulai Sekarang</a>
                                    <a href="#fitur" class="inline-block px-6 py-3 text-base font-medium text-indigo-700 bg-indigo-100 rounded-lg hover:bg-indigo-200 transform hover:scale-105 transition-all duration-300">Lihat Fitur</a>
                                </div>
                            </div>
                            <div class="hidden lg:block" data-aos="fade-left">
                                <div class="relative">
                                    <img src="images/Jubelio-Header.png" alt="[Suasana pabrik garmen modern]" class="rounded-xl shadow-2xl">
                                    <div class="absolute inset-0 hero-gradient rounded-xl"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Untuk Siapa Aplikasi Ini? (Peran) -->
                <section id="peran" class="py-20 sm:py-24">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                         <div class="text-center" data-aos="fade-up">
                            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Didesain untuk Setiap Peran</h2>
                            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">Platform kami memberikan solusi spesifik untuk setiap kebutuhan dalam ekosistem produksi fashion.</p>
                        </div>
                        <div class="mt-16 grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <!-- Card Investor -->
                            <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="100">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 text-green-600 rounded-lg mb-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900">Untuk Investor</h3>
                                <p class="mt-2 text-slate-600">Investasi pada proyek fashion yang menjanjikan. Pantau perkembangan dana dan lihat potensi profit secara transparan.</p>
                            </div>
                            <!-- Card Penjahit -->
                             <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2" data-aos="fade-up" data-aos-delay="200">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 text-blue-600 rounded-lg mb-4">
                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2 1M4 7l2-1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900">Untuk Penjahit</h3>
                                <p class="mt-2 text-slate-600">Temukan proyek borongan, catat progres harian, dan kelola tagihan pembayaran Anda dengan mudah dan tercatat.</p>
                            </div>
                            <!-- Card Admin/CEO -->
                             <div class="bg-white p-8 rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 md:col-span-2 lg:col-span-1" data-aos="fade-up" data-aos-delay="300">
                                <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 text-purple-600 rounded-lg mb-4">
                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900">Untuk Admin & CEO</h3>
                                <p class="mt-2 text-slate-600">Kelola seluruh alur proyek dari hulu ke hilir. Dapatkan laporan finansial, pantau produksi, dan ambil keputusan strategis berbasis data.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Alur Kerja -->
                <section id="alur-kerja" class="py-20 sm:py-24 bg-white">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center" data-aos="fade-up">
                            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Alur Kerja yang Sederhana</h2>
                            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">Empat langkah mudah untuk mengubah ide menjadi produk nyata.</p>
                        </div>
                        <div class="relative">
                             <!-- Garis penghubung -->
                             <div class="hidden lg:block absolute top-8 left-0 w-full h-0.5 bg-indigo-200" data-aos="zoom-in" data-aos-delay="300"></div>
                             <div class="mt-16 grid md:grid-cols-2 lg:grid-cols-4 gap-8 text-center relative">
                                <!-- Step 1 -->
                                <div class="p-6" data-aos="fade-up" data-aos-delay="100">
                                    <div class="flex items-center justify-center h-16 w-16 bg-white border-2 border-indigo-200 text-indigo-600 rounded-full mx-auto text-2xl font-bold relative z-10">1</div>
                                    <h3 class="mt-6 text-lg font-medium text-slate-900">Buat Proyek</h3>
                                    <p class="mt-2 text-sm text-slate-600">Admin mendefinisikan detail proyek, mulai dari desain, kuantitas, hingga rincian biaya dan profit.</p>
                                </div>
                                <!-- Step 2 -->
                                <div class="p-6" data-aos="fade-up" data-aos-delay="200">
                                    <div class="flex items-center justify-center h-16 w-16 bg-white border-2 border-indigo-200 text-indigo-600 rounded-full mx-auto text-2xl font-bold relative z-10">2</div>
                                    <h3 class="mt-6 text-lg font-medium text-slate-900">Danai Proyek</h3>
                                    <p class="mt-2 text-sm text-slate-600">Investor memilih proyek yang prospektif dan menyalurkan dana sesuai kebutuhan produksi.</p>
                                </div>
                                <!-- Step 3 -->
                                <div class="p-6" data-aos="fade-up" data-aos-delay="300">
                                    <div class="flex items-center justify-center h-16 w-16 bg-white border-2 border-indigo-200 text-indigo-600 rounded-full mx-auto text-2xl font-bold relative z-10">3</div>
                                    <h3 class="mt-6 text-lg font-medium text-slate-900">Mulai Produksi</h3>
                                    <p class="mt-2 text-sm text-slate-600">Penjahit mengambil tugas produksi, mencatat setiap kemajuan harian hingga proyek selesai.</p>
                                </div>
                                <!-- Step 4 -->
                                <div class="p-6" data-aos="fade-up" data-aos-delay="400">
                                    <div class="flex items-center justify-center h-16 w-16 bg-white border-2 border-indigo-200 text-indigo-600 rounded-full mx-auto text-2xl font-bold relative z-10">4</div>
                                    <h3 class="mt-6 text-lg font-medium text-slate-900">Pembagian Profit</h3>
                                    <p class="mt-2 text-sm text-slate-600">Setelah proyek selesai, sistem akan menghitung dan memfasilitasi pembagian profit kepada investor.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Fitur Unggulan -->
                <section id="fitur" class="py-20 sm:py-24">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="text-center" data-aos="fade-up">
                            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Fitur Unggulan Kami</h2>
                             <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">Alat canggih untuk memastikan setiap proyek berjalan lancar.</p>
                        </div>
                        <div class="mt-16 grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                            <div class="flex items-start gap-4" data-aos="fade-up">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg></div>
                                <div><h3 class="text-lg font-medium text-slate-900">Manajemen Proyek</h3><p class="mt-1 text-slate-600">Buat, edit, dan pantau status setiap proyek dalam satu dasbor.</p></div>
                            </div>
                             <div class="flex items-start gap-4" data-aos="fade-up" data-aos-delay="100">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg></div>
                                <div><h3 class="text-lg font-medium text-slate-900">Pendanaan Crowdfunding</h3><p class="mt-1 text-slate-600">Buka peluang investasi bagi publik untuk mendanai produksi Anda.</p></div>
                            </div>
                             <div class="flex items-start gap-4" data-aos="fade-up" data-aos-delay="200">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 0v6m0-6l-6 6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div>
                                <div><h3 class="text-lg font-medium text-slate-900">Pelacakan Progres Real-time</h3><p class="mt-1 text-slate-600">Penjahit dapat melaporkan progres harian, memberikan visibilitas penuh.</p></div>
                            </div>
                             <div class="flex items-start gap-4" data-aos="fade-up">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>
                                <div><h3 class="text-lg font-medium text-slate-900">Manajemen Gaji & Invoice</h3><p class="mt-1 text-slate-600">Sistem otomatis untuk mengelola tagihan dan pembayaran upah penjahit.</p></div>
                            </div>
                             <div class="flex items-start gap-4" data-aos="fade-up" data-aos-delay="100">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v.01"></path></svg></div>
                                <div><h3 class="text-lg font-medium text-slate-900">Pembayaran Profit</h3><p class="mt-1 text-slate-600">Kelola dan lacak pembayaran profit kepada para investor dengan mudah.</p></div>
                            </div>
                             <div class="flex items-start gap-4" data-aos="fade-up" data-aos-delay="200">
                                <div class="flex-shrink-0 w-10 h-10 flex items-center justify-center bg-indigo-100 text-indigo-600 rounded-lg"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"></path></svg></div>
                                <div><h3 class="text-lg font-medium text-slate-900">Laporan Komprehensif</h3><p class="mt-1 text-slate-600">Dasbor CEO menyediakan laporan analisis kohort, arus kas, dan produktivitas.</p></div>
                            </div>
                        </div>
                    </div>
                </section>
                
                <!-- Testimonials (Placeholder) -->
                <section class="py-20 sm:py-24 bg-white">
                    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                         <div class="text-center" data-aos="fade-up">
                            <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Apa Kata Mereka</h2>
                            <p class="mt-4 text-lg text-slate-600 max-w-2xl mx-auto">Pengalaman nyata dari para pengguna yang telah merasakan manfaat platform kami.</p>
                        </div>
                        <div class="mt-16 grid lg:grid-cols-3 gap-8">
                            <figure class="p-8 bg-slate-50 rounded-xl" data-aos="zoom-in-up">
                                <blockquote><p class="text-slate-700">“Platform ini merevolusi cara kami mendapatkan pendanaan. Sangat transparan dan mudah digunakan. Arus kas kami menjadi lebih sehat.”</p></blockquote>
                                <figcaption class="mt-6 flex items-center gap-4">
                                    <img class="w-12 h-12 rounded-full" src="https://i.pravatar.cc/48?u=1" alt="[Foto profil investor]">
                                    <div><div class="font-medium text-slate-900">Andi Pratama</div><div class="text-sm text-slate-600">Investor</div></div>
                                </figcaption>
                            </figure>
                             <figure class="p-8 bg-slate-50 rounded-xl" data-aos="zoom-in-up" data-aos-delay="150">
                                <blockquote><p class="text-slate-700">“Sebagai penjahit borongan, saya tidak pernah semudah ini mencari proyek dan mengelola pembayaran. Semuanya tercatat dengan rapi.”</p></blockquote>
                                <figcaption class="mt-6 flex items-center gap-4">
                                    <img class="w-12 h-12 rounded-full" src="https://i.pravatar.cc/48?u=2" alt="[Foto profil penjahit]">
                                    <div><div class="font-medium text-slate-900">Siti Aminah</div><div class="text-sm text-slate-600">Penjahit</div></div>
                                </figcaption>
                            </figure>
                             <figure class="p-8 bg-slate-50 rounded-xl" data-aos="zoom-in-up" data-aos-delay="300">
                                <blockquote><p class="text-slate-700">“Laporan keuangan dan produksi yang real-time membantu saya mengambil keputusan lebih cepat dan tepat. Wajib dimiliki setiap pemilik brand fashion.”</p></blockquote>
                                <figcaption class="mt-6 flex items-center gap-4">
                                    <img class="w-12 h-12 rounded-full" src="https://i.pravatar.cc/48?u=3" alt="[Foto profil CEO]">
                                    <div><div class="font-medium text-slate-900">Rian Darmawan</div><div class="text-sm text-slate-600">CEO, Fashion Brand</div></div>
                                </figcaption>
                            </figure>
                        </div>
                    </div>
                </section>

                <!-- Final CTA -->
                <section class="py-20 sm:py-24">
                     <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                         <div class="cta-gradient rounded-2xl p-8 md:p-16 text-center" data-aos="fade-up">
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight">Siap Mengubah Bisnis Fashion Anda?</h2>
                            <p class="mt-4 text-lg text-indigo-200 max-w-2xl mx-auto">
                                Bergabunglah dengan ratusan pengguna lain dan bawa manajemen produksi Anda ke level selanjutnya.
                            </p>
                            <div class="mt-8">
                                <a href="{{ route('register') }}" class="inline-block px-8 py-4 text-base font-medium text-indigo-600 bg-white rounded-lg shadow-lg hover:bg-indigo-50 transform hover:scale-105 transition-all duration-300">Buat Akun Gratis</a>
                            </div>
                         </div>
                     </div>
                </section>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-slate-200">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="sm:flex sm:items-center sm:justify-between">
                        <a href="/" class="flex items-center gap-2">
                            <x-application-logo class="w-6 h-6 text-indigo-600" />
                            <span class="text-lg font-bold">ProjekMan</span>
                        </a>
                        <p class="mt-4 text-center text-sm text-slate-500 sm:mt-0">&copy; {{ date('Y') }} ProjekMan. Seluruh hak cipta dilindungi.</p>
                    </div>
                </div>
            </footer>
        </div>
        
        <script>
            AOS.init({
                duration: 800, // Durasi animasi
                once: true, // Animasi hanya berjalan sekali
            });
        </script>
    </body>
</html>
