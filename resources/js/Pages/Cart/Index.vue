<script setup>
import { ref, computed, onMounted, inject } from "vue";
import { Link, router } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
const helpers = inject("helpers");
// Data keranjang dari localStorage
const cartItems = ref([]);
const showCheckoutForm = ref(false);

// Form data untuk checkout (hanya nama dan wa)
const form = ref({
    nama_pemesan: "",
    nomor_wa: "",
    catatan: "",
});

// Ambil data dari localStorage saat page dimuat
onMounted(() => {
    const savedCart = JSON.parse(localStorage.getItem("cart")) || [];
    cartItems.value = savedCart;
});

// Total harga keseluruhan berdasarkan tanggal masing-masing item
const totalHarga = computed(() => {
    if (!cartItems.value || cartItems.value.length === 0) return 0;
    console.log(cartItems);
    return cartItems.value.reduce((total, item) => {
        const harga = Number(item.product_harga) || 0;
        const jumlah = Number(item.jumlah) || 1;
        const days =
            calculateDays(item.tanggal_mulai, item.tanggal_selesai) || 1;
        return total + harga * jumlah * days;
    }, 0);
});
console.log(totalHarga);

// Total item
const totalItems = computed(() => {
    return cartItems.value.reduce((sum, item) => sum + item.jumlah, 0);
});

// Hitung durasi hari
const calculateDays = (startDate, endDate) => {
    if (!startDate || !endDate) return 0;
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
    return diffDays;
};

// Get subtotal untuk item
const getItemSubtotal = (item) => {
    const days = calculateDays(item.tanggal_mulai, item.tanggal_selesai);
    return item.harga_sewa_perhari * item.jumlah * days;
};

// Hapus item dari keranjang
const removeItem = (product_id) => {
    cartItems.value = cartItems.value.filter(
        (i) => i.product_id !== product_id
    );
    localStorage.setItem("cart", JSON.stringify(cartItems.value));
};

// Update jumlah item
const updateQuantity = (id, change) => {
    const item = cartItems.value.find((i) => i.id === id);
    if (item) {
        item.jumlah = Math.max(1, item.jumlah + change);
        localStorage.setItem("cart", JSON.stringify(cartItems.value));
    }
};

// Lanjutkan ke halaman booking
const goToBooking = (product) => {
    router.visit(`/rental/${product.id}`);
};

// Toggle checkout form
const toggleCheckoutForm = () => {
    showCheckoutForm.value = !showCheckoutForm.value;
};

// Submit checkout
const submitCheckout = () => {
    if (!form.value.nama_pemesan || !form.value.nomor_wa) {
        alert("Mohon lengkapi nama dan nomor WhatsApp");
        return;
    }

    // Prepare cart items data dengan tanggal
    const cartItemsData = cartItems.value.map((item) => ({
        id: item.id,
        jumlah: item.jumlah,
        tanggal_mulai: item.tanggal_mulai,
        tanggal_selesai: item.tanggal_selesai,
    }));

    // Submit via Inertia
    router.post(
        "/cart/checkout",
        {
            nama_pemesan: form.value.nama_pemesan,
            nomor_wa: form.value.nomor_wa,
            cart_items: cartItemsData,
            catatan: form.value.catatan,
        },
        {
            onSuccess: () => {
                // Clear cart after successful checkout
                localStorage.removeItem("cart");
                cartItems.value = [];
            },
            onError: (errors) => {
                console.error("Checkout error:", errors);
                alert("Terjadi kesalahan saat checkout. Silakan coba lagi.");
            },
        }
    );
};

