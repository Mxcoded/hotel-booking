@extends('layouts.admin')

@section('title', 'Manage Food Menu')

@section('content')

    {{-- Success / Error Alerts --}}
    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded" role="alert">
            <ul class="list-disc ml-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Upload Form --}}
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <h2 class="text-2xl font-semibold mb-1">Upload Food Menu PDF</h2>
        <p class="text-sm text-gray-500 mb-4">Uploading a new PDF will automatically replace the existing one. Max file size: 10MB.</p>

        <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center gap-4 flex-wrap">
                <input
                    type="file"
                    name="menu_pdf"
                    id="menu_pdf"
                    accept="application/pdf"
                    required
                    class="block text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                >
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-5 rounded-lg transition-colors">
                    <i class="fas fa-upload mr-2"></i> Upload PDF
                </button>
            </div>
        </form>
    </div>

    {{-- Current Menu --}}
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-semibold mb-4">Current Food Menu</h2>

        @if($menuSetting && $menuSetting->value)
            <div class="flex items-start gap-6 flex-wrap">
                {{-- PDF Preview --}}
                <div class="flex-1 min-w-0">
                    <iframe
                        src="{{ asset('storage/' . $menuSetting->value) }}"
                        class="w-full rounded-lg border border-gray-200 shadow-sm"
                        style="height: 600px;"
                        title="Food Menu Preview">
                    </iframe>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-3 shrink-0">
                    <a
                        href="{{ asset('storage/' . $menuSetting->value) }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-eye"></i> View Full PDF
                    </a>
                    <a
                        href="{{ asset('storage/' . $menuSetting->value) }}"
                        download
                        class="inline-flex items-center gap-2 bg-green-100 hover:bg-green-200 text-green-800 font-semibold py-2 px-4 rounded-lg transition-colors">
                        <i class="fas fa-download"></i> Download
                    </a>
                    <form action="{{ route('admin.menu.destroy') }}" method="POST" onsubmit="return confirm('Are you sure you want to remove the current food menu?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 bg-red-100 hover:bg-red-200 text-red-700 font-semibold py-2 px-4 rounded-lg transition-colors w-full">
                            <i class="fas fa-trash"></i> Remove PDF
                        </button>
                    </form>
                </div>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-file-pdf text-5xl text-gray-300 mb-4"></i>
                <p class="text-lg">No food menu has been uploaded yet.</p>
                <p class="text-sm mt-1">Use the form above to upload a PDF.</p>
            </div>
        @endif
    </div>

@endsection
