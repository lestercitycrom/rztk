<x-filament::section>
    <h3 class="text-lg font-medium mb-2">Лог (последние 20 рядків)</h3>
    <x-filament::card class="max-h-64 overflow-auto p-2 text-xs bg-gray-900 text-green-400 font-mono">
		@foreach($this->getLines() as $line)
			<div>{{ \Illuminate\Support\Str::limit($line, 200) }}</div>
		@endforeach
    </x-filament::card>
</x-filament::section>
