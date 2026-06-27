<script setup>
import { computed } from 'vue';

const props = defineProps({
    ethics: {
        type: Array,
        default: () => []
    },
    currentLocale: {
        type: String,
        default: 'en'
    }
});

// Translation helper
const __ = (key) => {
    const translations = {
        'Guiding Values': { en: 'Guiding Values', bn: 'নির্দেশক মূল্যবোধ' },
        'Core Principles & Ethics': { en: 'Core Principles & Ethics', bn: 'মূল নীতি ও নৈতিকতা' },
        'Guiding every project, course, and partnership on our platform.': { 
            en: 'Guiding every project, course, and partnership on our platform.', 
            bn: 'আমাদের প্ল্যাটফর্মের প্রতিটি প্রকল্প, কোর্স এবং অংশীদারিত্বের নির্দেশক।'
        },
        'Earth Care': { en: 'Earth Care', bn: 'আর্থ কেয়ার' },
        'People Care': { en: 'People Care', bn: 'পিপল কেয়ার' },
        'Fair Share': { en: 'Fair Share', bn: 'ফেয়ার শেয়ার' },
        'Rebuilding natural capital, restoring soil biology, conserving clean water, and fostering diverse ecosystems.': {
            en: 'Rebuilding natural capital, restoring soil biology, conserving clean water, and fostering diverse ecosystems.',
            bn: 'প্রাকৃতিক মূলধন পুনর্গঠন, মাটির জীববিজ্ঞান পুনরুদ্ধার, বিশুদ্ধ পানি সংরক্ষণ এবং বৈচিত্র্যময় বাস্তুতন্ত্র গড়ে তোলা।'
        },
        'Supporting self-reliance, community resilience, healthy food systems, and accessible lifelong learning.': {
            en: 'Supporting self-reliance, community resilience, healthy food systems, and accessible lifelong learning.',
            bn: 'স্বনির্ভরতা, সম্প্রদায়ের স্থিতিস্থাপকতা, স্বাস্থ্যকর খাদ্য ব্যবস্থা এবং সহজলভ্য আজীবন শিক্ষা সমর্থন করা।'
        },
        'Redistributing surplus resources to care for the Earth and others, fostering a collaborative economic model.': {
            en: 'Redistributing surplus resources to care for the Earth and others, fostering a collaborative economic model.',
            bn: 'পৃথিবী ও অন্যের যত্ন নিতে উদ্বৃত্ত সম্পদ পুনঃবন্টন করা, একটি সহযোগিতামূলক অর্থনৈতিক মডেল গড়ে তোলা।'
        }
    };
    return translations[key]?.[props.currentLocale] || key;
};
</script>

<template>
    <section class="py-5 bg-light-green-subtle border-top border-bottom position-relative overflow-hidden">
        <div class="container py-4">
            <div class="text-center mb-5 max-w-2xl mx-auto">
                <span class="text-success text-uppercase fw-bold tracking-wider fs-6 mb-2 d-block">{{ __('Guiding Values') }}</span>
                <h2 class="display-5 fw-bold text-dark mb-3">{{ __('Core Principles & Ethics') }}</h2>
                <p class="text-muted fs-5 mb-0">{{ __('Guiding every project, course, and partnership on our platform.') }}</p>
            </div>
            
            <div v-if="ethics && ethics.length > 0" class="row g-4 justify-content-center">
                <div v-for="(item, idx) in ethics" :key="idx" class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 bg-white p-4 p-md-5 rounded-4 shadow-sm text-center ethics-card position-relative overflow-hidden">
                        <div class="ethics-card-accent bg-success"></div>
                        <div class="ethics-icon-container bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i :class="`bi ${item.icon || 'bi-globe2'} fs-2`"></i>
                        </div>
                        <h3 class="h4 fw-bold text-dark mb-3">{{ item.title[currentLocale] || item.title['en'] }}</h3>
                        <p class="text-muted mb-0 fs-6 leading-relaxed">
                            {{ item.description[currentLocale] || item.description['en'] }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Fallback standard permaculture ethics -->
            <div v-else class="row g-4 text-center justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 bg-white p-4 p-md-5 rounded-4 shadow-sm text-center ethics-card position-relative overflow-hidden">
                        <div class="ethics-card-accent bg-success"></div>
                        <div class="ethics-icon-container bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-globe2 fs-2"></i>
                        </div>
                        <h3 class="h4 fw-bold text-dark mb-3">{{ __('Earth Care') }}</h3>
                        <p class="text-muted mb-0 fs-6 leading-relaxed">
                            {{ __('Rebuilding natural capital, restoring soil biology, conserving clean water, and fostering diverse ecosystems.') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 bg-white p-4 p-md-5 rounded-4 shadow-sm text-center ethics-card position-relative overflow-hidden">
                        <div class="ethics-card-accent bg-success"></div>
                        <div class="ethics-icon-container bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-people-fill fs-2"></i>
                        </div>
                        <h3 class="h4 fw-bold text-dark mb-3">{{ __('People Care') }}</h3>
                        <p class="text-muted mb-0 fs-6 leading-relaxed">
                            {{ __('Supporting self-reliance, community resilience, healthy food systems, and accessible lifelong learning.') }}
                        </p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 border-0 bg-white p-4 p-md-5 rounded-4 shadow-sm text-center ethics-card position-relative overflow-hidden">
                        <div class="ethics-card-accent bg-success"></div>
                        <div class="ethics-icon-container bg-success-subtle text-success rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-arrow-left-right fs-2"></i>
                        </div>
                        <h3 class="h4 fw-bold text-dark mb-3">{{ __('Fair Share') }}</h3>
                        <p class="text-muted mb-0 fs-6 leading-relaxed">
                            {{ __('Redistributing surplus resources to care for the Earth and others, fostering a collaborative economic model.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.bg-light-green-subtle {
    background-color: #f7faf8;
}
.ethics-card {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.ethics-card-accent {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.ethics-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(25, 135, 84, 0.08) !important;
}
.ethics-card:hover .ethics-card-accent {
    opacity: 1;
}
.ethics-icon-container {
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), background-color 0.3s ease, color 0.3s ease;
}
.ethics-card:hover .ethics-icon-container {
    transform: scale(1.1) rotate(5deg);
    background-color: #198754 !important;
    color: white !important;
}
.max-w-2xl {
    max-width: 780px;
}
</style>
