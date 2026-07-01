from pathlib import Path

base = Path(r'c:/Laraval Projects/china-algeria-api')

# Create directories
for path in [
    base / 'app/Enums',
    base / 'app/Models',
    base / 'app/Http/Requests',
    base / 'app/Http/Resources',
    base / 'app/Http/Controllers/Api',
    base / 'app/Services',
]:
    path.mkdir(parents=True, exist_ok=True)

# Enums
(base / 'app/Enums/StatusType.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Enums;\n\nenum StatusType: string\n{\n    case ITEM = 'ITEM';\n    case PACKAGE_ITEM = 'PACKAGE_ITEM';\n    case PACKAGE = 'PACKAGE';\n    case INSPECTION = 'INSPECTION';\n}\n''', encoding='utf-8')
(base / 'app/Enums/UserStatus.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Enums;\n\nenum UserStatus: string\n{\n    case ENABLED = 'ENABLED';\n    case DISABLED = 'DISABLED';\n    case PENDING = 'PENDING';\n    case CREATED = 'CREATED';\n    case DELETED = 'DELETED';\n}\n''', encoding='utf-8')
(base / 'app/Enums/ZoneType.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Enums;\n\nenum ZoneType: string\n{\n    case ZONE_A = 'ZONE_A';\n    case ZONE_B = 'ZONE_B';\n    case ZONE_C = 'ZONE_C';\n    case EVERYWHERE = 'EVERYWHERE';\n}\n''', encoding='utf-8')

# Table definitions
TABLES = [
    ('roles', 'Role'),
    ('zones', 'Zone'),
    ('countries', 'Country'),
    ('statuses', 'Status'),
    ('users', 'User'),
    ('user_sessions', 'UserSession'),
    ('wallets', 'Wallet'),
    ('wallet_transactions', 'WalletTransaction'),
    ('visas', 'Visa'),
    ('orders', 'Order'),
    ('order_items', 'OrderItem'),
    ('order_item_steps', 'OrderItemStep'),
    ('order_item_images', 'OrderItemImage'),
    ('packages', 'Package'),
    ('package_steps', 'PackageStep'),
    ('package_items', 'PackageItem'),
    ('package_item_steps', 'PackageItemStep'),
    ('package_item_receptions', 'PackageItemReception'),
]

# Simple model templates

