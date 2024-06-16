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

                    {{-- Success Message --}}
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 p-4 font-semibold text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Registration Status --}}
                    @if(auth()->user()?->activities->contains($activity))
                        @unless(session('success'))
                            <div class="mb-6 bg-indigo-100 p-4 font-semibold text-indigo-700">
                                You have already registered.
                            </div>
                        @endunless
                    @else
                        <form action="{{ route('activities.register', $activity) }}" method="POST">
                            @csrf
                            <x-secondary-button type="submit">
                                Register to Activity
                            </x-secondary-button>
                        </form>
                    @endif

                    {{-- Activity Details --}}
                    <div>${{ $activity->price }}</div>
                    <time>{{ $activity->start_time }}</time>
                    <div>Company: {{ $activity->company->name }}</div>
                    <p>{{ $activity->description }}</p>

                    {{-- Activity Images --}}
                    @if($activity->hasMedia('images'))

                        @if($activity->getFirstMedia('images'))
                            <a>
                            <img src="{{ $activity->getFirstMediaUrl('images', 'small') }}" >
                            </a>
                        @endif

                            @else
                        <a>
                        <img src="{{ asset('images/no_image.png') }}" alt="No Image" width="120" height="120">
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
