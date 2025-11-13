<script setup>
import { Link, usePage } from "@inertiajs/vue3";
</script>

<template>
    <footer class="bg-[#333333] text-[#ebebeb]">
        <div
            class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 pb-24 sm:pb-12"
        >
            <!-- Grid Layout -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Company Info -->
                <div class="md:col-span-2 text-center md:text-left">
                    <div
                        class="flex items-center justify-center md:justify-start space-x-2 mb-4"
                    >
                        <span class="text-2xl sm:text-3xl">📷</span>
                        <span class="text-xl sm:text-2xl font-bold"
                            >CameraRent Pro</span
                        >
                    </div>
                    <p class="text-[#ebebeb] text-sm sm:text-base mb-4">
                        Platform rental kamera profesional terpercaya di
                        Indonesia. Kami menyediakan berbagai peralatan
                        photography berkualitas tinggi dengan harga terjangkau.
                    </p>
                    <div class="flex justify-center md:justify-start space-x-4">
                        <a
                            href="#"
                            class="text-xl sm:text-2xl hover:text-blue-400 transition"
                            aria-label="Facebook"
                            >📘</a
                        >
                        <a
                            href="#"
                            class="text-xl sm:text-2xl hover:text-blue-400 transition"
                            aria-label="Instagram"
                            >📷</a
                        >
                        <a
                            href="#"
                            class="text-xl sm:text-2xl hover:text-blue-400 transition"
                            aria-label="Twitter"
                            >🐦</a
                        >
                        <a
                            href="#"
                            class="text-xl sm:text-2xl hover:text-blue-400 transition"
                            aria-label="YouTube"
                            >▶️</a
                        >
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center md:text-left">
                    <h4 class="font-bold text-base sm:text-lg mb-4">
                        Quick Links
                    </h4>
                    <ul class="space-y-2 text-[#ebebeb] text-sm sm:text-base">
                        <li>
                            <Link
                                href="/about"
                                class="hover:text-white transition"
                                >Tentang Kami</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/products"
                                class="hover:text-white transition"
                                >Produk</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/faq"
                                class="hover:text-white transition"
                                >FAQ</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/terms"
                                class="hover:text-white transition"
                                >Syarat & Ketentuan</Link
                            >
                        </li>
                        <li>
                            <Link
                                href="/privacy"
                                class="hover:text-white transition"
                                >Kebijakan Privasi</Link
                            >
                        </li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="text-center md:text-left">
                    <h4 class="font-bold text-base sm:text-lg mb-4">
                        Hubungi Kami
                    </h4>
                    <ul class="space-y-3 text-[#ebebeb] text-sm sm:text-base">
                        <li
                            class="flex items-start justify-center md:justify-start space-x-2"
                        >
                            <span class="flex-shrink-0">📍</span>
                            <span class="text-left">
                                Jazzphotostudio<br />Kudus, Jawa Tengah
                            </span>
                        </li>
                        <li
                            class="flex items-center justify-center md:justify-start space-x-2"
                        >
                            <span>📞</span>
                            <span>+62 812 3456 7890</span>
                        </li>
                        <li
                            class="flex items-center justify-center md:justify-start space-x-2"
                        >
                            <span>📧</span>
                            <span class="break-all">info@camerarent.com</span>
                        </li>
                        <li
                            class="flex items-center justify-center md:justify-start space-x-2"
                        >
                            <span>⏰</span>
                            <span>Sen-Sab: 09.00-18.00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Google Maps -->
            <div class="mb-8">
                <h4
                    class="font-bold text-base sm:text-lg mb-4 text-center md:text-left"
                >
                    Lokasi Kami
                </h4>
                <div
                    class="w-full h-64 sm:h-80 rounded-lg overflow-hidden shadow-lg"
                >
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d494.0723638856843!2d110.70977520596663!3d-6.615531035896612!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e711fe9dd48fbdf%3A0x23acd3348e773625!2sJazzphotostudio!5e0!3m2!1sid!2sid!4v1731482000000!5m2!1sid!2sid"
                        width="100%"
                        height="100%"
                        style="border: 0"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        class="grayscale"
                    ></iframe>
                </div>
            </div>

            <!-- Copyright -->
            <div
                class="border-t border-gray-700 pt-6 sm:pt-8 text-center text-[#ebebeb] text-sm sm:text-base"
            >
                <p>
                    &copy; 2025 CameraRent Pro. All rights reserved. Made with
                    ❤️ in Indonesia
                </p>
            </div>
        </div>
    </footer>
</template>

<style scoped>
/* Tambahan styling untuk memastikan footer tidak tertutup navbar mobile */
@media (max-width: 768px) {
    footer {
        margin-bottom: env(safe-area-inset-bottom);
    }
}

/* Styling untuk Google Maps */
iframe {
    transition: filter 0.3s ease;
}

iframe:hover {
    filter: grayscale(0%) !important;
}
</style>