def render_model(table: str, singular: str) -> str:
    uses = [
        'use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;',
        'use Illuminate\\Database\\Eloquent\\Model;',
    ]
    if singular in {'Order', 'OrderItem', 'User'}:
        uses.append('use Illuminate\\Database\\Eloquent\\SoftDeletes;')
    if table == 'statuses':
        uses.append('use App\\Enums\\StatusType;')
    if table == 'users':
        uses.append('use App\\Enums\\UserStatus;')
    if table == 'zones':
        uses.append('use App\\Enums\\ZoneType;')
    if table in {'order_items', 'packages', 'package_items'}:
        uses.extend([
            'use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;',
            'use Illuminate\\Database\\Eloquent\\Relations\\HasMany;',
        ])
    else:
        uses.extend([
            'use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;',
            'use Illuminate\\Database\\Eloquent\\Relations\\HasMany;',
        ])

    trait = 'use HasFactory, SoftDeletes;' if singular in {'Order', 'OrderItem', 'User'} else 'use HasFactory;'
    content = [
        '<?php',
        '',
        'declare(strict_types=1);',
        '',
        f'namespace App\\Models;',
        '',
    ]
    content.extend(uses)
    content.extend(['', f'class {singular} extends Model', '{', f'    {trait}', '', '    /**', '     * @property int $id', '     */', '', '    protected $fillable = [', '    ];', ''])
    content.append('    protected $casts = [];')
    content.append('')
    # add basic relationship methods
    if table == 'users':
        content.extend([
            '    public function role(): BelongsTo',
            '    {',
            '        return $this->belongsTo(Role::class, \"role_id\");',
            '    }',
            '',
            '    public function zone(): BelongsTo',
            '    {',
            '        return $this->belongsTo(Zone::class, \"zone_id\");',
            '    }',
            '',
        ])
    elif table == 'orders':
        content.extend([
            '    public function client(): BelongsTo',
            '    {',
            '        return $this->belongsTo(User::class, \"client_id\");',
            '    }',
            '',
        ])
    elif table == 'order_items':
        content.extend([
            '    public function order(): BelongsTo',
            '    {',
            '        return $this->belongsTo(Order::class, \"order_id\");',
            '    }',
            '',
            '    public function currentStep(): BelongsTo',
            '    {',
            '        return $this->belongsTo(OrderItemStep::class, \"current_step_id\");',
            '    }',
            '',
            '    public function steps(): HasMany',
            '    {',
            '        return $this->hasMany(OrderItemStep::class, \"item_id\")->orderByDesc(\"created_at\");',
            '    }',
            '',
        ])
    elif table == 'packages':
        content.extend([
            '    public function currentStep(): BelongsTo',
            '    {',
            '        return $this->belongsTo(PackageStep::class, \"current_step_id\");',
            '    }',
            '',
            '    public function steps(): HasMany',
            '    {',
            '        return $this->hasMany(PackageStep::class, \"package_id\")->orderByDesc(\"created_at\");',
            '    }',
            '',
        ])
    elif table == 'package_items':
        content.extend([
            '    public function currentStep(): BelongsTo',
            '    {',
            '        return $this->belongsTo(PackageItemStep::class, \"current_step_id\");',
            '    }',
            '',
            '    public function steps(): HasMany',
            '    {',
            '        return $this->hasMany(PackageItemStep::class, \"package_item_id\")->orderByDesc(\"created_at\");',
            '    }',
            '',
        ])
    elif table == 'wallets':
        content.extend([
            '    public function user(): BelongsTo',
            '    {',
            '        return $this->belongsTo(User::class, \"user_id\");',
            '    }',
            '',
        ])
    elif table == 'wallet_transactions':
        content.extend([
            '    public function wallet(): BelongsTo',
            '    {',
            '        return $this->belongsTo(Wallet::class, \"wallet_id\");',
            '    }',
            '',
        ])
    # Add a default relationship for a few key tables
    if table == 'roles':
        content.extend([
            '    public function users(): HasMany',
            '    {',
            '        return $this->hasMany(User::class, \"role_id\");',
            '    }',
            '',
        ])
    if table == 'zones':
        content.extend([
            '    public function users(): HasMany',
            '    {',
            '        return $this->hasMany(User::class, \"zone_id\");',
            '    }',
            '',
        ])
    content.append('}')
    return '\n'.join(content) + '\n'


def render_request(table: str, singular: str, kind: str) -> str:
    class_name = f'{kind}{singular}Request'
    return f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Requests\\{singular};\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\nuse Illuminate\\Validation\\Rule;\n\nclass {class_name} extends FormRequest\n{{\n    public function authorize(): bool\n    {{\n        return true;\n    }}\n\n    /**\n     * @return array<string, mixed>\n     */\n    public function rules(): array\n    {{\n        return [\n            'id' => ['sometimes', 'integer'],\n        ];\n    }}\n}}\n'''


def render_resource(singular: str) -> str:
    return f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass {singular}Resource extends JsonResource\n{{\n    public function toArray($request): array\n    {{\n        return [\n            'id' => $this->id,\n        ];\n    }}\n}}\n'''


