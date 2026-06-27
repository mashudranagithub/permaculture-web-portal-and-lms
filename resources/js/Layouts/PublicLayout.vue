<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const currentLocale = computed(() => {
    return (page.props.translations && page.props.translations['Home']) ? 'bn' : 'en';
});
const portalSettings = computed(() => page.props.portal_settings || {});

const getSettingVal = (val, fallback = '') => {
    if (!val) return fallback;
    if (typeof val === 'string') return val;
    if (typeof val === 'object') {
        return val[currentLocale.value] || val['en'] || fallback;
    }
    return fallback;
};
</script>

<template>
    <div class="min-vh-100 d-flex flex-column bg-light">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-success py-3 shadow-sm sticky-top">
            <div class="container">
                <Link class="navbar-brand d-flex align-items-center gap-2" href="/">
                    <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center p-1 fw-bold" style="width: 35px; height: 35px; font-size: 14px;">RS</div>
                    <span class="fw-bold tracking-tight">Regenerative Systems</span>
                </Link>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                        <li class="nav-item">
                            <Link :href="route('courses.browse')" class="nav-link text-white" :class="{ 'active fw-bold': route().current('courses.browse') }">{{ __('Education') }}</Link>
                        </li>
                        <li class="nav-item">
                            <Link :href="route('partners.index')" class="nav-link text-white" :class="{ 'active fw-bold': route().current('partners.index') || route().current('partners.show') }">{{ __('Partners') }}</Link>
                        </li>
                        <li class="nav-item">
                            <Link :href="route('about')" class="nav-link text-white" :class="{ 'active fw-bold': route().current('about') }">{{ __('About') }}</Link>
                        </li>
                        <li class="nav-item">
                            <Link :href="route('contact')" class="nav-link text-white" :class="{ 'active fw-bold': route().current('contact') }">{{ __('Contact') }}</Link>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center gap-2">
                        <!-- Language Switcher -->
                        <div class="btn-group btn-group-sm me-3" role="group">
                            <Link :href="route('locale', 'en')" 
                                class="btn btn-outline-light py-1 px-3 shadow-sm" 
                                :class="{ 'active': $page.props.translations && !$page.props.translations['Home'] }">
                                EN
                            </Link>
                            <Link :href="route('locale', 'bn')" 
                                class="btn btn-outline-light py-1 px-3 shadow-sm"
                                :class="{ 'active': $page.props.translations && $page.props.translations['Home'] }">
                                BN
                            </Link>
                        </div>

                        <template v-if="user">
                            <Link :href="route('dashboard')" class="btn btn-outline-light rounded-pill px-4 shadow-sm">
                                {{ __('Dashboard') }}
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('org.register')" class="btn btn-light rounded-pill px-4 shadow-sm fw-bold text-success">
                                {{ __('Become a Partner') }}
                            </Link>
                            <Link :href="route('login')" class="btn btn-outline-light rounded-pill px-4 shadow-sm fw-bold">
                                {{ __('Log in') }}
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow-1">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="text-white mt-auto border-top border-success-subtle-10" style="background-color: #0b1810;">
            <div class="container py-5">
                <div class="row g-5">
                    
                    <!-- Brand Column -->
                    <div class="col-lg-4 col-md-6">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center p-1 fw-bold font-outfit" style="width: 45px; height: 45px; font-size: 16px;">RS</div>
                            <h4 class="mb-0 fw-extrabold text-white tracking-tight font-outfit">{{ __('Regenerative Systems') }}</h4>
                        </div>
                        <p class="text-white-50 leading-relaxed mb-4" style="font-size: 0.95rem;">
                            {{ getSettingVal(portalSettings.footer_description) }}
                        </p>
                        <!-- Social Media Links -->
                        <div class="d-flex gap-2">
                            <a :href="getSettingVal(portalSettings.contact_facebook, '#')" target="_blank" class="btn btn-social rounded-circle"><i class="bi bi-facebook"></i></a>
                            <a :href="getSettingVal(portalSettings.contact_twitter, '#')" target="_blank" class="btn btn-social rounded-circle"><i class="bi bi-twitter-x"></i></a>
                            <a :href="getSettingVal(portalSettings.contact_youtube, '#')" target="_blank" class="btn btn-social rounded-circle"><i class="bi bi-youtube"></i></a>
                        </div>
                    </div>

                    <!-- Navigation Column -->
                    <div class="col-lg-2 col-md-6 col-6">
                        <h6 class="text-uppercase tracking-wider fw-bold text-white mb-3" style="font-size: 0.85rem;">{{ __('Explore') }}</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2" style="font-size: 0.9rem;">
                            <li><Link :href="route('courses.browse')" class="footer-link">{{ __('Course Catalog') }}</Link></li>
                            <li><Link :href="route('partners.index')" class="footer-link">{{ __('Partners') }}</Link></li>
                            <li><Link :href="route('about')" class="footer-link">{{ __('About Us') }}</Link></li>
                            <li><Link :href="route('contact')" class="footer-link">{{ __('Contact') }}</Link></li>
                        </ul>
                    </div>

                    <!-- Resources Column -->
                    <div class="col-lg-3 col-md-6 col-6">
                        <h6 class="text-uppercase tracking-wider fw-bold text-white mb-3" style="font-size: 0.85rem;">{{ __('Resources') }}</h6>
                        <ul class="list-unstyled d-flex flex-column gap-2" style="font-size: 0.9rem;">
                            <li><Link :href="route('terms')" class="footer-link">{{ __('Terms & Conditions') }}</Link></li>
                            <li><Link :href="route('privacy')" class="footer-link">{{ __('Privacy Policy') }}</Link></li>
                            <li><Link :href="route('org.register')" class="footer-link">{{ __('Become a Partner') }}</Link></li>
                            <li><Link :href="route('login')" class="footer-link">{{ __('Member Login') }}</Link></li>
                        </ul>
                    </div>

                    <!-- Contact & Location Column -->
                    <div class="col-lg-3 col-md-6">
                        <h6 class="text-uppercase tracking-wider fw-bold text-white mb-3" style="font-size: 0.85rem;">{{ __('Contact Us') }}</h6>
                        <ul class="list-unstyled d-flex flex-column gap-3 text-white-50" style="font-size: 0.9rem;">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-geo-alt-fill text-success fs-5"></i>
                                <span>{{ getSettingVal(portalSettings.contact_address) }}</span>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope-fill text-success fs-5"></i>
                                <a :href="'mailto:' + getSettingVal(portalSettings.contact_email)" class="footer-link-direct">
                                    {{ getSettingVal(portalSettings.contact_email) }}
                                </a>
                            </li>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-success fs-5"></i>
                                <a :href="'tel:' + getSettingVal(portalSettings.contact_phone)" class="footer-link-direct">
                                    {{ getSettingVal(portalSettings.contact_phone) }}
                                </a>
                            </li>
                        </ul>
                    </div>

                </div>

                <!-- Copyright / Bottom Row -->
                <div class="border-top border-success-subtle-10 mt-5 pt-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3" style="font-size: 0.85rem;">
                    <span class="text-white-50">&copy; 2026 {{ __('Regenerative Systems') }}. All rights reserved.</span>
                    <span class="text-white-50 d-flex align-items-center gap-1 flex-wrap">
                        {{ __('Designed and Developed By') }} <a href="https://mashudrana.com" target="_blank" class="text-success text-decoration-none fw-bold hover-underline">Mashud Rana</a> <i class="bi bi-heart-fill text-danger animate-pulse"></i>
                    </span>
                </div>

            </div>
        </footer>
    </div>
</template>

<style scoped>
.navbar-nav .nav-link:hover {
    color: #f8f9fa !important;
}
.btn-group .btn.active {
    background-color: white !important;
    color: var(--bs-success) !important;
    font-weight: bold;
}
.nav-link.active {
    border-bottom: 2px solid white;
}

/* Footer Premium Styles */
.border-success-subtle-10 {
    border-color: rgba(25, 135, 84, 0.12) !important;
}
.btn-social {
    color: #ced4da;
    border: 1px solid rgba(255, 255, 255, 0.125);
    background-color: rgba(255, 255, 255, 0.03);
    transition: all 0.2s ease-in-out;
    width: 38px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.btn-social:hover {
    color: white;
    background-color: var(--bs-success);
    border-color: var(--bs-success);
    transform: translateY(-2px);
}
.footer-link {
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-block;
}
.footer-link:hover {
    color: #39da8a;
    transform: translateX(3px);
}
.footer-link-direct {
    color: rgba(255, 255, 255, 0.6);
    text-decoration: none;
    transition: color 0.2s ease;
}
.footer-link-direct:hover {
    color: #39da8a;
}
</style>
