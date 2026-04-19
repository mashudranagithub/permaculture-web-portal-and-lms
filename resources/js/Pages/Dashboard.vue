<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head :title="__('Dashboard')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Admin Dashboard') }}
        </template>

        <div class="row g-4">
            <!-- Small Box for Students -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-primary shadow-sm text-white p-3 rounded-3 position-relative overflow-hidden">
                    <div class="inner">
                        <h3 class="fw-bold mb-0">1,250</h3>
                        <p class="mb-0">{{ __('Total Students') }}</p>
                    </div>
                    <div class="icon position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-people-fill fs-1"></i>
                    </div>
                    <a href="#" class="small-box-footer text-white-50 text-decoration-none d-block mt-3 small">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Small Box for Courses -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-success shadow-sm text-white p-3 rounded-3 position-relative overflow-hidden">
                    <div class="inner">
                        <h3 class="fw-bold mb-0">24</h3>
                        <p class="mb-0">{{ __('Active Courses') }}</p>
                    </div>
                    <div class="icon position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-journal-check fs-1"></i>
                    </div>
                    <a href="#" class="small-box-footer text-white-50 text-decoration-none d-block mt-3 small">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Small Box for Revenue -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning shadow-sm text-dark p-3 rounded-3 position-relative overflow-hidden">
                    <div class="inner">
                        <h3 class="fw-bold mb-0">৳ 45,200</h3>
                        <p class="mb-0">{{ __('Monthly Revenue') }}</p>
                    </div>
                    <div class="icon position-absolute top-0 end-0 p-3 opacity-25 text-dark">
                        <i class="bi bi-cash-stack fs-1"></i>
                    </div>
                    <a href="#" class="small-box-footer text-dark-50 text-decoration-none d-block mt-3 small">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>

            <!-- Small Box for Projects -->
            <div class="col-lg-3 col-6">
                <div class="small-box bg-danger shadow-sm text-white p-3 rounded-3 position-relative overflow-hidden">
                    <div class="inner">
                        <h3 class="fw-bold mb-0">85%</h3>
                        <p class="mb-0">{{ __('Project Growth') }}</p>
                    </div>
                    <div class="icon position-absolute top-0 end-0 p-3 opacity-25">
                        <i class="bi bi-graph-up-arrow fs-1"></i>
                    </div>
                    <a href="#" class="small-box-footer text-white-50 text-decoration-none d-block mt-3 small">
                        {{ __('More info') }} <i class="bi bi-arrow-right-circle-fill"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="row g-4 mt-2">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h3 class="card-title fw-bold text-success mb-0 d-flex align-items-center gap-2">
                            <i class="bi bi-person-lines-fill"></i> {{ __('Recent Enrollments') }}
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">{{ __('Student') }}</th>
                                        <th>{{ __('Location') }}</th>
                                        <th>{{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="ps-4">
                                        <td class="ps-4">
                                            <div class="fw-bold">Tanvir Ahmed</div>
                                            <div class="small text-muted">Dhaka, BD</div>
                                        </td>
                                        <td>Urban Permaculture</td>
                                        <td><span class="badge bg-success rounded-pill px-3">{{ __('Paid') }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">Selina Akter</div>
                                            <div class="small text-muted">Sylhet, BD</div>
                                        </td>
                                        <td>Ethics of Nature</td>
                                        <td><span class="badge bg-warning text-dark rounded-pill px-3">{{ __('Pending') }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-header border-bottom bg-transparent py-3">
                        <h3 class="card-title fw-bold text-dark mb-0"><i class="bi bi-pie-chart-fill me-2"></i>{{ __('Quick Actions') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <Link :href="route('courses.create')" class="btn btn-outline-success text-start d-flex align-items-center justify-content-between py-2">
                                <span><i class="bi bi-plus-circle me-2"></i>{{ __('Add New Course') }}</span>
                                <i class="bi bi-chevron-right small"></i>
                            </Link>
                            <button class="btn btn-outline-primary text-start d-flex align-items-center justify-content-between py-2 disabled opacity-50">
                                <span><i class="bi bi-envelope me-2"></i>{{ __('Send Announcement') }}</span>
                                <i class="bi bi-chevron-right small"></i>
                            </button>
                            <button class="btn btn-outline-dark text-start d-flex align-items-center justify-content-between py-2 disabled opacity-50">
                                <span><i class="bi bi-file-earmark-pdf me-2"></i>{{ __('Export Reports') }}</span>
                                <i class="bi bi-chevron-right small"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
