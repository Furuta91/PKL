<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenelitianResource\Pages;
use App\Models\Penelitian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;

class PenelitianResource extends Resource
{
    protected static ?string $model = Penelitian::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Penelitian';
    protected static ?string $pluralModelLabel = 'Data Penelitian';

    public static function form(Form $form): Form
    {
return $form
    ->schema([
        Forms\Components\Grid::make(2) // 2 kolom biar sejajar
            ->schema([
                Forms\Components\Select::make('tahun_ajaran')
                    ->label('Tahun Ajaran')
                    ->options(function () {
                        $tahun = date('Y');
                        $list = [];
                        for ($i = -1; $i <= 3; $i++) {
                            $start = $tahun + $i;
                            $end = $start + 1;
                            $list["$start/$end"] = "$start/$end";
                        }
                        return $list;
                    })
                    ->default(function () {
                        $tahun = date('Y');
                        $bulan = date('n');
                        return $bulan >= 7
                            ? "$tahun/" . ($tahun + 1)
                            : ($tahun - 1) . "/$tahun";
                    })
                    ->required(),

                Forms\Components\Select::make('semester')
                    ->label('Semester')
                    ->options([
                        'Ganjil' => 'Ganjil',
                        'Genap' => 'Genap',
                    ])
                    ->default(fn () => date('n') >= 7 ? 'Ganjil' : 'Genap')
                    ->required(),
            ]),

        Forms\Components\TextInput::make('judul_penelitian')
            ->label('Judul Penelitian')
            ->maxLength(255)
            ->required(),

        Forms\Components\TextInput::make('link_proposal')
            ->label('Link Proposal')
            ->url()
            ->nullable(),

        Forms\Components\TextInput::make('link_laporan_kemajuan')
            ->label('Link Laporan Kemajuan')
            ->url()
            ->nullable(),

        Forms\Components\TextInput::make('link_laporan_akhir')
            ->label('Link Laporan Akhir')
            ->url()
            ->nullable(),

      Forms\Components\Section::make('Data Luaran')
    ->schema([
        Forms\Components\Hidden::make('jenis_luaran')
            ->default(['HKI', 'Jurnal', 'Buku']),

        Forms\Components\Fieldset::make('Detail HKI')
            ->schema([
                Forms\Components\TextInput::make('link_hki')
                    ->label('Link HKI')
                    ->url()
                    ->nullable(),
            ]),

        Forms\Components\Fieldset::make('Detail Jurnal')
            ->schema([
                Forms\Components\TextInput::make('judul_jurnal')->label('Judul Jurnal'),
                Forms\Components\TextInput::make('jurnal_vol')->label('Volume'),
                Forms\Components\TextInput::make('jurnal_no')->label('Nomor'),
                Forms\Components\TextInput::make('jurnal_name')->label('Nama Jurnal'),
                Forms\Components\Select::make('tahun_jurnal')
                    ->label('Tahun Jurnal')
                    ->options(
                        collect(range(\Carbon\Carbon::now()->year, 2000))
                            ->mapWithKeys(fn ($year) => [$year => $year])
                    )
                    ->searchable(),
            ]),

        Forms\Components\Fieldset::make('Detail Buku')
            ->schema([
                Forms\Components\TextInput::make('judul_buku')->label('Judul Buku'),
                Forms\Components\TextInput::make('penerbit_buku')->label('Penerbit Buku'),
                Forms\Components\TextInput::make('isbn_buku')->label('ISBN Buku'),
                Forms\Components\Select::make('tahun_buku')
                    ->label('Tahun Buku')
                    ->options(
                        collect(range(\Carbon\Carbon::now()->year, 2000))
                            ->mapWithKeys(fn ($year) => [$year => $year])
                    )
                    ->searchable(),
            ]),
    ])
    ->collapsible()

    ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_penelitian')->searchable(),
Tables\Columns\TextColumn::make('jenis_luaran')
    ->separator(',')
    ->label('Jenis Luaran'),

                Tables\Columns\TextColumn::make('link_proposal')->url(fn ($record) => $record->link_proposal)->label('Proposal'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenelitians::route('/'),
            'create' => Pages\CreatePenelitian::route('/create'),
            'edit' => Pages\EditPenelitian::route('/{record}/edit'),
        ];
    }
}