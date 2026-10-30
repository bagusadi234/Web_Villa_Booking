
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pemesanan | VillaStay Bali</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #FAF7F2;
            color: #1C2A33;
        }

        .field {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #E3DED5;
            border-radius: 10px;
            background: white;
            outline: none;
        }

        .field:focus {
            border-color: #1F5F7A;
            box-shadow: 0 0 0 2px #1F5F7A20;
        }
    </style>
</head>

<body>
    <header class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-5 py-5">
            <a href="{{ route('villas.index') }}"
               class="text-xl font-bold text-[#1F5F7A]">
                VillaStay <span class="text-sm tracking-widest">BALI</span>
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-5 py-10">
        <a href="{{ route('villas.index') }}"
           class="text-sm text-[#1F5F7A] hover:underline">
            &larr; Kembali ke halaman villa
        </a>

        <div class="grid md:grid-cols-3 gap-8 mt-6">
            <section class="md:col-span-2 bg-white rounded-2xl p-6 md:p-8 shadow-sm">
                <p class="text-sm uppercase tracking-widest text-[#1F5F7A] font-bold">
                    Langkah berikutnya
                </p>

                <h1 class="text-3xl font-bold mt-2">
                    Form Pemesanan Villa
                </h1>

                <p class="text-gray-500 mt-2">
                    Isi data dengan benar untuk melanjutkan pemesanan.
                </p>

                @if ($errors->any())
                    <div class="mt-6 rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
                        <p class="font-semibold">
                            Ada data yang perlu diperiksa:
                        </p>

                        <ul class="list-disc pl-5 mt-2 text-sm space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('bookings.store') }}"
                      class="mt-7 space-y-5">
                    @csrf

                    <input type="hidden"
                           name="villa_type_id"
                           value="{{ $villa->id }}">

                    <input type="hidden"
                           name="check_in"
                           value="{{ $checkIn }}">

                    <input type="hidden"
                           name="check_out"
                           value="{{ $checkOut }}">

                    <div>
                        <label for="customer_name" class="block font-semibold mb-2">
                            Nama lengkap *
                        </label>

                        <input id="customer_name"
                               name="customer_name"
                               type="text"
                               maxlength="150"
                               autocomplete="name"
                               required
                               value="{{ old('customer_name') }}"
                               class="field"
                               placeholder="Masukkan nama lengkap">
                    </div>

                    <div>
                        <label for="customer_email" class="block font-semibold mb-2">
                            Email
                        </label>

                        <input id="customer_email"
                               name="customer_email"
                               type="email"
                               maxlength="150"
                               autocomplete="email"
                               value="{{ old('customer_email') }}"
                               class="field"
                               placeholder="nama@email.com"
                               required>
                    </div>

                    <div>
                        <label for="customer_phone" class="block font-semibold mb-2">
                            Nomor telepon / WhatsApp *
                        </label>

                        <input id="customer_phone"
                               name="customer_phone"
                               type="tel"
                               maxlength="30"
                               autocomplete="tel"
                               required
                               value="{{ old('customer_phone') }}"
                               class="field"
                               placeholder="Contoh: 08xxxxxxxxxx">
                    </div>

                    <div>
                        <label for="guest_count" class="block font-semibold mb-2">
                            Jumlah tamu *
                        </label>

                        <select id="guest_count"
                                name="guest_count"
                                required
                                class="field">
                            @for ($i = 1; $i <= $villa->max_capacity; $i++)
                                <option value="{{ $i }}"
                                    {{ (int) old('guest_count', $guests) === $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i === 1 ? 'tamu' : 'tamu' }}
                                </option>
                            @endfor
                        </select>

                        <p class="text-sm text-gray-500 mt-2">
                            Kapasitas maksimal {{ $villa->max_capacity }} tamu.
                        </p>
                    </div>

                    <div>
                        <label for="special_requests" class="block font-semibold mb-2">
                            Permintaan khusus (opsional)
                        </label>

                        <textarea id="special_requests"
                                  name="special_requests"
                                  rows="3"
                                  maxlength="1000"
                                  class="field"
                                  placeholder="Contoh: permintaan tempat tidur tambahan">{{ old('special_requests') }}</textarea>
                    </div>

                    <button type="submit"
                            class="w-full rounded-xl bg-[#1F5F7A] text-white font-bold py-4 px-5 hover:bg-[#17485D] transition">
                        Buat Pemesanan
                    </button>

                    <p class="text-xs text-gray-500">
                        Pemesanan akan menahan unit selama 20 menit.
                        Pembayaran belum dilakukan pada tahap ini.
                    </p>
                </form>
            </section>

            <aside class="bg-white rounded-2xl p-6 shadow-sm h-fit">
                <p class="text-sm uppercase tracking-widest text-[#1F5F7A] font-bold">
                    Ringkasan
                </p>

                <h2 class="text-xl font-bold mt-3">
                    {{ $villa->name }}
                </h2>

                <p class="text-gray-500 mt-1">
                    {{ $villa->branch->name ?? 'Villa Bali' }}
                </p>

                <div class="border-t border-gray-200 my-5"></div>

                <div class="flex justify-between gap-3 mb-3">
                    <span class="text-gray-500">Check-in</span>
                    <strong>{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</strong>
                </div>

                <div class="flex justify-between gap-3 mb-3">
                    <span class="text-gray-500">Check-out</span>
                    <strong>{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</strong>
                </div>

                <div class="flex justify-between gap-3 mb-3">
                    <span class="text-gray-500">Jumlah tamu</span>
                    <strong>{{ $guests }} tamu</strong>
                </div>

                <div class="rounded-lg bg-[#FAF7F2] p-4 mt-5">
                    <p class="text-sm text-gray-500">
                        Total harga akan dihitung ulang oleh sistem saat pemesanan dibuat.
                    </p>
                </div>
            </aside>
        </div>
    </main>
</body>
</html>
