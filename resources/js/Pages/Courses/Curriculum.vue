<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link, router, usePage } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import draggable from 'vuedraggable';
import RichTextEditor from '@/Components/RichTextEditor.vue';
import Modal from '@/Components/Modal.vue';
import Select2 from '@/Components/Select2.vue';
import Swal from 'sweetalert2';
const props = defineProps({
    course: Object
});

const page = usePage();
const showTopicModal = ref(false);
const editingTopic = ref(null);
const activeTab = ref('bn');

// Sync list with props
const list = ref([]);
watch(() => props.course.topics, (newTopics) => {
    list.value = [...newTopics];
}, { immediate: true });

const form = useForm({
    id: null,
    course_id: props.course.id,
    title: { en: '', bn: '' },
    description: { en: '', bn: '' },
    content_body: { en: '', bn: '' },
    topic_type: 'content',
    video_url: '',
    estimated_duration: '',
    is_published: true,
    is_free_preview: false,
    pdf_file_en: null,
    pdf_file_bn: null,
    audio_file_en: null,
    audio_file_bn: null,
});

const openCreateModal = () => {
    editingTopic.value = null;
    form.reset();
    form.id = null;
    form.course_id = props.course.id;
    showTopicModal.value = true;
};

const openEditModal = (topic) => {
    editingTopic.value = topic;
    form.id = topic.id;
    form.course_id = topic.course_id;
    form.title = { ...topic.title };
    form.description = { ...topic.description };
    form.content_body = { ...topic.content_body };
    form.topic_type = topic.topic_type;
    form.video_url = topic.video_url || '';
    form.estimated_duration = topic.estimated_duration || '';
    form.is_published = !!topic.is_published;
    form.is_free_preview = !!topic.is_free_preview;
    form.pdf_file_en = null;
    form.pdf_file_bn = null;
    form.audio_file_en = null;
    form.audio_file_bn = null;
    showTopicModal.value = true;
};

const submit = () => {
    if (form.id) {
        form.transform((data) => ({
            ...data,
            _method: 'PUT',
        })).post(route('topics.update', form.id), {
            forceFormData: true,
            onSuccess: () => {
                showTopicModal.value = false;
                router.reload({ only: ['course'] });
            }
        });
    } else {
        form.post(route('topics.store'), {
            forceFormData: true,
            onSuccess: () => {
                showTopicModal.value = false;
                router.reload({ only: ['course'] });
            }
        });
    }
};

const deleteTopic = (id) => {
    Swal.fire({
        title: __('Delete Topic?'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: __('Yes, Delete'),
        cancelButtonText: __('Cancel')
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('topics.destroy', id), {
                onSuccess: () => router.reload({ only: ['course'] })
            });
        }
    });
};

const handleReorder = () => {
    const reordered = list.value.map((item, index) => ({
        id: item.id,
        order_index: index + 1
    }));
    router.post(route('topics.reorder'), { topics: reordered }, { preserveScroll: true });
};

const getTypeIcon = (type) => {
    switch(type) {
        case 'video': return 'bx bxl-youtube text-danger';
        case 'pdf': return 'bx bxs-file-pdf text-warning';
        case 'audio': return 'bx bx-volume-full text-info';
        case 'online_class': return 'bx bx-video text-primary';
        default: return 'bx bx-file-blank text-success';
    }
};

const totalDuration = computed(() => {
    return props.course.topics.reduce((acc, topic) => {
        const match = topic.estimated_duration?.match(/\d+/);
        return acc + (match ? parseInt(match[0]) : 0);
    }, 0);
});
</script>

