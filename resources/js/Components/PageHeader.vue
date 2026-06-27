<script setup>
defineProps({
    title: {
        type: String,
        required: true
    },
    subtitle: {
        type: String,
        default: ''
    },
    badge: {
        type: String,
        default: ''
    },
    bgImage: {
        type: String,
        default: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?auto=format&fit=crop&w=1920&q=80'
    }
});
</script>

<template>
    <section class="page-hero-section py-5 text-white position-relative" :style="{ background: 'linear-gradient(rgba(10, 47, 28, 0.82), rgba(25, 135, 84, 0.88)), url(' + bgImage + ')', backgroundSize: 'cover', backgroundPosition: 'center', backgroundAttachment: 'fixed' }">
        <div class="container py-5 text-center position-relative z-1 animate-up">
            <div v-if="badge" class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-success-light-20 border border-success-light-40 mb-3">
                <span class="pulse-dot"></span>
                <span class="text-uppercase tracking-wider fw-bold small text-light">{{ badge }}</span>
            </div>
            <h1 class="display-2 fw-extrabold mb-3 text-shadow">{{ title }}</h1>
            <p v-if="subtitle" class="lead max-w-3xl mx-auto fs-4 opacity-90 text-shadow mb-0">
                {{ subtitle }}
            </p>
        </div>
    </section>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300..900&display=swap');

.page-hero-section {
    min-height: 40vh;
    display: flex;
    align-items: center;
}

h1 {
    font-family: 'Outfit', sans-serif;
    letter-spacing: -0.02em;
    font-weight: 850 !important;
}

.bg-success-light-20 {
    background-color: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
}
.border-success-light-40 {
    border: 1px solid rgba(255, 255, 255, 0.3) !important;
}
.text-shadow {
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
}
.pulse-dot {
    width: 8px;
    height: 8px;
    background-color: #ffc107;
    border-radius: 50%;
    display: inline-block;
    animation: pulseGlow 2.0s infinite ease-in-out;
}

.animate-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    width: 100%;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulseGlow {
    0% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
    70% { transform: scale(1.1); box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
    100% { transform: scale(0.9); box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
}
</style>
