<script setup>
import Footer from "@/Components/Footer.vue";
import Navbar from "@/Components/Navbar.vue";
import { ref, computed, inject, watch } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";

const page = usePage();

// Gunakan computed agar reactive terhadap perubahan props
const products = computed(() => page.props.products || []);
const categories = computed(() => page.props.categories || []);
const filters = computed(() => page.props.filters || {});

const isMobileMenuOpen = ref(false);
const searchQuery = ref(filters.value.search || "");
const selectedCategory = ref(filters.value.category_id || "");
const isLoading = ref(false); // Tambahkan loading state
const helpers = inject("helpers");

// Watch untuk perubahan search query dengan debounce
let searchTimeout = null;
watch(searchQuery, (newValue) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

// Watch untuk perubahan kategori
watch(selectedCategory, (newValue) => {
    applyFilters();
});

// Fungsi untuk apply filters
const applyFilters = () => {
    const params = {};

    if (selectedCategory.value) {
        params.category_id = selectedCategory.value;
    }

    if (searchQuery.value) {
        params.search = searchQuery.value;
    }

    isLoading.value = true;

    router.get(page.url, params, {
        preserveState: true,
        preserveScroll: true,
        only: ["products", "filters"], // Hanya reload products dan filters
        onFinish: () => {
            isLoading.value = false;
        },
    });
};

// Reset filters
const resetFilters = () => {
    searchQuery.value = "";
    selectedCategory.value = "";
    applyFilters();
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <div class="min-h-screen bg-[#FFFFFF] text-[#ebebeb]">
        <!-- Navbar -->
        <Navbar />

        <!-- Hero Section -->
        <section
            class="relative bg-cover bg-center bg-no-repeat overflow-hidden"
            style="background-image: url('/images/hero1.png')"
        >
            <div class="absolute inset-0 bg-black/50"></div>

            <div
                class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24"
            >
                <div class="text-center max-w-3xl mx-auto">
                    <h1
                        class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight font-poppins"
                    >
                        Sewa Kamera Profesional
                        <span class="block mt-2">Dengan Mudah</span>
                    </h1>
                    <p class="text-lg md:text-xl mb-8">
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
                                class="w-full px-6 py-4 pr-32 rounded-full shadow-xl focus:outline-none focus:ring-4 focus:ring-blue-300"
                                :disabled="isLoading"
                            />
                            <button
                                @click="applyFilters"
                                :disabled="isLoading"
                                class="absolute right-2 top-2 px-6 py-2 text-[#CCCCCC] rounded-full font-medium transition disabled:opacity-50"
                            >
                                {{ isLoading ? "Mencari..." : "Cari" }}
                            </button>
                        </div>
                    </div>

                    <!-- Features -->
                    <div
                        class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12 text-[#CCCCCC]"
                    >
                        <!-- Card 1 -->
                        <div
                            class="bg-gradient-to-br from-green-500/20 to-emerald-500/10 backdrop-blur-sm rounded-xl p-6 border border-green-500/20 hover:border-green-500/40 transition flex flex-col items-center text-center"
                        >
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-green-500/20"
                            >
                                <span
                                    class="mdi mdi-shield-check text-3xl text-white"
                                ></span>
                            </div>

                            <h3 class="font-bold text-lg mb-2 text-white">
                                Garansi Kualitas
                            </h3>

                            <p class="text-sm">
                                Peralatan terawat & berkualitas tinggi
                            </p>
                        </div>

                        <!-- Card 2 -->
                        <div
                            class="bg-gradient-to-br from-blue-500/20 to-cyan-500/10 backdrop-blur-sm rounded-xl p-6 border border-blue-500/20 hover:border-blue-500/40 transition flex flex-col items-center text-center"
                        >
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-blue-500/20"
                            >
                                <span
                                    class="mdi mdi-rocket-launch text-3xl text-white"
                                ></span>
                            </div>

                            <h3 class="font-bold text-lg mb-2 text-white">
                                Proses Cepat
                            </h3>

                            <p class="text-sm">
                                Booking online, ambil langsung
                            </p>
                        </div>

                        <!-- Card 3 -->
                        <div
                            class="bg-gradient-to-br from-yellow-500/20 to-orange-500/10 backdrop-blur-sm rounded-xl p-6 border border-yellow-500/20 hover:border-yellow-500/40 transition flex flex-col items-center text-center"
                        >
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center mb-4 shadow-lg shadow-yellow-500/20"
                            >
                                <span
                                    class="mdi mdi-cash-multiple text-3xl text-white"
                                ></span>
                            </div>

                            <h3 class="font-bold text-lg mb-2 text-white">
                                Harga Terjangkau
                            </h3>

                            <p class="text-sm">Harga sewa mulai 50rb/hari</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <!-- Category Filter -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">
                        Kategori Produk
                    </h2>
                    <button
                        v-if="selectedCategory || searchQuery"
                        @click="resetFilters"
                        :disabled="isLoading"
                        class="text-sm text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2 disabled:opacity-50"
                    >
                        Reset Filter
                    </button>
                </div>
                <div class="flex flex-wrap gap-6 border-b border-gray-200 pb-2">
                    <!-- Semua kategori -->
                    <Link href="/">
                        <button
                            @click="selectedCategory = ''"
                            :disabled="isLoading"
                            :class="[
                                'relative pb-2 font-medium transition disabled:opacity-50 text-base',
                                selectedCategory === ''
                                    ? 'text-[#333333] after:content-[\'\'] after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-full after:bg-[#E69A00]'
                                    : 'text-[#333333] hover:text-[#E69A00]',
                            ]"
                        >
                            Semua Kategori
                        </button></Link
                    >

                    <!-- Kategori lainnya -->
                    <button
                        v-for="category in categories"
                        :key="category.id"
                        @click="selectedCategory = category.id"
                        :disabled="isLoading"
                        :class="[
                            'relative pb-2 font-medium transition disabled:opacity-50 text-base',
                            selectedCategory == category.id
                                ? 'text-[#333333] after:content-[\'\'] after:absolute after:left-0 after:bottom-0 after:h-[2px] after:w-full after:bg-[#E69A00]'
                                : 'text-[#333333] hover:text-[#E69A00]',
                        ]"
                    >
                        {{ category.nama }}
                    </button>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div v-if="isLoading" class="text-center py-8">
                <div
                    class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"
                ></div>
                <p class="mt-4 text-[#333333]">Memuat produk...</p>
            </div>

            <!-- Products Header -->
            <div
                v-else
                class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6"
            >
                <h2 class="text-2xl font-bold text-[#333333] mb-4 sm:mb-0">
                    Produk Tersedia
                    <span class="">({{ products.length }})</span>
                </h2>
            </div>

            <!-- Empty State -->
            <div
                v-if="!isLoading && products.length === 0"
                class="text-center py-20 bg-white rounded-2xl shadow"
            >
                <span class="text-7xl mb-4 block">📭</span>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">
                    Produk tidak ditemukan
                </h3>
                <p class="text-[#333333] mb-6">
                    Coba kata kunci lain atau lihat semua produk
                </p>
                <button
                    @click="resetFilters"
                    class="px-6 py-3 bg-blue-600 text-[#ebebeb] rounded-lg hover:bg-blue-700 font-medium"
                >
                    Lihat Semua Produk
                </button>
            </div>

            <!-- Products Grid -->
            <div
                v-else-if="!isLoading"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12"
            >
                <transition-group name="fade">
                    <div
                        v-for="product in products"
                        :key="product.id"
                        class="bg-white shadow-md hover:shadow-2xl transition-all duration-300 overflow-hidden group"
                    >
                        <div class="relative overflow-hidden h-64 md:h-72">
                            <img
                                :src="helpers.imageUrl(product.gambar)"
                                :alt="product.nama"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                            />
                            <div
                                class="absolute top-4 right-4 bg-white px-3 py-1 rounded-full shadow-lg"
                            >
                                <span
                                    class="text-green-800 text-sm font-bold flex items-center"
                                >
                                    <span
                                        class="w-2 h-2 bg-green-800 rounded-full mr-2"
                                    ></span>
                                    Tersedia
                                </span>
                            </div>
                            <div
                                class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4"
                            >
                                <span
                                    class="text-[#ebebeb] text-xs bg-green-800 px-3 py-1 rounded-full"
                                >
                                    {{ product.category?.nama || "Kategori" }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4 bg-[#f5f5f5] h-full">
                            <h3
                                class="text-xl font-bold mb-2 text-[#333333] group-hover:text-gray-700 transition"
                            >
                                {{ product.nama }}
                            </h3>
                            <p class="text-[#333333] mb-4 text-sm line-clamp-2">
                                {{ product.deskripsi }}
                            </p>

                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <p
                                        class="text-3xl font-bold text-[#333333]"
                                    >
                                        {{
                                            formatCurrency(
                                                product.harga_sewa_perhari
                                            )
                                        }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        per hari
                                    </p>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">
                                        {{ product.bookings_count || 0 }}
                                        booking
                                    </div>
                                </div>
                            </div>

                            <Link
                                :href="`/product/${product.id}`"
                                class="block text-center bg-[#333333] text-[#ebebeb] font-semibold py-3 transition-colors shadow-lg hover:shadow-xl"
                            >
                                Lihat Detail & Booking
                            </Link>
                        </div>
                    </div>
                </transition-group>
            </div>

            <!-- CTA Section -->
            <section
                class="bg-gradient-to-r bg-[#333333] p-12 text-center text-[#ebebeb] mb-12 shadow-xl"
            >
                <h2 class="text-3xl md:text-4xl font-bold mb-4">
                    Butuh Bantuan Memilih?
                </h2>
                <p class="text-lg text-[#ebebeb] mb-8 max-w-2xl mx-auto">
                    Tim kami siap membantu Anda menemukan peralatan yang tepat
                    untuk kebutuhan photography Anda
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link
                        href="/contact"
                        class="px-8 py-4 bg-transparent border-2 border-[#f4f4f4] text-[#ebebeb] rounded-xl font-bold hover:bg-white hover:text-yellow-800 transition"
                    >
                        💬 Hubungi Kami
                    </Link>
                    <Link
                        href="/"
                        class="px-8 py-4 bg-transparent border-2 border-[#f4f4f4] text-[#ebebeb] rounded-xl font-bold hover:bg-white hover:text-yellow-800 transition"
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

/* Transition untuk smooth animation */
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}

.fade-move {
    transition: transform 0.3s ease;
}
</style>
