<x-filament::page>
	<form wire:submit.prevent="submit" class="space-y-4">
		{{ $this->form }}
		<x-filament::button type="submit">
			Зберегти
		</x-filament::button>
	</form>

	{{-- Модалки для Form Actions --}}
	<x-filament-actions::modals />
</x-filament::page>
