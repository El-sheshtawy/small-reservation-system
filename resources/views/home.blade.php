<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-4 gap-x-5 gap-y-8">
                        @forelse($activities as $activity)
                            <div>
                                @if($activity->hasMedia('images'))
                                    @if($activity->getFirstMedia('images'))
                                        <a href="{{ route('activity.show', $activity) }}">
                                        <img src="{{ $activity->getFirstMediaUrl('images', 'small') }}" />
                                        </a>
                                    @endif

                                @else
                                    <a href="{{ route('activity.show', $activity) }}">
                                    <img src="{{ asset('images/no_image.png') }}" alt="No Image" width="120" height="120">
                                    </a>
                                @endif
                                <h2>
                                    <a href="{{ route('activity.show', $activity) }}" class="text-lg font-semibold">{{ $activity->name }}</a>
                                </h2>
                                <time>{{ $activity->start_time }}</time>
                            </div>
                        @empty
                            <p>No activities</p>
                        @endforelse
                    </div>

                    <div class="mt-6">{{ $activities->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
