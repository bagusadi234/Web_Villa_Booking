<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ringkasan Pemesanan | VillaStay Bali</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#FAF7F2] text-[#1C2A33]">
    <main class="max-w-2xl mx-auto px-5 py-12">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 md:p-8">

            {{-- Header --}}
            <p class="text-sm uppercase tracking-widest text-[#2E8B6A] font-bold">
                Ringkasan Pemesanan
            </p>

            <h1 class="text-3xl font-bold mt-3">
                Booking Villa
            </h1>

            <p class="text-gray-500 mt-2">
                Simpan kode booking ini untuk referensi pemesanan kamu.
            </p>

            {{-- Kode booking --}}
            <div class="rounded-xl bg-gray-50 p-4 mt-6">
                <p class="text-sm text-gray-500">Kode booking</p>

                <p class="text-xl font-bold mt-1 break-words">
                    {{ $booking->booking_code }}
                </p>
            </div>

            {{-- Informasi booking --}}
            <div class="space-y-5 mt-6">

                <div>
                    <p class="text-sm text-gray-500">Nama pemesan</p>
                    <p class="font-semibold">
                        {{ $booking->customer_name }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Villa</p>
                    <p class="font-semibold">
                        {{ $booking->bookingUnits->first()?->villaUnit?->villaType?->name ?? 'Villa' }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Tanggal menginap</p>
                    <p class="font-semibold">
                        {{ $booking->check_in->format('d M Y') }}
                        – {{ $booking->check_out->format('d M Y') }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Jumlah tamu</p>
                    <p class="font-semibold">
                        {{ $booking->guest_count }} tamu
                    </p>
                </div>

                <div>
                    <p class="text-sm text-gray-500">Total harga</p>
                    <p class="text-2xl font-bold text-[#1F5F7A]">
                        Rp{{ number_format((int) $booking->total_amount, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Status booking --}}
                <div>
                    <p class="text-sm text-gray-500">Status booking</p>

                    <p class="font-semibold mt-1">
                        @switch($booking->status)
                            @case('pending_payment')
                                <span class="text-amber-700">
                                    Menunggu pembayaran
                                </span>
                                @break

                            @case('confirmed')
                                <span class="text-green-700">
                                    Booking dikonfirmasi
                                </span>
                                @break

                            @case('checked_in')
                                Sedang menginap
                                @break

                            @case('checked_out')
                                Selesai menginap
                                @break

                            @case('cancelled')
                                <span class="text-red-700">
                                    Booking dibatalkan
                                </span>
                                @break

                            @case('expired')
                                <span class="text-red-700">
                                    Waktu pemesanan berakhir
                                </span>
                                @break

                            @case('no_show')
                                Tidak hadir
                                @break

                            @default
                                {{ $booking->status }}
                        @endswitch
                    </p>
                </div>

                {{-- Status pembayaran --}}
                <div>
                    <p class="text-sm text-gray-500">Status pembayaran</p>

                    <p class="font-semibold mt-1">
                        @switch($booking->payment_status)
                            @case('unpaid')
                                Belum dibayar
                                @break

                            @case('partially_paid')
                                Pembayaran sebagian
                                @break

                            @case('paid')
                                <span class="text-green-700">Lunas</span>
                                @break

                            @case('partially_refunded')
                                Pengembalian dana sebagian
                                @break

                            @case('refunded')
                                Dana dikembalikan
                                @break

                            @default
                                {{ $booking->payment_status }}
                        @endswitch
                    </p>
                </div>

                {{-- Batas waktu hold --}}
                <div>
                    <p class="text-sm text-gray-500">Batas waktu hold</p>

                    <p class="font-semibold mt-1">
                        @if ($booking->hold_expires_at)
                            {{ $booking->hold_expires_at->format('d M Y, H:i') }}
                            WITA
                        @else
                            -
                        @endif
                    </p>
                </div>

            </div>

            {{-- Informasi sesuai kondisi booking --}}
            @if ($booking->status === 'pending_payment')
                @if ($booking->hold_expires_at && $booking->hold_expires_at->isFuture())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 mt-6 text-sm text-amber-900">
                        <p class="font-bold">Booking menunggu pembayaran</p>

                        <p class="mt-1">
                            Selesaikan pembayaran sebelum batas waktu hold berakhir.
                            Saat ini halaman ini belum memproses pembayaran.
                        </p>
                    </div>
                @else
                    <div class="rounded-xl border border-red-200 bg-red-50 p-4 mt-6 text-sm text-red-900">
                        <p class="font-bold">Waktu hold telah berakhir</p>

                        <p class="mt-1">
                            Jangan melakukan pembayaran sebelum status booking
                            dikonfirmasi oleh pengelola.
                        </p>
                    </div>
                @endif

            @elseif ($booking->payment_status === 'partially_paid')
                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 mt-6 text-sm text-amber-900">
                    <p class="font-bold">Pembayaran sebagian telah tercatat</p>

                    <p class="mt-1">
                        Hubungi pengelola untuk memastikan sisa pembayaran
                        dan status konfirmasi booking.
                    </p>
                </div>

            @elseif ($booking->payment_status === 'paid')
                <div class="rounded-xl border border-green-200 bg-green-50 p-4 mt-6 text-sm text-green-900">
                    <p class="font-bold">Pembayaran lunas</p>

                    <p class="mt-1">
                        Pembayaran telah tercatat berhasil di sistem.
                    </p>
                </div>

            @elseif ($booking->status === 'expired' || $booking->status === 'cancelled')
                <div class="rounded-xl border border-red-200 bg-red-50 p-4 mt-6 text-sm text-red-900">
                    Jika kamu sudah melakukan pembayaran, hubungi pengelola
                    untuk pemeriksaan transaksi.
                </div>
            @endif

            {{-- Tombol kembali --}}
            <a href="{{ route('villas.index') }}"
               class="mt-6 inline-flex w-full justify-center rounded-xl bg-[#1F5F7A] px-5 py-3 font-semibold text-white hover:bg-[#194d63] transition">
                Kembali ke halaman villa
            </a>

        </div>
    </main>
</body>
</html>
