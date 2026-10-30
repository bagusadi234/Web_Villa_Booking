<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1F5F7A">

    <title>VillaStay Bali | Your Private Escape</title>
    <meta name="description" content="Temukan villa pilihan di Bali untuk liburan yang tenang, nyaman, dan berkesan.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Ocean Breeze color system */
        :root {
            --primary: #1F5F7A;
            --primary-dark: #174A5F;
            --background: #FAF7F2;
            --coral: #1F5F7A;
            --coral-hover: #1F5F7A;
            --text: #1C2A33;
            --muted: #5B6B75;
            --border: #E3DED5;
            --success: #2E8B6A;
            --error: #1F5F7A;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }

        body {
            margin: 0;
            background: var(--background);
            color: var(--text);
            font-family: "DM Sans", sans-serif;
        }

        h1,
        h2,
        h3,
        .serif {
            font-family: "Playfair Display", serif;
        }

        .text-primary {
            color: var(--primary);
        }

        .text-muted {
            color: var(--muted);
        }

        .bg-primary {
            background: var(--primary);
        }

        .bg-sand {
            background: var(--background);
        }

        .border-soft {
            border-color: var(--border);
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 22px;
            border-radius: 8px;
            background: var(--coral);
            color: white;
            font-weight: 700;
            transition: background .2s ease, transform .2s ease;
        }

        .btn-primary:hover {
            background: var(--coral-hover);
            transform: translateY(-1px);
        }

        .btn-primary:focus-visible,
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        select:focus-visible {
            outline: 3px solid var(--primary);
            outline-offset: 3px;
        }

        .hero {
            position: relative;
            isolation: isolate;
            background-image:
                linear-gradient(90deg, rgba(23, 74, 95, .94) 0%, rgba(23, 74, 95, .72) 48%, rgba(23, 74, 95, .15) 100%),
                url("https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=2200&q=85");
            background-size: cover;
            background-position: center;
        }

        .hero-image {
            background-size: cover;
            background-position: center;
        }

        .section-label {
            color: var(--primary);
            font-size: .76rem;
            font-weight: 700;
            letter-spacing: .18em;
            text-transform: uppercase;
        }

        .villa-card {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .villa-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(28, 42, 51, .10);
        }

        .villa-photo {
            transition: transform .45s ease;
        }

        .villa-card:hover .villa-photo {
            transform: scale(1.04);
        }

        .feature-icon {
            display: grid;
            place-items: center;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: #E8F0F2;
            color: var(--primary);
        }

        .search-panel {
            box-shadow: 0 18px 45px rgba(23, 74, 95, .15);
        }

        .faq-answer[hidden] {
            display: none;
        }

        .faq-icon {
            transition: transform .2s ease;
        }

        .faq-trigger[aria-expanded="true"] .faq-icon {
            transform: rotate(180deg);
        }

        .gallery-item {
            overflow: hidden;
            border-radius: 12px;
            min-height: 200px;
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            min-height: 200px;
            object-fit: cover;
            transition: transform .4s ease;
        }

        .gallery-item:hover img {
            transform: scale(1.04);
        }

        .form-field {
            width: 100%;
            min-width: 0;
            border: 1px solid var(--border);
            border-radius: 7px;
            padding: 11px 12px;
            background: #fff;
            color: var(--text);
        }

        .form-field:focus {
            border-color: var(--primary);
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
                animation-duration: .01ms !important;
            }
        }
    </style>
