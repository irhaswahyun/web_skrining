<x-guest-layout>
    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div class="text-start mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="text-start mb-4">
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring focus:border-blue-300" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>


        <!-- Submit Button -->
        <div>
            <button type="submit" class="btn btn-gradient w-full py-2 rounded-md bg-blue-600 text-white hover:bg-blue-700 transition">
               MASUK
            </button>
        </div>
    </form>
</x-guest-layout>
