@extends('layouts.admin')

@section('title', 'Change Password')

@section('content')
<div class="max-w-lg mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Change Password</h1>

    @if ($errors->any())
        <div class="mb-4 text-red-600">
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <label class="block mb-2">Current Password</label>
        <input type="password" name="current_password" class="w-full border p-2 rounded mb-4" required>

        <label class="block mb-2">New Password</label>
        <input type="password" name="password" class="w-full border p-2 rounded mb-4" required>

        <label class="block mb-2">Confirm New Password</label>
        <input type="password" name="password_confirmation" class="w-full border p-2 rounded mb-4" required>

        <button class="w-full bg-black text-white py-2 rounded">Update Password</button>
    </form>
</div>
@endsection
