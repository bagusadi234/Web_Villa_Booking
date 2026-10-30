
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $villa->name }} | VillaStay Bali</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        h1, h2 { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

<header class="bg-white border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-5 flex justify-between items-center">
        <a href="{{ route('villas.index') }}"
           class="text-xl font-bold text-blue-800">
            VillaStay <span class="text-sky-500">Bali</span>
        </a>
        <a href="{{ route('villas.index') }}"
           class="text-sm font-semibold text-slate-600 hover:text-blue-700">
            ← Kembali ke daftar villa
        </a>
    </div>
</header>

<main class="max-w-6xl mx-auto px-5 py-8">

    <div class="mb-6">
        <p class="text-sm text-blue-700 font-semibold mb-2">
            {{ $villa->branch?->name ?? 'VillaStay Bali' }}
        </p>

        <h1 class="text-3xl md:text-4xl font-bold mb-3">
            {{ $villa->name }}
        </h1>

        <p class="text-slate-500">
            {{ $villa->branch?->address ?? 'Lokasi villa' }}
            @if ($villa->branch?->city)
                · {{ $villa->branch->city }}
            @endif
        </p>
    </div>

    {{-- Galeri foto --}}
    @php
        $images = $villa->images;
    @endphp

    @if ($images->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-10">
            @foreach ($images->take(4) as $image)
                <div class="rounded-2xl overflow-hidden bg-slate-200
                            {{ $loop->first ? 'md:row-span-2' : '' }}">
                    <img
                        src="{{ \Illuminate\Support\Facades\Storage::url($image->image_path) }}"
                        alt="{{ $image->alt_text ?: $villa->name }}"
                        class="w-full h-64 md:h-72 object-cover"
                    >
                </div>
            @endforeach
        </div>
    @else
        <div class="rounded-2xl bg-blue-100 text-blue-800 p-10 mb-10 text-center">
            <p class="font-semibold">Foto villa belum tersedia.</p>
            <p class="text-sm mt-2">Foto akan ditampilkan setelah ditambahkan oleh pengelola.</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Informasi villa --}}
        <section class="lg:col-span-2 space-y-6">

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-2xl font-bold mb-4">Tentang Villa</h2>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-sm text-slate-500">Kamar tidur</p>
                        <p class="text-xl font-bold">{{ $villa->bedrooms }}</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-sm text-slate-500">Kamar mandi</p>
                        <p class="text-xl font-bold">{{ $villa->bathrooms }}</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-sm text-slate-500">Kapasitas normal</p>
                        <p class="text-xl font-bold">{{ $villa->standard_capacity }} orang</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-4">
                        <p class="text-sm text-slate-500">Maksimal tamu</p>
                        <p class="text-xl font-bold">{{ $villa->max_capacity }} orang</p>
                    </div>
                </div>

                <div class="text-slate-600 leading-7 whitespace-pre-line">
                    {{ $villa->description ?: 'Informasi villa akan segera diperbarui.' }}
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-sm">
                <h2 class="text-2xl font-bold mb-4">Fasilitas</h2>

                @if ($villa->facilities->isNotEmpty())
                    <div class="flex flex-wrap gap-3">
                        @foreach ($villa->facilities as $facility)
                            <span class="px-4 py-2 rounded-full bg-blue-50
                                         text-blue-800 text-sm font-medium">
                                {{ $facility->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500">
                        Informasi fasilitas belum tersedia.
                    </p>
                @endif
            </div>

        </section>

        {{-- Form cek ketersediaan --}}
        <aside>
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100
                        lg:sticky lg:top-6">
                <h2 class="text-2xl font-bold mb-2">Cek Ketersediaan</h2>
                <p class="text-sm text-slate-500 mb-5">
                    Pilih tanggal menginap untuk melihat harga dan ketersediaan.
                </p>

                <form method="GET"
                      action="{{ route('villas.show', $villa->slug) }}"
                      class="space-y-4">

                    <div>
                        <label for="check_in" class="block text-sm font-semibold mb-1">
                            Check-in
                        </label>
                        <input
                            type="date"
                            id="check_in"
                            name="check_in"
                            min="{{ now()->toDateString() }}"
                            value="{{ old('check_in', $validated['check_in'] ?? '') }}"
                            required
                            class="w-full border border-slate-300 rounded-xl px-3 py-3"
                        >
                        @error('check_in')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="check_out" class="block text-sm font-semibold mb-1">
                            Check-out
                        </label>
                        <input
                            type="date"
                            id="check_out"
                            name="check_out"
                            min="{{ now()->addDay()->toDateString() }}"
                            value="{{ old('check_out', $validated['check_out'] ?? '') }}"
                            required
                            class="w-full border border-slate-300 rounded-xl px-3 py-3"
                        >
                        @error('check_out')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="guests" class="block text-sm font-semibold mb-1">
                            Jumlah tamu
                        </label>
                        <select
                            id="guests"
                            name="guests"
                            required
                            class="w-full border border-slate-300 rounded-xl px-3 py-3"
                        >
                            @for ($i = 1; $i <= $villa->max_capacity; $i++)
                                <option
                                    value="{{ $i }}"
                                    @selected((int) old('guests', $validated['guests'] ?? 2) === $i)
                                >
                                    {{ $i }} orang
                                </option>
                            @endfor
                        </select>
                        @error('guests')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full bg-blue-800 hover:bg-blue-900 text-white
                               font-bold rounded-xl px-5 py-3 transition"
                    >
                        Cek Harga & Ketersediaan
                    </button>
                </form>

                @if ($searchError)
                    <div class="mt-5 rounded-xl bg-amber-50 text-amber-800 p-4 text-sm">
                        {{ $searchError }}
                    </div>
                @endif

                @if ($searching && $availableCount !== null)
                    <div class="mt-5 border-t border-slate-200 pt-5">
                        @if ($availableCount > 0)
                            <p class="text-green-700 font-bold">
                                {{ $availableCount }} unit tersedia
                            </p>
                        @else
                            <p class="text-red-600 font-bold">
                                Tidak ada unit tersedia pada tanggal tersebut.
                            </p>
                        @endif

                        @if ($pricing)
                            <div class="mt-4">
                                <p class="text-sm text-slate-500">
                                    {{ $pricing['total_nights'] }} malam
                                </p>
                                <p class="text-2xl font-bold text-blue-800">
                                    Rp{{ number_format($pricing['subtotal'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Total harga menginap, sebelum biaya tambahan jika ada.
                                </p>
                            </div>
                        @endif

                        @if ($availableCount > 0 && $pricing)
                            <a
                                href="{{ route('bookings.create', [
                                    'villa_type_id' => $villa->id,
                                    'check_in' => $validated['check_in'],
                                    'check_out' => $validated['check_out'],
                                    'guests' => $validated['guests'],
                                ]) }}"
                                class="block text-center w-full mt-5 bg-sky-500
                                       hover:bg-sky-600 text-white font-bold
                                       rounded-xl px-5 py-3 transition"
                            >
                                Lanjutkan Pemesanan
                            </a>
                        @endif
                    </div>
                @endif

                <div class="mt-5 text-sm text-slate-500 border-t pt-4">
                    <p>Check-in: 14.00 WITA</p>
                    <p>Check-out: 12.00 WITA</p>
                </div>
            </div>
        </aside>

    </div>
</main>

<footer class="mt-12 bg-slate-900 text-slate-300 py-6">
    <div class="max-w-6xl mx-auto px-5 text-center text-sm">
        © {{ date('Y') }} VillaStay Bali
    </div>
</footer>

</body>
</html>
