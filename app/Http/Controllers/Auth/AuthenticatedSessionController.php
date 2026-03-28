public function store(Request $request)
{
    $request->validate([
        'login' => ['required', 'string'],
        'password' => ['required', 'string'],
    ]);

    $login = trim($request->input('login'));
    $password = $request->input('password');

    // Decide login field
    $field = str_starts_with(strtoupper($login), 'NGI') ? 'student_id' : 'email';

    $credentials = [
        $field => $login,
        'password' => $password,
    ];

    if (!\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        return back()->withErrors([
            'login' => 'Invalid login or password.',
        ])->onlyInput('login');
    }

    $request->session()->regenerate();

    // Redirect based on role
    $role = $request->user()->role;

    return match ($role) {
        'admin' => redirect()->to('/admin'),
        'teacher' => redirect()->to('/teacher'),
        default => redirect()->to('/student'),
    };
}
