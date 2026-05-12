<?php

namespace App\Services;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\TopicProgress;
use Illuminate\Support\Str;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\View;

class CertificateService
{
    /**
     * Check if enrollment is eligible for certificate and issue it if so.
     */
    public function checkAndIssue(Enrollment $enrollment): ?Certificate
    {
        if ($enrollment->status !== 'active') return null;

        // Check if already issued
        $existing = Certificate::where('user_id', $enrollment->user_id)
            ->where('batch_id', $enrollment->batch_id)
            ->first();
        
        if ($existing) return $existing;

        // Calculate progress
        $topics = $enrollment->batch->course->topics()->where('is_published', true)->get();
        $totalTopics = $topics->count();
        
        if ($totalTopics === 0) return null;

        $completedTopics = TopicProgress::where('user_id', $enrollment->user_id)
            ->whereIn('topic_id', $topics->pluck('id'))
            ->where('status', 'completed')
            ->count();
        
        $progress = $totalTopics > 0 ? ($completedTopics / $totalTopics) * 100 : 0;

        if ($progress >= 100) {
            return $this->issue($enrollment);
        }

        return null;
    }

    /**
     * Issue a new certificate.
     */
    protected function issue(Enrollment $enrollment): Certificate
    {
        $issueDate = now();
        $certificateNo = $this->generateCertificateNo($enrollment);
        $token = Str::random(32);

        return Certificate::create([
            'organization_id' => $enrollment->organization_id,
            'user_id' => $enrollment->user_id,
            'course_id' => $enrollment->batch->course_id,
            'batch_id' => $enrollment->batch_id,
            'certificate_no' => $certificateNo,
            'issue_date' => $issueDate,
            'verification_token' => $token,
            'metadata' => [
                'student_name' => $enrollment->user->name,
                'course_title' => $enrollment->batch->course->translate('title'),
                'organization_name' => $enrollment->organization->name,
                'batch_title' => $enrollment->batch->translate('title'),
            ],
        ]);
    }

    /**
     * Generate PDF for a certificate.
     */
    public function generatePdf(Certificate $certificate)
    {
        $html = View::make('student.certificates.pdf', [
            'student' => $certificate->metadata['student_name'] ?? $certificate->user->name,
            'course' => $certificate->metadata['course_title'] ?? $certificate->course->translate('title'),
            'organization' => [
                'name' => $certificate->metadata['organization_name'] ?? $certificate->organization->name,
                'logo_path' => $certificate->organization->logo ? storage_path('app/public/' . $certificate->organization->logo) : null
            ],
            'favicon_path' => public_path('favicon.png'),
            'issue_date' => \Carbon\Carbon::parse($certificate->issue_date)->format('d M, Y'),
            'certificate_no' => $certificate->certificate_no,
            'verify_url' => route('certificates.verify', $certificate->verification_token)
        ])->render();

        $mpdf = new Mpdf([
            'format' => 'A4-L',
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
        ]);

        $mpdf->SetTitle('Certificate - ' . $certificate->certificate_no);
        $mpdf->WriteHTML($html);
        
        return $mpdf->Output('Certificate_' . $certificate->certificate_no . '.pdf', 'I'); 
    }

    /**
     * Get image as base64 string.
     */
    protected function getImageBase64(?string $path): ?string
    {
        if (!$path) return null;
        
        $fullPath = str_starts_with($path, '/') ? $path : storage_path('app/public/' . $path);
        
        if (!file_exists($fullPath)) return null;
        
        $type = pathinfo($fullPath, PATHINFO_EXTENSION);
        $data = file_get_contents($fullPath);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * Helper to generate certificate number.
     */
    protected function generateCertificateNo(Enrollment $enrollment): string
    {
        $prefix = strtoupper(substr($enrollment->organization->name, 0, 3));
        $year = date('Y');
        $count = Certificate::where('organization_id', $enrollment->organization_id)->count() + 1;
        
        return sprintf('%s-%s-%05d', $prefix, $year, $count);
    }
}
