<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PadSync - Sanitary Pad Distribution System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900 font-sans">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-indigo-600">PadSync</h1>
        <div>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-semibold mx-4">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600 font-semibold mx-4">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 font-semibold">Coordinator Registration</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Main Content Summary Banner -->
    <header class="text-center py-16 bg-indigo-50 px-4">
        <h2 class="text-4xl font-extrabold text-indigo-900">Keeping Girls in School, One Pack at a Time</h2>
        <p class="mt-4 text-lg text-indigo-700 max-w-2xl mx-auto">PadSync automates sanitary pad distribution tracking across participating schools.</p>
    </header>

    {{-- <!-- Public Donor Pledge Form Section -->
    <section class="max-w-md mx-auto my-12 bg-white p-8 rounded-lg shadow-md">
        <h3 class="text-2xl font-bold text-gray-800 text-center mb-6">Make a Donation Pledge</h3>
        
        @if(session('success'))
            <div class="bg-green-100 text-green-800 p-4 rounded-md mb-4 font-semibold text-center">{{ session('success') }}</div>
        @endif

        <form action="{{ route('public.pledge') }}" method="POST">
            @csrf
            <!-- Email (Since they aren't logged in, we collect this to track them) -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Your Email Address</label>
                <input type="email" name="email" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <!-- Quantity Pledged -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Quantity of Packs to Pledge</label>
                <input type="number" name="quantity_pledged" min="1" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            </div>

            <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded-md hover:bg-green-700 transition">Submit Donation Pledge</button>
        </form>
    </section>
</body>
</html> --}}
