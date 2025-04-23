{{-- resources/views/filament/resources/product-resource/pages/view-product.blade.php --}}
<x-filament::page>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('gallery', (count) => ({
                    idx: 0,
                    next() { this.idx = (this.idx + 1) % count },
                    prev() { this.idx = (this.idx - 1 + count) % count },
                }))
            });
        </script>
    @endpush

    {{-- Основной контент --}}
    <x-filament::section>
        <x-filament::grid :columns="6" class="gap-6">
            {{-- Галерея --}}
            <div class="col-span-6 lg:col-span-3" x-data="gallery({{ count($product->images ?? []) }})">
                <div class="relative w-full bg-gray-100 aspect-video rounded-xl overflow-hidden">
                    @foreach($product->images ?? [] as $i => $src)
                        <img
                            x-show="idx === {{ $i }}"
                            class="absolute inset-0 w-full h-full object-contain transition-all duration-300"
                            src="{{ $src }}"
                        />
                    @endforeach

                    {{-- Стрелки --}}
                    <button @click="prev" class="absolute left-0 top-1/2 -translate-y-1/2 p-2 bg-white/70 rounded-r">
                        <x-heroicon-o-chevron-left class="w-6 h-6"/>
                    </button>
                    <button @click="next" class="absolute right-0 top-1/2 -translate-y-1/2 p-2 bg-white/70 rounded-l">
                        <x-heroicon-o-chevron-right class="w-6 h-6"/>
                    </button>
                </div>

                {{-- Миниатюры --}}
                <div class="flex mt-2 space-x-2">
                    @foreach($product->images ?? [] as $i => $src)
                        <img
                            @click="idx = {{ $i }}"
                            :class="{'ring-2 ring-primary-600': idx === {{ $i }}}"
                            class="w-14 h-14 object-cover rounded cursor-pointer"
                            src="{{ $src }}"
                        />
                    @endforeach
                </div>
            </div>

            {{-- Информация --}}
            <div class="col-span-6 lg:col-span-3">
                <h1 class="text-2xl font-bold mb-1">{{ $product->h1 ?? $product->title }}</h1>
                <p class="text-gray-500 mb-4">{{ $product->category?->title }}</p>

                <p class="text-3xl font-semibold mb-1">
                    {{ number_format($product->price, 0, ' ', ' ') }} ₴
                </p>
                @if($product->old_price)
                    <p class="text-sm text-gray-400 line-through mb-4">
                        {{ number_format($product->old_price, 0, ' ', ' ') }} ₴
                    </p>
                @endif

                @if($product->short_description)
                    <p class="text-sm mb-6">{{ $product->short_description }}</p>
                @endif

                <x-filament::button
                    tag="a"
                    href="{{ $product->url }}"
                    target="_blank"
                    icon="heroicon-o-shopping-cart"
                >
                    Перейти на ROZETKA
                </x-filament::button>
            </div>
        </x-filament::grid>
    </x-filament::section>

    {{-- Характеристики --}}
    <x-filament::section class="mt-6">
        <h2 class="text-lg font-medium mb-4">Характеристики</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
            @foreach($product->attributes as $attr)
                <div class="flex justify-between border-b py-2">
                    <span class="text-gray-600">{{ $attr->name }}</span>
                    <span>{{ $attr->pivot->value }}</span>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- Полное описание --}}
    @if($product->description)
        <x-filament::section class="mt-6">
            <h2 class="text-lg font-medium mb-4">Опис</h2>
            <div class="prose max-w-none">{!! $product->description !!}</div>
        </x-filament::section>
    @endif

</x-filament::page>
