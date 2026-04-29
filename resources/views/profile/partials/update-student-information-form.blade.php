<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Student Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Update your personal student profile details.') }}
        </p>

        @php
            $missingItems = $student->incompleteProfileItems();
        @endphp

        @if(count($missingItems) > 0)
            <p class="mt-2 text-sm text-orange-600">
                {{ __('Complete these next: ') . implode(', ', array_slice($missingItems, 0, 4)) . (count($missingItems) > 4 ? '...' : '') }}
            </p>
        @endif
    </header>

    <form method="post" action="{{ route('profile.student.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <x-input-label for="first_name" :value="__('First Name')" />
                <x-text-input id="first_name" name="first_name" type="text" class="mt-1 block w-full" :value="old('first_name', $student->first_name)" required />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <x-input-label for="last_name" :value="__('Last Name')" />
                <x-text-input id="last_name" name="last_name" type="text" class="mt-1 block w-full" :value="old('last_name', $student->last_name)" required />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>

            <div>
                <x-input-label for="student_id_display" :value="__('Student ID')" />
                <x-text-input id="student_id_display" type="text" class="mt-1 block w-full bg-gray-50" :value="$student->student_id" disabled />
            </div>

            <div>
                <x-input-label for="year_level_display" :value="__('Year Level')" />
                <x-text-input id="year_level_display" type="text" class="mt-1 block w-full bg-gray-50" :value="$student->year_level" disabled />
            </div>

            <div>
                <x-input-label for="section" :value="__('Section')" />
                <x-text-input id="section" name="section" type="text" class="mt-1 block w-full" :value="old('section', $student->section)" />
                <x-input-error class="mt-2" :messages="$errors->get('section')" />
            </div>

            <div>
                <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', optional($student->date_of_birth)->format('Y-m-d'))" />
                <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
            </div>

            <div>
                <x-input-label for="gender" :value="__('Gender')" />
                <select id="gender" name="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">
                    <option value="">{{ __('Select Gender') }}</option>
                    <option value="male" @selected(old('gender', $student->gender) === 'male')>{{ __('Male') }}</option>
                    <option value="female" @selected(old('gender', $student->gender) === 'female')>{{ __('Female') }}</option>
                    <option value="other" @selected(old('gender', $student->gender) === 'other')>{{ __('Other') }}</option>
                </select>
                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
            </div>
        </div>

        <div>
            <x-input-label for="photo" :value="__('Profile Photo')" />
            <input id="photo" name="photo" type="file" accept="image/*" class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-orange-500 focus:ring-orange-500" />
            <p class="mt-1 text-xs text-gray-500">JPG, PNG, or WEBP up to 2MB.</p>
            <x-input-error class="mt-2" :messages="$errors->get('photo')" />
        </div>

        <div>
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $student->phone)" />
            <x-input-error class="mt-2" :messages="$errors->get('phone')" />
        </div>

        <div>
            <x-input-label for="address" :value="__('Address')" />
            <textarea id="address" name="address" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-orange-500 focus:ring-orange-500">{{ old('address', $student->address) }}</textarea>
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Student Info') }}</x-primary-button>

            @if (session('status') === 'student-profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