def render_service(singular: str, table: str) -> str:
    service_name = f'{singular}Service'
    model_name = singular
    if table == 'wallet_transactions':
        body = f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Services;\n\nuse App\\Models\\Wallet;\nuse App\\Models\\WalletTransaction;\nuse Illuminate\\Support\\Facades\\DB;\n\nclass {service_name}\n{{\n    public function list(array $filters = []): mixed\n    {{\n        return WalletTransaction::query()->get();\n    }}\n\n    public function find(int $id): ?WalletTransaction\n    {{\n        return WalletTransaction::find($id);\n    }}\n\n    public function create(array $data): WalletTransaction\n    {{\n        return DB::transaction(function () use ($data): WalletTransaction {{\n            $wallet = Wallet::findOrFail($data['wallet_id']);\n            $balanceBefore = (float) $wallet->balance;\n            $amount = (float) ($data['amount'] ?? 0);\n            $direction = (int) ($data['direction'] ?? 1);\n            $balanceAfter = $balanceBefore + ($direction === 1 ? $amount : -$amount);\n            $wallet->balance = $balanceAfter;\n            $wallet->save();\n\n            return WalletTransaction::create([\n                ...$data,\n                'balance_before' => $balanceBefore,\n                'balance_after' => $balanceAfter,\n            ]);\n        }});\n    }}\n\n    public function update(int $id, array $data): WalletTransaction\n    {{\n        $transaction = WalletTransaction::findOrFail($id);\n        $transaction->fill($data);\n        $transaction->save();\n\n        return $transaction->fresh();\n    }}\n\n    public function delete(int $id): bool\n    {{\n        $transaction = WalletTransaction::findOrFail($id);\n\n        return (bool) $transaction->delete();\n    }}\n}}\n'''
    elif table == 'package_item_receptions':
        body = f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Services;\n\nuse App\\Models\\PackageItemReception;\n\nclass {service_name}\n{{\n    public function list(array $filters = []): mixed\n    {{\n        return PackageItemReception::query()->get();\n    }}\n\n    public function find(int $id): ?PackageItemReception\n    {{\n        return PackageItemReception::find($id);\n    }}\n\n    public function create(array $data): PackageItemReception\n    {{\n        $payload = array_diff_key($data, array_flip(['difference_quantity', 'difference_weight']));\n\n        return PackageItemReception::create($payload);\n    }}\n\n    public function update(int $id, array $data): PackageItemReception\n    {{\n        $model = PackageItemReception::findOrFail($id);\n        $payload = array_diff_key($data, array_flip(['difference_quantity', 'difference_weight']));\n        $model->fill($payload);\n        $model->save();\n\n        return $model->fresh();\n    }}\n\n    public function delete(int $id): bool\n    {{\n        $model = PackageItemReception::findOrFail($id);\n\n        return (bool) $model->delete();\n    }}\n}}\n'''
    else:
        body = f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Services;\n\nuse App\\Models\\{model_name};\n\nclass {service_name}\n{{\n    public function list(array $filters = []): mixed\n    {{\n        return {model_name}::query()->get();\n    }}\n\n    public function find(int $id): ?{model_name}\n    {{\n        return {model_name}::find($id);\n    }}\n\n    public function create(array $data): {model_name}\n    {{\n        return {model_name}::create($data);\n    }}\n\n    public function update(int $id, array $data): {model_name}\n    {{\n        $model = {model_name}::findOrFail($id);\n        $model->fill($data);\n        $model->save();\n\n        return $model->fresh();\n    }}\n\n    public function delete(int $id): bool\n    {{\n        $model = {model_name}::findOrFail($id);\n\n        return (bool) $model->delete();\n    }}\n}}\n'''
    return body


def render_controller(singular: str) -> str:
    return f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Controllers\\Api;\n\nuse App\\Http\\Controllers\\Controller;\nuse App\\Http\\Requests\\{singular}\\Store{singular}Request;\nuse App\\Http\\Requests\\{singular}\\Update{singular}Request;\nuse App\\Http\\Resources\\{singular}Resource;\nuse App\\Services\\{singular}Service;\nuse Illuminate\\Http\\JsonResponse;\nuse Illuminate\\Http\\Request;\n\nclass {singular}Controller extends Controller\n{{\n    public function __construct(private readonly {singular}Service $service)\n    {{\n    }}\n\n    public function index(Request $request): JsonResponse\n    {{\n        $items = $this->service->list($request->all());\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Items retrieved successfully.',\n            'data' => {singular}Resource::collection($items),\n        ]);\n    }}\n\n    public function show(int $id): JsonResponse\n    {{\n        $item = $this->service->find($id);\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item retrieved successfully.',\n            'data' => $item ? new {singular}Resource($item) : null,\n        ]);\n    }}\n\n    public function store(Store{singular}Request $request): JsonResponse\n    {{\n        $item = $this->service->create($request->validated());\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item created successfully.',\n            'data' => new {singular}Resource($item),\n        ], 201);\n    }}\n\n    public function update(int $id, Update{singular}Request $request): JsonResponse\n    {{\n        $item = $this->service->update($id, $request->validated());\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item updated successfully.',\n            'data' => new {singular}Resource($item),\n        ]);\n    }}\n\n    public function destroy(int $id): JsonResponse\n    {{\n        $this->service->delete($id);\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item deleted successfully.',\n            'data' => null,\n        ]);\n    }}\n}}\n'''

