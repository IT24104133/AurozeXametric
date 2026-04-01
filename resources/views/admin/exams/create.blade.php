@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6">
  <h1 class="text-2xl font-bold mb-4">Create Exam</h1>

  <form method="POST" action="{{ route('admin.exams.store') }}">
    @csrf

    <label class="block mb-2">Title</label>
    <input name="title" class="w-full border p-2 mb-3" required value="{{ old('title') }}">

    <label class="block mb-2">Teacher Name</label>
    <input name="teacher_name" class="w-full border p-2 mb-3" value="{{ old('teacher_name') }}">

    <label class="block mb-2">Description</label>
    <textarea name="description" class="w-full border p-2 mb-3">{{ old('description') }}</textarea>

    <label class="block mb-2">Instructions</label>
    <textarea name="instructions" class="w-full border p-2 mb-3">{{ old('instructions') }}</textarea>

    <label class="block mb-2">Duration (minutes)</label>
    <input type="number" name="duration_minutes" class="w-full border p-2 mb-3" value="{{ old('duration_minutes', 30) }}">

    <label class="block mb-2">Question Order (inside the selected set)</label>
    <select name="question_mode" class="w-full border p-2 mb-4">
      <option value="ordered" @selected(old('question_mode')==='ordered')>Ordered</option>
      <option value="shuffled" @selected(old('question_mode')==='shuffled')>Shuffled</option>
    </select>

    <hr class="my-4">

    {{-- ✅ Question Selection Settings --}}
    <h2 class="text-lg font-semibold mb-3">Question Selection Settings</h2>

    <label class="block mb-2">Students Must Answer (Question Limit)</label>
    <input
      type="number"
      name="question_limit"
      min="1"
      class="w-full border p-2 mb-3"
      value="{{ old('question_limit', 40) }}"
    >
    <p class="text-sm text-gray-600 mb-4">
      Example: You can add 60 questions, but students answer only 40.
    </p>

    <label class="block mb-2">Selection Mode</label>
    <select name="selection_mode" class="w-full border p-2 mb-4">
      <option value="all" @selected(old('selection_mode','all')==='all')>All Questions (show all)</option>
      <option value="first_n" @selected(old('selection_mode')==='first_n')>First N (by position/order)</option>
      <option value="random_n" @selected(old('selection_mode')==='random_n')>Random N (new random set per student attempt)</option>
      <option value="manual" @selected(old('selection_mode')==='manual')>Manual (teacher selects included questions)</option>
    </select>

    <hr class="my-4">

    {{-- ✅ NEW: Custom Submit/Timeout Popup Settings --}}
    <h2 class="text-lg font-semibold mb-3">Custom Popup Message (After Submit / Time Out)</h2>

    <label class="inline-flex items-center gap-2 mb-3">
      <input
        type="checkbox"
        name="custom_success_popup_enabled"
        value="1"
        @checked(old('custom_success_popup_enabled', false))
      >
      <span class="font-semibold">Enable custom popup message</span>
    </label>

    <label class="block mb-2">Popup Title (optional)</label>
    <input
      name="custom_success_popup_title"
      class="w-full border p-2 mb-3"
      value="{{ old('custom_success_popup_title') }}"
      placeholder="✅ Exam submitted successfully!"
    >

    <label class="block mb-2">Popup Message (optional)</label>
    <textarea
      name="custom_success_popup_message"
      class="w-full border p-2 mb-3"
      rows="4"
      placeholder="Example: Join the WhatsApp group. Next class is tomorrow 7PM."
    >{{ old('custom_success_popup_message') }}</textarea>

    <label class="block mb-2">Popup Link (optional)</label>
    <input
      name="custom_success_popup_link"
      class="w-full border p-2 mb-3"
      value="{{ old('custom_success_popup_link') }}"
      placeholder="https://chat.whatsapp.com/xxxxxx"
    >
    <p class="text-sm text-gray-600 mb-3">
      If you add a link, students can open it and (optionally) copy it.
    </p>

    <label class="inline-flex items-center gap-2 mb-6">
      <input
        type="checkbox"
        name="custom_success_popup_show_copy"
        value="1"
        @checked(old('custom_success_popup_show_copy', true))
      >
      <span class="font-semibold">Show Copy button (when link exists)</span>
    </label>

    <button class="px-5 py-2 bg-black text-white rounded">Create</button>
  </form>
</div>
@endsection
