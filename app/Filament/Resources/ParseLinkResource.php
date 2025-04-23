<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ParseLinkResource\Pages;
use App\Models\ParseLink;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

use App\Filament\Resources\ParseLinkResource\RelationManagers\ErrorsRelationManager;

class ParseLinkResource extends Resource
{
    protected static ?string $model = ParseLink::class;
    protected static ?string $navigationGroup = 'Парсер Rozetka';
    protected static ?string $navigationLabel = 'Посилання';
    protected static ?string $pluralLabel = 'Посилання';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('url')
                    ->label('URL')
                    ->required()
                    ->columnSpanFull(),
                Select::make('type')
                    ->label('Тип')
                    ->options([
                        'category' => 'Категорія',
                        'vendor'   => 'Продавець',
                    ])
                    ->required(),
                Toggle::make('is_active')
                    ->label('Активний')
                    ->default(true),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->label('Назва')->searchable(),
                TextColumn::make('total_pages')->label('Сторінок'),
                TextColumn::make('last_parsed_page')->label('Остання'),
                BadgeColumn::make('status')
                    ->label('Статус')
                    ->colors(['success'=>'success','danger'=>'danger']),
                ToggleColumn::make('is_active')->label('Активний'),
                TextColumn::make('last_parsed_at')
                    ->label('Останній запуск')
                    ->dateTime(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
				Tables\Actions\DeleteAction::make(), 
                Tables\Actions\Action::make('run')
                    ->label('Запустити')
                    ->icon('heroicon-o-play')
                    ->action(fn(ParseLink $record) => \Artisan::call('rozetka:parse-link', ['id' => $record->id])),
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
