<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Mpdf\Mpdf;

class ReportController extends Controller
{
    /**
     * Common PDF Generator helper
     */
    private function generatePdf($html, $filename)
    {
        $mpdf = new Mpdf([
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'format' => 'A4',
            'orientation' => 'P',
        ]);

        $mpdf->SetTitle($filename);
        $mpdf->SetAuthor('Permaculture');
        $mpdf->WriteHTML($html);
        return $mpdf->Output($filename . '.pdf', 'D'); // 'D' forces download
    }

    public function downloadUsers(Request $request)
    {
        $users = User::with('roles')->get();
        $title = "System Users Report";

        $html = view('admin.reports.pdf', [
            'title' => $title,
            'headers' => ['SL', 'Name', 'Email', 'Roles', 'Joined At'],
            'rows' => $users->map(function ($user, $index) {
                return [
                    $index + 1,
                    $user->name,
                    $user->email,
                    $user->roles->pluck('name')->implode(', '),
                    $user->created_at->format('d M, Y')
                ];
            })
        ])->render();

        return $this->generatePdf($html, 'users_report_' . date('Y-m-d'));
    }

    public function downloadRoles(Request $request)
    {
        $roles = Role::withCount('permissions')->get();
        $title = "Platform Roles Report";

        $html = view('admin.reports.pdf', [
            'title' => $title,
            'headers' => ['SL', 'Role Name', 'System Slug', 'Permissions Count'],
            'rows' => $roles->map(function ($role, $index) {
                return [
                    $index + 1,
                    $role->name,
                    $role->slug,
                    $role->permissions_count
                ];
            })
        ])->render();

        return $this->generatePdf($html, 'roles_report_' . date('Y-m-d'));
    }

    public function downloadPermissions(Request $request)
    {
        $permissions = Permission::withCount('roles')->get();
        $title = "Action Permissions Report";

        $html = view('admin.reports.pdf', [
            'title' => $title,
            'headers' => ['SL', 'Permission Name', 'System Slug', 'Roles Count'],
            'rows' => $permissions->map(function ($perm, $index) {
                return [
                    $index + 1,
                    $perm->name,
                    $perm->slug,
                    $perm->roles_count
                ];
            })
        ])->render();

        return $this->generatePdf($html, 'permissions_report_' . date('Y-m-d'));
    }

    public function downloadCourses(Request $request)
    {
        $courses = \App\Models\Course::all();
        $title = "Course Catalog Report";

        $html = view('admin.reports.pdf', [
            'title' => $title,
            'headers' => ['SL', 'Course Title', 'Price (BDT)', 'Level', 'Mode', 'Status'],
            'rows' => $courses->map(function ($course, $index) {
                return [
                    $index + 1,
                    $course->translate('title'),
                    number_format((float) $course->price, 2),
                    $course->level,
                    $course->delivery_mode,
                    $course->status
                ];
            })
        ])->render();

        return $this->generatePdf($html, 'courses_report_' . date('Y-m-d'));
    }

    public function downloadBatches(Request $request)
    {
        $batches = \App\Models\Batch::with('course')->get();
        $title = "Course Batches Report";

        $html = view('admin.reports.pdf', [
            'title' => $title,
            'headers' => ['SL', 'Course', 'Batch Title', 'Start Date', 'End Date', 'Seats', 'Status'],
            'rows' => $batches->map(function ($batch, $index) {
                return [
                    $index + 1,
                    $batch->course ? $batch->course->translate('title') : 'N/A',
                    $batch->translate('title'),
                    $batch->start_date ? $batch->start_date->format('d M, Y') : 'N/A',
                    $batch->end_date ? $batch->end_date->format('d M, Y') : 'N/A',
                    ($batch->enrollments()->count()) . ' / ' . ($batch->capacity ?: '∞'),
                    ucfirst($batch->status)
                ];
            })
        ])->render();

        return $this->generatePdf($html, 'batches_report_' . date('Y-m-d'));
    }
}
