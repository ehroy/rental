<script setup>
import { Link, usePage } from "@inertiajs/vue3";
</script>
<template>
    <footer class="bg-gray-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <span class="text-3xl">📷</span>
                        <span class="text-2xl font-bold">CameraRent Pro</span>
                    </div>
                    <p class="text-gray-400 mb-4">
                        Platform rental kamera profesional terpercaya di
                        Indonesia. Kami menyediakan berbagai peralatan
                        photography berkualitas tinggi dengan harga terjangkau.
                    </p>
                    <div class="flex space-x-4">
                        <a
                            href="#"
                            class="text-2xl hover:text-blue-400 transition"
                            >📘</a
                        >
                        <a
                            href="#"
                            class="text-2xl hover:text-blue-400 transition"
                            >📷</a
                        >
                        <a
                            href="#"
                            class="text-2xl hover:text-blue-400 transition"
                            >🐦</a
                        >
                        <a
                            href="#"
                            class="text-2xl hover:text-blue-400 transition"
                            >▶️</a
                        >
                    </div>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
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

                <div>
                    <h4 class="font-bold text-lg mb-4">Hubungi Kami</h4>
                    <ul class="space-y-3 text-gray-400">
                        <li class="flex items-start space-x-2">
                            <span>📍</span>
                            <span
                                >Jl. Sudirman No. 123<br />Jakarta Pusat,
                                10110</span
                            >
                        </li>
                        <li class="flex items-center space-x-2">
                            <span>📞</span>
                            <span>+62 812 3456 7890</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span>📧</span>
                            <span>info@camerarent.com</span>
                        </li>
                        <li class="flex items-center space-x-2">
                            <span>⏰</span>
                            <span>Sen-Sab: 09.00-18.00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="border-t border-gray-800 pt-8 text-center text-gray-400"
            >
                <p>
                    &copy; 2025 CameraRent Pro. All rights reserved. Made with
                    ❤️ in Indonesia
                </p>
            </div>
        </div>
    </footer>
</template>
