<x-filament::page>

    @php
        // мини-подготовка, допустимая в Blade
        $p       = $this->record;
        $imgs    = $p->images ?: ($p->image_url ? [$p->image_url] : []);
        $brand   = $p->brandRelation?->name ?? $p->brand;
        $catText = $p->category
            ? 'c' . $p->category->rozetka_id . ' – ' . $p->category->title
            : null;
    @endphp

    <div
        x-data="{
            imgActive: 0,
            tab: 'details',
            imgs: @js($imgs),
            setImg(i){ this.imgActive = i },
            setTab(t){ this.tab = t },
        }"
        class="container mx-auto px-4 py-8 space-y-14"
    >
        {{-- ─── Галерея + информация ─── --}}
        <div class="grid lg:grid-cols-2 gap-10">
            {{-- Галерея --}}
            <div class="space-y-4">
                <div class="aspect-[4/3] rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center overflow-hidden">
                    <template x-if="imgs.length">
                        <img :src="imgs[imgActive]" class="w-full h-full object-contain" />
                    </template>
                    <span x-show="! imgs.length" class="text-sm text-gray-400">No image</span>
                </div>
                <div class="grid grid-cols-4 gap-3">
                    <template x-for="(img,i) in imgs" :key="i">
                        <button
                            @click="setImg(i)"
                            class="aspect-square overflow-hidden rounded-md border"
                            :class="imgActive===i ? 'border-primary' : 'border-gray-300 dark:border-gray-700'"
                        >
                            <img :src="img" class="w-full h-full object-cover" />
                        </button>
                    </template>
                </div>
            </div>

            {{-- Информация --}}
            <div class="space-y-6">
                {{-- Цена + старая цена --}}
                <div class="flex items-baseline gap-4">
                    <span class="text-3xl font-semibold">
                        {{ number_format($p->price,0,' ',' ') }} ₴
                    </span>
                    @if($p->old_price)
                        <span class="text-xl text-gray-500 line-through">
                            {{ number_format($p->old_price,0,' ',' ') }} ₴
                        </span>
                    @endif
                </div>

                {{-- Бренд и категория --}}
                <div class="flex flex-wrap items-center gap-3">
                    @if($brand)
                        <x-filament::badge color="gray">{{ $brand }}</x-filament::badge>
                    @endif
                    @if($catText)
                        <x-filament::badge color="primary">{{ $catText }}</x-filament::badge>
                    @endif
                </div>

                {{-- Полное описание --}}
                @if($p->short_description)
                    <div class="prose max-w-none">{!! $p->short_description !!}</div>
                @endif

                {{-- Ссылка на товар --}}
                <x-filament::button
                    tag="a"
                    href="{{ $p->url }}"
                    color="primary"
                    size="lg"
                    icon="heroicon-o-arrow-top-right-on-square"
                    target="_blank"
                >
                    Перейти на товар
                </x-filament::button>
            </div>
        </div>

        {{-- ─── Вкладки ─── --}}
        <div>
            <div class="border-b dark:border-gray-700 mb-6 flex gap-8">
                <button
                    @click="setTab('details')"
                    :class="tab==='details' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 dark:text-gray-400'"
                    class="py-2"
                >
                    Характеристики
                </button>
                <button
                    @click="setTab('meta')"
                    :class="tab==='meta' ? 'border-b-2 border-primary text-primary' : 'text-gray-600 dark:text-gray-400'"
                    class="py-2"
                >
                    Meta
                </button>
            </div>

            {{-- Характеристики (динамические + системные) --}}
            <div x-show="tab==='details'" class="space-y-6">
                <table class="text-sm w-full">
                    <tbody>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">URL</td>
                            <td class="py-2">
                                <a href="{{ $p->url }}" target="_blank" class="text-primary hover:underline">{{ $p->url }}</a>
                            </td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">Код товару</td>
                            <td class="py-2">{{ $p->rozetka_id }}</td>
                        </tr>
                        @if($p->category)
                            <tr class="border-b dark:border-gray-700">
                                <td class="py-2 pr-4 font-medium">Категорія</td>
                                <td class="py-2">{{ $catText }}</td>
                            </tr>
                        @endif
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">Створено</td>
                            <td class="py-2">{{ $p->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">Оновлено</td>
                            <td class="py-2">{{ $p->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>

                        {{-- Динамические атрибуты --}}
                        @foreach($p->attributes as $attr)
                            <tr class="border-b dark:border-gray-700">
                                <td class="py-2 pr-4 font-medium">{{ $attr->name }}</td>
                                <td class="py-2">{{ $attr->pivot->value }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Блок Meta --}}
            <div x-show="tab==='meta'" class="space-y-4">
                <table class="text-sm w-full">
                    <tbody>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">H1</td>
                            <td class="py-2">{{ $p->h1 }}</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">Meta Title</td>
                            <td class="py-2">{{ $p->meta_title }}</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">Meta Description</td>
                            <td class="py-2">{{ $p->meta_description }}</td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 pr-4 font-medium">Meta Keywords</td>
                            <td class="py-2">{{ $p->meta_keywords }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament::page>
