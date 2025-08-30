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
use Illuminate\Support\Facades\Auth;

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
                            ->default(fn() => date('n') >= 7 ? 'Ganjil' : 'Genap')
                            ->required(),
                    ]),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn(): mixed => Auth::id())
                    ->dehydrated(),


                Forms\Components\TextInput::make('judul_pengabdian')
                    ->label('Judul Pengabdian')
                    ->maxLength(255)
                    ->required(),

                Forms\Components\TextInput::make('link_proposal')
                    ->label('Link Proposal')
                    ->url()
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),

                Forms\Components\TextInput::make('link_laporan_kemajuan')
                    ->label('Link Laporan Kemajuan')
                    ->url()
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),

                Forms\Components\TextInput::make('link_laporan_akhir')
                    ->label('Link Laporan Akhir')
                    ->url()
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),

                Forms\Components\Hidden::make('status')
                    ->dehydrated(true)
                    ->default(
                        fn($get) => ($get('progres') ?? 0) >= 100 ? 'Pending' : 'On Progress'
                    )
                    ->afterStateHydrated(
                        fn($component, $state, $get) =>
                        $component->state(
                            ($get('progres') ?? 0) >= 100 ? 'Pending' : 'On Progress'
                        )
                    )
                    ->mutateDehydratedStateUsing(
                        fn($state, $get) => ($get('progres') ?? 0) >= 100 ? 'Pending' : 'On Progress'
                    ),

                Forms\Components\TextInput::make('progres')
                    ->label('Progress (%)')
                    ->numeric()
                    ->suffix('%')
                    ->default(0)
                    ->disabled()
                    ->reactive()
                    ->afterStateHydrated(function ($component, $state, $record, $set, $get) {
                        self::updateProgress($component, $set, $get);
                    }),

                Forms\Components\Repeater::make('luaransPengabdian')
                    ->label('Data Luaran')
                    ->relationship('luaransPengabdian')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Hidden::make('jenis_luaran')->default('HKI, JURNAL, BUKU'),
                                Forms\Components\Section::make('Detail HKI')
                                    ->schema([
                                        Forms\Components\TextInput::make('link_hki')
                                            ->label('Link HKI')
                                            ->url()
                                            ->nullable()
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                    ])
                                    ->columnSpan(2)
                                    ->collapsible(),


                                Forms\Components\Section::make('Detail Jurnal')
                                    ->schema([
                                        Forms\Components\TextInput::make('judul_jurnal')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                        Forms\Components\TextInput::make('jurnal_vol')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                        Forms\Components\TextInput::make('jurnal_no')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                        Forms\Components\TextInput::make('jurnal_name')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                        Forms\Components\Select::make('tahun_jurnal')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get))
                                            ->options(
                                                collect(range(now()->year, 2000))
                                                    ->mapWithKeys(fn($year) => [$year => $year])
                                            )
                                            ->searchable(),

                                    ])
                                    ->columnSpan(1)
                                    ->collapsible(),


                                Forms\Components\Section::make('Detail Buku')
                                    ->schema([
                                        Forms\Components\TextInput::make('judul_buku')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                        Forms\Components\TextInput::make('penerbit_buku')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                        Forms\Components\TextInput::make('isbn_buku')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),
                                        Forms\Components\Select::make('tahun_buku')
                                            ->reactive()
                                            ->nullable()
                                            ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get))
                                            ->options(
                                                collect(range(now()->year, 2000))
                                                    ->mapWithKeys(fn($year) => [$year => $year])
                                            )
                                            ->searchable(),
                                    ])
                                    ->columnSpan(1)
                                    ->collapsible(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->collapsible()
                    ->disableItemCreation()
                    ->disableItemDeletion()
                    ->reactive()
                    ->afterStateUpdated(fn($state, $set, $get) => self::updateProgress(null, $set, $get)),

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
                    ->color(fn(string $state): string => match ($state) {
                        'Pending'     => 'warning',
                        'Approved'    => 'success',
                        'Rejected'    => 'danger',
                        'On Progress' => 'info',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('progres')
                    ->label('Progress')
                    ->formatStateUsing(fn($state) => $state . '%')
                    ->badge()
                    ->color(fn($state) => match (true) {
                        $state < 30 => 'danger',
                        $state < 70 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('link_proposal')
                    ->url(fn($record) => $record->link_proposal)
                    ->label('Proposal'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengabdians::route('/'),
            'create' => Pages\CreatePengabdian::route('/create'),
            'edit' => Pages\EditPengabdian::route('/{record}/edit'),
        ];
    }

    protected static function updateProgress($component, $set, $get): void
    {
        $luarans = $get('luaransPengabdian') ?? [];

        $totalFields = 0;
        $filled      = 0;

        // Hitung field di dalam repeater
        foreach ($luarans as $luaran) {
            $fields = [
                $luaran['link_hki']        ?? null,
                $luaran['judul_jurnal']    ?? null,
                $luaran['jurnal_vol']      ?? null,
                $luaran['jurnal_no']       ?? null,
                $luaran['jurnal_name']     ?? null,
                $luaran['tahun_jurnal']    ?? null,
                $luaran['judul_buku']      ?? null,
                $luaran['penerbit_buku']   ?? null,
                $luaran['isbn_buku']       ?? null,
                $luaran['tahun_buku']      ?? null,
            ];

            $totalFields += count($fields);
            $filled      += collect($fields)->filter(fn($v) => filled($v))->count();
        }

        // Hitung field level-atas (bukan repeater)
        $topLevel = [
            $get('link_proposal'),
            $get('link_laporan_kemajuan'),
            $get('link_laporan_akhir'),
        ];
        $totalFields += count($topLevel);
        $filled      += collect($topLevel)->filter(fn($v) => filled($v))->count();

        $progress = $totalFields > 0 ? (int) round(($filled / $totalFields) * 100) : 0;

        $set('progres', $progress);
        $set('status', $progress >= 100 ? 'Pending' : 'In Progress');

        if ($component) {
            $component->state($progress);
        }
    }
}
