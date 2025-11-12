<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, router } from "@inertiajs/vue3";

// Data keranjang dari localStorage
const cartItems = ref([]);

// Ambil data dari localStorage saat page dimuat
onMounted(() => {
    const savedCart = JSON.parse(localStorage.getItem("cart")) || [];
    cartItems.value = savedCart;
});

// Total harga keseluruhan
const totalHarga = computed(() => {
    return cartItems.value.reduce(
        (sum, item) => sum + item.harga_sewa_perhari * item.jumlah,
        0
    );
});

// Hapus item dari keranjang
const removeItem = (id) => {
    cartItems.value = cartItems.value.filter((i) => i.id !== id);
    localStorage.setItem("cart", JSON.stringify(cartItems.value));
};

// Lanjutkan ke halaman booking
const goToBooking = (product) => {
    router.visit(`/rental/${product.id}`); // arahkan ke halaman booking produk
};
</script>

<template>
    <div class="max-w-4xl mx-auto py-10 px-4">
        <h1 class="text-2xl font-bold mb-6">🛒 Keranjang Sewa</h1>

        <!-- Jika keranjang kosong -->
        <div
            v-if="cartItems.length === 0"
            class="text-gray-600 text-center py-16"
        >
            <p>Keranjang kamu masih kosong 😅</p>
            <Link
                href="/"
                class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition"
            >
                Lihat Produk
            </Link>
        </div>

        <!-- Jika ada produk -->
        <div v-else class="space-y-4">
            <div
                v-for="(item, index) in cartItems"
                :key="index"
                class="flex items-center justify-between p-4 bg-white rounded-lg shadow-md"
            >
                <div class="flex items-center space-x-4">
                    <img
                        :src="
                            item.gambar
                                ? '/storage/' + item.gambar
                                : '/img/no-image.png'
                        "
                        alt="Gambar Produk"
                        class="w-20 h-20 object-cover rounded-md"
                    />
                    <div>
                        <h2 class="font-semibold text-gray-800">
                            {{ item.nama }}
                        </h2>
                        <p class="text-sm text-gray-600">
                            Rp
                            {{
                                Number(item.harga_sewa_perhari).toLocaleString(
                                    "id-ID"
                                )
                            }}
                            / hari
                        </p>
                        <p class="text-sm text-gray-500">
                            Jumlah: {{ item.jumlah }}
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <p class="font-semibold text-gray-700 mb-2">
                        Rp
                        {{
                            (
                                item.harga_sewa_perhari * item.jumlah
                            ).toLocaleString("id-ID")
                        }}
                    </p>

                    <div class="flex gap-2 justify-end">
                        <button
                            @click="goToBooking(item)"
                            class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm"
                        >
                            Booking
                        </button>
                        <button
                            @click="removeItem(item.id)"
                            class="px-3 py-1 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm"
                        >
                            Hapus
                        </button>
                    </div>
                </div>
            </div>

            <div class="text-right mt-6 border-t pt-4">
                <h3 class="text-lg font-bold">
                    Total: Rp {{ totalHarga.toLocaleString("id-ID") }}
                </h3>
                <Link
                    href="/rental/checkout"
                    class="mt-4 inline-block bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition"
                >
                    Lanjut ke Checkout
                </Link>
            </div>
        </div>
    </div>
</template>
