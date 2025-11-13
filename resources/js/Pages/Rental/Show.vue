<script setup>
import { ref, computed, watch, onMounted, inject } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import Navbar from "@/Components/Navbar.vue";
const { props } = usePage();
const helpers = inject("helpers");
const product = ref(props.product);

const bookedDates = ref(props.bookedDates || []); // Array of {start, end, status}

// Form data
const rentalForm = ref({
    image: "",
    tanggal_mulai: "",
    tanggal_selesai: "",
    jumlah: 1,
    catatan: "",
});

const selectedImage = ref(0);
const quantity = ref(1);
const showDateWarning = ref(false);
const dateWarningMessage = ref("");

// Sample images
const productImages = ref([
    product.value.gambar,
    product.value.gambar,
    product.value.gambar,
]);

// Check if date is booked and get its status
const getDateStatus = (date) => {
    for (const booking of bookedDates.value) {
        // Bandingkan langsung sebagai string ISO (YYYY-MM-DD)
        // Tidak perlu convert ke Date object
        if (date >= booking.start && date <= booking.end) {
            return booking.status; // 'pending' or 'approved'
        }
    }
    return null; // not booked
};

// Check if date range contains booked dates (approved only)
const hasBookedDatesInRange = computed(() => {
    if (!rentalForm.value.tanggal_mulai || !rentalForm.value.tanggal_selesai) {
        return false;
    }

    const start = rentalForm.value.tanggal_mulai; // sudah string "YYYY-MM-DD"
    const end = rentalForm.value.tanggal_selesai;

    // Loop menggunakan string comparison
    const startDate = new Date(start + "T00:00:00"); // Force local timezone
    const endDate = new Date(end + "T00:00:00");

    const currentDate = new Date(startDate);

    while (currentDate <= endDate) {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, "0");
        const day = String(currentDate.getDate()).padStart(2, "0");
        const dateString = `${year}-${month}-${day}`;

        const status = getDateStatus(dateString);

        if (status === "approved") {
            return true;
        }

        currentDate.setDate(currentDate.getDate() + 1);
    }

    return false;
});

// Watch for date changes to show warnings
watch(
    [
        () => rentalForm.value.tanggal_mulai,
        () => rentalForm.value.tanggal_selesai,
    ],
    () => {
        if (hasBookedDatesInRange.value) {
            showDateWarning.value = true;
        } else {
            showDateWarning.value = false;
        }
    }
);

// Calculate rental duration
const rentalDuration = computed(() => {
    if (rentalForm.value.tanggal_mulai && rentalForm.value.tanggal_selesai) {
        const start = new Date(rentalForm.value.tanggal_mulai);
        const end = new Date(rentalForm.value.tanggal_selesai);
        // Tambah 1 hari agar tanggal yang sama dihitung sebagai 1 hari
        // 15-15 = 0 + 1 = 1 hari
        // 15-16 = 1 + 1 = 2 hari
        const days = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
        return days > 0 ? days : 0;
    }
    return 0;
});

// Calculate total price
const totalPrice = computed(() => {
    return (
        product.value.harga_sewa_perhari * rentalDuration.value * quantity.value
    );
});

