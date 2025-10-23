<div id="feedback-modal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-8 shadow-2xl max-w-md w-full relative">
            <button id="close-feedback-modal-btn" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-3xl">&times;</button>
            
            {{-- Include the new reusable form content --}}
            @include('partials._feedback_form_content')

        </div>
    </div>