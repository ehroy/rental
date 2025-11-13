<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
const form = useForm({
    name: "",
    email: "",
    phone: "",
    subject: "",
    message: "",
});

const submitForm = () => {
    form.post("/contact", {
        onSuccess: () => {
            alert(
                "Pesan Anda telah terkirim! Kami akan segera menghubungi Anda."
            );
            form.reset();
        },
    });
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Hero Section -->
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Contact Info Cards -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Phone Card -->
                    <div
                        class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition"
                    >
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center text-2xl"
                            >
                                📞
                            </div>
                            <div>
                                <h3
                                    class="font-bold text-lg text-gray-900 mb-2"
                                >
                                    Telepon
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    Hubungi kami langsung
                                </p>
                                <a
                                    href="tel:+6281234567890"
                                    class="text-blue-600 hover:text-blue-700 font-semibold"
                                >
                                    +62 812 3456 7890
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    Sen-Sab: 09.00-18.00
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Email Card -->
                    <div
                        class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition"
                    >
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-2xl"
                            >
                                📧
                            </div>
                            <div>
                                <h3
                                    class="font-bold text-lg text-gray-900 mb-2"
                                >
                                    Email
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    Kirim email ke kami
                                </p>
                                <a
                                    href="mailto:info@camerarent.com"
                                    class="text-blue-600 hover:text-blue-700 font-semibold break-all"
                                >
                                    info@camerarent.com
                                </a>
                                <p class="text-sm text-gray-500 mt-1">
                                    Respon dalam 24 jam
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Location Card -->
                    <div
                        class="bg-white rounded-lg shadow-lg p-6 hover:shadow-xl transition"
                    >
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center text-2xl"
                            >
                                📍
                            </div>
                            <div>
                                <h3
                                    class="font-bold text-lg text-gray-900 mb-2"
                                >
                                    Lokasi
                                </h3>
                                <p class="text-gray-600 mb-2">
                                    Kunjungi toko kami
                                </p>
                                <p class="text-gray-700">
                                    Jazzphotostudio<br />
                                    Kudus, Jawa Tengah<br />
                                    Indonesia
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Card -->
                    <div
                        class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 hover:shadow-xl transition text-white"
                    >
                        <div class="flex items-start space-x-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white rounded-lg flex items-center justify-center text-2xl"
                            >
                                💬
                            </div>
                            <div>
                                <h3 class="font-bold text-lg mb-2">WhatsApp</h3>
                                <p class="text-green-50 mb-3">
                                    Chat dengan kami sekarang
                                </p>
                                <a
                                    href="https://wa.me/6281234567890"
                                    target="_blank"
                                    class="inline-block bg-white text-green-600 px-4 py-2 rounded-lg font-semibold hover:bg-green-50 transition"
                                >
                                    Chat Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">
                            Kirim Pesan
                        </h2>
                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Nama Lengkap *
                                    </label>
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="John Doe"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Email *
                                    </label>
                                    <input
                                        v-model="form.email"
                                        type="email"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="john@example.com"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        No. Telepon *
                                    </label>
                                    <input
                                        v-model="form.phone"
                                        type="tel"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                        placeholder="+62 812 3456 7890"
                                    />
                                </div>
                                <div>
                                    <label
                                        class="block text-sm font-semibold text-gray-700 mb-2"
                                    >
                                        Subjek *
                                    </label>
                                    <select
                                        v-model="form.subject"
                                        required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                    >
                                        <option value="">Pilih subjek</option>
                                        <option value="rental">
                                            Rental Peralatan
                                        </option>
                                        <option value="price">
                                            Informasi Harga
                                        </option>
                                        <option value="booking">
                                            Booking & Reservasi
                                        </option>
                                        <option value="support">
                                            Technical Support
                                        </option>
                                        <option value="other">Lainnya</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-semibold text-gray-700 mb-2"
                                >
                                    Pesan *
                                </label>
                                <textarea
                                    v-model="form.message"
                                    required
                                    rows="6"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition resize-none"
                                    placeholder="Tuliskan pesan Anda di sini..."
                                ></textarea>
                            </div>

                            <div class="flex items-start space-x-2">
                                <input
                                    type="checkbox"
                                    required
                                    class="mt-1 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                                <label class="text-sm text-gray-600">
                                    Saya setuju dengan
                                    <a
                                        href="/privacy"
                                        class="text-blue-600 hover:text-blue-700"
                                        >kebijakan privasi</a
                                    >
                                    dan
                                    <a
                                        href="/terms"
                                        class="text-blue-600 hover:text-blue-700"
                                        >syarat & ketentuan</a
                                    >
                                </label>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold py-4 rounded-lg transition transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="!form.processing"
                                    >Kirim Pesan 📨</span
                                >
                                <span v-else>Mengirim...</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Map Section -->
            <div class="mt-16">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-8">
                    Temukan Kami
                </h2>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d494.0723638856843!2d110.70977520596663!3d-6.615531035896612!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e711fe9dd48fbdf%3A0x23acd3348e773625!2sJazzphotostudio!5e0!3m2!1sid!2sid!4v1731482000000!5m2!1sid!2sid"
                        width="100%"
                        height="450"
                        style="border: 0"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
            </div>

            <!-- FAQ Quick Links -->
            <div class="mt-16 bg-blue-50 rounded-lg p-8">
                <h3 class="text-2xl font-bold text-center text-gray-900 mb-6">
                    Pertanyaan yang Sering Diajukan
                </h3>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-4xl mx-auto"
                >
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="font-bold text-lg text-gray-900 mb-2">
                            🕐 Jam Operasional
                        </h4>
                        <p class="text-gray-600">
                            Senin - Sabtu: 09.00 - 18.00 WIB<br />Minggu &
                            Libur: Tutup
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="font-bold text-lg text-gray-900 mb-2">
                            ⚡ Respon Time
                        </h4>
                        <p class="text-gray-600">
                            Email: Maksimal 24 jam<br />WhatsApp: Instan (jam
                            kerja)
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="font-bold text-lg text-gray-900 mb-2">
                            💳 Metode Pembayaran
                        </h4>
                        <p class="text-gray-600">
                            Transfer Bank, E-Wallet, QRIS, dan Cash
                        </p>
                    </div>
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h4 class="font-bold text-lg text-gray-900 mb-2">
                            📦 Pengiriman
                        </h4>
                        <p class="text-gray-600">
                            Tersedia pengiriman ke seluruh Indonesia via kurir
                            terpercaya
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
