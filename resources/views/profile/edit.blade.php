<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ auth()->user()->isAdmin() ? __('System Admin Account Settings') : __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(auth()->user()->isStudent() && $student)
                @php
                    $completion = $student->profileCompletionPercentage();
                    $missingItems = $student->incompleteProfileItems();
                @endphp

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section class="space-y-4">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Profile Completion Tracker') }}
                            </h2>

                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Keep your student profile complete so your records stay useful and up to date.') }}
                            </p>
                        </header>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                            <div class="md:col-span-2">
                                <div class="flex items-center justify-between text-sm font-medium text-gray-700">
                                    <span>{{ __('Completion Progress') }}</span>
                                    <span>{{ $completion }}%</span>
                                </div>

                                <div class="mt-2 h-3 w-full overflow-hidden rounded-full bg-gray-200">
                                    <div
                                        class="h-full rounded-full bg-orange-500 transition-all"
                                        style="width: {{ $completion }}%;"
                                    ></div>
                                </div>
                            </div>

                            <div class="md:justify-self-end text-center">
                                <div class="mx-auto overflow-hidden rounded-full border-2 border-orange-100 bg-gray-100" style="width:50px; height:50px; min-width:50px; min-height:50px; max-width:50px; max-height:50px;">
                                    @if($student->photo_path)
                                        <img src="{{ asset('storage/'.$student->photo_path) }}" alt="Student profile photo" style="width:50px; height:50px; min-width:50px; min-height:50px; max-width:50px; max-height:50px; object-fit:cover; display:block;">
                                    @else
                                        <div class="h-full w-full flex items-center justify-center text-[6px] font-semibold text-gray-500">
                                            No Photo
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-2 text-xs font-medium text-gray-600">{{ __('Profile Photo') }}</p>
                                <form method="POST" action="{{ route('profile.student.photo.update') }}" enctype="multipart/form-data" class="mt-3 space-y-2 text-left">
                                    @csrf
                                    @method('PATCH')
                                    <input
                                        id="quick_photo"
                                        name="photo"
                                        type="file"
                                        accept="image/*"
                                        class="block w-full text-xs !text-gray-800 file:mr-2 file:rounded-md file:border-0 file:bg-orange-100 file:px-3 file:py-2 file:text-xs file:font-semibold file:!text-orange-800 hover:file:bg-orange-200"
                                        required
                                    />
                                    <button type="submit" class="w-full rounded-md bg-orange-500 px-3 py-2 text-xs font-semibold !text-gray-900 hover:bg-orange-600 hover:!text-white">
                                        Upload Photo
                                    </button>
                                </form>
                                @if (session('status') === 'student-photo-updated')
                                    <p class="mt-2 text-xs font-medium text-green-700">{{ __('Photo updated.') }}</p>
                                @endif
                                @error('photo')
                                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        @if(count($missingItems) > 0)
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ __('Still missing') }}</p>
                                <p class="mt-1 text-sm text-gray-600">{{ implode(', ', array_slice($missingItems, 0, 5)) }}{{ count($missingItems) > 5 ? '...' : '' }}</p>
                            </div>
                        @else
                            <p class="text-sm font-medium text-green-700">{{ __('Your profile is fully complete.') }}</p>
                        @endif
                    </section>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            @if(auth()->user()->isStudent() && $student)
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-3xl">
                        @include('profile.partials.update-student-information-form')
                    </div>
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            @if(!auth()->user()->isStudent())
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            @endif

            @if(!auth()->user()->isAdmin() && !auth()->user()->isStudent())
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <section class="space-y-6">
                            <header>
                                <h2 class="text-lg font-medium text-gray-900">
                                    {{ __('Sign Out') }}
                                </h2>

                                <p class="mt-1 text-sm text-gray-600">
                                    {{ __('End your current session and return to the login page.') }}
                                </p>
                            </header>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-danger-button>
                                    {{ __('Sign Out') }}
                                </x-danger-button>
                            </form>
                        </section>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