<template>
    <Head :title="__('Curriculum Builder')" />

    <AuthenticatedLayout>
        <template #header>
            <div class="d-flex align-items-center justify-content-between w-100">
                <div class="d-flex align-items-center">
                    <Link :href="route('courses.index')" class="btn btn-sm btn-light border rounded-3 me-3 px-2 shadow-sm" :title="__('Back to Courses')">
                        <i class="bx bx-chevron-left fs-4"></i>
                    </Link>
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="mb-0 fw-bold text-dark lh-1">{{ course.title }}</h4>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 x-small fw-bold">
                                <i class="bx bx-layer me-1"></i>{{ course.topics.length }} {{ __('Topics') }}
                            </span>
                            <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1 x-small fw-bold">
                                <i class="bx bx-time-five me-1"></i>{{ totalDuration }} {{ __('Minutes') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="container py-5">
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                        <!-- THE FIXED CARD HEADER WITH PSEUDO-ELEMENT DISABLE -->
                        <div class="card-header bg-white border-bottom py-4 px-4 d-flex justify-content-between align-items-center curriculum-header">
                            <div class="header-info">
                                <h5 class="mb-1 fw-bold text-dark">{{ __('Course Curriculum') }}</h5>
                                <p class="mb-0 text-muted small">{{ __('Organize and manage your lesson topics below.') }}</p>
                            </div>
                            <div class="header-actions">
                                <button @click="openCreateModal" class="btn btn-success rounded-3 px-4 py-2 fw-bold d-flex align-items-center shadow-sm">
                                    <i class="bx bx-plus me-2 fs-5"></i>{{ __('Add New Topic') }}
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <!-- Empty State -->
                            <div v-if="course.topics.length === 0" class="p-5 text-center">
                                <div class="bg-light rounded-circle p-4 d-inline-block mb-4">
                                    <i class="bx bx-layer-plus fs-1 text-muted"></i>
                                </div>
                                <h5 class="fw-bold">{{ __('No topics added yet') }}</h5>
                                <p class="text-muted small">{{ __('Your curriculum is empty. Add your first topic to get started.') }}</p>
                                <button @click="openCreateModal" class="btn btn-outline-success rounded-3 mt-2 px-4">{{ __('Add First Topic') }}</button>
                            </div>

                            <!-- List -->
                            <div v-else class="p-4">
                                <draggable v-model="list" item-key="id" handle=".drag-handle" @end="handleReorder" ghost-class="ghost-item">
                                    <template #item="{ element, index }">
                                        <div class="topic-item mb-3 p-3 rounded-3 border bg-white shadow-sm-hover d-flex align-items-center transition-all">
                                            <div class="drag-handle p-2 text-muted cursor-move me-3 hover-text-success">
                                                <i class="bx bx-grid-vertical fs-3"></i>
                                            </div>
                                            
                                            <div class="topic-counter fw-bold text-muted opacity-50 me-3">{{ index + 1 }}</div>

                                            <div class="topic-icon-box bg-light rounded-3 me-3 d-flex align-items-center justify-content-center">
                                                <i :class="getTypeIcon(element.topic_type) + ' fs-4'"></i>
                                            </div>

                                            <div class="flex-grow-1 overflow-hidden me-3">
                                                <div class="d-flex align-items-center gap-2">
                                                    <h6 class="mb-0 fw-bold text-dark text-truncate">{{ element.title.bn || element.title.en }}</h6>
                                                    <span v-if="!element.is_published" class="badge bg-light text-muted border border-secondary-subtle x-small fw-normal rounded-1">{{ __('Draft') }}</span>
                                                    <span v-if="element.is_free_preview" class="text-success small" title="Free Preview"><i class="bx bx-lock-open-alt"></i></span>
                                                </div>
                                                <div class="x-small text-muted mt-1 fw-medium text-capitalize">
                                                    <i class="bx bx-time-five me-1"></i>{{ element.estimated_duration || '0m' }} &bull; {{ element.topic_type.replace('_', ' ') }}
                                                </div>
                                            </div>

                                            <div class="topic-item-actions d-flex gap-2">
                                                <button @click="openEditModal(element)" class="btn btn-sm btn-outline-primary border-0 bg-primary-subtle text-primary rounded-2 d-inline-flex align-items-center justify-content-center shadow-sm transition-all hover-scale" style="width: 32px; height: 32px;" :title="__('Edit Lesson')">
                                                    <i class="bx bx-edit-alt fs-6"></i>
                                                </button>
                                                <button @click="deleteTopic(element.id)" class="btn btn-sm btn-outline-danger border-0 bg-danger-subtle text-danger rounded-2 d-inline-flex align-items-center justify-content-center shadow-sm transition-all hover-scale" style="width: 32px; height: 32px;" :title="__('Delete Lesson')">
                                                    <i class="bx bx-trash fs-6"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vertically Centered Modal -->
        <Modal :show="showTopicModal" @close="showTopicModal = false" maxWidth="md">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg bg-white">
                <div class="modal-header border-bottom p-4">
                    <h5 class="modal-title fw-bold text-dark">{{ editingTopic ? __('Edit Topic') : __('Create New Topic') }}</h5>
                    <button type="button" @click="showTopicModal = false" class="btn-close shadow-none"></button>
                </div>
                <div class="modal-body p-4 scrollable-body">
                    <form @submit.prevent="submit">
                        <div class="row g-4">
                            <div class="col-md-7">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">{{ __('Topic Type') }}</label>
                                <Select2 v-model="form.topic_type" :placeholder="__('Select Type')" required>
                                    <option value="content">{{ __('Rich Content Lesson') }}</option>
                                    <option value="pdf">{{ __('PDF / Document Handout') }}</option>
                                    <option value="audio">{{ __('Audio Class / Podcast') }}</option>
                                    <option value="video">{{ __('YouTube Video Lesson') }}</option>
                                    <option value="online_class">{{ __('Live Class Session') }}</option>
                                    <option value="assignment">{{ __('Student Assignment') }}</option>
                                    <option value="quiz">{{ __('Topic Quiz') }}</option>
                                </Select2>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">{{ __('Duration') }}</label>
                                <input v-model="form.estimated_duration" type="text" class="form-control standard-height border-2" :placeholder="__('e.g. 45 mins')">
                            </div>

                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label x-small fw-bold text-muted text-uppercase mb-0">{{ __('Bilingual Lesson Content') }}</label>
                                    <div class="btn-group btn-group-sm p-1 bg-light rounded-3 border">
                                        <button @click="activeTab = 'bn'" type="button" class="btn px-4 fw-bold" :class="activeTab === 'bn' ? 'btn-success shadow-sm' : 'btn-light border-0 text-muted'">{{ __('Bangla') }}</button>
                                        <button @click="activeTab = 'en'" type="button" class="btn px-4 fw-bold" :class="activeTab === 'en' ? 'btn-success shadow-sm' : 'btn-light border-0 text-muted'">{{ __('English') }}</button>
                                    </div>
                                </div>

                                <div class="p-3 bg-light rounded-3 border">
                                    <div v-show="activeTab === 'bn'" class="animate__animated animate__fadeIn">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">{{ __('Lesson Title (Bangla)') }}</label>
                                            <input v-model="form.title.bn" type="text" class="form-control standard-height border-2" required>
                                        </div>
                                        <RichTextEditor v-model="form.content_body.bn" :height="250" />
                                    </div>
                                    <div v-show="activeTab === 'en'" class="animate__animated animate__fadeIn">
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">{{ __('Lesson Title (English)') }}</label>
                                            <input v-model="form.title.en" type="text" class="form-control standard-height border-2" required>
                                        </div>
                                        <RichTextEditor v-model="form.content_body.en" :height="250" />
                                    </div>
                                </div>
                            </div>

                            <!-- Media Fields -->
                            <div v-if="form.topic_type === 'video'" class="col-12">
                                <div class="p-3 bg-danger-subtle rounded-3 border border-danger border-opacity-10">
                                    <label class="form-label x-small fw-bold text-danger">{{ __('YouTube Link') }}</label>
                                    <input v-model="form.video_url" type="url" class="form-control border-2" placeholder="https://www.youtube.com/watch?v=...">
                                </div>
                            </div>

                            <div v-if="form.topic_type === 'pdf'" class="col-12 row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Bangla PDF') }}</label>
                                    <input type="file" @input="form.pdf_file_bn = $event.target.files[0]" class="form-control border-2 bg-white" accept=".pdf">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('English PDF') }}</label>
                                    <input type="file" @input="form.pdf_file_en = $event.target.files[0]" class="form-control border-2 bg-white" accept=".pdf">
                                </div>
                            </div>

                            <div v-if="form.topic_type === 'audio'" class="col-12 row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('Bangla Audio') }}</label>
                                    <input type="file" @input="form.audio_file_bn = $event.target.files[0]" class="form-control border-2 bg-white" accept="audio/*">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">{{ __('English Audio') }}</label>
                                    <input type="file" @input="form.audio_file_en = $event.target.files[0]" class="form-control border-2 bg-white" accept="audio/*">
                                </div>
                            </div>

                            <div class="col-12 d-flex gap-5 justify-content-center p-3 border rounded-3 bg-white shadow-sm mt-4">
                                <div class="form-check form-switch">
                                    <input v-model="form.is_published" class="form-check-input custom-switch" type="checkbox" id="pubCheck">
                                    <label class="form-check-label fw-bold small ms-2" for="pubCheck">{{ __('Published') }}</label>
                                </div>
                                <div class="form-check form-switch">
                                    <input v-model="form.is_free_preview" class="form-check-input custom-switch" type="checkbox" id="freeCheck">
                                    <label class="form-check-label fw-bold small ms-2" for="freeCheck">{{ __('Free Preview') }}</label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light p-4 d-flex justify-content-between">
                    <button @click="showTopicModal = false" class="btn btn-link text-muted fw-bold text-decoration-none px-0">{{ __('Cancel') }}</button>
                    <button @click="submit" class="btn btn-success btn-lg px-5 rounded-3 fw-bold shadow-sm" :disabled="form.processing">
                        {{ editingTopic ? __('Update Lesson') : __('Save Lesson') }}
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
.standard-height { height: 42px !important; }
.x-small { font-size: 0.72rem; }
.transition-all { transition: all 0.2s ease-in-out; }

/* FIX FOR THE CENTERED BUTTON ISSUE */
.curriculum-header::after {
    display: none !important;
}

.shadow-sm-hover:hover { box-shadow: 0 5px 15px rgba(0,0,0,0.05) !important; transform: translateY(-2px); border-color: #198754 !important; }

.topic-icon-box { width: 42px; height: 42px; flex-shrink: 0; }
.drag-handle { cursor: grab; }
.drag-handle:active { cursor: grabbing; }

.ghost-item { opacity: 0.2; background: #e8f5e9 !important; border: 2px dashed #198754 !important; }

.modal-content { min-width: 600px !important; }
.scrollable-body { max-height: 65vh; overflow-y: auto; }
.scrollable-body::-webkit-scrollbar { width: 5px; }
.scrollable-body::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }

.custom-switch { width: 2.8rem; height: 1.4rem; cursor: pointer; }
.hover-text-primary:hover { color: #0d6efd !important; }
.hover-text-danger:hover { color: #dc3545 !important; }
.hover-text-success:hover { color: #198754 !important; }
</style>
