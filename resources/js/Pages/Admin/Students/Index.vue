<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    users: Object,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 10);
const sortField = ref(props.filters.sort_field || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('admin.students.index'), {
        search: search.value,
        per_page: perPage.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value
    }, {
        preserveState: true,
        replace: true
    });
};

watch([search, perPage], debounce(updateTable, 300));

const toggleSort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    updateTable();
};

const exportData = (type) => {
    const data = props.users.data.map((u, index) => ({
        SL: (props.users.current_page - 1) * props.users.per_page + index + 1,
        Name: u.name,
        Email: u.email,
        Courses: u.enrollments_count,
        Joined: u.created_at
    }));

    if (type === 'copy') {
        const text = data.map(obj => Object.values(obj).join('\t')).join('\n');
        navigator.clipboard.writeText(text);
        alert(__('Data copied to clipboard!'));
    }
    // Other export logic could be added here
};
</script>

<template>
    <Head :title="__('Manage Students')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Student Management') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-people-fill text-success"></i>{{ __('Organization Students') }}
                            </h3>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center">
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted">{{ __('Show') }}</span>
                                <select v-model="perPage" class="form-select form-select-sm w-auto shadow-none border-success-subtle">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ __('All') }}</option>
                                </select>
                                <span class="small fw-bold text-muted">{{ __('entries') }}</span>
                            </div>
                            <div class="col-md-auto ms-md-auto d-flex align-items-center gap-2">
                                <div class="btn-group btn-group-sm shadow-sm rounded-1 overflow-visible border">
                                    <button @click="exportData('copy')" class="btn btn-light border-0 px-2 text-nowrap d-flex align-items-center"><i class="bi bi-clipboard me-2 small text-secondary"></i>{{ __('Copy') }}</button>
                                </div>

                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm">
                                    <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-search"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none" :placeholder="__('Search...')" style="min-width: 180px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0 custom-small-table">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-center py-2" style="width: 50px;">{{ __('SL.') }}</th>
                                        <th @click="toggleSort('name')" class="py-2 cursor-pointer">
                                            {{ __('Student Name') }}
                                            <i v-if="sortField === 'name'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th @click="toggleSort('email')" class="py-2 cursor-pointer">
                                            {{ __('Email Address') }}
                                            <i v-if="sortField === 'email'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th class="py-2">{{ __('Enrolled Courses & Batches') }}</th>
                                        <th @click="toggleSort('created_at')" class="py-2 cursor-pointer">
                                            {{ __('Joined At') }}
                                            <i v-if="sortField === 'created_at'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th class="text-center py-2">{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(user, index) in users.data" :key="user.id">
                                        <td class="text-center fw-bold text-muted">
                                            {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <Link :href="route('admin.students.show', user.id)" class="d-flex align-items-center gap-2 text-decoration-none transition-all hover-translate">
                                                <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center fw-bold border shadow-xs" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                                    {{ user.name.charAt(0) }}
                                                </div>
                                                <div class="fw-bold text-dark">{{ user.name }}</div>
                                            </Link>
                                        </td>
                                        <td class="text-muted small">{{ user.email }}</td>
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                <div v-for="enr in user.enrollments" :key="enr.id" class="d-flex align-items-center gap-1">
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle x-small">
                                                        {{ enr.batch?.course?.title?.bn || enr.batch?.course?.title?.en }}
                                                    </span>
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle x-small">
                                                        {{ enr.batch?.title?.bn || enr.batch?.title?.en }}
                                                    </span>
                                                </div>
                                                <div v-if="user.enrollments.length === 0" class="text-muted x-small italic">{{ __('No active enrollments') }}</div>
                                            </div>
                                        </td>
                                        <td class="text-muted small">
                                            {{ new Date(user.created_at).toLocaleDateString() }}
                                        </td>
                                        <td class="text-center">
                                            <span v-if="user.email_verified_at" class="badge bg-success-subtle text-success border border-success-subtle px-2">{{ __('Verified') }}</span>
                                            <span v-else class="badge bg-warning-subtle text-warning border border-warning-subtle px-2">{{ __('Unverified') }}</span>
                                        </td>
                                    </tr>
                                    <tr v-if="users.data.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No students found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="card-footer bg-white border-top py-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted fw-bold">
                            {{ __('Showing') }} {{ users.from || 0 }} {{ __('to') }} {{ users.to || 0 }} {{ __('of') }} {{ users.total }} {{ __('students') }}
                        </div>
                        <nav v-if="users.links.length > 3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="(link, k) in users.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
                                    <Link class="page-link rounded-1 mx-1 shadow-none border" :href="link.url" v-html="link.label" preserve-scroll />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cursor-pointer { cursor: pointer; }
.custom-small-table { font-size: 0.85rem; }
.badge { border-radius: 4px; }
.x-small { font-size: 0.7rem; }
.hover-translate { transition: transform 0.2s ease; }
.hover-translate:hover { transform: translateX(5px); }
</style>
