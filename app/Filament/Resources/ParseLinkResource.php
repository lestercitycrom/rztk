<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParseLinkResource\Pages;
use App\Filament\Resources\ParseLinkResource\RelationManagers\ErrorsRelationManager;
use App\Models\ParseLink;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\{BadgeColumn, TextColumn, ToggleColumn};
use App\Tables\Columns\{CreatedAt, ProductTitle};

class ParseLinkResource extends Resource
{
	protected static ?string $model = ParseLink::class;
	protected static ?string $navigationIcon = 'heroicon-o-queue-list';



	// navigation title
	protected static ?string $navigationLabel = "Парсера";
	// header
	protected static ?string $modelLabel = "Парсера";
	protected static ?string $pluralModelLabel = "Парсера";


	public static function form(Forms\Form $form): Forms\Form
	{
		return $form
			->schema([
				TextInput::make('url')
					->label('URL')
					->required()
					->columnSpanFull(),
				Toggle::make('is_active')
					->label('Активний')
					->default(true),
			]);
	}

	public static function table(Tables\Table $table): Tables\Table
	{
		return $table
			->columns([
					ProductTitle::make(),
					
					TextColumn::make('total_pages')->label('Сторінок')->sortable(),
					
					TextColumn::make('last_parsed_page')->label('Остання')->sortable(),
					
					BadgeColumn::make('status')->label('Статус')
						->colors([
							'success' => 'success',
							'danger'  => 'danger',
						])
						->sortable(),
						
					ToggleColumn::make('is_active')->label('Активний'),
					
					CreatedAt::make()	->label('Останній запуск'),
			])
			->headerActions([
				Tables\Actions\CreateAction::make(),
			])
			->actions([
				Tables\Actions\EditAction::make()->modalHeading('Змінити посилання на парсинг'),
				Tables\Actions\DeleteAction::make()->modalHeading('Видалити посилання на парсинг'),
			])
			->bulkActions([
				Tables\Actions\DeleteBulkAction::make(),
			]);
	}

	public static function getPages(): array
	{
		return [
			'index'  => Pages\ListParseLinks::route('/'),
			'create' => Pages\CreateParseLink::route('/create'),
			'edit'   => Pages\EditParseLink::route('/{record}/edit'),
		];
	}

	public static function getRelations(): array
	{
		return [
			ErrorsRelationManager::class,
		];
	}
}
