<x-app-layout>
     <!-- Safety Threshold Alert Framework Notice -->
    @if($metrics['available_stock'] <= $metrics['reorder_threshold'])
        <div class="bg-amber-50 text-amber-900 border border-amber-200 rounded-xl p-4 text-xs font-semibold shadow-sm mb-6 flex items-center space-x-3">
            <span class="text-base">⚠️</span>
            <div>
                <span class="font-bold uppercase tracking-wider block text-[10px] text-amber-700">Central Stock Reorder Warning Level Active</span>
                Central storage stock balance ({{ number_format($metrics['available_stock']) }} packets) has dropped below your configured safety threshold of {{ number_format($metrics['reorder_threshold']) }} units. Update intake ledgers.
            </div>
        </div>
    @endif

    <!-- Logistical Overview Metrics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Metric 1 -->
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Warehouse Stock Pool</div>
            <div class="text-3xl font-black mt-2 {{ $metrics['available_stock'] <= $metrics['reorder_threshold'] ? 'text-amber-600' : 'text-slate-800' }}">
                {{ number_format($metrics['available_stock']) }}
            </div>
            <div class="text-[10px] text-gray-400 font-medium mt-1">packets unallocated</div>
        </div>

        <!-- Metric 2 -->
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Active Shortfall Reports</div>
            <div class="text-3xl font-black mt-2 {{ $metrics['active_shortfalls'] > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                {{ $metrics['active_shortfalls'] }}
            </div>
            <div class="text-[10px] text-gray-400 font-medium mt-1">schools awaiting dispatch</div>
        </div>

        <!-- Metric 3 -->
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Beneficiary Schools</div>
            <div class="text-3xl font-black text-slate-800 mt-2">{{ $metrics['schools_count'] }}</div>
            <div class="text-[10px] text-gray-400 font-medium mt-1">registered track sites</div>
        </div>

        <!-- Metric 4 -->
        <div class="bg-white border border-gray-200 p-6 rounded-xl shadow-sm">
            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Pending Coordinators</div>
            <div class="text-3xl font-black mt-2 {{ $metrics['pending_profiles'] > 0 ? 'text-blue-600' : 'text-slate-800' }}">
                {{ $metrics['pending_profiles'] }}
            </div>
            <div class="text-[10px] text-gray-400 font-medium mt-1">awaiting signup review</div>
        </div>
    </div>

    <!-- Layout Grid: Tasks Pending Matrix vs Shortcuts Console -->
    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6 items-start mt-6">
        
        <!-- Left Content: Top Critical Deficits Tracking Component (8 Columns) -->
        <div class="xl:col-span-8 bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-sm text-gray-800">Critical School Shortfalls</h3>
                @if(count($criticalNeeds) > 0)
                    <span class="text-[10px] font-extrabold uppercase bg-rose-50 text-rose-600 px-2.5 py-0.5 rounded-md border border-rose-200 animate-pulse">Action Required</span>
                @endif
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-400 text-[11px] font-bold uppercase tracking-wider border-b border-gray-100">
                            <th class="px-6 py-3">School Name Site</th>
                            <th class="px-6 py-3 text-center">Required Packets</th>
                            <th class="px-6 py-3 text-right">Shortfall Delta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-600">
                        @forelse($criticalNeeds as $need)
                        <tr class="hover:bg-gray-50/40 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 text-sm">{{ $need->school->school_name }}</div>
                                <div class="text-xs text-gray-400 font-medium mt-0.5">{{ $need->school->school_location }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-semibold">{{ number_format($need->required_pads) }}</td>
                            <td class="px-6 py-4 text-right font-black text-rose-600">
                                {{ number_format($need->shortfall) }} packets
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-10 text-center text-gray-400 font-medium">All school shortfall tickets are clear. No pending deficits to report.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Content: System Shortcuts Quick Access Command Deck Panel (4 Columns) -->
        <div class="xl:col-span-4 bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-4">
            <h3 class="font-bold text-sm text-gray-800 border-b border-gray-100 pb-3">Logistics Operations Deck</h3>
            <div class="grid grid-cols-1 gap-2.5">
                <a href="{{ route('manager.distributions.index') }}" class="block text-center bg-[#0F766E] hover:bg-[#0D635C] text-white text-xs font-bold py-3 rounded-lg transition shadow-sm">
                    ⚡ Open Dispatch Allocation Engine
                </a>
                <a href="{{ route('manager.inventory.index') }}" class="block text-center bg-white border border-gray-200 hover:bg-slate-50 text-gray-700 text-xs font-bold py-3 rounded-lg transition">
                    📦 Log Incoming T towels Batch
                </a>
                <a href="{{ route('manager.coordinators.index') }}" class="block text-center bg-white border border-gray-200 hover:bg-slate-50 text-gray-700 text-xs font-bold py-3 rounded-lg transition relative">
                    👥 Profile Signup Verifications Hub
                    @if($metrics['pending_profiles'] > 0)
                        <span class="absolute top-2.5 right-3 h-2 w-2 bg-blue-500 rounded-full"></span>
                    @endif
                </a>
            </div>
        </div>

    </div>
</x-app-layout>
