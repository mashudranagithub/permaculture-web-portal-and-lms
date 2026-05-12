<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CertificateController extends Controller
{
    protected CertificateService $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->certificateService = $certificateService;
    }

    /**
     * Display a listing of certificates for the organization (Admin).
     */
    public function index(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $query = Certificate::where('organization_id', $orgId)
            ->with(['user', 'course', 'batch']);

        if ($request->search) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            })->orWhere('certificate_no', 'like', "%{$request->search}%");
        }

        $sortField = $request->sort_field ?: 'issue_date';
        $sortDirection = $request->sort_direction ?: 'desc';
        $query->orderBy($sortField, $sortDirection);

        $perPage = $request->per_page === 'all' ? $query->count() : ($request->per_page ?: 10);
        $certificates = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Admin/Certificates/Index', [
            'certificates' => $certificates,
            'filters' => $request->only(['search', 'per_page', 'sort_field', 'sort_direction'])
        ]);
    }

    /**
     * Display certificates for the authenticated student.
     */
    public function studentCertificates()
    {
        $certificates = Certificate::where('user_id', Auth::id())
            ->with(['course', 'batch', 'organization'])
            ->latest()
            ->get();

        return Inertia::render('Student/Certificates', [
            'certificates' => $certificates
        ]);
    }

    /**
     * Download certificate PDF for the student.
     */
    public function download(Certificate $certificate)
    {
        // Security: Student can only download their own certificate
        if ($certificate->user_id !== Auth::id()) {
            abort(403);
        }

        return $this->certificateService->generatePdf($certificate);
    }

    /**
     * Public verification page.
     */
    public function verify(string $token)
    {
        $certificate = Certificate::where('verification_token', $token)
            ->with(['user', 'course', 'batch', 'organization'])
            ->firstOrFail();

        return Inertia::render('Public/CertificateVerify', [
            'certificate' => [
                'no' => $certificate->certificate_no,
                'student' => $certificate->metadata['student_name'] ?? $certificate->user->name,
                'course' => $certificate->metadata['course_title'] ?? $certificate->course->translate('title'),
                'organization' => $certificate->metadata['organization_name'] ?? $certificate->organization->name,
                'issue_date' => $certificate->issue_date->format('d M, Y'),
                'is_valid' => true
            ]
        ]);
    }
}
