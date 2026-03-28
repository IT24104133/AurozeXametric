@extends('layouts.app')

@section('content')
<div class="max-w-xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Complete Your Profile</h1>

  @if ($errors->any())
    <div class="p-3 bg-red-100 text-red-700 rounded mb-4">
      <ul class="list-disc list-inside">
        @foreach($errors->all() as $e) <li>{{ $e }}</li> @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
    @csrf

    <div>
      <label class="block text-sm font-medium">First Name</label>
      <input name="first_name" class="w-full border p-2 rounded" value="{{ old('first_name', $user->first_name) }}">
    </div>

    <div>
      <label class="block text-sm font-medium">Last Name</label>
      <input name="last_name" class="w-full border p-2 rounded" value="{{ old('last_name', $user->last_name) }}">
    </div>

    <div>
      <label class="block text-sm font-medium">Contact Number</label>
      <input name="contact_number" class="w-full border p-2 rounded" value="{{ old('contact_number', $user->contact_number) }}">
    </div>

    @if($user->role === 'student')
      <div>
        <label class="block text-sm font-medium">School Name</label>
        <input name="school_name" class="w-full border p-2 rounded" value="{{ old('school_name', $user->school_name) }}">
      </div>
    @endif

    @if($user->role === 'teacher')
      <div>
        <label class="block text-sm font-medium">NIC Number</label>
        <input name="nic_number" class="w-full border p-2 rounded" value="{{ old('nic_number', $user->nic_number) }}">
      </div>
    @endif

    <button class="bg-black text-white px-4 py-2 rounded">Save Profile</button>
  </form>
</div>
@endsection
