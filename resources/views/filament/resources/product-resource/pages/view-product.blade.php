@extends('filament::page')

@php $record = $this->record; @endphp

@section('content')
    <x-filament::section>
        <x-filament::grid :columns="2">
            <div>
                <h2 class="text-xl font-bold">{{ $record->title }}</h2>
                <p class="text-gray-500">{{ $record->category?->title }}</p>
                <p class="text-lg font-semibold">{{ number_format($record->price,0,' ','&nbsp;') }} ₴</p>
                <p>{!! $record->description !!}</p>
            </div>
            <div>
                <img src="{{ $record->image_url }}" class="rounded-xl max-w-full"/>
            </div>
        </x-filament::grid>
    </x-filament::section>

    <x-filament::section>
        <h3 class="text-lg font-medium mb-4">Характеристики</h3>
        <table class="w-full text-sm">
            @foreach($record->attributes as $attr)
                <tr class="border-b">
                    <td class="py-2 font-medium">{{ $attr->name }}</td>
                    <td class="py-2">{{ $attr->pivot->value }}</td>
                </tr>
            @endforeach
        </table>
    </x-filament::section>
@endsection