</head>
<!-- <script src="https://cdn.adolin.id/live-chat/v1/widget.js" data-adolin-key="pk_live_caeff2c1a84f742d7f86f8ab" async></script> -->
<body>

    <!-- 1. Navbar -->
    <header class="sticky top-0 z-50 bg-white border-b border-soft">
        <nav class="max-w-7xl mx-auto px-5 lg:px-8 h-[76px] flex items-center justify-between"
            aria-label="Navigasi utama">

            <a href="#beranda" class="flex items-center gap-3" aria-label="VillaStay Bali - Beranda">
                <span class="grid place-items-center w-10 h-10 rounded-lg bg-primary text-white font-bold text-lg">V</span>
                <span>
                    <span class="block text-lg font-bold tracking-tight">VillaStay</span>
                    <span class="block text-xs tracking-[.2em] text-muted">B A L I</span>
                </span>
            </a>

            <div class="hidden lg:flex items-center gap-7 text-sm font-medium">
                <a class="hover:text-primary" href="#villa">Villa</a>
                <a class="hover:text-primary" href="#fasilitas">Fasilitas</a>
                <a class="hover:text-primary" href="#galeri">Galeri</a>
                <a class="hover:text-primary" href="#ulasan">Ulasan</a>
                <a class="hover:text-primary" href="#kontak">Kontak</a>
            </div>

            <a href="#pencarian" class="btn-primary hidden sm:inline-flex">
                Book Now <span aria-hidden="true">↗</span>
            </a>

            <button id="menuButton"
                class="lg:hidden p-2 rounded-lg border border-soft"
                aria-label="Buka menu"
                aria-expanded="false"
                aria-controls="mobileMenu">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </button>
        </nav>

        <div id="mobileMenu" class="hidden lg:hidden bg-white border-t border-soft px-5 py-4">
            <div class="flex flex-col gap-4 text-sm">
                <a href="#villa">Villa</a>
                <a href="#fasilitas">Fasilitas</a>
                <a href="#galeri">Galeri</a>
                <a href="#ulasan">Ulasan</a>
                <a href="#kontak">Kontak</a>
                <a href="#pencarian" class="btn-primary">Book Now</a>
            </div>
        </div>
    </header>

    <main>

        <!-- 2. Hero -->
        <section id="beranda" class="hero text-white">
            <div class="max-w-7xl mx-auto px-5 lg:px-8 pt-20 pb-36 md:pt-28 md:pb-44">
                <div class="max-w-3xl">
                    <p class="text-white font-bold text-xs tracking-[.24em] uppercase mb-6">
                        Your private escape in Bali
                    </p>

                    <h1 class="text-4xl sm:text-5xl lg:text-7xl leading-[1.12] font-medium">
                        Find your own<br>
                        <span class="italic">piece of paradise.</span>
                    </h1>

                    <p class="mt-6 text-white text-base md:text-lg leading-8 max-w-xl">
                        Nikmati waktu yang lebih bermakna di villa pilihan,
                        dengan kenyamanan dan ketenangan khas Pulau Bali.
                    </p>

                    <a href="#villa" class="btn-primary mt-8">
                        Explore Our Villas <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </section>

        <!-- 3. Quick search form -->
        <section id="pencarian" class="relative z-10 max-w-6xl mx-auto px-5 -mt-20">
            <div class="search-panel bg-white rounded-2xl border border-soft p-5 md:p-7">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-2 mb-5">
                    <div>
                        <h2 class="text-2xl font-semibold">Plan your stay</h2>
                        <p class="text-muted text-sm mt-1">Temukan villa untuk tanggal pilihanmu.</p>
                    </div>
                    <span class="text-sm text-primary font-semibold">Bali, Indonesia</span>
                </div>


                <form id="searchForm" method="GET" action="{{ route('villas.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="checkin" class="block text-sm font-semibold mb-2">Check-in</label>
                        <input id="checkin" name="check_in" type="date" value="{{ $searching ? ($validated['check_in'] ?? '') : '' }}" required class="form-field">
                    </div>

                    <div>
                        <label for="checkout" class="block text-sm font-semibold mb-2">Check-out</label>
                        <input id="checkout" name="check_out" type="date" value="{{ $searching ? ($validated['check_out'] ?? '') : '' }}" required class="form-field">
                    </div>

                    <div>
                        <label for="guests" class="block text-sm font-semibold mb-2">Jumlah tamu</label>
                        <select id="guests" name="guests" class="form-field">
                            @foreach (range(1, 8) as $guest)
                            <option
                                value="{{ $guest }}"
                                {{ (int) request('guests', 2) === $guest ? 'selected' : '' }}>
                                {{ $guest }} tamu{{ $guest === 8 ? ' atau lebih' : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="btn-primary w-full min-h-[46px]">
                            Check Availability
                        </button>
                    </div>

                    <p id="searchMessage" class="hidden sm:col-span-2 lg:col-span-4 text-sm"
                        role="status" aria-live="polite"></p>
                </form>
            </div>
        </section>

        @if ($searching)
        <section id="hasil-pencarian"
            class="max-w-6xl mx-auto px-5 pt-10"
            aria-live="polite">
            <div class="bg-white rounded-2xl border border-soft p-6 md:p-8">
                <p class="section-label mb-2">Hasil pencarian</p>
                <h2 class="text-2xl md:text-3xl">
                    Villa untuk perjalananmu
                </h2>

                @if ($searchError)
                <p class="text-muted mt-4">
                    {{ $searchError }}
                </p>
                @elseif ($searchResults->isEmpty())
                <p class="text-muted mt-4">
                    Tidak ada villa yang tersedia untuk tanggal dan
                    jumlah tamu tersebut. Coba tanggal lain atau
                    kurangi jumlah tamu.
                </p>
                @else
                <p class="text-muted mt-3">
                    {{ \Carbon\Carbon::parse($validated['check_in'])->format('d M Y') }}
                    –
                    {{ \Carbon\Carbon::parse($validated['check_out'])->format('d M Y') }}
                    · {{ $validated['guests'] }} tamu
                </p>

                <div class="grid md:grid-cols-2 gap-5 mt-6">
                    @foreach ($searchResults as $result)
                    @php
                    $villa = $result['villa'];
                    $pricing = $result['pricing'];
                    @endphp

                    <article class="rounded-xl border border-soft p-5">
                        <h3 class="text-xl">
                            {{ $villa->name }}
                        </h3>

                        <p class="text-muted text-sm mt-2">
                            {{ $villa->branch->name ?? 'Villa Bali' }}
                        </p>

                        <div class="flex flex-wrap gap-3 text-sm text-muted mt-4">
                            <span>
                                {{ $pricing['total_nights'] }} malam
                            </span>
                            <span>
                                Maks. {{ $villa->max_capacity }} tamu
                            </span>
                            <span>
                                {{ $result['available_count'] }}
                                unit tersedia
                            </span>
                        </div>

                        <div class="border-t border-soft mt-5 pt-4">
                            <p class="text-sm text-muted">
                                Total harga menginap
                            </p>
                            <p class="text-2xl font-bold text-primary mt-1">
                                Rp{{ number_format($pricing['subtotal'], 0, ',', '.') }}
                            </p>
                            
                            <a
                                href="{{ route('bookings.create', [
                                    'villa_type_id' => $villa->id,
                                    'check_in' => $validated['check_in'],
                                    'check_out' => $validated['check_out'],
                                    'guests' => $validated['guests'],
                                ]) }}"
                                class="mt-5 inline-flex w-full items-center justify-center rounded-xl bg-[#1F5F7A] px-5 py-3 font-semibold text-white transition hover:bg-[#17485D]"
                            >
                                Book Villa
                            </a>


                            <p class="text-xs text-muted mt-2">
                                Harga berdasarkan aturan yang aktif.
                                Biaya tambahan belum termasuk.
                            </p>
                        </div>
                    </article>
                    @endforeach
                </div>
                @endif
            </div>
        </section>
        @endif

        <!-- 4. Highlights -->
        <section class="max-w-7xl mx-auto px-5 lg:px-8 py-20 md:py-24">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                <article class="flex flex-col items-start gap-4">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.7">
                            <path d="M2 18h20M3 14l4-5 4 3 4-7 6 9" />
                            <path d="M4 5h5" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Ocean Views</h3>
                        <p class="text-muted text-sm leading-6 mt-1">Nikmati pemandangan dan suasana pesisir.</p>
                    </div>
                </article>

                <article class="flex flex-col items-start gap-4">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.7">
                            <path d="M3 12h18v8H3zM5 12V7a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v5" />
                            <path d="M13 12V8a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v4M5 20v2m14-2v2" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Private Comfort</h3>
                        <p class="text-muted text-sm leading-6 mt-1">Ruang pribadi untuk istirahat lebih nyaman.</p>
                    </div>
                </article>

                <article class="flex flex-col items-start gap-4">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.7">
                            <path d="M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Trusted Stay</h3>
                        <p class="text-muted text-sm leading-6 mt-1">Informasi akomodasi yang jelas dan terpercaya.</p>
                    </div>
                </article>

                <article class="flex flex-col items-start gap-4">
                    <div class="feature-icon" aria-hidden="true">
                        <svg width="25" height="25" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.7">
                            <path d="M4 10h16M5 10l1 11h12l1-11M8 10V6a4 4 0 0 1 8 0v4" />
                            <path d="M9 15h6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold">Thoughtful Service</h3>
                        <p class="text-muted text-sm leading-6 mt-1">Pengalaman menginap yang dirancang dengan perhatian.</p>
                    </div>
                </article>
            </div>
        </section>

        <!-- 5. Villa listings: dynamic data from Laravel -->
        <section id="villa" class="bg-white py-20 md:py-24">
            <div class="max-w-7xl mx-auto px-5 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-5 mb-10">
                    <div>
                        <p class="section-label mb-3">Stay your way</p>
                        <h2 class="text-3xl md:text-5xl leading-tight">Find your perfect villa.</h2>
                        <p class="text-muted mt-4 max-w-xl leading-7">
                            Pilih akomodasi yang sesuai dengan kebutuhan liburanmu.
                        </p>
                    </div>

                    <span class="text-sm text-muted">
                        {{ $villas->count() }} tipe villa
                    </span>
                </div>

                @if ($villas->isEmpty())
                <div class="rounded-xl border border-soft bg-sand p-10 text-center">
                    <h3 class="text-2xl">Villa segera hadir</h3>
                    <p class="text-muted mt-3">Belum ada tipe villa aktif yang ditampilkan.</p>
                </div>
                @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
                    @foreach ($villas as $villa)
                    @php
                    $primaryImage = $villa->images->first();
                    $startingPrice = $villa->pricingRules->min('price');
                    @endphp

                    <article class="villa-card">
                        <div class="h-64 overflow-hidden bg-sand">
                            @if ($primaryImage)
                            <img
                                src="{{ asset('storage/' . $primaryImage->image_path) }}"
                                alt="{{ $primaryImage->alt_text ?: 'Foto ' . $villa->name }}"
                                class="villa-photo w-full h-full object-cover"
                                loading="lazy">
                            @else
                            <img
                                src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=900&q=80"
                                alt="Foto ilustrasi villa tropis"
                                class="villa-photo w-full h-full object-cover"
                                loading="lazy">
                            @endif
                        </div>

                        <div class="p-6">
                            <p class="text-primary text-xs font-bold uppercase tracking-wider">
                                {{ $villa->branch->name ?? 'Villa Bali' }}
                            </p>

                            <h3 class="text-2xl mt-2">{{ $villa->name }}</h3>

                            <p class="text-muted text-sm leading-6 mt-3">
                                {{ $villa->description ?: 'Tempat beristirahat yang nyaman untuk perjalananmu.' }}
                            </p>

                            <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-muted mt-5">
                                <span>{{ $villa->bedrooms }} kamar tidur</span>
                                <span>{{ $villa->bathrooms }} kamar mandi</span>
                                <span>Maks. {{ $villa->max_capacity }} tamu</span>
                            </div>

                            <div class="border-t border-soft mt-5 pt-5 flex items-end justify-between gap-3">
                                <div>
                                    <p class="text-xs text-muted">Harga mulai</p>
                                    @if ($startingPrice !== null)
                                    <p class="text-lg font-bold mt-1">
                                        Rp{{ number_format($startingPrice, 0, ',', '.') }}
                                        <span class="text-xs text-muted font-normal">/ malam</span>
                                    </p>
                                    @else
                                    <p class="font-semibold mt-1">Hubungi kami</p>
                                    @endif
                                </div>
                                    <a href="{{ route('villas.show', $villa->slug) }}"
                                    class="text-primary font-bold text-sm underline underline-offset-4">
                                        Lihat Detail
                                    </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
                @endif
            </div>
        </section>

        <!-- 6. Gallery -->
        <section id="galeri" class="max-w-7xl mx-auto px-5 lg:px-8 py-20 md:py-24">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <p class="section-label mb-3">A glimpse of Bali</p>
                <h2 class="text-3xl md:text-5xl">Moments worth remembering.</h2>
                <p class="text-muted leading-7 mt-4">
                    Inspirasi suasana untuk liburanmu berikutnya.
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="gallery-item col-span-2 row-span-2">
                    <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1100&q=85"
                        alt="Arsitektur rumah liburan tropis dengan halaman luas" loading="lazy">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=700&q=80"
                        alt="Kolam renang akomodasi liburan" loading="lazy">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811?auto=format&fit=crop&w=700&q=80"
                        alt="Villa tropis dengan area terbuka" loading="lazy">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=80"
                        alt="Interior ruang santai yang elegan" loading="lazy">
                </div>
                <div class="gallery-item">
                    <img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=700&q=80"
                        alt="Interior akomodasi dengan cahaya alami" loading="lazy">
                </div>
            </div>
        </section>

        <!-- 7. Facilities -->
        <section id="fasilitas" class="bg-primary text-white py-20 md:py-24">
            <div class="max-w-7xl mx-auto px-5 lg:px-8 grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <p class="text-white font-bold text-xs uppercase tracking-[.2em] mb-4">
                        Made for your comfort
                    </p>
                    <h2 class="text-3xl md:text-5xl leading-tight">
                        Everything you need to unwind.
                    </h2>
                    <p class="text-white leading-7 mt-5 max-w-lg">
                        Kenyamanan dimulai dari detail kecil. Jelajahi fasilitas yang
                        dapat tersedia sesuai tipe villa yang kamu pilih.
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    @foreach ([
                    ['Wi-Fi', 'Koneksi untuk kebutuhan harian'],
                    ['Air Conditioning', 'Suasana kamar yang nyaman'],
                    ['Private Pool', 'Pilihan villa dengan kolam pribadi'],
                    ['Kitchen', 'Area untuk menyiapkan makanan']
                    ] as $facility)
                    <article class="rounded-xl border border-white/30 p-5 md:p-6">
                        <div class="text-white text-2xl mb-4" aria-hidden="true">✦</div>
                        <h3 class="text-xl">{{ $facility[0] }}</h3>
                        <p class="text-white text-sm leading-6 mt-2">{{ $facility[1] }}</p>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 8. Testimonials: demo content, replace with real reviews -->
        <section id="ulasan" class="py-20 md:py-24">
            <div class="max-w-7xl mx-auto px-5 lg:px-8">
                <div class="text-center mb-12">
                    <p class="section-label mb-3">Guest stories</p>
                    <h2 class="text-3xl md:text-5xl">Words from our guests.</h2>
                    <p class="text-muted mt-4">Contoh tampilan ulasan yang nantinya diganti dengan ulasan tamu asli.</p>
                </div>

                <div class="grid md:grid-cols-3 gap-6">
                    @foreach ([
                    ['“Suasananya tenang dan desainnya terasa nyaman untuk beristirahat.”', 'Guest Example 01'],
                    ['“Informasi villa mudah dipahami dan tampilannya sangat membantu.”', 'Guest Example 02'],
                    ['“Tempat yang menarik untuk merencanakan liburan bersama keluarga.”', 'Guest Example 03']
                    ] as $review)
                    <article class="bg-white rounded-xl border border-soft p-7">
                        <div class="text-primary tracking-widest text-lg" aria-label="Contoh elemen rating">★★★★★</div>
                        <blockquote class="leading-7 mt-5">{{ $review[0] }}</blockquote>
                        <p class="text-sm text-muted font-semibold mt-6">{{ $review[1] }}</p>
                        <p class="text-xs text-muted mt-1">Konten demo, bukan ulasan terverifikasi</p>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 9. FAQ accordion -->
        <section id="faq" class="bg-white py-20 md:py-24">
            <div class="max-w-3xl mx-auto px-5">
                <div class="text-center mb-10">
                    <p class="section-label mb-3">Need to know</p>
                    <h2 class="text-3xl md:text-5xl">Frequently asked questions.</h2>
                </div>

                <div class="divide-y divide-[var(--border)]">
                    @foreach ([
                    ['Bagaimana cara memesan villa?', 'Pilih tanggal check-in, check-out, dan jumlah tamu. Fitur pencarian dan reservasi akan dihubungkan ke sistem booking pada tahap pengembangan berikutnya.'],
                    ['Jam berapa check-in dan check-out?', 'Jam standar yang direncanakan adalah check-in pukul 14.00 WITA dan check-out pukul 12.00 WITA. Kebijakan akhir mengikuti informasi villa yang dipilih.'],
                    ['Apakah harga sudah termasuk semua biaya?', 'Harga yang tampil saat ini adalah harga mulai berdasarkan aturan harga di database. Pajak, biaya layanan, tambahan tamu, dan ketentuan lainnya perlu ditampilkan sesuai kebijakan final.'],
                    ['Bagaimana jika saya ingin membatalkan booking?', 'Ketentuan pembatalan akan mengikuti kebijakan pembatalan yang ditetapkan untuk reservasi tersebut.']
                    ] as $index => $faq)
                    <article class="py-5">
                        <h3>
                            <button type="button"
                                class="faq-trigger flex items-center justify-between gap-5 w-full text-left font-semibold"
                                aria-expanded="false"
                                aria-controls="faq-answer-{{ $index }}"
                                id="faq-trigger-{{ $index }}">
                                <span>{{ $faq[0] }}</span>
                                <span class="faq-icon text-primary text-xl" aria-hidden="true">⌄</span>
                            </button>
                        </h3>
                        <div id="faq-answer-{{ $index }}"
                            class="faq-answer text-muted leading-7 mt-4"
                            role="region"
                            aria-labelledby="faq-trigger-{{ $index }}"
                            hidden>
                            {{ $faq[1] }}
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- 10. Contact CTA -->
        <section class="px-5 py-16">
            <div class="max-w-7xl mx-auto rounded-2xl bg-primary text-white px-7 py-12 md:p-16 flex flex-col md:flex-row md:items-center justify-between gap-8">
                <div class="max-w-2xl">
                    <p class="text-white uppercase text-xs font-bold tracking-[.2em]">Your next escape</p>
                    <h2 class="text-3xl md:text-5xl mt-4">Ready to slow down?</h2>
                    <p class="text-white leading-7 mt-4">
                        Mulai rencanakan waktu istirahatmu di Bali.
                    </p>
                </div>

                <a href="#pencarian" class="btn-primary shrink-0">
                    Check Availability <span aria-hidden="true">→</span>
                </a>
            </div>
        </section>
    </main>

    <!-- 11. Footer -->
    <footer id="kontak" class="bg-white border-t border-soft">
        <div class="max-w-7xl mx-auto px-5 lg:px-8 py-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <div>
                <a href="#beranda" class="text-xl font-bold">VillaStay Bali.</a>
                <p class="text-muted text-sm leading-7 mt-4">
                    Temukan ruang untuk beristirahat dan menikmati Bali dengan caramu.
                </p>
            </div>

            <div>
                <h2 class="font-bold mb-4">Explore</h2>
                <ul class="space-y-3 text-sm text-muted">
                    <li><a href="#villa" class="hover:text-primary">Our Villas</a></li>
                    <li><a href="#fasilitas" class="hover:text-primary">Facilities</a></li>
                    <li><a href="#galeri" class="hover:text-primary">Gallery</a></li>
                    <li><a href="#faq" class="hover:text-primary">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h2 class="font-bold mb-4">Contact</h2>
                <ul class="space-y-3 text-sm text-muted">
                    <li>Bali, Indonesia</li>
                    <li>Email: isi-email-resmi@example.com</li>
                    <li>WhatsApp: isi-nomor-resmi</li>
                </ul>
            </div>

            <div>
                <h2 class="font-bold mb-4">Follow our journey</h2>
                <div class="flex gap-3 text-sm">
                    <a href="#kontak" class="underline underline-offset-4">Instagram</a>
                    <a href="#kontak" class="underline underline-offset-4">Facebook</a>
                </div>
                <p class="text-xs text-muted mt-4">
                    Ganti tautan demo dengan akun resmi bisnis.
                </p>
            </div>
        </div>

        <div class="border-t border-soft py-5">
            <div class="max-w-7xl mx-auto px-5 lg:px-8 flex flex-col sm:flex-row justify-between gap-3 text-xs text-muted">
                <p>© {{ date('Y') }} VillaStay Bali. All rights reserved.</p>
                <p>Made for a more mindful stay.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp shortcut: replace number with official business number -->
    <a href="https://wa.me/6200000000000"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Hubungi kami melalui WhatsApp"
        class="fixed bottom-5 right-5 z-40 bg-primary text-white rounded-full px-5 py-4 shadow-lg font-bold text-sm">
        WhatsApp ↗
    </a>

    <!-- Lightweight JavaScript for mobile navigation, search validation and FAQ -->
    <script>
        // Mobile navigation
        const menuButton = document.getElementById('menuButton');
        const mobileMenu = document.getElementById('mobileMenu');

        menuButton.addEventListener('click', () => {
            const isExpanded = menuButton.getAttribute('aria-expanded') === 'true';

            menuButton.setAttribute('aria-expanded', String(!isExpanded));
            menuButton.setAttribute('aria-label', isExpanded ? 'Buka menu' : 'Tutup menu');
            mobileMenu.classList.toggle('hidden', isExpanded);
        });

        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
                menuButton.setAttribute('aria-expanded', 'false');
                menuButton.setAttribute('aria-label', 'Buka menu');
            });
        });

        // Date validation: check-out must be after check-in
        const checkin = document.getElementById('checkin');
        const checkout = document.getElementById('checkout');
        const searchForm = document.getElementById('searchForm');
        const searchMessage = document.getElementById('searchMessage');

        // Use local date rather than UTC to avoid date shifts in WITA.
        function localDateString(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        const today = localDateString(new Date());
        checkin.min = today;
        checkout.min = today;

        checkin.addEventListener('change', () => {
            checkout.min = checkin.value || today;

            if (checkout.value && checkout.value <= checkin.value) {
                checkout.value = '';
            }
        });


        searchForm.addEventListener('submit', event => {
            searchMessage.classList.remove('hidden');
            searchMessage.classList.remove('text-[var(--error)]');
            searchMessage.classList.add('text-[var(--primary)]');

            if (checkin.value < today) {
                event.preventDefault();

                searchMessage.textContent =
                    'Tanggal check-in tidak boleh di masa lalu.';
                searchMessage.classList.replace(
                    'text-[var(--primary)]',
                    'text-[var(--error)]'
                );
                checkin.focus();
                return;
            }

            if (checkout.value <= checkin.value) {
                event.preventDefault();

                searchMessage.textContent =
                    'Tanggal check-out harus setelah tanggal check-in.';
                searchMessage.classList.replace(
                    'text-[var(--primary)]',
                    'text-[var(--error)]'
                );
                checkout.focus();
                return;
            }

            // Jika valid, form dikirim ke Laravel menggunakan GET.
            searchMessage.textContent = 'Sedang mencari villa yang tersedia...';
        });


        // Accessible FAQ accordion
        document.querySelectorAll('.faq-trigger').forEach(button => {
            button.addEventListener('click', () => {
                const expanded = button.getAttribute('aria-expanded') === 'true';
                const answer = document.getElementById(button.getAttribute('aria-controls'));

                button.setAttribute('aria-expanded', String(!expanded));
                answer.hidden = expanded;
            });
        });
        // Bersihkan URL setelah hasil pencarian selesai dirender.
        // Hasil tetap tampil, tetapi tidak muncul lagi saat halaman di-refresh.
        const searchParams = new URLSearchParams(window.location.search);

        if (
            searchParams.has('check_in') ||
            searchParams.has('check_out') ||
            searchParams.has('guests')
        ) {
            window.history.replaceState(
                {},
                document.title,
                window.location.pathname + window.location.hash
            );
        }
    </script>
</body>

</html>