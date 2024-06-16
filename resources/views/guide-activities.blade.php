<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('My Activities') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-4 gap-5">
                        @forelse($activities as $activity)
                            <div class="space-y-3">
                                {{-- Activity Images --}}
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
                                <div class="flex space-x-2">
                                    {{-- Download PDF Button --}}
                                    <form method="GET" action="{{ route('guide-activity.export', $activity) }}">
                                        <input type="hidden" name="action" value="download">
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            Download PDF
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p>No activities</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
