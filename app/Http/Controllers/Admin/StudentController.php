<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\StudentIdGenerator;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private readonly StudentIdGenerator $generator)
    {
    }

    public function create()
    {
        return view('admin.students.bulk-create', [
            'created' => [],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'count' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $created = $this->generator->createStudents((int) $validated['count']);

        return view('admin.students.bulk-create', [
            'created' => $created,
        ]);
    }
}
