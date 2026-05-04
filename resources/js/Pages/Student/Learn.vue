<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    course: Object,
    initialTopicId: Number
});

const activeTopic = ref(props.course.topics.find(t => t.id === props.initialTopicId) || props.course.topics[0]);

const selectTopic = (topic) => {
    activeTopic.value = topic;
};

const nextTopic = computed(() => {
    const index = props.course.topics.findIndex(t => t.id === activeTopic.value.id);
    return props.course.topics[index + 1] || null;
});

const prevTopic = computed(() => {
    const index = props.course.topics.findIndex(t => t.id === activeTopic.value.id);
    return props.course.topics[index - 1] || null;
});

const completeLesson = () => {
    router.post(route('topics.complete', activeTopic.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // Update local state if needed, but props will update from backend
            if (nextTopic.value) {
                selectTopic(nextTopic.value);
            }
        }
    });
};

const selectedAnswers = ref({});

const selectOption = (qIndex, oIdx) => {
    selectedAnswers.value[qIndex] = oIdx;
};

const submitQuiz = () => {
    // Basic validation: ensure all questions are answered
    if (activeTopic.value.quiz_data && Object.keys(selectedAnswers.value).length < activeTopic.value.quiz_data.length) {
        alert('Please answer all questions before submitting.');
        return;
    }
    completeLesson();
};

const getTypeIcon = (type) => {
    switch(type) {
        case 'video': return 'bi bi-play-btn';
        case 'pdf': return 'bi bi-file-earmark-pdf';
        case 'audio': return 'bi bi-volume-up';
        case 'online_class': return 'bi bi-broadcast';
        case 'quiz': return 'bi bi-patch-question';
        case 'assignment': return 'bi bi-journal-text';
        default: return 'bi bi-file-text';
    }
};

const getPdfUrl = (topic) => {
    const file = topic.pdf_file_bn || topic.pdf_file_en;
    if (!file) return null;
    
    const url = file.startsWith('http') ? file : `${window.location.origin}/storage/${file}`;
    
    // If it's an external URL, wrap it in Google Docs Viewer to avoid X-Frame-Options issues
    if (url.startsWith('http') && !url.includes(window.location.hostname)) {
        return `https://docs.google.com/viewer?url=${encodeURIComponent(url)}&embedded=true`;
    }
    
    return url;
};

const getYoutubeId = (url) => {
    if (!url) return null;
    const regex = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i;
    const match = url.match(regex);
    return match ? match[1] : null;
};
</script>

