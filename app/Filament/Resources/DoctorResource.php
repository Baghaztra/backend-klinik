<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Filament\Resources\DoctorResource\RelationManagers;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name', function ($query) {
                        $query->where('role', 'doctor')
                            ->orWhereHas('doctor', function ($subQuery) {
                                $subQuery->whereColumn('doctors.user_id', '=', 'users.id');
                            });
                    })
                    ->required()            
                    ->helperText(function ($state) {
                        return $state == null ? 'If no data available, create new user as doctor!' : '';
                    }),
                Forms\Components\TextInput::make('specialization')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Name'),
                Tables\Columns\TextColumn::make('specialization'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('specialization')
                    ->options(fn () => Doctor::query()->pluck('specialization', 'specialization')->toArray()),
            ])            
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDoctors::route('/'),
        ];
    }
}
