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
                Forms\Components\Grid::make(2)
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

                        Forms\Components\Select::make('periode')
                            ->label('Periode')
                            ->options([
                                'Ganjil' => 'Ganjil',
                                'Genap'  => 'Genap',
                            ])
                            ->default(fn () => date('n') >= 7 ? 'Ganjil' : 'Genap')
                            ->required(),
                    ]),

                Forms\Components\TextInput::make('judul_pengabdian')
                    ->label('Judul Pengabdian')
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

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\TextInput::make('progress')
                    ->label('Progress (%)')
                    ->numeric()
                    ->suffix('%')
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100),

                // Relasi ke luarans
                Forms\Components\Section::make('Data Luaran')
                    ->relationship('luaransPengabdian')
                    ->schema([
                        Forms\Components\Select::make('jenis_luaran')
                            ->label('Jenis Luaran')
                            ->options([
                                'HKI'    => 'HKI',
                                'Jurnal' => 'Jurnal',
                                'Buku'   => 'Buku',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('link_hki')
                            ->label('Link HKI')
                            ->url()
                            ->nullable(),

                        Forms\Components\TextInput::make('judul_jurnal')
                            ->label('Judul Jurnal')
                            ->nullable(),
                        Forms\Components\TextInput::make('jurnal_vol')
                            ->label('Volume')
                            ->nullable(),
                        Forms\Components\TextInput::make('jurnal_no')
                            ->label('Nomor')
                            ->nullable(),
                        Forms\Components\TextInput::make('jurnal_name')
                            ->label('Nama Jurnal')
                            ->nullable(),
                        Forms\Components\Select::make('tahun_jurnal')
                            ->label('Tahun Jurnal')
                            ->options(
                                collect(range(now()->year, 2000))
                                    ->mapWithKeys(fn ($year) => [$year => $year])
                            )
                            ->searchable()
                            ->nullable(),

                        Forms\Components\TextInput::make('judul_buku')
                            ->label('Judul Buku')
                            ->nullable(),
                        Forms\Components\TextInput::make('penerbit_buku')
                            ->label('Penerbit Buku')
                            ->nullable(),
                        Forms\Components\TextInput::make('isbn_buku')
                            ->label('ISBN Buku')
                            ->nullable(),
                        Forms\Components\Select::make('tahun_buku')
                            ->label('Tahun Buku')
                            ->options(
                                collect(range(now()->year, 2000))
                                    ->mapWithKeys(fn ($year) => [$year => $year])
                            )
                            ->searchable()
                            ->nullable(),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_pengabdian')
                    ->label('Judul')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ]),

                Tables\Columns\TextColumn::make('progress')
                    ->label('Progress')
                    ->suffix('%'),

                Tables\Columns\TextColumn::make('link_proposal')
                    ->url(fn ($record) => $record->link_proposal)
                    ->label('Proposal'),
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