<template>
    <Head :title="activeTopic.title.bn || activeTopic.title.en" />

    <AuthenticatedLayout>
        <div class="learn-container d-flex">
            <!-- Sidebar Navigation -->
            <div class="curriculum-sidebar bg-white border-end shadow-sm">
                <div class="p-4 border-bottom bg-light">
                    <h6 class="fw-bold mb-1 text-dark">{{ course.title }}</h6>
                    <div class="progress mt-2" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" :style="{ width: (course.topics.filter(t => t.is_completed).length / course.topics.length * 100) + '%' }"></div>
                    </div>
                    <small class="text-muted mt-1 d-block" style="font-size: 0.7rem;">
                        {{ Math.round(course.topics.filter(t => t.is_completed).length / course.topics.length * 100) }}% Complete ({{ course.topics.filter(t => t.is_completed).length }}/{{ course.topics.length }})
                    </small>
                </div>
                <div class="topic-list scrollbar-simple">
                    <div v-for="(topic, index) in course.topics" :key="topic.id" 
                        @click="selectTopic(topic)"
                        class="topic-nav-item p-3 border-bottom cursor-pointer transition-all"
                        :class="{ 'active bg-success-subtle border-start border-4 border-success': activeTopic.id === topic.id }">
                        <div class="d-flex align-items-center gap-3">
                            <div class="topic-status-icon">
                                <i v-if="topic.is_completed" class="bi bi-check-circle-fill text-success"></i>
                                <i v-else class="bi bi-circle text-muted opacity-50"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="small fw-bold text-dark text-truncate">{{ topic.title.bn || topic.title.en }}</div>
                                <div class="x-small text-muted mt-1 d-flex align-items-center gap-2">
                                    <i :class="getTypeIcon(topic.topic_type)"></i>
                                    <span>{{ topic.estimated_duration }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area flex-grow-1 bg-light">
                <div class="content-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold mb-0 text-dark">{{ activeTopic.title.bn || activeTopic.title.en }}</h4>
                    <div class="d-flex gap-2">
                        <button v-if="prevTopic" @click="selectTopic(prevTopic)" class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">
                            <i class="bi bi-arrow-left me-1"></i> {{ __('Previous') }}
                        </button>
                        <button v-if="nextTopic" @click="completeLesson" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm">
                            {{ __('Next Lesson') }} <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                        <button v-else-if="!activeTopic.is_completed" @click="completeLesson" class="btn btn-success btn-sm rounded-pill px-4 fw-bold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> {{ __('Mark as Complete') }}
                        </button>
                    </div>
                </div>

                <div class="p-5 overflow-auto" style="height: calc(100vh - 160px);">
                    <div class="max-w-4xl mx-auto">
                        <!-- Video Player -->
                        <div v-if="activeTopic.topic_type === 'video'" class="mb-5 shadow-lg rounded-4 overflow-hidden bg-black">
                            <div v-if="activeTopic.video_url && getYoutubeId(activeTopic.video_url)" class="ratio ratio-16x9">
                                <iframe :src="`https://www.youtube.com/embed/${getYoutubeId(activeTopic.video_url)}`" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                            </div>
                            <div v-if="activeTopic.video_url" class="p-3 bg-dark border-top d-flex justify-content-between align-items-center">
                                <span class="text-white-50 small"><i class="bi bi-info-circle me-1"></i> {{ __('Video not playing?') }}</span>
                                <a :href="activeTopic.video_url" target="_blank" class="btn btn-outline-light btn-xs rounded-pill px-3">
                                    <i class="bi bi-youtube me-1"></i> {{ __('Watch on YouTube') }}
                                </a>
                            </div>
                            <div v-else class="p-5 text-center text-white">
                                <i class="bi bi-play-circle fs-1 text-muted mb-3 d-block"></i>
                                <h5>{{ __('Video Lesson') }}</h5>
                                <p class="text-muted small">{{ __('Video URL:') }} {{ activeTopic.video_url || 'None provided' }}</p>
                                <div v-if="!activeTopic.video_url" class="alert alert-warning d-inline-block mt-3 small">
                                    {{ __('Technical Error: video_url attribute is missing from the data stream.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Rich Content -->
                        <div v-if="activeTopic.content_body" class="bg-white p-5 rounded-4 shadow-sm border mb-5 topic-content">
                            <div v-html="activeTopic.content_body.bn || activeTopic.content_body.en"></div>
                        </div>

                        <!-- PDF Viewer -->
                        <div v-if="activeTopic.topic_type === 'pdf'" class="mb-5 shadow-lg rounded-4 overflow-hidden bg-white border">
                            <div v-if="getPdfUrl(activeTopic)" class="pdf-container">
                                <iframe :src="getPdfUrl(activeTopic)" width="100%" height="800px" style="border: none;"></iframe>
                                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="bi bi-file-earmark-pdf me-1"></i> {{ __('Design Guide PDF') }}</span>
                                    <a :href="getPdfUrl(activeTopic)" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill px-4">
                                        <i class="bi bi-download me-1"></i> {{ __('Download for Offline Reading') }}
                                    </a>
                                </div>
                            </div>
                            <div v-else class="text-center p-5">
                                <i class="bi bi-file-earmark-pdf fs-1 text-danger mb-4 d-block"></i>
                                <h5 class="fw-bold">{{ __('Download Learning Material') }}</h5>
                                <p class="text-muted">{{ __('This lesson includes a PDF guide. Please download it below.') }}</p>
                                <a href="javascript:void(0)" class="btn btn-danger rounded-pill px-5 py-2 fw-bold mt-3 shadow-sm">
                                    <i class="bi bi-download me-2"></i> {{ __('Download PDF') }}
                                </a>
                            </div>
                        </div>

                        <!-- Quiz Placeholder -->
                        <div v-if="activeTopic.topic_type === 'quiz'" class="bg-white p-5 rounded-4 shadow-sm border mb-5">
                            <div class="text-center">
                                <div class="bg-warning-subtle d-inline-block p-4 rounded-circle mb-4">
                                    <i class="bi bi-patch-question fs-1 text-warning"></i>
                                </div>
                                <h4 class="fw-bold">{{ __('Lesson Quiz') }}</h4>
                                <p class="text-muted mb-5">{{ __('Test your knowledge on what you just learned.') }}</p>
                                
                                <div v-if="activeTopic.quiz_data" class="text-start">
                                    <div v-for="(q, index) in activeTopic.quiz_data" :key="index" class="p-4 border rounded-4 mb-4 bg-light shadow-sm">
                                        <h6 class="fw-bold mb-3 d-flex justify-content-between">
                                            <span>{{ index + 1 }}. {{ q.question.bn || q.question.en }}</span>
                                            <span class="badge bg-secondary-subtle text-secondary small">{{ q.points }} pts</span>
                                        </h6>
                                        <div class="d-grid gap-2">
                                            <div v-for="(opt, oIdx) in q.options" :key="oIdx" 
                                                @click="selectOption(index, oIdx)"
                                                class="form-check p-3 border rounded-3 bg-white transition-all cursor-pointer d-flex align-items-center"
                                                :class="{ 'border-success bg-success-subtle shadow-sm': selectedAnswers[index] === oIdx }">
                                                <input class="form-check-input ms-0 me-3 mt-0" type="radio" 
                                                    :name="'q_'+index" 
                                                    :id="'q_'+index+'_'+oIdx"
                                                    :checked="selectedAnswers[index] === oIdx">
                                                <label class="form-check-label fw-medium w-100 cursor-pointer" :for="'q_'+index+'_'+oIdx">
                                                    {{ opt.bn || opt.en }}
                                                </label>
                                            </div>
                                            <div v-if="q.type === 'true_false'" class="d-flex gap-3 mt-2">
                                                <button @click="selectOption(index, 1)" 
                                                    class="btn flex-grow-1 py-2 fw-bold transition-all"
                                                    :class="selectedAnswers[index] === 1 ? 'btn-success shadow-sm' : 'btn-outline-success'">
                                                    {{ __('True') }}
                                                </button>
                                                <button @click="selectOption(index, 0)" 
                                                    class="btn flex-grow-1 py-2 fw-bold transition-all"
                                                    :class="selectedAnswers[index] === 0 ? 'btn-danger shadow-sm' : 'btn-outline-danger'">
                                                    {{ __('False') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <button v-if="!activeTopic.is_completed" @click="submitQuiz" class="btn btn-success w-100 py-3 rounded-pill fw-bold shadow-lg mt-4 transition-all">
                                        <i class="bi bi-send me-2"></i> {{ __('Submit Quiz Answers') }}
                                    </button>
                                    <div v-else class="alert alert-success text-center rounded-pill py-3 fw-bold shadow-sm">
                                        <i class="bi bi-check-circle me-2"></i> {{ __('Quiz Completed Successfully') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Audio Placeholder -->
                        <div v-if="activeTopic.topic_type === 'audio'" class="p-5 bg-white rounded-4 border shadow-sm mb-5 text-center">
                            <i class="bi bi-volume-up fs-1 text-info mb-4 d-block"></i>
                            <h5 class="fw-bold mb-4">{{ __('Listen to Audio Lecture') }}</h5>
                            <div class="max-w-md mx-auto p-4 bg-light rounded-pill">
                                <audio controls class="w-100">
                                    <source src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3" type="audio/mpeg">
                                </audio>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.learn-container {
    height: calc(100vh - 65px);
    overflow: hidden;
}
.curriculum-sidebar {
    width: 320px;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.topic-list {
    flex-grow: 1;
    overflow-y: auto;
}
.topic-nav-item:hover:not(.active) {
    background-color: #f8f9fa;
}
.topic-nav-item.active {
    z-index: 1;
}
.content-area {
    height: 100%;
}
.topic-content :deep(p) { margin-bottom: 1.5rem; line-height: 1.8; color: #444; font-size: 1.1rem; }
.topic-content :deep(h1), .topic-content :deep(h2), .topic-content :deep(h3) { margin-top: 2rem; margin-bottom: 1rem; font-weight: 700; color: #111; }
.x-small { font-size: 0.7rem; }
.hover-bg-light:hover { background-color: #f8f9fa !important; border-color: #198754 !important; }
</style>
