<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    courses: Object,
    filters: Object
});

const search = ref(props.filters.search);
const perPage = ref(props.filters.per_page || 10);
const sortField = ref(props.filters.sort_field || 'created_at');
const sortDirection = ref(props.filters.sort_direction || 'desc');

// Zero-dependency debounce helper
const debounce = (fn, delay) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn(...args), delay);
    };
};

const updateTable = () => {
    router.get(route('courses.index'), {
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
    // Generate data for Copy/CSV/Excel/Print
    const exportSet = props.courses.data.map((c, index) => ({
        SL: (props.courses.current_page - 1) * props.courses.per_page + index + 1,
        Title: c.title,
        Price: c.price,
        Level: c.level,
        Mode: c.is_online ? 'Online' : 'In-Person',
        Status: c.is_active ? 'Active' : 'Inactive'
    }));

    if (type === 'pdf') {
        window.location.href = route('admin.reports.courses.pdf');
        return;
    }

    if (type === 'csv' || type === 'excel') {
        const headers = Object.keys(exportSet[0]).join(',');
        const rows = exportSet.map(obj => Object.values(obj).map(v => `"${v}"`).join(',')).join('\n');
        const blob = new Blob([headers + '\n' + rows], { type: 'text/csv;charset=utf-8;' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `courses_report_${new Date().toISOString().slice(0, 10)}.${type === 'csv' ? 'csv' : 'xlsx'}`;
        a.click();
    } else if (type === 'print') {
        const printWindow = window.open('', '_blank');
        const tableHtml = `
            <html>
            <head>
                <title>Course Catalog - PRINT</title>
                <style>
                    body { font-family: sans-serif; padding: 30px; color: #333; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #000; padding: 10px; text-align: left; font-size: 11px; }
                    th { background-color: #f0f0f0; font-weight: bold; text-transform: uppercase; }
                    h2 { color: #198754; margin: 0; border-bottom: 2px solid #198754; padding-bottom: 10px; }
                </style>
            </head>
            <body>
                <h2>Course Management Catalog</h2>
                <p><strong>Generated on:</strong> ${new Date().toLocaleString()}</p>
                <table>
                    <thead>
                        <tr>${Object.keys(exportSet[0]).map(h => `<th>${h}</th>`).join('')}</tr>
                    </thead>
                    <tbody>
                        ${exportSet.map(row => `<tr>${Object.values(row).map(v => `<td>${v}</td>`).join('')}</tr>`).join('')}
                    </tbody>
                </table>
            </body>
            </html>
        `;
        printWindow.document.write(tableHtml);
        printWindow.document.close();
        setTimeout(() => { printWindow.print(); }, 500);
    } else if (type === 'copy') {
        const text = exportSet.map(obj => Object.values(obj).join('\t')).join('\n');
        navigator.clipboard.writeText(text);
        alert('Data copied to clipboard!');
    }
};

const deleteCourse = (course) => {
    if (confirm('Are you sure you want to delete this course?')) {
        router.delete(route('courses.destroy', course.id));
    }
};
</script>

<template>
    <Head :title="__('Course Gallery')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Course Management') }}
        </template>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-1">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <h3 class="card-title fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-journal-bookmark-fill text-success"></i>{{ __('Course Catalog') }}
                            </h3>
                            <Link :href="route('courses.create')" class="btn btn-success btn-sm rounded-1 px-4 shadow-sm fw-bold">
                                <i class="bi bi-plus-circle me-2"></i>{{ __('Create Course') }}
                            </Link>
                        </div>
                        
                        <div class="mt-4 row g-3 align-items-center">
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
                            <div class="col-md-auto ms-md-auto d-flex align-items-center gap-2">
                                <!-- Export Buttons Pack (Standard Component Pattern) -->
                                <div class="btn-group btn-group-sm shadow-sm rounded-1 overflow-visible border">
                                    <button @click="exportData('copy')" class="btn btn-light border-0 px-2 text-nowrap d-flex align-items-center" title="Copy"><i class="bi bi-clipboard me-2 small text-secondary"></i>{{ __('Copy') }}</button>
                                    <button @click="exportData('csv')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="CSV"><i class="bi bi-file-earmark-spreadsheet me-2 small text-info"></i>CSV</button>
                                    <button @click="exportData('excel')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Excel"><i class="bi bi-file-earmark-excel me-2 small text-success"></i>Excel</button>
                                    <button @click="exportData('pdf')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="PDF"><i class="bi bi-file-earmark-pdf-fill me-2 small text-danger"></i>PDF</button>
                                    <button @click="exportData('print')" class="btn btn-light border-0 px-2 border-start text-nowrap d-flex align-items-center" title="Print"><i class="bi bi-printer me-2 small text-dark"></i>{{ __('Print') }}</button>
                                </div>

                                <div class="input-group input-group-sm rounded-1 overflow-hidden border shadow-sm">
                                    <span class="input-group-text bg-white border-0 text-muted px-2"><i class="bi bi-search"></i></span>
                                    <input v-model="search" type="text" class="form-control border-0 ps-0 shadow-none" :placeholder="__('Search courses...')" style="min-width: 200px; height: 31px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover align-middle mb-0 dataTable">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3 text-center py-2" style="width: 50px;">SL.</th>
                                        <th class="py-2" style="width: 60px;">{{ __('Thumb') }}</th>
                                        <th @click="toggleSort('title')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'title' && sortDirection === 'asc', 'sorting_desc': sortField === 'title' && sortDirection === 'desc' }">
                                            {{ __('Course Title') }}
                                        </th>
                                        <th @click="toggleSort('price')" class="sorting py-2 cursor-pointer" :class="{ 'sorting_asc': sortField === 'price' && sortDirection === 'asc', 'sorting_desc': sortField === 'price' && sortDirection === 'desc' }">
                                            {{ __('Price (BDT)') }}
                                        </th>
                                        <th class="py-2">{{ __('Level') }}</th>
                                        <th class="py-2">{{ __('Mode') }}</th>
                                        <th class="py-2 text-center">{{ __('Status') }}</th>
                                        <th class="text-center py-2" style="width: 120px;">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(course, index) in courses.data" :key="course.id">
                                        <td class="text-center fw-bold text-muted">
                                            {{ (courses.current_page - 1) * courses.per_page + index + 1 }}
                                        </td>
                                        <td>
                                            <div class="avatar-sm rounded-1 bg-light border d-flex align-items-center justify-content-center overflow-hidden" style="width: 40px; height: 40px;">
                                                <img v-if="course.image_url" :src="course.image_url" class="img-fluid" alt="thumb">
                                                <i v-else class="bi bi-image text-muted"></i>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ course.title }}</div>
                                            <div v-if="course.slug" class="small text-muted"><code class="slug-text">{{ course.slug }}</code></div>
                                        </td>
                                        <td class="fw-bold text-success">
                                            {{ Number(course.price).toLocaleString() }}
                                        </td>
                                        <td>
                                            <span class="badge" :class="{
                                                'bg-info-subtle text-info border border-info-subtle': course.level === 'Foundation',
                                                'bg-primary-subtle text-primary border border-primary-subtle': course.level === 'Intermediate',
                                                'bg-warning-subtle text-warning border border-warning-subtle': course.level === 'Advanced'
                                            }">{{ course.level }}</span>
                                        </td>
                                        <td>
                                            <span v-if="course.is_online" class="badge bg-primary-subtle text-primary fw-normal">
                                                <i class="bi bi-globe me-1"></i>{{ __('Online') }}
                                            </span>
                                            <span v-else class="badge bg-secondary-subtle text-secondary fw-normal">
                                                <i class="bi bi-geo-alt me-1"></i>{{ __('In-Person') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span v-if="course.is_active" class="badge bg-success-subtle text-success border border-success-subtle rounded-1 px-2 py-1">
                                                <i class="bi bi-check-circle-fill me-1"></i>{{ __('Active') }}
                                            </span>
                                            <span v-else class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-1 px-2 py-1">
                                                <i class="bi bi-x-circle-fill me-1"></i>{{ __('Draft') }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                <Link :href="route('courses.edit', course.id)" class="btn btn-outline-primary btn-xs rounded-1 p-1 px-2" :title="__('Edit')">
                                                    <i class="bi bi-pencil-square"></i>
                                                </Link>
                                                <button @click="deleteCourse(course)" class="btn btn-outline-danger btn-xs rounded-1 p-1 px-2" :title="__('Delete')">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="courses.data.length === 0">
                                        <td colspan="8" class="text-center py-5 text-muted bg-light-subtle">
                                            {{ __('No courses found matching your search.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- Pagination Footer -->
                    <div class="card-footer bg-white border-top py-2 d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="small text-muted fw-bold">
                            {{ __('Showing') }} {{ courses.from || 0 }} {{ __('to') }} {{ courses.to || 0 }} {{ __('of') }} {{ courses.total }} {{ __('items') }}
                        </div>
                        <nav v-if="courses.links.length > 3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="(link, k) in courses.links" :key="k" class="page-item" :class="{ 'active': link.active, 'disabled': !link.url }">
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

.badge { border-radius: 2px !important; }
.slug-text { color: #6c757d; font-size: 0.75rem; background: none; padding: 0; }

@media print {
    body * { visibility: hidden; }
    .table-responsive, .table-responsive * { visibility: visible; }
    .table-responsive { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
