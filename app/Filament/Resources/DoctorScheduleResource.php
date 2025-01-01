<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorScheduleResource\Pages;
use App\Filament\Resources\DoctorScheduleResource\RelationManagers;
use App\Models\DoctorSchedule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DoctorScheduleResource extends Resource
{
    protected static ?string $model = DoctorSchedule::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('doctor_id')
                    ->label('Dokter')
                    ->options(function () {
                        return \App\Models\Doctor::all()->pluck('user.name', 'id');
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('day')
                    ->options([
                        'Senin'=>'Senin',
                        'Selasa'=>'Selasa',
                        'Rabu'=>'Rabu',
                        'Kamis'=>'Kamis',
                        'Jumat'=>'Jumat',
                        'Sabtu'=>'Sabtu',
                        'Minggu'=>'Minggu',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.user.name')->label('Dokter'),
                Tables\Columns\TextColumn::make('day')->label('Hari'),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageDoctorSchedules::route('/'),
        ];
    }
}
