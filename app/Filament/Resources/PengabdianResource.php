<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengabdianResource\Pages;
use App\Filament\Resources\PengabdianResource\RelationManagers;
use App\Models\Pengabdian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;

class PengabdianResource extends Resource
{
    protected static ?string $model = Pengabdian::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Pengabdian';
    protected static ?string $pluralModelLabel = 'Data Pengabdian';
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

        Forms\Components\TextInput::make('judul_pengabdian')
            ->label('Judul Pengabdian')
            ->required()
            ->maxLength(255)
            ->required(),

               Forms\Components\TextInput::make('link_proposal')
            ->label('Link Proposal')
            ->url()
            ->nullable()
            ->required(),

        Forms\Components\TextInput::make('link_laporan_kemajuan')
            ->label('Link Laporan Kemajuan')
            ->url()
            ->nullable()
            ->required(),

        Forms\Components\TextInput::make('link_laporan_akhir')
            ->label('Link Laporan Akhir')
            ->url()
            ->nullable()
            ->required(),

        Forms\Components\Section::make('Data Luaran')
            ->schema([
                Forms\Components\Select::make('jenis_luaran')
                    ->label('Jenis Luaran')
                    ->options([
                        'HKI' => 'HKI',
                        'Jurnal' => 'Jurnal',
                        'Buku' => 'Buku',
                    ])
                    ->reactive()
                    ->nullable()
                    ->required(),

                // Field yang muncul sesuai pilihan
                Forms\Components\TextInput::make('link_hki')
                    ->label('Link HKI')
                    ->visible(fn ($get) => $get('jenis_luaran') === 'HKI')
                    ->url()
                    ->nullable(),

                Forms\Components\Fieldset::make('Detail Jurnal')
                    ->schema([
                        Forms\Components\TextInput::make('judul_jurnal')
                            ->label('Judul Jurnal'),
                        Forms\Components\TextInput::make('jurnal_vol')
                            ->label('Volume'),
                        Forms\Components\TextInput::make('jurnal_no')
                            ->label('Nomor'),
                        Forms\Components\TextInput::make('jurnal_name')
                            ->label('Nama Jurnal'),
                        Forms\Components\Select::make(name: 'tahun_jurnal')
                        ->label('Tahun Jurnal')
                        ->options(
                            collect(range(Carbon::now()->year, 2000))
                                ->mapWithKeys(fn ($year) => [$year => $year])
                        )
                        ->searchable(),

                    ])
                    ->visible(fn ($get) => $get('jenis_luaran') === 'Jurnal'),

               Forms\Components\Fieldset::make('Detail Buku')
                    ->schema([
                        Forms\Components\TextInput::make('judul_buku')
                            ->label('Judul Buku'),
                        Forms\Components\TextInput::make('penerbit_buku')
                            ->label('Penerbit Buku'),
                        Forms\Components\TextInput::make('isbn_buku')
                            ->label('ISBN Buku'),
                       Forms\Components\Select::make('tahun_buku')
                        ->label('Tahun Buku')
                        ->options(
                            collect(value: range(Carbon::now()->year, 2000))
                                ->mapWithKeys(fn ($year) => [$year => $year])
                        )
                        ->searchable(),

                    ])
                    ->visible(fn ($get) => $get('jenis_luaran') === 'Buku'),
            ])
            ->collapsible(),
    ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengabdian')->searchable(),
                Tables\Columns\TextColumn::make('jenis_luaran')->sortable(),
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
            'index' => Pages\ListPengabdians::route('/'),
            'create' => Pages\CreatePengabdian::route('/create'),
            'edit' => Pages\EditPengabdian::route('/{record}/edit'),
        ];
    }
}