<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalRecordResource\Pages;
use App\Filament\Resources\MedicalRecordResource\RelationManagers;
use App\Models\MedicalRecord;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('patient_id')
                    ->options(function () {
                        return \App\Models\Patient::query()
                            ->join('users', 'patients.user_id', '=', 'users.id')
                            ->select('patients.id', 'users.name')
                            ->orderBy('users.name')
                            ->pluck('users.name', 'patients.id');
                    })
                    ->label('Patient')
                    ->required(),
                Forms\Components\Select::make('doctor_id')
                    ->options(function () {
                        return \App\Models\Doctor::query()
                            ->join('users', 'doctors.user_id', '=', 'users.id')
                            ->select('doctors.id', 'users.name')
                            ->orderBy('users.name')
                            ->pluck('users.name', 'doctors.id');
                    })
                    ->label('Doctor')
                    ->required(),
                Forms\Components\Textarea::make('diagnosis')
                    ->required(),
                Forms\Components\Textarea::make('treatment')
                    ->required(),
                Forms\Components\DatePicker::make('date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('patient.user.name')
                    ->label('Patient'),
                Tables\Columns\TextColumn::make('doctor.user.name')
                    ->label('Doctor'),
                Tables\Columns\TextColumn::make('diagnosis'),
                Tables\Columns\TextColumn::make('treatment'),
                Tables\Columns\TextColumn::make('date'),
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
            'index' => Pages\ManageMedicalRecords::route('/'),
        ];
    }
}
