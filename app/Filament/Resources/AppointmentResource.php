<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AppointmentResource\Pages;
use App\Filament\Resources\AppointmentResource\RelationManagers;
use App\Models\Appointment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

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
                Forms\Components\Textarea::make('complaints')
                    ->required(),
                Forms\Components\DatePicker::make('appointment_date')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ])
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
                Tables\Columns\TextColumn::make('complaints'),
                Tables\Columns\TextColumn::make('appointment_date'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'canceled' => 'Canceled',
                    ]),
                Tables\Filters\SelectFilter::make('doctor_id')
                    ->options(function () {
                        return \App\Models\Doctor::query()
                            ->join('users', 'doctors.user_id', '=', 'users.id')
                            ->select('doctors.id', 'users.name')
                            ->orderBy('users.name')
                            ->pluck('users.name', 'doctors.id');
                    })
                    ->label('Doctor'),
                Tables\Filters\SelectFilter::make('patient_id')
                    ->options(function () {
                        return \App\Models\Patient::query()
                            ->join('users', 'patients.user_id', '=', 'users.id')
                            ->select('patients.id', 'users.name')
                            ->orderBy('users.name')
                            ->pluck('users.name', 'patients.id');
                    })
                    ->label('Patient'),
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
            'index' => Pages\ManageAppointments::route('/'),
        ];
    }
}