// Check if form is valid
const isFormValid = computed(() => {
    return (
        rentalForm.value.tanggal_mulai &&
        rentalForm.value.tanggal_selesai &&
        rentalDuration.value > 0 &&
        !hasBookedDatesInRange.value
    );
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString("id-ID", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const submitBooking = () => {
    if (!isFormValid.value) {
        return;
    }

    const payload = {
        tanggal_mulai: rentalForm.value.tanggal_mulai,
        tanggal_selesai: rentalForm.value.tanggal_selesai,
        jumlah: quantity.value,
        image: rentalForm.value.gambar,
        catatan: rentalForm.value.catatan,
        nama_pemesan: "GUEST",
        nomor_wa: "+6289532873283",
    };

    // Gunakan router.post dengan onSuccess callback
    router.post(`/rental/product/${product.value.id}/booking`, payload, {
        onSuccess: (page) => {
            // Ambil whatsapp URL dari response
            if (page.props.whatsappUrl) {
                // Redirect ke WhatsApp menggunakan window.location
                window.location.href = page.props.whatsappUrl;
            }
        },
        onError: (errors) => {
            console.error("Booking failed:", errors);
        },
    });
};
const cart = ref([]);
const cartCount = ref(0);

// Load cart dari localStorage saat component mounted
onMounted(() => {
    loadCart();
});

// Function untuk load cart
const loadCart = () => {
    const savedCart = localStorage.getItem("cart");
    if (savedCart) {
        cart.value = JSON.parse(savedCart);
        cartCount.value = cart.value.length;
    }
};
const removeFromCart = (itemId) => {
    cart.value = cart.value.filter((item) => item.id !== itemId);
    saveCart();
};

// Function untuk update quantity di cart
const updateCartQuantity = (itemId, newQuantity) => {
    const item = cart.value.find((item) => item.id === itemId);
    if (item && newQuantity > 0) {
        item.jumlah = newQuantity;
        item.total_harga = item.product_harga * item.durasi * newQuantity;
        saveCart();
    }
};

// Function untuk clear cart
const clearCart = () => {
    cart.value = [];
    localStorage.removeItem("cart");
    cartCount.value = 0;
};
// Function untuk save cart
const saveCart = () => {
    localStorage.setItem("cart", JSON.stringify(cart.value));
    cartCount.value = cart.value.length;
};
const addToCart = () => {
    if (!isFormValid.value) {
        return;
    }

    // Buat cart item
    const cartItem = {
        id: product.value.id, // Unique ID untuk cart item
        product_id: Date.now(),
        product_nama: product.value.nama,
        product_gambar: product.value.gambar,
        product_harga: product.value.harga_sewa_perhari,
        tanggal_mulai: rentalForm.value.tanggal_mulai,
        tanggal_selesai: rentalForm.value.tanggal_selesai,
        jumlah: quantity.value,
        durasi: rentalDuration.value,
        total_harga: totalPrice.value,
        catatan: rentalForm.value.catatan,
        created_at: new Date().toISOString(),
    };

    // Cek apakah produk sudah ada di cart dengan tanggal yang sama
    const existingIndex = cart.value.findIndex(
        (item) =>
            item.product_id === cartItem.product_id &&
            item.tanggal_mulai === cartItem.tanggal_mulai &&
            item.tanggal_selesai === cartItem.tanggal_selesai
    );

    if (existingIndex !== -1) {
        // Update jumlah jika sudah ada
        cart.value[existingIndex].jumlah += cartItem.jumlah;
        cart.value[existingIndex].total_harga =
            cart.value[existingIndex].jumlah *
            cart.value[existingIndex].product_harga *
            cart.value[existingIndex].durasi;
    } else {
        // Tambah item baru
        cart.value.push(cartItem);
    }

    // Save ke localStorage
    saveCart();

    // Show success notification
    alert(`✅ ${product.value.nama} berhasil ditambahkan ke keranjang!`);

    // Optional: Reset form
    // rentalForm.value.tanggal_mulai = "";
    // rentalForm.value.tanggal_selesai = "";
    // rentalForm.value.catatan = "";
    // quantity.value = 1;
};
// Get minimum date (today)
const minDate = new Date().toISOString().split("T")[0];

// Format booked dates untuk ditampilkan
const formatBookedDatesDisplay = computed(() => {
    if (bookedDates.value.length === 0) return [];

    // Group consecutive dates
    const grouped = [];
    let currentGroup = [];

    const sortedDates = [...bookedDates.value].sort();

    sortedDates.forEach((dateStr, index) => {
        if (index === 0) {
            currentGroup.push(dateStr);
        } else {
            const prevDate = new Date(sortedDates[index - 1]);
            const currDate = new Date(dateStr);
            const diffDays = (currDate - prevDate) / (1000 * 60 * 60 * 24);

            if (diffDays === 1) {
                currentGroup.push(dateStr);
            } else {
                grouped.push([...currentGroup]);
                currentGroup = [dateStr];
            }
        }
    });

    if (currentGroup.length > 0) {
        grouped.push(currentGroup);
    }

    return grouped.map((group) => {
        if (group.length === 1) {
            return formatDate(group[0]);
        } else {
            return `${formatDate(group[0])} - ${formatDate(
                group[group.length - 1]
            )}`;
        }
    });
});

// Calendar functionality
const currentMonth = ref(new Date());
const showCalendar = ref(true);

const calendarDays = computed(() => {
    const year = currentMonth.value.getFullYear();
    const month = currentMonth.value.getMonth();

    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDay = firstDay.getDay();

    const days = [];

    // Add empty cells for days before month starts
    for (let i = 0; i < startDay; i++) {
        days.push(null);
    }

    // Add all days of the month
    for (let day = 1; day <= lastDay.getDate(); day++) {
        // ✅ Buat date string langsung tanpa timezone issue
        const dateStr = `${year}-${String(month + 1).padStart(2, "0")}-${String(
            day
        ).padStart(2, "0")}`;

        const status = getDateStatus(dateStr);

        // Untuk isPast dan isToday, gunakan date string comparison
        const today = new Date();
        const todayStr = `${today.getFullYear()}-${String(
            today.getMonth() + 1
        ).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`;

        const isPast = dateStr < todayStr;
        const isToday = dateStr === todayStr;

        days.push({
            day,
            date: dateStr,
            status,
            isPast,
            isToday,
        });
    }

    return days;
});

const monthNames = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
];
const dayNames = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"];

const currentMonthName = computed(() => {
    return `${
        monthNames[currentMonth.value.getMonth()]
    } ${currentMonth.value.getFullYear()}`;
});

const previousMonth = () => {
    currentMonth.value = new Date(
        currentMonth.value.getFullYear(),
        currentMonth.value.getMonth() - 1,
        1
    );
};

const nextMonth = () => {
    currentMonth.value = new Date(
        currentMonth.value.getFullYear(),
        currentMonth.value.getMonth() + 1,
        1
    );
};
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navbar -->
        <Navbar />

        <!-- Breadcrumb -->
        <div class="bg-white border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <Link href="/" class="hover:text-blue-600">Beranda</Link>
                    <span>/</span>
                    <Link href="/products" class="hover:text-blue-600"
                        >Produk</Link
                    >
                    <span>/</span>
                    <span class="text-gray-900 font-medium">{{
                        product.nama
                    }}</span>
                </div>
            </div>
        </div>

        <!-- Product Detail -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
                <!-- Left: Images -->
                <div>
                    <!-- Main Image -->
                    <div
                        class="bg-white rounded-2xl shadow-lg overflow-hidden mb-4"
                    >
                        <img
                            :src="
                                helpers.imageUrl(productImages[selectedImage])
                            "
                            :alt="product.nama"
                            class="w-full h-96 object-cover"
                        />
                    </div>

                    <!-- Thumbnail Images -->
                    <div class="grid grid-cols-4 gap-2">
                        <div
                            v-for="(image, index) in productImages"
                            :key="index"
                            @click="selectedImage = index"
                            :class="[
                                'cursor-pointer rounded-lg overflow-hidden border-2 transition',
                                selectedImage === index
                                    ? 'border-blue-600'
                                    : 'border-gray-200 hover:border-blue-400',
                            ]"
                        >
                            <img
                                :src="helpers.imageUrl(image)"
                                class="w-full h-20 object-cover"
                            />
                        </div>
                    </div>

                    <!-- Specifications -->
                    <!-- <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                        <h3 class="text-xl font-bold mb-4">
                            Spesifikasi Produk
                        </h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Kategori</span>
                                <span class="font-medium"
                                    >Mirrorless Camera</span
                                >
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Brand</span>
                                <span class="font-medium">Canon</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Resolusi</span>
                                <span class="font-medium">45 MP</span>
                            </div>
                            <div class="flex justify-between py-2 border-b">
                                <span class="text-gray-600">Video</span>
                                <span class="font-medium">8K 30fps</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Kondisi</span>
                                <span class="font-medium text-green-600"
                                    >Excellent</span
                                >
                            </div>
                        </div>
                    </div> -->

                    <!-- Calendar Availability -->
                    <div class="bg-white rounded-2xl shadow-lg p-6 mt-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold text-gray-800">
                                📅 Ketersediaan
                            </h3>
                            <button
                                @click="showCalendar = !showCalendar"
                                class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                            >
                                {{ showCalendar ? "Sembunyikan" : "Tampilkan" }}
                            </button>
                        </div>

                        <div v-show="showCalendar">
                            <!-- Calendar Header -->
                            <div class="flex items-center justify-between mb-4">
                                <button
                                    @click="previousMonth"
                                    class="p-2 hover:bg-gray-100 rounded-lg transition"
                                >
                                    ◀
                                </button>
                                <h4 class="font-bold text-gray-800">
                                    {{ currentMonthName }}
                                </h4>
                                <button
                                    @click="nextMonth"
                                    class="p-2 hover:bg-gray-100 rounded-lg transition"
                                >
                                    ▶
                                </button>
                            </div>

                            <!-- Day names -->
                            <div class="grid grid-cols-7 gap-1 mb-2">
                                <div
                                    v-for="dayName in dayNames"
                                    :key="dayName"
                                    class="text-center text-xs font-semibold text-gray-600 py-2"
                                >
                                    {{ dayName }}
                                </div>
                            </div>

                            <!-- Calendar days -->
                            <div class="grid grid-cols-7 gap-1">
                                <div
                                    v-for="(dayData, index) in calendarDays"
                                    :key="index"
                                    :class="[
                                        'aspect-square flex items-center justify-center text-sm rounded-lg transition relative',
                                        !dayData ? 'invisible' : '',
                                        dayData && dayData.isPast
                                            ? 'bg-gray-100 text-gray-400 cursor-not-allowed'
                                            : '',
                                        dayData &&
                                        dayData.status === 'approved' &&
                                        !dayData.isPast
                                            ? 'bg-red-500 text-white font-bold cursor-not-allowed'
                                            : '',
                                        dayData &&
                                        dayData.status === 'pending' &&
                                        !dayData.isPast
                                            ? 'bg-orange-100 text-orange-700 font-bold'
                                            : '',
                                        dayData &&
                                        !dayData.status &&
                                        !dayData.isPast
                                            ? 'bg-green-50 text-green-700 hover:bg-green-100 cursor-pointer font-medium'
                                            : '',
                                        dayData && dayData.isToday
                                            ? 'ring-2 ring-blue-500'
                                            : '',
                                    ]"
                                    :title="
                                        dayData && dayData.status
                                            ? `Status: ${dayData.status}`
                                            : ''
                                    "
                                >
                                    <span v-if="dayData">{{
                                        dayData.day
                                    }}</span>
                                    <!-- Status indicator badge -->
                                    <span
                                        v-if="
                                            dayData &&
                                            dayData.status === 'pending'
                                        "
                                        class="absolute top-0 right-0 w-2 h-2 bg-orange-500 rounded-full"
                                    ></span>
                                    <span
                                        v-if="
                                            dayData &&
                                            dayData.status === 'approved'
                                        "
                                        class="absolute top-0 right-0 w-2 h-2 bg-red-700 rounded-full"
                                    ></span>
                                </div>
                            </div>

                            <!-- Legend -->
                            <div class="mt-4 pt-4 border-t space-y-2">
                                <div
                                    class="flex items-center space-x-2 text-sm"
                                >
                                    <div
                                        class="w-4 h-4 bg-green-50 border border-green-200 rounded"
                                    ></div>
                                    <span class="text-gray-600"
                                        >Tersedia untuk booking</span
                                    >
                                </div>
                                <div
                                    class="flex items-center space-x-2 text-sm"
                                >
                                    <div
                                        class="w-4 h-4 bg-orange-100 border border-orange-300 rounded"
                                    ></div>
                                    <span class="text-gray-600"
                                        >Pending (Menunggu konfirmasi)</span
                                    >
                                </div>
                                <div
                                    class="flex items-center space-x-2 text-sm"
                                >
                                    <div
                                        class="w-4 h-4 bg-red-500 rounded"
                                    ></div>
                                    <span class="text-gray-600"
                                        >Approved (Sudah dibooking)</span
                                    >
                                </div>
                                <div
                                    class="flex items-center space-x-2 text-sm"
                                >
                                    <div
                                        class="w-4 h-4 bg-gray-100 border border-gray-300 rounded"
                                    ></div>
                                    <span class="text-gray-600"
                                        >Tanggal lampau</span
                                    >
                                </div>
                                <div
                                    class="flex items-center space-x-2 text-sm"
                                >
                                    <div
                                        class="w-4 h-4 bg-white border-2 border-blue-500 rounded"
                                    ></div>
                                    <span class="text-gray-600">Hari ini</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Details & Booking -->
                <div>
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <!-- Title & Rating -->
                        <div class="mb-6">
                            <h1 class="text-3xl font-bold text-gray-900 mb-3">
                                {{ product.nama }}
                            </h1>
                            <div class="flex items-center space-x-4 mb-3">
                                <div class="flex items-center text-yellow-500">
                                    ⭐⭐⭐⭐⭐
                                    <span class="text-gray-600 ml-2">4.9</span>
                                </div>
                                <span class="text-gray-500">|</span>
                                <span class="text-gray-600">128 Ulasan</span>
                                <span class="text-gray-500">|</span>
                                <span class="text-green-600 font-medium"
                                    >✓ Tersedia</span
                                >
                            </div>
                            <div class="flex items-baseline space-x-2">
                                <span class="text-4xl font-bold text-blue-600">
                                    {{
                                        formatCurrency(
                                            product.harga_sewa_perhari
                                        )
                                    }}
                                </span>
                                <span class="text-gray-500">/ hari</span>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-6 pb-6 border-b">
                            <h3 class="font-bold text-lg mb-2">Deskripsi</h3>
                            <p class="text-gray-600 leading-relaxed">
                                {{ product.deskripsi }}
                            </p>
                        </div>

                        <!-- Date Warning Alert -->
                        <div v-if="showDateWarning">
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                                leave-active-class="transition duration-200 ease-in"
                                leave-from-class="opacity-100 scale-100"
                                leave-to-class="opacity-0 scale-95"
                            >
                                <div
                                    v-if="hasBookedDatesInRange"
                                    class="mt-6 bg-red-50 border-2 border-red-200 rounded-xl p-4 flex items-start gap-3"
                                >
                                    <svg
                                        class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                    <div>
                                        <p class="font-semibold text-red-800">
                                            Tanggal tidak tersedia
                                        </p>
                                        <p class="text-sm text-red-600 mt-1">
                                            Terdapat tanggal yang sudah
                                            dibooking pada rentang waktu yang
                                            Anda pilih. Silakan pilih tanggal
                                            lain.
                                        </p>
                                    </div>
                                </div>
                            </transition>

                            <!-- Info Durasi -->
                            <transition
                                enter-active-class="transition duration-300 ease-out"
                                enter-from-class="opacity-0 scale-95"
                                enter-to-class="opacity-100 scale-100"
                            >
                                <div
                                    v-if="
                                        rentalForm.tanggal_mulai &&
                                        rentalForm.tanggal_selesai &&
                                        !hasBookedDatesInRange
                                    "
                                    class="mt-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-4"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="bg-blue-500 rounded-full p-2"
                                            >
                                                <svg
                                                    class="w-5 h-5 text-white"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    />
                                                </svg>
                                            </div>
                                            <div>
                                                <p
                                                    class="text-sm text-gray-600"
                                                >
                                                    Durasi Rental
                                                </p>
                                                <p
                                                    class="text-xl font-bold text-gray-800"
                                                >
                                                    {{ calculateDuration() }}
                                                    Hari
                                                </p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">
                                                Dari
                                            </p>
                                            <p
                                                class="text-sm font-semibold text-gray-700"
                                            >
                                                {{
                                                    formatDate(
                                                        rentalForm.tanggal_mulai
                                                    )
                                                }}
                                            </p>
                                            <p
                                                class="text-xs text-gray-500 mt-1"
                                            >
                                                Sampai
                                            </p>
                                            <p
                                                class="text-sm font-semibold text-gray-700"
                                            >
                                                {{
                                                    formatDate(
                                                        rentalForm.tanggal_selesai
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>

                        <!-- Booking Form -->
                        <form
                            @submit.prevent="submitBooking"
                            class="space-y-6 pt-2"
                        >
                            <!-- Date Range -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="group">
                                    <label
                                        class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3"
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
                                            />
                                        </svg>
                                        Tanggal Mulai
                                    </label>
                                    <div class="relative">
                                        <input
                                            v-model="rentalForm.tanggal_mulai"
                                            type="date"
                                            :min="minDate"
                                            required
                                            :class="[
                                                'w-full px-5 py-4 pr-12',
                                                'border-2 rounded-xl',
                                                'font-medium text-gray-700',
                                                'transition-all duration-300',
                                                'focus:outline-none focus:ring-4',
                                                hasBookedDatesInRange
                                                    ? 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-100'
                                                    : 'border-gray-200 bg-white focus:border-blue-500 focus:ring-blue-100 hover:border-blue-300 hover:shadow-md',
                                            ]"
                                        />
                                        <div
                                            class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"
                                        >
                                            <svg
                                                v-if="!hasBookedDatesInRange"
                                                class="w-5 h-5 text-blue-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                class="w-5 h-5 text-red-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <transition
                                        enter-active-class="transition duration-200 ease-out"
                                        enter-from-class="opacity-0 -translate-y-1"
                                        enter-to-class="opacity-100 translate-y-0"
                                        leave-active-class="transition duration-150 ease-in"
                                        leave-from-class="opacity-100 translate-y-0"
                                        leave-to-class="opacity-0 -translate-y-1"
                                    >
                                        <p
                                            v-if="rentalForm.tanggal_mulai"
                                            class="mt-2 text-xs text-green-600 font-medium flex items-center gap-1"
                                        >
                                            <svg
                                                class="w-3 h-3"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            Tanggal dipilih
                                        </p>
                                    </transition>
                                </div>
                                <div class="group">
                                    <label
                                        class="flex items-center gap-2 text-sm font-semibold text-gray-700 mb-3"
                                    >
                                        <svg
                                            class="w-4 h-4 text-indigo-600"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        Tanggal Selesai
                                    </label>
                                    <div class="relative">
                                        <input
                                            v-model="rentalForm.tanggal_selesai"
                                            type="date"
                                            :min="
                                                rentalForm.tanggal_mulai ||
                                                minDate
                                            "
                                            required
                                            :disabled="
                                                !rentalForm.tanggal_mulai
                                            "
                                            :class="[
                                                'w-full px-5 py-4 pr-12',
                                                'border-2 rounded-xl',
                                                'font-medium text-gray-700',
                                                'transition-all duration-300',
                                                'focus:outline-none focus:ring-4',
                                                !rentalForm.tanggal_mulai &&
                                                    'opacity-50 cursor-not-allowed',
                                                hasBookedDatesInRange
                                                    ? 'border-red-300 bg-red-50 focus:border-red-500 focus:ring-red-100'
                                                    : 'border-gray-200 bg-white focus:border-indigo-500 focus:ring-indigo-100 hover:border-indigo-300 hover:shadow-md',
                                            ]"
                                        />
                                        <div
                                            class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none"
                                        >
                                            <svg
                                                v-if="
                                                    !hasBookedDatesInRange &&
                                                    rentalForm.tanggal_mulai
                                                "
                                                class="w-5 h-5 text-indigo-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            <svg
                                                v-else-if="
                                                    hasBookedDatesInRange
                                                "
                                                class="w-5 h-5 text-red-500"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                class="w-5 h-5 text-gray-400"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <transition
                                        enter-active-class="transition duration-200 ease-out"
                                        enter-from-class="opacity-0 -translate-y-1"
                                        enter-to-class="opacity-100 translate-y-0"
                                        leave-active-class="transition duration-150 ease-in"
                                        leave-from-class="opacity-100 translate-y-0"
                                        leave-to-class="opacity-0 -translate-y-1"
                                    >
                                        <p
                                            v-if="!rentalForm.tanggal_mulai"
                                            class="mt-2 text-xs text-gray-500 flex items-center gap-1"
                                        >
                                            <svg
                                                class="w-3 h-3"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            Pilih tanggal mulai terlebih dahulu
                                        </p>
                                        <p
                                            v-else-if="
                                                rentalForm.tanggal_selesai
                                            "
                                            class="mt-2 text-xs text-green-600 font-medium flex items-center gap-1"
                                        >
                                            <svg
                                                class="w-3 h-3"
                                                fill="currentColor"
                                                viewBox="0 0 20 20"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                            Tanggal dipilih
                                        </p>
                                    </transition>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Jumlah Unit
                                </label>
                                <div class="flex items-center space-x-4">
                                    <button
                                        type="button"
                                        @click="
                                            quantity = Math.max(1, quantity - 1)
                                        "
                                        class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg font-bold"
                                    >
                                        -
                                    </button>
                                    <input
                                        v-model.number="quantity"
                                        type="number"
                                        min="1"
                                        class="w-20 text-center px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500"
                                    />
                                    <button
                                        type="button"
                                        @click="quantity++"
                                        class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg font-bold"
                                    >
                                        +
                                    </button>
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Catatan (Opsional)
                                </label>
                                <textarea
                                    v-model="rentalForm.catatan"
                                    rows="3"
                                    placeholder="Tambahkan catatan untuk pesanan Anda..."
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-blue-500"
                                ></textarea>
                            </div>

                            <!-- Summary -->
                            <div
                                v-if="rentalDuration > 0"
                                class="bg-blue-50 rounded-xl p-6 space-y-3"
                            >
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600"
                                        >Durasi Sewa</span
                                    >
                                    <span class="font-bold"
                                        >{{ rentalDuration }} Hari</span
                                    >
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600"
                                        >Harga per Hari</span
                                    >
                                    <span class="font-bold">{{
                                        formatCurrency(
                                            product.harga_sewa_perhari
                                        )
                                    }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Jumlah</span>
                                    <span class="font-bold"
                                        >{{ quantity }} Unit</span
                                    >
                                </div>
                                <div class="border-t pt-3 flex justify-between">
                                    <span class="font-bold text-lg">Total</span>
                                    <span
                                        class="font-bold text-2xl text-blue-600"
                                    >
                                        {{ formatCurrency(totalPrice) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <button
                                    type="submit"
                                    :disabled="!isFormValid"
                                    :class="[
                                        'w-full font-bold py-4 rounded-xl transition shadow-lg',
                                        isFormValid
                                            ? 'bg-blue-600 hover:bg-blue-700 text-white'
                                            : 'bg-gray-300 text-gray-500 cursor-not-allowed',
                                    ]"
                                >
                                    🎯 Booking Sekarang
                                </button>
                                <button
                                    type="button"
                                    @click="addToCart"
                                    :disabled="!isFormValid"
                                    :class="[
                                        'w-full font-bold py-4 rounded-xl transition border-2',
                                        isFormValid
                                            ? 'bg-white border-blue-600 text-blue-600 hover:bg-blue-50'
                                            : 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed',
                                    ]"
                                >
                                    🛒 Tambah ke Keranjang
                                </button>
                            </div>
                        </form>

                        <!-- Additional Info -->
                        <div class="mt-6 pt-6 border-t space-y-3">
                            <div
                                class="flex items-center space-x-3 text-sm text-gray-600"
                            >
                                <span>✅</span>
                                <span>Gratis antar-jemput area Jakarta</span>
                            </div>
                            <div
                                class="flex items-center space-x-3 text-sm text-gray-600"
                            >
                                <span>🛡️</span>
                                <span>Garansi peralatan & asuransi</span>
                            </div>
                            <div
                                class="flex items-center space-x-3 text-sm text-gray-600"
                            >
                                <span>💳</span>
                                <span>Pembayaran fleksibel</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
input[type="date"]::-webkit-calendar-picker-indicator {
    cursor: pointer;
}
</style>
