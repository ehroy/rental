// ========================================
// FILE: resources/js/composables/useCart.js
// ========================================

import { ref, computed } from "vue";

const cart = ref([]);
const CART_KEY = "cart";

export function useCart() {
    // Load cart dari localStorage
    const loadCart = () => {
        try {
            const savedCart = localStorage.getItem(CART_KEY);
            if (savedCart) {
                cart.value = JSON.parse(savedCart);
            }
        } catch (error) {
            console.error("Error loading cart:", error);
            cart.value = [];
        }
        return cart.value;
    };

    // Save cart ke localStorage
    const saveCart = () => {
        try {
            localStorage.setItem(CART_KEY, JSON.stringify(cart.value));
        } catch (error) {
            console.error("Error saving cart:", error);
        }
    };

    // Add item to cart
    const addToCart = (item) => {
        // Cek apakah item sudah ada
        const existingIndex = cart.value.findIndex(
            (cartItem) =>
                cartItem.product_id === item.product_id &&
                cartItem.tanggal_mulai === item.tanggal_mulai &&
                cartItem.tanggal_selesai === item.tanggal_selesai
        );

        if (existingIndex !== -1) {
            // Update quantity jika sudah ada
            cart.value[existingIndex].jumlah += item.jumlah;
            cart.value[existingIndex].total_harga =
                cart.value[existingIndex].jumlah *
                cart.value[existingIndex].product_harga *
                cart.value[existingIndex].durasi;
        } else {
            // Tambah item baru dengan ID unik
            cart.value.push({
                ...item,
                id: Date.now() + Math.random(), // Unique ID
                created_at: new Date().toISOString(),
            });
        }

        saveCart();
        return true;
    };

    // Remove item from cart
    const removeFromCart = (itemId) => {
        cart.value = cart.value.filter((item) => item.id !== itemId);
        saveCart();
    };

    // Update item quantity
    const updateQuantity = (itemId, quantity) => {
        const item = cart.value.find((item) => item.id === itemId);
        if (item && quantity > 0) {
            item.jumlah = quantity;
            item.total_harga = item.product_harga * item.durasi * quantity;
            saveCart();
        }
    };

    // Clear cart
    const clearCart = () => {
        cart.value = [];
        localStorage.removeItem(CART_KEY);
    };

    // Get cart count
    const cartCount = computed(() => cart.value.length);

    // Get cart total
    const cartTotal = computed(() => {
        return cart.value.reduce((total, item) => total + item.total_harga, 0);
    });

    // Check if product already in cart
    const isInCart = (productId, startDate, endDate) => {
        return cart.value.some(
            (item) =>
                item.product_id === productId &&
                item.tanggal_mulai === startDate &&
                item.tanggal_selesai === endDate
        );
    };

    return {
        cart,
        cartCount,
        cartTotal,
        loadCart,
        addToCart,
        removeFromCart,
        updateQuantity,
        clearCart,
        isInCart,
    };
}

// ========================================
// CARA PAKAI DI COMPONENT:
// ========================================

/*
<script setup>
import { useCart } from '@/composables/useCart';
import { onMounted } from 'vue';

const { 
    cart, 
    cartCount, 
    cartTotal,
    loadCart, 
    addToCart, 
    removeFromCart,
    updateQuantity 
} = useCart();

onMounted(() => {
    loadCart();
});

// Add to cart
const handleAddToCart = () => {
    const item = {
        product_id: product.value.id,
        product_nama: product.value.nama,
        product_gambar: product.value.gambar_url,
        product_harga: product.value.harga_sewa_perhari,
        tanggal_mulai: rentalForm.value.tanggal_mulai,
        tanggal_selesai: rentalForm.value.tanggal_selesai,
        jumlah: quantity.value,
        durasi: rentalDuration.value,
        total_harga: totalPrice.value,
        catatan: rentalForm.value.catatan,
    };
    
    addToCart(item);
    alert('✅ Produk berhasil ditambahkan ke keranjang!');
};
</script>

<template>
    <div>
        Cart Count: {{ cartCount }}
        Total: {{ cartTotal }}
    </div>
</template>
*/
