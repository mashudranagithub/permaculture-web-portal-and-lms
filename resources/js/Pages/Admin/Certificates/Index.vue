<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    certificates: Object,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 10);
const sortField = ref(props.filters.sort_field || 'issue_date');
const sortDirection = ref(props.filters.sort_direction || 'desc');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('admin.certificates.index'), {
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
</script>

<template>
    <Head :title="__('Manage Certificates')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Issued Certificates') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-patch-check-fill text-success"></i>{{ __('Organization Certificates') }}
                            </h3>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center">
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted">{{ __('Show') }}</span>
                                <select v-model="perPage" class="form-select form-select-sm w-auto shadow-none border-success-subtle">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="all">{{ __('All') }}</option>
                                </select>
                            </div>
                            <div class="col-md-auto ms-md-auto">
                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm">
                                    <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-search"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none" :placeholder="__('Search...')" style="min-width: 200px;">
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
                                        <th class="py-2">{{ __('Certificate No') }}</th>
                                        <th class="py-2">{{ __('Student') }}</th>
                                        <th class="py-2">{{ __('Course') }}</th>
                                        <th @click="toggleSort('issue_date')" class="py-2 cursor-pointer">
                                            {{ __('Issued Date') }}
                                            <i v-if="sortField === 'issue_date'" :class="sortDirection === 'asc' ? 'bi bi-sort-up' : 'bi bi-sort-down'" class="ms-1"></i>
                                        </th>
                                        <th class="text-center py-2">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(cert, index) in certificates.data" :key="cert.id">
                                        <td class="text-center fw-bold text-muted">
                                            {{ (certificates.current_page - 1) * certificates.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ cert.certificate_no }}</span>
                                        </td>
                                        <td>{{ cert.user?.name }}</td>
                                        <td>{{ cert.course?.title?.bn || cert.course?.title?.en }}</td>
                                        <td class="text-muted small">
                                            {{ new Date(cert.issue_date).toLocaleDateString() }}
                                        </td>
                                        <td class="text-center">
                                            <a :href="route('certificates.download', cert.id)" target="_blank" class="btn btn-outline-success btn-xs px-2 rounded-1">
                                                <i class="bi bi-download me-1"></i> {{ __('PDF') }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr v-if="certificates.data.length === 0">
                                        <td colspan="6" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No certificates found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-2">
                        <!-- Pagination same as others -->
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.cursor-pointer { cursor: pointer; }
.custom-small-table { font-size: 0.85rem; }
.btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }
</style>
