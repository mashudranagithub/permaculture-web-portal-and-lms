<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref, computed, watch } from 'vue';

import Swal from 'sweetalert2';

const isSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
};

// Detect if any ACL route is active
const isAclActive = computed(() => {
    return route().current('admin.users.*') || 
           route().current('admin.roles.*') || 
           route().current('admin.permissions.*');
});

// Vue-native toggle state for the menu
const aclMenuOpen = ref(isAclActive.value);

// Sync open state when the route changes (e.g. clicking a deep link elsewhere)
watch(isAclActive, (newVal) => {
    if (newVal) aclMenuOpen.value = true;
}, { immediate: true });

const toggleAclMenu = () => {
    aclMenuOpen.value = !aclMenuOpen.value;
};

// Organization Menu State
const orgMenuOpen = ref(route().current('admin.organizations.*'));
watch(() => route().current('admin.organizations.*'), (newVal) => {
    if (newVal) orgMenuOpen.value = true;
});

onMounted(() => {
    // Basic initialization
});

const page = usePage();

// Global Flash Message Watcher using SweetAlert
watch(() => page.props.flash, (flash) => {
    if (flash?.message) {
        Swal.fire({
            icon: 'success',
            title: flash.message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }
    if (flash?.error) {
        Swal.fire({
            icon: 'error',
            title: flash.error,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true
        });
    }
}, { deep: true });
</script>

<template>
    <div class="app-wrapper" :class="{ 'sidebar-collapse': isSidebarCollapsed, 'sidebar-open': !isSidebarCollapsed, 'sidebar-mini': true }">
        <!-- Header Navbar -->
        <nav class="app-header navbar navbar-expand bg-white border-bottom shadow-sm">
            <div class="container-fluid">
                <!-- Left Menu -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#" role="button" @click.prevent="toggleSidebar">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <Link :href="route('dashboard')" class="nav-link fw-bold text-success">{{ __('Dashboard') }}</Link>
                    </li>
                </ul>

                <!-- Right Menu -->
                <ul class="navbar-nav ms-auto align-items-center">
                    <!-- Language Switcher -->
                    <li class="nav-item me-3">
                        <div class="btn-group btn-group-sm rounded-pill overflow-hidden border border-success-subtle">
                            <Link :href="route('locale', 'en')" class="btn btn-outline-success border-0 px-2 py-0" :class="{ 'active': $page.props.translations && !$page.props.translations['Home'] }">EN</Link>
                            <Link :href="route('locale', 'bn')" class="btn btn-outline-success border-0 px-2 py-0" :class="{ 'active': $page.props.translations && $page.props.translations['Home'] }">BN</Link>
                        </div>
                    </li>

                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <img :src="$page.props.auth.user.avatar_url" class="user-image rounded-circle shadow border border-success-subtle object-fit-cover" style="width: 32px; height: 32px;" alt="User Image">
                            <span class="d-none d-md-inline fw-semibold text-dark">{{ $page.props.auth.user.name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end shadow border-0 rounded-3">
                            <li class="user-header bg-success text-white p-3 text-center">
                                <img :src="$page.props.auth.user.avatar_url" class="rounded-circle shadow mb-2 border border-2 border-white object-fit-cover" style="width: 70px; height: 70px;" alt="User Image">
                                <p class="fw-bold mb-0">{{ $page.props.auth.user.name }}</p>
                                <small v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.length">{{ $page.props.auth.user.roles[0] }}</small>
                            </li>
                            <li class="user-footer p-2 text-center bg-light">
                                <Link :href="route('profile.edit')" class="btn btn-default btn-flat btn-sm rounded-pill px-3">{{ __('Profile') }}</Link>
                                <Link :href="route('logout')" method="post" as="button" class="btn btn-default btn-flat btn-sm rounded-pill px-3 ms-2 text-danger">{{ __('Log Out') }}</Link>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar -->
        <aside class="app-sidebar bg-dark shadow" data-bs-theme="dark">
            <div class="sidebar-brand bg-success py-2">
                <Link :href="route('dashboard')" class="brand-link text-decoration-none d-flex align-items-center justify-content-center w-100 px-2">
                    <template v-if="$page.props.auth.user.organization">
                        <div class="d-flex align-items-center w-100 overflow-hidden">
                            <img :src="$page.props.auth.user.organization.logo_url" alt="Org Logo" 
                                class="shadow-sm me-2 bg-white p-1 rounded" 
                                style="height: 38px; width: 38px; object-fit: contain; min-width: 38px;">
                            <span class="brand-text fw-bold text-white lh-sm text-start" style="font-size: 0.85rem; display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $page.props.auth.user.organization.name }}
                            </span>
                        </div>
                    </template>
                    <template v-else>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-leaf-fill fs-3 text-white me-2"></i>
                            <span class="brand-text fw-bold text-white fs-5">Permaculture</span>
                        </div>
                    </template>
                </Link>
            </div>
            <div class="sidebar-wrapper scrollbar-simple">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <Link :href="route('dashboard')" class="nav-link" :class="{ 'active': route().current('dashboard') }">
                                <i class="nav-icon bi bi-speedometer2"></i>
                                <p>{{ __('Dashboard') }}</p>
                            </Link>
                        </li>

                        <!-- Education Section -->
                        <li class="nav-header text-uppercase small text-white-50 mt-3">{{ __('Education') }}</li>
                        <li class="nav-item">
                            <Link :href="route('courses.index')" class="nav-link" :class="{ 'active': route().current('courses.*') }">
                                <i class="nav-icon bi bi-journal-bookmark-fill"></i>
                                <p>{{ __('Courses') }}</p>
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link :href="route('batches.index')" class="nav-link" :class="{ 'active': route().current('batches.*') }">
                                <i class="nav-icon bi bi-collection-fill"></i>
                                <p>{{ __('Batches') }}</p>
                            </Link>
                        </li>

                        <!-- Student Section -->
                        <li class="nav-header text-uppercase small text-white-50 mt-3">{{ __('Student') }}</li>
                        <li class="nav-item">
                            <Link :href="route('enrollments.my-courses')" class="nav-link" :class="{ 'active': route().current('enrollments.my-courses') }">
                                <i class="nav-icon bi bi-display"></i>
                                <p>{{ __('My Learning') }}</p>
                            </Link>
                        </li>
                        <li class="nav-item">
                            <Link :href="route('courses.browse')" class="nav-link" :class="{ 'active': route().current('courses.browse') }">
                                <i class="nav-icon bi bi-search"></i>
                                <p>{{ __('Browse Catalog') }}</p>
                            </Link>
                        </li>

                        <!-- Organizations (LMS Admin only) -->
                        <li v-if="$page.props.auth.user.roles.includes('super-admin')" class="nav-header text-uppercase small text-white-50 mt-3">{{ __('Organizations') }}</li>
                        <li v-if="$page.props.auth.user.roles.includes('super-admin')" class="nav-item" :class="{ 'menu-open': orgMenuOpen }">
                            <a href="#" class="nav-link" :class="{ 'active': route().current('admin.organizations.*') }" @click.prevent="orgMenuOpen = !orgMenuOpen">
                                <i class="nav-icon bi bi-buildings-fill"></i>
                                <p>
                                    {{ __('Organizations') }}
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" :style="{ display: orgMenuOpen ? 'block' : 'none' }">
                                <li class="nav-item">
                                    <Link :href="route('admin.organizations.index')" class="nav-link" :class="{ 'active': route().current('admin.organizations.index') }">
                                        <i class="nav-icon bi bi-card-list"></i>
                                        <p>All Organizations</p>
                                    </Link>
                                </li>
                                <li class="nav-item">
                                    <Link :href="route('admin.organizations.queue')" class="nav-link" :class="{ 'active': route().current('admin.organizations.queue') }">
                                        <i class="nav-icon bi bi-hourglass-split"></i>
                                        <p>
                                            Approval Queue
                                            <span v-if="$page.props.pendingCount" class="badge bg-warning text-dark ms-auto rounded-pill px-1" style="font-size: 0.65rem;">{{ $page.props.pendingCount }}</span>
                                        </p>
                                    </Link>
                                </li>
                            </ul>
                        </li>

                        <!-- ACL Section -->
                        <li v-if="can('manage-users') || can('manage-roles') || can('manage-permissions')" class="nav-header text-uppercase small text-white-50 mt-3">{{ __('System Management') }}</li>
                        <li v-if="can('manage-users') || can('manage-roles') || can('manage-permissions')" class="nav-item" :class="{ 'menu-open': aclMenuOpen }">
                            <a href="#" class="nav-link" :class="{ 'active': isAclActive }" @click.prevent="toggleAclMenu">
                                <i class="nav-icon bi bi-shield-lock-fill"></i>
                                <p>
                                    {{ __('Access Management') }}
                                    <i class="nav-arrow bi bi-chevron-right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" :style="{ display: aclMenuOpen ? 'block' : 'none' }">
                                <li v-if="can('manage-users')" class="nav-item">
                                    <Link :href="route('admin.users.index')" class="nav-link" :class="{ 'active': route().current('admin.users.*') }">
                                        <i class="nav-icon bi bi-person-gear"></i>
                                        <p>{{ __('User Management') }}</p>
                                    </Link>
                                </li>
                                <li v-if="can('manage-roles')" class="nav-item">
                                    <Link :href="route('admin.roles.index')" class="nav-link" :class="{ 'active': route().current('admin.roles.*') }">
                                        <i class="nav-icon bi bi-shield-check"></i>
                                        <p>{{ __('Role Management') }}</p>
                                    </Link>
                                </li>
                                <li v-if="can('manage-permissions')" class="nav-item">
                                    <Link :href="route('admin.permissions.index')" class="nav-link" :class="{ 'active': route().current('admin.permissions.*') }">
                                        <i class="nav-icon bi bi-key"></i>
                                        <p>{{ __('Permission Management') }}</p>
                                    </Link>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="app-main bg-light min-vh-100 position-relative">

            <div class="app-content-header border-bottom bg-white py-3 mb-4" v-if="$slots.header">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-8">
                            <h3 class="mb-0 fw-bold text-dark font-outfit"><slot name="header" /></h3>
                        </div>
                        <div class="col-sm-4 text-sm-end">
                            <ol class="breadcrumb float-sm-end mb-0 small">
                                <li class="breadcrumb-item"><a href="#" class="text-success">{{ __('Home') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ __('Dashboard') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content">
                <div class="container-fluid">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>

<style>
/* Dashboard Font Size Reduction Only */
.app-wrapper {
    font-size: 0.85rem;
}

/* Sidebar Active & Hover Styles */
.sidebar-menu .nav-link {
    transition: all 0.2s ease;
    border-radius: 0.25rem;
    margin: 0.1rem 0.5rem;
    color: #ced4da; /* Default muted text */
}

.sidebar-menu .nav-link p {
    font-size: 0.8rem;
    margin-bottom: 0;
}

/* Parent Active State (Branch/Folder) - Subtle Accent, No Background */
.sidebar-menu > .nav-item > .nav-link.active {
    background-color: transparent !important; /* No background as requested */
    color: #198754 !important; /* Green text instead */
    font-weight: 600;
}

.sidebar-menu > .nav-item > .nav-link.active i {
    color: #198754 !important;
}


/* Leaf Node (Final Link) Active State - SOLID GREEN BAGROUND */
/* This shows exactly where the user is */
.nav-link.active:not(:has(.nav-arrow)), 
.nav-treeview .nav-link.active {
    background-color: #198754 !important;
    color: #fff !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.nav-link.active:not(:has(.nav-arrow)) i,
.nav-treeview .nav-link.active i {
    color: #fff !important;
}

/* Submenu Padding & Hierarchy */
.nav-treeview .nav-link.active {
    border-left: 3px solid #fff !important;
    padding-left: 1.8rem !important;
}

.sidebar-menu .nav-link:hover:not(.active) {
    background-color: rgba(25, 135, 84, 0.1);
    color: #198754 !important;
}

.sidebar-brand.bg-success { height: 3.5rem; display: flex; align-items: center; justify-content: center; }
.user-header { min-height: 150px; }
.nav-header { padding: 0.75rem 1.25rem 0.25rem; font-size: 0.7rem; font-weight: 800; color: #6c757d !important; letter-spacing: 0.5px; }

/* AdminLTE 4 Hierarchy Styling */
.nav-treeview { padding-left: 0; background-color: rgba(0,0,0,0.05); }
.nav-treeview .nav-item .nav-link { padding-left: 2rem !important; font-size: 0.8rem; }
.nav-arrow { 
    transition: transform 0.3s ease; 
    float: right;
    margin-top: 2px;
}
.menu-open > .nav-link .nav-arrow {
    transform: rotate(90deg);
}

/* Compact Table & Form Defaults */
.table, 
.table th, 
.table td, 
.dataTable th, 
.dataTable td { 
    font-size: 0.8rem !important; 
}

.table th, .dataTable th {
    text-transform: capitalize !important;
    font-weight: 700 !important;
    letter-spacing: 0.2px;
}

.btn-sm { font-size: 0.75rem; }
.form-control-sm, .form-select-sm { font-size: 0.75rem; }
</style>
