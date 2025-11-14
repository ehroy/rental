<script setup>
import { ref, onMounted } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const cartCount = ref(0);
const cart = ref([]);

// Get current path untuk active state
const page = usePage();
const currentPath = ref(page.url);

onMounted(() => {
    // Load cart from localStorage
    cart.value = JSON.parse(localStorage.getItem("cart")) || [];
    cartCount.value = cart.value.length;
});

const navLinks = [
    { href: "/", label: "Beranda", icon: "mdi-home-outline" },
    { href: "/about", label: "Tentang", icon: "mdi-information-outline" },
    { href: "/contact", label: "Contact", icon: "mdi-account-outline" },
];
</script>

<template>
    <!-- Desktop Navbar -->
    <nav
        class="hidden md:block fixed top-0 w-full bg-gradient-to-r bg-[#FFFFFF] text-gray-600 shadow-xl z-50"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <Link
                    href="/"
                    class="flex items-center space-x-3 hover:opacity-90 transition group"
                    aria-label="Quick Rental Home"
                >
                    <div class="relative">
                        <div
                            class="absolute inset-0 bg-white/20 rounded-full blur-md group-hover:blur-lg transition"
                        ></div>
                        <img
                            src="/images/icon.png"
                            class="w-12 h-12 rounded-full"
                            alt="rental camera"
                        />
                    </div>
                    <div>
                        <span
                            class="text-2xl font-bold text-gray-600 tracking-tight"
                        >
                            Quick&nbsp;<span class="text-yellow-600"
                                >Rental</span
                            >
                        </span>
                        <span class="text-xs text-gray-600 block -mt-1">
                            Sewa Kamera Profesional
                        </span>
                    </div>
                </Link>

                <!-- Desktop Menu -->
                <div class="flex items-center space-x-5">
                    <Link
                        v-for="link in navLinks.filter((l) => !l.isCart)"
                        :key="link.href"
                        :href="link.href"
                        :aria-label="link.label"
                        :class="[
                            'flex items-center space-x-2 px-4 py-2 transition-all font-medium border-b-2 border-transparent',
                            currentPath === link.href
                                ? 'border-gray-600 text-gray-600' // aktif: garis bawah & teks aksen
                                : 'text-gray-600 hover:text-gray-700 hover:border-gray-700/70', // normal: putih, berubah saat hover
                        ]"
                    >
                        <i :class="['mdi', link.icon, 'text-xl']"></i>
                        <span>{{ link.label }}</span>
                    </Link>
                </div>

                <!-- Right Side - Cart & Login -->
                <div class="flex items-center space-x-3">
                    <!-- Desktop Cart -->
                    <Link
                        href="/cart"
                        aria-label="Shopping Cart"
                        class="flex items-center space-x-2 px-4 py-2 hover:bg-white/20 rounded-lg transition text-gray-600"
                    >
                        <div class="relative">
                            <i class="mdi mdi-cart-outline text-2xl"></i>

                            <!-- Badge menempel di pojok kanan atas ikon -->
                            <span
                                v-if="cartCount > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center"
                            >
                                {{ cartCount }}
                            </span>
                        </div>
                    </Link>

                    <!-- Desktop Login Button -->
                </div>
            </div>
        </div>
    </nav>

    <!-- Desktop Spacer -->
    <div class="hidden md:block h-16"></div>

    <!-- Mobile Top Bar - Minimal -->
    <div
        class="md:hidden fixed top-0 left-0 w-full bg-white shadow-md z-50 px-4 py-3 flex justify-between items-center"
    >
        <!-- Logo Mobile -->
        <Link href="/" class="flex items-center space-x-2" aria-label="Home">
            <img
                src="/images/icon.png"
                class="w-10 h-10 rounded-full"
                alt="rental camera"
            />
            <div>
                <span class="text-2xl font-bold text-gray-600 tracking-tight">
                    Quick&nbsp;<span class="text-yellow-600">Rental</span>
                </span>
            </div>
        </Link>

        <!-- Cart Icon (Info Only) -->
        <Link
            href="/cart"
            aria-label="Shopping Cart"
            class="flex items-center space-x-2 px-4 py-2 hover:bg-white/20 rounded-lg transition text-gray-600"
        >
            <div class="relative">
                <i class="mdi mdi-cart-outline text-2xl text-gray-400"></i>
                <span
                    v-if="cartCount > 0"
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-4 h-4 flex items-center justify-center"
                >
                    {{ cartCount }}
                </span>
            </div>
        </Link>
    </div>

    <!-- Mobile Top Spacer -->
    <div class="md:hidden h-14"></div>

    <!-- Mobile Bottom Navigation - iOS/Android Style -->
    <div
        class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 shadow-2xl"
        style="padding-bottom: env(safe-area-inset-bottom)"
    >
        <div class="grid grid-cols-3 h-16">
            <!-- Navigation Items -->
            <Link
                v-for="link in navLinks"
                :key="link.href"
                :href="link.href"
                :aria-label="link.label"
                :class="[
                    'flex flex-col items-center justify-center transition-all relative',
                    currentPath === link.href
                        ? 'text-gray-600'
                        : 'text-gray-400 hover:text-gray-600',
                ]"
            >
                <!-- Elevated Cart Button (Center) -->
                <div
                    v-if="link.isCart"
                    class="absolute -top-8 left-1/2 transform -translate-x-1/2"
                >
                    <div
                        class="relative bg-gradient-to-br from-blue-500 to-purple-600 rounded-full w-16 h-16 flex items-center justify-center shadow-2xl hover:scale-105 transition-transform"
                    >
                        <i
                            :class="['mdi', link.icon, 'text-white text-3xl']"
                        ></i>
                        <span
                            v-if="cartCount > 0"
                            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center ring-2 ring-white"
                        >
                            {{ cartCount }}
                        </span>
                    </div>
                </div>

                <!-- Regular Icon (Non-Cart) -->
                <i
                    v-if="!link.isCart"
                    :class="[
                        'mdi',
                        link.icon,
                        'text-2xl transition-transform',
                        currentPath === link.href ? 'scale-110' : 'scale-100',
                    ]"
                ></i>

                <!-- Label -->
                <span
                    :class="[
                        'text-xs font-medium transition-all',
                        link.isCart ? 'mt-10' : 'mt-1',
                        currentPath === link.href ? 'font-bold' : '',
                    ]"
                >
                    {{ link.label }}
                </span>

                <!-- Active Indicator (Top Bar) -->
                <div
                    v-if="currentPath === link.href && !link.isCart"
                    class="absolute top-0 left-1/2 transform -translate-x-1/2 w-12 h-1 bg-gray-600 rounded-b-full"
                ></div>
            </Link>
        </div>
    </div>

    <!-- Mobile Bottom Spacer -->
</template>

<style scoped>
/* Smooth transitions */
* {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Prevent tap highlight on mobile */
.md\:hidden a {
    -webkit-tap-highlight-color: transparent;
    user-select: none;
}

/* Safe area for iOS devices */
@supports (padding-bottom: env(safe-area-inset-bottom)) {
    .md\:hidden.fixed.bottom-0 {
        padding-bottom: calc(env(safe-area-inset-bottom));
    }
}

/* Active link animation */
.text-blue-600 {
    animation: pulse-subtle 2s ease-in-out infinite;
}

@keyframes pulse-subtle {
    0%,
    100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}
</style>
