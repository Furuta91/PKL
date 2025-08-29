<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenelitianResource\Pages;
use App\Models\Penelitian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

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

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => Auth::id())
                            ->dehydrated(),


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

Forms\Components\Hidden::make('status')
    ->dehydrated(true)
    ->default(fn ($get) =>
        ($get('progres') ?? 0) >= 100 ? 'Pending' : 'In Progress'
    )
    ->afterStateHydrated(fn ($component, $state, $get) =>
        $component->state(
            ($get('progres') ?? 0) >= 100 ? 'Pending' : 'In Progress'
        )
    )
    ->mutateDehydratedStateUsing(fn ($state, $get) =>
        ($get('progres') ?? 0) >= 100 ? 'Pending' : 'In Progress'
        ),





Forms\Components\TextInput::make('progres')
    ->label('Progress (%)')
    ->numeric()
    ->suffix('%')
    ->default(0)
    ->disabled()
    ->afterStateHydrated(function ($component, $state, $record, $set) {
        if ($record) {
            $totalFields = 0;
            $filled = 0;

            foreach ($record->luaransPenelitian as $luaran) {
                $fields = [
                    $luaran->link_hki,
                    $luaran->judul_jurnal,
                    $luaran->jurnal_vol,
                    $luaran->jurnal_no,
                    $luaran->jurnal_name,
                    $luaran->tahun_jurnal,
                    $luaran->judul_buku,
                    $luaran->penerbit_buku,
                    $luaran->isbn_buku,
                    $luaran->tahun_buku,
                ];

                $totalFields += count($fields);
                $filled += collect($fields)->filter(fn ($val) => !empty($val))->count();
            }

            $progress = $totalFields > 0 ? round(($filled / $totalFields) * 100) : 0;

            // update progres
            $component->state($progress);

            // update status ikut terset saat update progres
            $set('status', $progress >= 100 ? 'Pending' : 'In Progress');
        }
    }),





Forms\Components\Repeater::make('luaransPenelitian')
    ->label('Data Luaran')
    ->relationship('luaransPenelitian')
    ->schema([
        Forms\Components\Grid::make(2)
            ->schema([
                Forms\Components\Section::make('Detail HKI')
                    ->schema([
                        Forms\Components\Hidden::make('jenis_luaran')->default('HKI, JURNAL, BUKU'),
                        Forms\Components\TextInput::make('link_hki')
                            ->label('Link HKI')
                            ->url()
                            ->nullable(),
                    ])
                    ->columnSpan(2)
                    ->collapsible(),

                Forms\Components\Section::make('Detail Jurnal')
                    ->schema([
                        Forms\Components\TextInput::make('judul_jurnal')->label('Judul Jurnal'),
                        Forms\Components\TextInput::make('jurnal_vol')->label('Volume'),
                        Forms\Components\TextInput::make('jurnal_no')->label('Nomor'),
                        Forms\Components\TextInput::make('jurnal_name')->label('Nama Jurnal'),
                        Forms\Components\Select::make('tahun_jurnal')
                            ->label('Tahun Jurnal')
                            ->options(
                                collect(range(now()->year, 2000))
                                    ->mapWithKeys(fn ($year) => [$year => $year])
                            )
                            ->searchable(),
                    ])
                    ->columnSpan(1)
                    ->collapsible(),

                Forms\Components\Section::make('Detail Buku')
                    ->schema([
                        Forms\Components\TextInput::make('judul_buku')->label('Judul Buku'),
                        Forms\Components\TextInput::make('penerbit_buku')->label('Penerbit Buku'),
                        Forms\Components\TextInput::make('isbn_buku')->label('ISBN Buku'),
                        Forms\Components\Select::make('tahun_buku')
                            ->label('Tahun Buku')
                            ->options(
                                collect(range(now()->year, 2000))
                                    ->mapWithKeys(fn ($year) => [$year => $year])
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
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('judul_penelitian')
                    ->label('Judul')
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors(colors: [
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                        'info' => 'in progress',
                    ]),

                Tables\Columns\TextColumn::make('progres')
                    ->label('Progress')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state < 30 => 'danger',
                        $state < 70 => 'warning',
                        default => 'success',
                    })
                    ->sortable(),


                Tables\Columns\TextColumn::make('link_proposal')
                    ->url(fn ($record) => $record->link_proposal)
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
            'index'  => Pages\ListPenelitians::route('/'),
            'create' => Pages\CreatePenelitian::route('/create'),
            'edit'   => Pages\EditPenelitian::route('/{record}/edit'),
        ];
    }
}