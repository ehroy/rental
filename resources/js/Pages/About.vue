<script setup>
import { Link } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <Navbar />
        <!-- Hero Section -->

        <!-- Story Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">
                        Cerita Kami
                    </h2>
                    <div class="space-y-4 text-gray-700">
                        <p>
                            CameraRent Pro didirikan pada tahun 2018 dengan misi
                            sederhana: membuat peralatan photography profesional
                            lebih mudah diakses oleh semua orang, dari pemula
                            hingga profesional.
                        </p>
                        <p>
                            Kami memahami bahwa tidak semua orang mampu membeli
                            peralatan photography yang mahal. Oleh karena itu,
                            kami hadir sebagai solusi dengan menyediakan layanan
                            rental yang terpercaya dan terjangkau.
                        </p>
                        <p>
                            Dengan pengalaman lebih dari 5 tahun, kami telah
                            melayani ribuan pelanggan dari berbagai kalangan -
                            fotografer wedding, videographer, content creator,
                            hingga perusahaan besar.
                        </p>
                    </div>
                </div>
                <div class="relative">
                    <div
                        class="aspect-w-16 aspect-h-12 rounded-lg overflow-hidden shadow-2xl"
                    >
                        <img
                            src="https://images.unsplash.com/photo-1452587925148-ce544e77e70d?w=800"
                            alt="Camera Equipment"
                            class="w-full h-96 object-cover"
                        />
                    </div>
                    <div
                        class="absolute -bottom-6 -right-6 bg-blue-600 text-white p-6 rounded-lg shadow-xl"
                    >
                        <p class="text-4xl font-bold">5+</p>
                        <p class="text-sm">Tahun Pengalaman</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="bg-white py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                    Nilai-Nilai Kami
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div
                        class="text-center p-6 rounded-lg hover:shadow-lg transition"
                    >
                        <div class="text-5xl mb-4">🎯</div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">
                            Kualitas Terjamin
                        </h3>
                        <p class="text-gray-600">
                            Semua peralatan kami dijaga dalam kondisi prima dan
                            selalu dilakukan pengecekan sebelum disewakan.
                        </p>
                    </div>
                    <div
                        class="text-center p-6 rounded-lg hover:shadow-lg transition"
                    >
                        <div class="text-5xl mb-4">💎</div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">
                            Harga Terjangkau
                        </h3>
                        <p class="text-gray-600">
                            Kami menawarkan harga rental yang kompetitif tanpa
                            mengorbankan kualitas peralatan.
                        </p>
                    </div>
                    <div
                        class="text-center p-6 rounded-lg hover:shadow-lg transition"
                    >
                        <div class="text-5xl mb-4">🤝</div>
                        <h3 class="text-xl font-bold mb-3 text-gray-900">
                            Pelayanan Prima
                        </h3>
                        <p class="text-gray-600">
                            Tim kami siap membantu Anda 24/7 untuk memastikan
                            pengalaman rental yang menyenangkan.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div
                    class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center text-white"
                >
                    <div>
                        <p class="text-4xl font-bold mb-2">5000+</p>
                        <p class="text-blue-100">Pelanggan Puas</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold mb-2">150+</p>
                        <p class="text-blue-100">Peralatan Ready</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold mb-2">98%</p>
                        <p class="text-blue-100">Rating Kepuasan</p>
                    </div>
                    <div>
                        <p class="text-4xl font-bold mb-2">24/7</p>
                        <p class="text-blue-100">Customer Support</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">
                Tim Kami
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div
                        class="w-32 h-32 mx-auto mb-4 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white text-4xl font-bold"
                    >
                        AR
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Ahmad Rizky</h3>
                    <p class="text-gray-600 text-sm">Founder & CEO</p>
                </div>
                <div class="text-center">
                    <div
                        class="w-32 h-32 mx-auto mb-4 rounded-full bg-gradient-to-br from-green-400 to-blue-500 flex items-center justify-center text-white text-4xl font-bold"
                    >
                        SP
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Siti Putri</h3>
                    <p class="text-gray-600 text-sm">Operations Manager</p>
                </div>
                <div class="text-center">
                    <div
                        class="w-32 h-32 mx-auto mb-4 rounded-full bg-gradient-to-br from-pink-400 to-red-500 flex items-center justify-center text-white text-4xl font-bold"
                    >
                        BW
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Budi Wijaya</h3>
                    <p class="text-gray-600 text-sm">Technical Specialist</p>
                </div>
                <div class="text-center">
                    <div
                        class="w-32 h-32 mx-auto mb-4 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center text-white text-4xl font-bold"
                    >
                        DA
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">
                        Dewi Anggraini
                    </h3>
                    <p class="text-gray-600 text-sm">Customer Service</p>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="bg-gray-900 text-white py-16 mb-24 sm:mb-0">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold mb-4">
                    Siap Memulai Project Anda?
                </h2>
                <p class="text-gray-300 mb-8">
                    Hubungi kami sekarang untuk mendapatkan peralatan
                    photography terbaik
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/products"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-semibold transition"
                    >
                        Lihat Produk
                    </Link>
                    <Link
                        href="/contact"
                        class="bg-white hover:bg-gray-100 text-gray-900 px-8 py-3 rounded-lg font-semibold transition"
                    >
                        Hubungi Kami
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
