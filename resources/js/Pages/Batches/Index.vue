<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    batches: Object,
    courses: Array,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 10);
const sortField = ref(props.filters.sort_field || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');
const courseId = ref(props.filters.course_id || '');

const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('batches.index'), {
        search: search.value,
        per_page: perPage.value,
        sort_field: sortField.value,
        sort_direction: sortDirection.value,
        course_id: courseId.value
    }, {
        preserveState: true,
        replace: true
    });
};

watch([search, perPage, courseId], debounce(updateTable, 300));

const toggleSort = (field) => {
    if (sortField.value === field) {
        sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortField.value = field;
        sortDirection.value = 'asc';
    }
    updateTable();
};

const exportData = async (type) => {
    const data = props.batches.data.map((batch, index) => ({
        SL: (props.batches.current_page - 1) * props.batches.per_page + index + 1,
        Course: batch.course_title,
        Title: batch.title,
        'Start Date': batch.start_date,
        'End Date': batch.end_date,
        Price: batch.price,
        Status: batch.status
    }));

    if (type === 'pdf') {
        window.location.href = route('admin.reports.batches.pdf');
        return;
    }

    if (type === 'csv' || type === 'excel') {
        const headers = Object.keys(data[0]).join(',');
        const rows = data.map(obj => Object.values(obj).map(v => `"${v}"`).join(',')).join('\n');
        const blob = new Blob([headers + '\n' + rows], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `batches_report_${new Date().toISOString().slice(0, 10)}.${type === 'csv' ? 'csv' : 'xlsx'}`;
        a.click();
    } else if (type === 'print') {
        const printWindow = window.open('', '_blank');
        const tableHtml = `
            <html>
            <head>
                <title>Batch Report - PRINT</title>
                <style>
                    body { font-family: sans-serif; padding: 30px; color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 10px; text-align: left; font-size: 11px; }
                    th { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; }
                    h2 { color: #198754; margin-top: 0; border-bottom: 2px solid #198754; padding-bottom: 10px; }
                    .footer { margin-top: 20px; font-size: 10px; color: #666; text-align: right; }
                </style>
            </head>
            <body>
                <h2>Course Batches Report</h2>
                <p><strong>Generated on:</strong> ${new Date().toLocaleString()}</p>
                <table>
                    <thead>
                        <tr>${Object.keys(data[0]).map(h => `<th>${h}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${data.map(row => `<tr>${Object.values(row).map(v => `<td>${v}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
                <div class="footer">Generated via Permaculture ERP Management System</div>
                <script>
                    window.onload = function() {
                        setTimeout(function() {
                            window.print();
                        }, 500);
                    }
                <\/script>
            </body>
            </html>
        `;
        printWindow.document.write(tableHtml);
        printWindow.document.close();
    } else if (type === 'copy') {
        const text = data.map(obj => Object.values(obj).join('\t')).join('\n');
        navigator.clipboard.writeText(text);
        alert('Data copied to clipboard!');
    }
};

const deleteBatch = (batch) => {
    if (confirm('Are you sure you want to delete this batch?')) {
        router.delete(route('batches.destroy', batch.id));
    }
};

const getStatusBadge = (status) => {
    switch(status) {
        case 'upcoming': return 'bg-info-subtle text-info border-info-subtle';
        case 'ongoing': return 'bg-success-subtle text-success border-success-subtle';
        case 'completed': return 'bg-secondary-subtle text-secondary border-secondary-subtle';
        case 'cancelled': return 'bg-danger-subtle text-danger border-danger-subtle';
        default: return 'bg-light text-dark';
    }
};
</script>

<template>
    <Head :title="__('Batch Management')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Batch Management') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-1">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-collection-fill text-success"></i>{{ __('Course Batches') }}
                            </h3>
                            <Link :href="route('batches.create')" class="btn btn-success btn-sm rounded-1 px-4 shadow-sm fw-bold">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('Create Batch') }}
                            </Link>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center">
                            <!-- Left Section: Pagination -->
                            <div class="col-md-auto d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted">{{ __('Show') }}</span>
                                <Select2 v-model="perPage" class="w-auto">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="all">{{ __('All') }}</option>
                                </Select2>
                                <span class="small fw-bold text-muted">{{ __('entries') }}</span>
                            </div>

                            <!-- Right Section: Filters + Exports + Search -->
                            <div class="col-md-auto ms-auto d-flex align-items-center">
                                <!-- Course Filter -->
                                <div class="me-2" style="min-width: 180px;">
                                    <Select2 v-model="courseId" :placeholder="__('All Courses')">
                                        <option value="">{{ __('All Courses') }}</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.title }}
                                        </option>
                                    </Select2>
                                </div>

                                <!-- Export Buttons Pack -->
                                <div class="btn-group btn-group-sm shadow-sm rounded-1 overflow-visible border me-2">
                                    <button @click="exportData('copy')" class="btn btn-light border-0 px-2 text-nowrap d-flex align-items-center" :title="__('Copy')">
                                        <i class="bi bi-clipboard me-2 small text-secondary"></i>{{ __('Copy') }}
                                    </button>
                                    <button @click="exportData('csv')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" :title="__('CSV')">
                                        <i class="bi bi-file-earmark-spreadsheet me-2 small text-info"></i>CSV
                                    </button>
                                    <button @click="exportData('excel')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" :title="__('Excel')">
                                        <i class="bi bi-file-earmark-excel me-2 small text-success"></i>Excel
                                    </button>
                                    <button @click="exportData('pdf')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" :title="__('PDF')">
                                        <i class="bi bi-file-earmark-pdf-fill me-2 small text-danger"></i>PDF
                                    </button>
                                    <button @click="exportData('print')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" :title="__('Print')">
                                        <i class="bi bi-printer me-2 small text-dark"></i>{{ __('Print') }}
                                    </button>
                                </div>

                                <!-- Search -->
                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm" style="width: 12vw; min-width: 140px;">
                                    <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-search"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none" :placeholder="__('Search...')" style="height: 31px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0 dataTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-center py-2" style="width: 50px;">{{ __('SL.') }}</th>
                                        <th @click="toggleSort('title')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'title' && sortDirection === 'asc', 'sorting_desc': sortField === 'title' && sortDirection === 'desc' }">
                                            {{ __('Batch Title') }}
                                        </th>
                                        <th class="py-2">{{ __('Course') }}</th>
                                        <th @click="toggleSort('start_date')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'start_date' && sortDirection === 'asc', 'sorting_desc': sortField === 'start_date' && sortDirection === 'desc' }">
                                            {{ __('Start Date') }}
                                        </th>
                                        <th @click="toggleSort('end_date')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'end_date' && sortDirection === 'asc', 'sorting_desc': sortField === 'end_date' && sortDirection === 'desc' }">
                                            {{ __('End Date') }}
                                        </th>
                                        <th class="py-2 text-center">{{ __('Seats') }}</th>
                                        <th class="py-2 text-center">{{ __('Status') }}</th>
                                        <th class="text-center py-2" style="width: 120px;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(batch, index) in batches.data" :key="batch.id">
                                        <td class="text-center fw-bold text-muted small">
                                            {{ (batches.current_page - 1) * batches.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ batch.title }}</div>
                                            <div class="small text-success fw-bold">৳ {{ Number(batch.price).toLocaleString() }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border fw-normal">{{ batch.course_title }}</span>
                                        </td>
                                        <td class="text-muted fw-semibold small">
                                            <i class="bi bi-calendar-event me-1"></i>{{ batch.start_date }}
                                        </td>
                                        <td class="text-muted fw-semibold small">
                                            <i class="bi bi-calendar-check-fill me-1"></i>{{ batch.end_date }}
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-bold">{{ batch.enrolled_count }} / {{ batch.capacity || '∞' }}</div>
                                            <div class="progress mt-1" style="height: 4px;">
                                                <div class="progress-bar bg-success" :style="{ width: (batch.capacity ? (batch.enrolled_count / batch.capacity * 100) : 0) + '%' }"></div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge border py-1 rounded-1 text-capitalize" :class="getStatusBadge(batch.status)">
                                                {{ __(batch.status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <Link :href="route('batches.edit', batch.id)" class="btn btn-outline-primary btn-xs rounded-1 p-1 px-2" :title="__('Edit')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </Link>
                                                <button @click="deleteBatch(batch)" class="btn btn-outline-danger btn-xs rounded-1 p-1 px-2" :title="__('Delete')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="batches.data.length === 0">
                                        <td colspan="8" class="text-center py-5 text-muted bg-light-subtle small font-italic">
                                            {{ __('No batches found.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top py-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted fw-bold">
                            {{ __('Showing') }} {{ batches.from || 0 }} {{ __('to') }} {{ batches.to || 0 }} {{ __('of') }} {{ batches.total }} {{ __('items') }}
                        </div>
                        <nav v-if="batches.links.length > 3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="(link, k) in batches.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
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
.btn-xs { padding: 0.125rem 0.25rem; font-size: 0.75rem; }
.cursor-pointer { cursor: pointer; }
th.sorting { position: relative; padding-right: 20px !important; }
th.sorting::after { position: absolute; right: 8px; content: "↑"; opacity: 0.2; }
th.sorting::before { position: absolute; right: 14px; content: "↓"; opacity: 0.2; }
th.sorting_asc::after { opacity: 1; color: #198754; }
th.sorting_desc::before { opacity: 1; color: #198754; }
.badge { font-size: 0.7rem; }
</style>