# Generate files only if absent, avoiding overwrite of existing app models.
for table, singular in TABLES:
    model_path = base / f'app/Models/{singular}.php'
    if not model_path.exists():
        model_path.write_text(render_model(table, singular), encoding='utf-8')

    request_dir = base / f'app/Http/Requests/{singular}'
    request_dir.mkdir(parents=True, exist_ok=True)
    (request_dir / f'Store{singular}Request.php').write_text(render_request(table, singular, 'Store'), encoding='utf-8')
    (request_dir / f'Update{singular}Request.php').write_text(render_request(table, singular, 'Update'), encoding='utf-8')

    (base / f'app/Http/Resources/{singular}Resource.php').write_text(render_resource(singular), encoding='utf-8')
    (base / f'app/Services/{singular}Service.php').write_text(render_service(singular, table), encoding='utf-8')
    (base / f'app/Http/Controllers/Api/{singular}Controller.php').write_text(render_controller(singular), encoding='utf-8')

# Minimal nested resources used by resources
(base / 'app/Http/Resources/UserResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass UserResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'public_code' => $this->public_code,\n            'full_name' => $this->full_name,\n        ];\n    }\n}\n''', encoding='utf-8')
(base / 'app/Http/Resources/OrderItemStepResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass OrderItemStepResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'status_id' => $this->status_id,\n            'created_at' => $this->created_at,\n        ];\n    }\n}\n''', encoding='utf-8')
(base / 'app/Http/Resources/PackageStepResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass PackageStepResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'status_id' => $this->status_id,\n            'created_at' => $this->created_at,\n        ];\n    }\n}\n''', encoding='utf-8')
(base / 'app/Http/Resources/PackageItemStepResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass PackageItemStepResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'status_id' => $this->status_id,\n            'created_at' => $this->created_at,\n        ];\n    }\n}\n''', encoding='utf-8')

# Append routes if necessary
routes_path = base / 'routes/api.php'
routes_text = routes_path.read_text(encoding='utf-8')
if 'use App\\Http\\Controllers\\Api\\RoleController;' not in routes_text:
    imports = '\n'.join([f'use App\\Http\\Controllers\\Api\\{singular}Controller;' for _, singular in TABLES])
    if 'use Illuminate\\Support\\Facades\\Route;' in routes_text:
        routes_text = routes_text.replace('use Illuminate\\Support\\Facades\\Route;\n', 'use Illuminate\\Support\\Facades\\Route;\n\n' + imports + '\n')
    routes_text = routes_text.rstrip() + '\n\n' + '\n'.join([f"Route::apiResource('{table}', {singular}Controller::class);" for table, singular in TABLES]) + '\n'
    routes_path.write_text(routes_text, encoding='utf-8')

print('Generated files for', len(TABLES), 'tables')
