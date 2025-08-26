@extends('layouts.admin')

@section('title', 'Manage Settings')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-semibold">Site Settings</h2>
            <a href="{{ route('admin.settings.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-plus mr-2"></i> Add New Setting
            </a>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Key</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Type</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Value</th>
                        <th class="py-3 px-4 uppercase font-semibold text-sm text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @forelse($settings as $setting)
                        <tr>
                            <td class="py-3 px-4 font-mono text-sm">{{ $setting->key }}</td>
                            <td class="py-3 px-4"><span class="bg-gray-200 text-gray-700 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $setting->type }}</span></td>
                            <td class="py-3 px-4">
                                @if($setting->type === 'image')
                                    <img src="{{ asset('storage/' . $setting->value) }}" alt="{{ $setting->key }}" class="h-10 w-auto rounded">
                                @elseif(in_array($setting->type, ['video', 'file']))
                                    <a href="{{ asset('storage/' . $setting->value) }}" target="_blank" class="text-blue-500 hover:underline">View File</a>
                                @else
                                    {{ Str::limit($setting->value, 50) }}
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('admin.settings.show', $setting) }}" class="text-green-500 hover:text-green-700 mr-4"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('admin.settings.edit', $setting) }}" class="text-blue-500 hover:text-blue-700 mr-4"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.settings.destroy', $setting) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">No settings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
         <div class="mt-6">
            {{ $settings->links() }}
        </div>
    </div>
@endsection
