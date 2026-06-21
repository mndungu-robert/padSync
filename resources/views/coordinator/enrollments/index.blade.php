@extends('layouts.coordinator', ['active' => 'enrollments'])

@section('title', 'Enrollment Logs - ' . config('app.name'))
@section('page_title', 'Enrollment Logs')
@section('page_subtitle', 'Submit and review school enrollment updates.')

@section('content')
    <div class="bg-indigo-50 text-indigo-800 p-3 rounded-lg text-xs font-semibold border border-indigo-200">
        Notice: Only one enrollment entry is allowed per month for the same academic year.
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 text-emerald-800 p-3 rounded-lg text-xs font-semibold border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 text-rose-800 p-3 rounded-lg text-xs font-semibold border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 text-rose-800 p-3 rounded-lg text-xs border border-rose-200">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
        <h3 class="text-sm font-bold text-gray-800">Enrollment Submission</h3>
        @if($school)
            <p class="text-xs text-gray-500 mt-1">School: <span class="font-semibold text-gray-700">{{ $school->school_name }}</span></p>

            <form method="POST" action="{{ route('coordinator.enrollments.store') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Academic Year <span class="text-rose-500">*</span></label>
                    <input type="text" name="academic_year" value="{{ old('academic_year') }}" required placeholder="e.g. 2026/2027"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Month <span class="text-rose-500">*</span></label>
                    <select name="month" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                        <option value="">Select month</option>
                        @foreach(['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                            <option value="{{ $month }}" @selected(old('month') === $month)>{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Total girls enrolled <span class="text-rose-500">*</span></label>
                    <input type="number" name="girl_count" value="{{ old('girl_count') }}" min="0" required placeholder="e.g. 312"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-300">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 mb-1">Govt Pads Received <span class="text-rose-500">*</span></label>
                    <input type="number" name="government_pads_received" value="{{ old('government_pads_received') }}" min="0" required placeholder="e.g. 260"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 placeholder-gray-300">
                </div>
                <div class="md:col-span-3 pt-1">
                    <button type="submit" class="bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-bold px-4 py-2.5 rounded-md transition shadow-sm">
                        Submit Enrollment Log
                    </button>
                </div>
            </form>
        @else
            <p class="text-xs text-rose-600 mt-2">Your account is not linked to a school yet. Contact a Program Manager.</p>
        @endif
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden mt-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-bold text-sm text-gray-800">Enrollment History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                        <th class="px-6 py-3">Academic Year</th>
                        <th class="px-6 py-3">Month / Cycle</th>
                        <th class="px-6 py-3">Girls Count</th>
                        <th class="px-6 py-3">Govt Pads Received</th>
                        <th class="px-6 py-3">Submitted</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-600">
                    @forelse($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $enrollment->academic_year }}</td>
                            <td class="px-6 py-4">{{ $enrollment->month }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format($enrollment->girl_count) }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format($enrollment->government_pads_received ?? 0) }}</td>
                            <td class="px-6 py-4 text-xs text-gray-500">{{ $enrollment->created_at?->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400 font-medium">No enrollment records submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
