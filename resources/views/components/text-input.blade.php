@props(['type' => 'text', 'class' => ''])

<input {{ $attributes->merge(['type' => $type])->class(['block w-full rounded-lg border border-gray-300 bg-white py-3 px-4 text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-amber-500 focus:ring-amber-500 focus:ring-2 focus:ring-offset-0 focus:outline-none sm:text-sm transition-all duration-200', $class]) }} />