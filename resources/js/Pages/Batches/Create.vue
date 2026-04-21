<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    courses: Array
});

const form = useForm({
    course_id: '',
    title: '',
    start_date: '',
    end_date: '',
    enrollment_deadline: '',
    capacity: 0,
    price: 0,
    discount_amount: 0,
    discount_type: 'fixed',
    status: 'upcoming',
    is_enrollment_open: true,
});

// Auto-fill price when course changes
watch(() => form.course_id, (newId) => {
    const course = props.courses.find(c => c.id == newId);
    if (course) {
        form.price = course.price;
        // Auto-generate a title snippet if empty
        if (!form.title) {
            form.title = `Batch ${new Date().getFullYear()}-${new Date().getMonth() + 1}`;
        }
    }
});

const submit = () => {
    form.post(route('batches.store'));
};
</script>

<template>
    <Head :title="__('Create Batch')" />

    <AuthenticatedLayout>
        <template #header>
            {{ __('Batch Management') }}
        </template>

        <div class="row">
            <div class="col-xl-8 mx-auto">
                <form @submit.prevent="submit">
                    <div class="card border-0 shadow-sm rounded-1 mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                                <i class="bi bi-plus-circle-fill text-success me-2"></i>{{ __('New Course Batch') }}
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- Course Selection -->
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Select Course') }}</label>
                                    <select v-model="form.course_id" class="form-select rounded-1" required>
                                        <option value="">{{ __('Choose a course...') }}</option>
                                        <option v-for="course in courses" :key="course.id" :value="course.id">
                                            {{ course.title }}
                                        </option>
                                    </select>
                                    <div v-if="form.errors.course_id" class="text-danger small mt-1">{{ form.errors.course_id }}</div>
                                </div>

                                <!-- Batch Title -->
                                <div class="col-md-6">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Batch Title / Name') }}</label>
                                    <input v-model="form.title" type="text" class="form-control rounded-1" placeholder="e.g. Winter Cohort 2026" required>
                                    <div v-if="form.errors.title" class="text-danger small mt-1">{{ form.errors.title }}</div>
                                </div>

                                <div class="col-12"><hr class="my-0 opacity-5"></div>

                                <!-- Dates -->
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Start Date') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-play"></i></span>
                                        <input v-model="form.start_date" type="date" class="form-control rounded-1 border-start-0 ps-0" required>
                                    </div>
                                    <div v-if="form.errors.start_date" class="text-danger small mt-1">{{ form.errors.start_date }}</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('End Date') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar-check"></i></span>
                                        <input v-model="form.end_date" type="date" class="form-control rounded-1 border-start-0 ps-0" required>
                                    </div>
                                    <div v-if="form.errors.end_date" class="text-danger small mt-1">{{ form.errors.end_date }}</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Enrollment Deadline') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-alarm"></i></span>
                                        <input v-model="form.enrollment_deadline" type="date" class="form-control rounded-1 border-start-0 ps-0">
                                    </div>
                                    <div v-if="form.errors.enrollment_deadline" class="text-danger small mt-1">{{ form.errors.enrollment_deadline }}</div>
                                </div>

                                <div class="col-12"><hr class="my-0 opacity-5"></div>

                                <!-- Seat capacity -->
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Total Seats') }}</label>
                                    <input v-model="form.capacity" type="number" class="form-control rounded-1" min="0">
                                    <div class="small text-muted mt-1">{{ __('Set 0 for unlimited') }}</div>
                                </div>

                                <!-- Pricing -->
                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Batch Price (BDT)') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">৳</span>
                                        <input v-model="form.price" type="number" class="form-control rounded-1" required>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-uppercase small fw-bold text-muted">{{ __('Status') }}</label>
                                    <select v-model="form.status" class="form-select rounded-1">
                                        <option value="upcoming">{{ __('Upcoming') }}</option>
                                        <option value="ongoing">{{ __('Ongoing') }}</option>
                                        <option value="completed">{{ __('Completed') }}</option>
                                        <option value="cancelled">{{ __('Cancelled') }}</option>
                                    </select>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="form-check form-switch bg-light p-3 rounded-1 border d-flex align-items-center justify-content-between px-4">
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ __('Open Enrollment') }}</h6>
                                            <p class="small text-muted mb-0">{{ __('Allow students to register for this batch immediately') }}</p>
                                        </div>
                                        <input v-model="form.is_enrollment_open" class="form-check-input ms-0" type="checkbox" style="width: 3rem; height: 1.5rem;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-top p-4 d-flex gap-3">
                            <button @click="submit" class="btn btn-success btn-lg px-5 rounded-1 fw-bold shadow-sm" :disabled="form.processing">
                                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                <i class="bi bi-cloud-upload me-2"></i>{{ __('Create Batch') }}
                            </button>
                            <Link :href="route('batches.index')" class="btn btn-outline-secondary btn-lg px-4 rounded-1 fw-bold">{{ __('Cancel') }}</Link>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-control:focus, .form-select:focus { border-color: #28a745; box-shadow: 0 0 0 0.15rem rgba(40, 167, 69, 0.1); }
.btn-lg { font-size: 1rem; padding: 0.75rem 1.5rem; }
hr { border-top: 1px solid #000; }
</style>
