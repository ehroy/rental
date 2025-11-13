<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-camera';
    
    protected static ?string $navigationLabel = 'Produk Kamera';
    
    protected static ?string $pluralLabel = 'Produk Kamera';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Produk')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Produk'),
                        Select::make('category_id')
                            ->label('Kategori')
                            ->options(Category::all()->pluck('nama', 'id'))
                            ->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('deskripsi')
                            ->required()
                            ->rows(4)
                            ->label('Deskripsi'),
                        
                        Forms\Components\FileUpload::make('gambar')
                            ->image()
                            ->disablePreview()
                            ->directory('products')
                            ->acceptedFileTypes(['image/*'])
                            ->maxSize(5120),
                        
                        Forms\Components\TextInput::make('harga_sewa_perhari')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->label('Harga Sewa per Hari'),
                        
                        Forms\Components\Toggle::make('is_available')
                            ->label('Tersedia')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('gambar')
                    ->label('Gambar')
                    ->square(),
                
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.nama')->label('Kategori'),
                Tables\Columns\TextColumn::make('harga_sewa_perhari')
                    ->label('Harga/Hari')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                
                Tables\Columns\TextColumn::make('bookings_count')
                    ->counts('bookings')
                    ->label('Total Booking'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_available')
                    ->label('Status Ketersediaan')
                    ->placeholder('Semua')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}