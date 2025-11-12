<script setup>
import Navbar from "@/Components/Navbar.vue";
import { ref, computed, inject } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import Footer from "../../Components/Footer.vue";

const { props } = usePage();
const products = ref(props.products || []);

const isMobileMenuOpen = ref(false);
const searchQuery = ref("");
const selectedCategory = ref("all");
const helpers = inject("helpers");
console.log(helpers.imageUrl("01K9SAKX90WSW4KYS47XPY1XGC.jpg"));
const categories = [
    { id: "all", name: "Semua Produk" },
    { id: "mirrorless", name: "Mirrorless" },
    { id: "dslr", name: "DSLR" },
    { id: "action", name: "Action Camera" },
    { id: "lens", name: "Lensa" },
];

const filteredProducts = computed(() => {
    let result = products.value;

    // Filter by search
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        result = result.filter(
            (product) =>
                product.nama.toLowerCase().includes(query) ||
                product.deskripsi.toLowerCase().includes(query)
        );
    }

    return result;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navbar -->
        <Navbar />
        <!-- Hero Section -->
        <section
            class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white overflow-hidden"
        >
            <div class="absolute inset-0 opacity-10">
                <div
                    class="absolute transform rotate-45 -top-32 -right-32 w-96 h-96 bg-white rounded-full"
                ></div>
                <div
                    class="absolute transform -rotate-12 -bottom-32 -left-32 w-96 h-96 bg-white rounded-full"
                ></div>
            </div>

            <div
                class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24"
            >
                <div class="text-center max-w-3xl mx-auto">
                    <h1
                        class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight"
                    >
                        Sewa Kamera Profesional
                        <span class="block text-blue-200 mt-2"
                            >Dengan Mudah</span
                        >
                    </h1>
                    <p class="text-lg md:text-xl text-blue-100 mb-8">
                        Kualitas terbaik untuk momen terbaik Anda. Ribuan
                        pelanggan puas telah mempercayai kami.
                    </p>

                    <!-- Search Bar Hero -->
                    <div class="max-w-2xl mx-auto">
                        <div class="relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari kamera, lensa, atau aksesoris..."
                                class="w-full px-6 py-4 pr-32 rounded-full text-gray-800 shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300"
                            />
                            <button
                                class="absolute right-2 top-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full font-medium transition"
                            >
                                Cari
                            </button>
                        </div>
                    </div>

                    <!-- Features -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
                        <div
                            class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6"
                        >
                            <span class="text-4xl mb-3 block">✅</span>
                            <h3 class="font-bold text-lg mb-2">
                                Garansi Kualitas
                            </h3>
                            <p class="text-blue-100 text-sm">
                                Peralatan terawat & berkualitas tinggi
                            </p>
                        </div>
                        <div
                            class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6"
                        >
                            <span class="text-4xl mb-3 block">⚡</span>
                            <h3 class="font-bold text-lg mb-2">Proses Cepat</h3>
                            <p class="text-blue-100 text-sm">
                                Booking online, ambil langsung
                            </p>
                        </div>
                        <div
                            class="bg-white bg-opacity-10 backdrop-blur-sm rounded-xl p-6"
                        >
                            <span class="text-4xl mb-3 block">💰</span>
                            <h3 class="font-bold text-lg mb-2">
                                Harga Terjangkau
                            </h3>
                            <p class="text-blue-100 text-sm">
                                Harga sewa mulai 50rb/hari
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Category Filter -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    Kategori Produk
                </h2>
                <div class="flex flex-wrap gap-3">
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="selectedCategory = category.id"
                        :class="[
                            'px-6 py-2 rounded-full font-medium transition',
                            selectedCategory === category.id
                                ? 'bg-blue-600 text-white shadow-lg'
                                : 'bg-white text-gray-600 hover:bg-gray-100',
                        ]"
                    >
                        {{ category.name }}
                    </button>
                </div>
            </div>

            <!-- Products Header -->
            <div
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
            >
                <h2 class="text-2xl font-bold text-gray-800 mb-4 sm:mb-0">
                    Produk Tersedia
                    <span class="text-blue-600"
                        >({{ filteredProducts.length }})</span
                    >
                </h2>

                <select
                    class="px-4 py-2 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500"
                >
                    <option>Urutkan: Terpopuler</option>
                    <option>Harga: Rendah ke Tinggi</option>
                    <option>Harga: Tinggi ke Rendah</option>
                    <option>Terbaru</option>
                </select>
            </div>

            <!-- Empty State -->
            <div
                v-if="filteredProducts.length === 0"
                class="text-center py-20 bg-white rounded-2xl shadow"
            >
                <span class="text-7xl mb-4 block">📭</span>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    Produk tidak ditemukan
                </h3>
                <p class="text-gray-600 mb-6">
                    Coba kata kunci lain atau lihat semua produk
                </p>
                <button
                    @click="
                        searchQuery = '';
                        selectedCategory = 'all';
                    "
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                >
                    Lihat Semua Produk
                </button>
            </div>

            <!-- Products Grid -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12"
            >
                <div
                    v-for="product in filteredProducts"
                    :key="product.id"
                    class="bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden group"
                >
                    <div class="relative overflow-hidden h-56">
                        <img
                            :src="helpers.imageUrl(product.gambar)"
                            :alt="product.nama"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            type="image/webp"
                        />
                        <div
                            class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full shadow-lg"
                        >
                            <span
                                class="text-green-600 text-sm font-bold flex items-center"
                            >
                                <span
                                    class="w-2 h-2 bg-green-500 rounded-full mr-2"
                                ></span>
                                Tersedia
                            </span>
                        </div>
                        <div
                            class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4"
                        >
                            <span
                                class="text-white text-xs bg-blue-600 px-3 py-1 rounded-full"
                            >
                                Mirrorless
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3
                            class="text-xl font-bold mb-2 text-gray-800 group-hover:text-blue-600 transition"
                        >
                            {{ product.nama }}
                        </h3>
                        <p class="text-gray-600 mb-4 text-sm line-clamp-2">
                            {{ product.deskripsi }}
                        </p>

                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-3xl font-bold text-blue-600">
                                    {{
                                        formatCurrency(
                                            product.harga_sewa_perhari
                                        )
                                    }}
                                </p>
                                <p class="text-xs text-gray-500">per hari</p>
                            </div>
                            <div class="text-right">
                                <div
                                    class="flex items-center text-yellow-500 mb-1"
                                >
                                    ⭐⭐⭐⭐⭐
                                </div>
                                <p class="text-xs text-gray-500">128 ulasan</p>
                            </div>
                        </div>

                        <Link
                            :href="`/rental/product/${product.id}`"
                            class="block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition-colors shadow-lg hover:shadow-xl"
                        >
                            Lihat Detail & Booking
                        </Link>
                    </div>
                </div>
            </div>

            <!-- CTA Section -->
            <section
                class="bg-gradient-to-r from-indigo-600 to-blue-600 rounded-3xl p-12 text-center text-white mb-12 shadow-xl"
            >
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Butuh Bantuan Memilih?
                </h2>
                <p class="text-lg text-blue-100 mb-8 max-w-2xl mx-auto">
                    Tim kami siap membantu Anda menemukan peralatan yang tepat
                    untuk kebutuhan photography Anda
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/contact"
                        class="px-8 py-4 bg-white text-blue-600 rounded-xl font-bold hover:bg-gray-100 transition shadow-lg"
                    >
                        💬 Hubungi Kami
                    </Link>
                    <Link
                        href="/products"
                        class="px-8 py-4 bg-transparent border-2 border-white text-white rounded-xl font-bold hover:bg-white hover:text-blue-600 transition"
                    >
                        📦 Lihat Semua Produk
                    </Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <Footer />
    </div>
</template>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
