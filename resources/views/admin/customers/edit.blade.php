@extends('admin.layouts.default')

@section('content')
    @include('admin.includes.breadcrumb')

    <div class="space-y-4">
        @include('admin.includes.message')

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-gray-800">Edit Customer</h2>
                    <p class="text-xs text-gray-500">Update customer profile details.</p>
                </div>
                <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-xs text-primary hover:underline">Back to profile</a>
            </div>

            <form method="POST" action="{{ route('admin.customers.update', $customer->id) }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Name</label>
                        <input type="text" name="name" value="{{ old('name', $customer->name) }}" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Email</label>
                        <input type="email" name="email" value="{{ old('email', $customer->email) }}" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Gender</label>
                        <div class="relative mt-1">
                            <select name="gender" class="w-full appearance-none border border-gray-300 rounded-lg px-3 py-2 text-xs bg-white pr-8">
                                <option value="">Not Specified</option>
                                <option value="1" @selected(old('gender', $customer->gender) == 1)>Male</option>
                                <option value="2" @selected(old('gender', $customer->gender) == 2)>Female</option>
                                <option value="3" @selected(old('gender', $customer->gender) == 3)>Other</option>
                            </select>
                            <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400">
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </div>
                        @error('gender')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600">Birthday</label>
                        <input type="date" name="birthday" value="{{ old('birthday', optional($customer->birthday)->format('Y-m-d')) }}" class="mt-1 w-full border border-gray-300 rounded-lg px-3 py-2 text-xs">
                        @error('birthday')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.customers.show', $customer->id) }}" class="text-xs text-gray-500 hover:text-gray-700">Cancel</a>
                    <button type="submit" class="bg-primary text-white text-xs font-semibold rounded-lg px-4 py-2 hover:bg-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
