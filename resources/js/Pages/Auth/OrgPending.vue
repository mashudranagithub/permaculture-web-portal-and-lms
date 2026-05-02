<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: String,           // 'pending' | 'rejected' | 'suspended'
    rejectionReason: String,
    orgName: String,
});

const logoutForm = useForm({});
const logout = () => logoutForm.post(route('logout'));
</script>

<template>
    <GuestLayout>
        <Head title="Account Status" />

        <!-- PENDING -->
        <div v-if="status === 'pending'" class="text-center py-2">
            <div class="status-icon pending-icon mb-4">
                <i class="bx bx-time-five"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Awaiting Approval</h4>
            <p class="text-muted mb-1">
                Your organization <strong>{{ orgName }}</strong> has been registered and is currently under review.
            </p>
            <p class="text-muted small mb-4">
                You will receive an email notification once an LMS administrator approves your account.
                This process usually takes 1–2 business days.
            </p>
            <div class="alert alert-warning rounded-3 small text-start d-flex gap-2 align-items-start">
                <i class="bx bx-info-circle mt-1"></i>
                <div>
                    Make sure you have <strong>verified your email address</strong>. Check your inbox for the verification link we sent.
                </div>
            </div>
        </div>

        <!-- REJECTED -->
        <div v-else-if="status === 'rejected'" class="text-center py-2">
            <div class="status-icon rejected-icon mb-4">
                <i class="bx bx-x-circle"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Registration Not Approved</h4>
            <p class="text-muted mb-3">
                Unfortunately, your organization <strong>{{ orgName }}</strong> registration was not approved.
            </p>
            <div v-if="rejectionReason" class="alert alert-danger rounded-3 small text-start d-flex gap-2 align-items-start">
                <i class="bx bx-error mt-1"></i>
                <div><strong>Reason:</strong> {{ rejectionReason }}</div>
            </div>
            <p class="text-muted small mt-3">
                If you believe this is an error, please contact support with your details.
            </p>
        </div>

        <!-- SUSPENDED -->
        <div v-else-if="status === 'suspended'" class="text-center py-2">
            <div class="status-icon suspended-icon mb-4">
                <i class="bx bx-block"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Account Suspended</h4>
            <p class="text-muted mb-4">
                Your organization <strong>{{ orgName }}</strong> has been suspended.
                Please contact the LMS administrator for more information.
            </p>
        </div>

        <!-- UNKNOWN -->
        <div v-else class="text-center py-2">
            <div class="status-icon suspended-icon mb-4">
                <i class="bx bx-question-mark"></i>
            </div>
            <h4 class="fw-bold text-dark mb-2">Access Restricted</h4>
            <p class="text-muted">Your organization account is not currently active. Please contact support.</p>
        </div>

        <!-- Logout Button -->
        <div class="d-grid mt-4">
            <button @click="logout" class="btn btn-outline-secondary rounded-pill fw-bold">
                <i class="bx bx-log-out me-2"></i>Log Out
            </button>
        </div>
    </GuestLayout>
</template>

<style scoped>
.status-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2.5rem;
}
.pending-icon   { background: #fef3cd; color: #856404; }
.rejected-icon  { background: #f8d7da; color: #842029; }
.suspended-icon { background: #e2e3e5; color: #495057; }
</style>
