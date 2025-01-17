<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $activity->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    @if($activity->hasMedia('images'))
                        @if($activity->getFirstMedia('images'))
                            <img src="{{ $activity->getFirstMediaUrl('images', 'small') }}" />
                        @endif

                    @else
                        <img src="{{ asset('images/no_image.png') }}" alt="No Image" width="120" height="120">

                    @endif
                    <div>${{ $activity->price }}</div>
                    <time>{{ $activity->start_time }}</time>
                    <div>Company: {{ $activity->company->name }}</div>
                    <p>{{ $activity->description }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