// Format tanggal untuk display
const formatDate = (date) => {
    if (!date) return "-";
    const d = new Date(date);
    return d.toLocaleDateString("id-ID", {
        day: "numeric",
        month: "short",
        year: "numeric",
    });
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <Navbar />

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 rounded-lg">
                        <svg
                            class="w-7 h-7 text-gray-600"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                            ></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-600">
                        Keranjang Sewa
                    </h1>
                </div>
                <p
                    class="text-gray-600 text-sm sm:text-base"
                    v-if="cartItems.length > 0"
                >
                    {{ totalItems }} item dalam keranjang
                </p>
            </div>

            <!-- Jika keranjang kosong -->
            <div
                v-if="cartItems.length === 0"
                class="bg-white rounded-2xl shadow-lg p-8 sm:p-16 text-center"
            >
                <div class="max-w-sm mx-auto">
                    <div
                        class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center"
                    >
                        <svg
                            class="w-12 h-12 text-gray-400"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                            ></path>
                        </svg>
                    </div>
                    <h2
                        class="text-xl sm:text-2xl font-semibold text-gray-800 mb-2"
                    >
                        Keranjang Masih Kosong
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Yuk mulai tambahkan produk yang ingin kamu sewa!
                    </p>
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            ></path>
                        </svg>
                        Jelajahi Produk
                    </Link>
                </div>
            </div>

            <!-- Jika ada produk -->
            <div v-else class="grid lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- List Produk -->
                <div class="lg:col-span-2 space-y-4">
                    <div
                        v-for="(item, index) in cartItems"
                        :key="index"
                        class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden group"
                    >
                        <div class="p-4 sm:p-6">
                            <div class="flex flex-col sm:flex-row gap-4">
                                <!-- Image -->
                                <div class="relative flex-shrink-0">
                                    <img
                                        :src="
                                            helpers.imageUrl(
                                                item.product_gambar
                                            )
                                                ? '/storage/' +
                                                  item.product_gambar
                                                : '/img/no-image.png'
                                        "
                                        alt="Gambar Produk"
                                        class="w-full sm:w-32 h-40 sm:h-32 object-cover rounded-lg group-hover:scale-105 transition-transform duration-300"
                                    />
                                    <div
                                        class="absolute top-2 right-2 bg-green-800 text-white text-xs px-2 py-1 rounded-full font-semibold"
                                    >
                                        {{ item.product_id }}
                                    </div>
                                </div>

                                <!-- Content -->
                                <div class="flex-1 min-w-0">
                                    <div
                                        class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-2 mb-3"
                                    >
                                        <div class="flex-1">
                                            <h2
                                                class="font-bold text-gray-900 text-lg mb-1"
                                            >
                                                {{ item.nama }}
                                            </h2>
                                            <div
                                                class="flex items-center gap-2 text-sm text-gray-600 mb-2"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    ></path>
                                                </svg>
                                                <span
                                                    class="font-semibold text-gray-600"
                                                >
                                                    Rp
                                                    {{
                                                        Number(
                                                            item.product_harga
                                                        ).toLocaleString(
                                                            "id-ID"
                                                        )
                                                    }}
                                                </span>
                                                <span class="text-gray-600"
                                                    >/ hari</span
                                                >
                                            </div>
                                        </div>

                                        <!-- Mobile Remove Button -->
                                        <button
                                            @click="removeItem(item.id)"
                                            class="sm:hidden self-end p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                        >
                                            <svg
                                                class="w-5 h-5"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                ></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Tanggal Pemesanan -->
                                    <div class="mb-3 p-3 bg-blue-50 rounded-lg">
                                        <div
                                            class="flex items-center gap-2 mb-2"
                                        >
                                            <svg
                                                class="w-4 h-4 text-blue-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                ></path>
                                            </svg>
                                            <span
                                                class="text-sm font-semibold text-blue-900"
                                            >
                                                Jadwal Sewa
                                            </span>
                                        </div>
                                        <div
                                            class="text-sm text-blue-800 space-y-1"
                                        >
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <span class="text-blue-600"
                                                    >Mulai:</span
                                                >
                                                <span class="font-semibold">{{
                                                    formatDate(
                                                        item.tanggal_mulai
                                                    )
                                                }}</span>
                                            </div>
                                            <div
                                                class="flex items-center gap-2"
                                            >
                                                <span class="text-blue-600"
                                                    >Selesai:</span
                                                >
                                                <span class="font-semibold">{{
                                                    formatDate(
                                                        item.tanggal_selesai
                                                    )
                                                }}</span>
                                            </div>
                                            <div
                                                class="flex items-center gap-2 pt-1 border-t border-blue-200"
                                            >
                                                <span class="text-blue-600"
                                                    >Durasi:</span
                                                >
                                                <span class="font-bold">
                                                    {{
                                                        calculateDays(
                                                            item.tanggal_mulai,
                                                            item.tanggal_selesai
                                                        )
                                                    }}
                                                    hari
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div
                                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3"
                                    >
                                        <div class="flex items-center gap-2">
                                            <!-- Desktop Remove Button -->
                                            <button
                                                @click="
                                                    removeItem(item.product_id)
                                                "
                                                class="hidden sm:flex items-center gap-1 px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors text-sm font-medium"
                                            >
                                                <svg
                                                    class="w-4 h-4"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                    ></path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div
                                        class="mt-3 pt-3 border-t border-gray-100"
                                    >
                                        <div
                                            class="flex justify-between items-center"
                                        >
                                            <span class="text-sm text-gray-600"
                                                >Subtotal:</span
                                            >
                                            <span
                                                class="font-bold text-gray-600 text-lg"
                                            >
                                                Rp
                                                {{
                                                    Number(
                                                        item.total_harga
                                                    ).toLocaleString("id-ID")
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-1">
                        <h3
                            class="text-xl font-bold text-gray-600 mb-6 flex items-center gap-2"
                        >
                            <svg
                                class="w-5 h-5 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                ></path>
                            </svg>
                            Ringkasan Pesanan
                        </h3>

                        <!-- Checkout Form (Collapsed/Expanded) -->
                        <div v-if="!showCheckoutForm" class="space-y-4 mb-6">
                            <div class="flex justify-between text-gray-600">
                                <span>Total Item:</span>
                                <span class="font-semibold"
                                    >{{ totalItems }} item</span
                                >
                            </div>

                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span
                                        class="text-lg font-bold text-gray-600"
                                        >Total Pembayaran:</span
                                    >
                                    <span
                                        class="text-2xl font-bold text-gray-600"
                                    >
                                        Rp
                                        {{ totalHarga.toLocaleString("id-ID") }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Checkout Form Detail -->
                        <div v-else class="space-y-4 mb-6">
                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Nama Lengkap *
                                    </label>
                                    <input
                                        v-model="form.nama_pemesan"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Masukkan nama lengkap"
                                        required
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        No. WhatsApp *
                                    </label>
                                    <input
                                        v-model="form.nomor_wa"
                                        type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="628xxxxxxxxxx"
                                        required
                                    />
                                </div>

                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 mb-1"
                                    >
                                        Catatan (Opsional)
                                    </label>
                                    <textarea
                                        v-model="form.catatan"
                                        rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="Tambahkan catatan khusus..."
                                    ></textarea>
                                </div>
                            </div>

                            <!-- Summary -->
                            <div class="border-t pt-4 space-y-2">
                                <div
                                    class="flex justify-between text-sm text-gray-600"
                                >
                                    <span>Total Item:</span>
                                    <span class="font-semibold"
                                        >{{ totalItems }} pesanan</span
                                    >
                                </div>
                                <div
                                    class="flex justify-between items-center pt-2 border-t"
                                >
                                    <span
                                        class="text-lg font-bold text-gray-900"
                                        >Total:</span
                                    >
                                    <span
                                        class="text-xl font-bold text-blue-600"
                                    >
                                        Rp
                                        {{ totalHarga.toLocaleString("id-ID") }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div v-if="!showCheckoutForm">
                            <button
                                @click="toggleCheckoutForm"
                                class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4 rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                                    ></path>
                                </svg>
                                Lanjut ke Checkout
                            </button>
                        </div>

                        <div v-else class="space-y-3">
                            <button
                                @click="submitCheckout"
                                class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-4 rounded-xl hover:from-green-700 hover:to-green-800 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
                                    ></path>
                                </svg>
                                Pesan via WhatsApp
                            </button>

                            <button
                                @click="toggleCheckoutForm"
                                class="w-full flex items-center justify-center gap-2 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-100 transition-colors font-medium"
                            >
                                <svg
                                    class="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 19l-7-7 7-7"
                                    ></path>
                                </svg>
                                Kembali
                            </button>
                        </div>

                        <Link
                            href="/"
                            class="mt-3 w-full flex items-center justify-center gap-2 text-gray-700 px-6 py-3 rounded-xl hover:bg-gray-100 transition-colors font-medium"
                        >
                            <svg
                                class="w-5 h-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 19l-7-7 7-7"
                                ></path>
                            </svg>
                            Lanjut Belanja
                        </Link>

                        <!-- Info Box -->
                        <div
                            class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100"
                        >
                            <div class="flex gap-3">
                                <svg
                                    class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                    ></path>
                                </svg>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold mb-1">
                                        Info Penting
                                    </p>
                                    <p class="text-blue-700">
                                        Setiap produk memiliki jadwal sewa
                                        berbeda. Pastikan ketersediaan sebelum
                                        checkout.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media (max-width: 640px) {
    .shadow-lg {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
}
</style>
