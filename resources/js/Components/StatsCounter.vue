<script setup>
defineProps({
    counterCourses: {
        type: [String, Number],
        required: true,
    },
    counterStudents: {
        type: [String, Number],
        required: true,
    },
    counterTeachers: {
        type: [String, Number],
        required: true,
    },
    counterBatches: {
        type: [String, Number],
        required: true,
    },
});
</script>

<template>
    <!-- Stats Section -->
    <section class="py-5 bg-gradient-stats text-white position-relative overflow-hidden">
        <div class="container py-5 position-relative z-1 animate-up">
            <div class="row g-4 justify-content-center text-center">
                <div class="col-6 col-lg-3">
                    <div class="p-3 stat-item">
                        <div class="fs-3 mb-2 text-warning"><i class="bi bi-building"></i></div>
                        <h2 class="display-4 fw-extrabold mb-1 tracking-tight">{{ counterCourses }}</h2>
                        <p class="mb-0 text-white-50 fw-semibold text-uppercase tracking-wider small">{{ __('Partner Organizations') }}</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="p-3 stat-item">
                        <div class="fs-3 mb-2 text-warning"><i class="bi bi-mortarboard-fill"></i></div>
                        <h2 class="display-4 fw-extrabold mb-1 tracking-tight">{{ counterStudents }}</h2>
                        <p class="mb-0 text-white-50 fw-semibold text-uppercase tracking-wider small">{{ __('Certified Graduates') }}</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="p-3 stat-item">
                        <div class="fs-3 mb-2 text-warning"><i class="bi bi-people-fill"></i></div>
                        <h2 class="display-4 fw-extrabold mb-1 tracking-tight">{{ counterTeachers }}</h2>
                        <p class="mb-0 text-white-50 fw-semibold text-uppercase tracking-wider small">{{ __('Expert Instructors') }}</p>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="p-3 stat-item">
                        <div class="fs-3 mb-2 text-warning"><i class="bi bi-play-circle-fill"></i></div>
                        <h2 class="display-4 fw-extrabold mb-1 tracking-tight">{{ counterBatches }}</h2>
                        <p class="mb-0 text-white-50 fw-semibold text-uppercase tracking-wider small">{{ __('Active Batches') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300..900&display=swap');

h2.display-4 {
    font-family: 'Outfit', sans-serif;
    letter-spacing: -0.02em;
}

.animate-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.bg-gradient-stats {
    background: linear-gradient(135deg, #0b341f 0%, #155536 100%);
}

.stat-item {
    border-right: 1px solid rgba(255, 255, 255, 0.1);
}

@media (max-width: 991.98px) {
    .stat-item {
        border-right: none;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .stat-item:last-child {
        border-bottom: none;
    }
}
</style>
