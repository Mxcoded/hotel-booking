@props(['messages' => [], 'class' => ''])

@php
    $messageArray = is_array($messages) ? $messages : ($messages instanceof \Illuminate\Support\MessageBag ? $messages->all() : []);
@endphp

@if (!empty($messageArray))
    <div class="{{ $class }}">
        @foreach ($messageArray as $message)
            <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @endforeach
    </div>
@endif