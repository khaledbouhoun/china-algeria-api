from pathlib import Path

base = Path(r'c:/Laraval Projects/china-algeria-api')

# Create directories
for p in [
    base / 'app/Enums',
    base / 'app/Models',
    base / 'app/Http/Requests',
    base / 'app/Http/Resources',
    base / 'app/Http/Controllers/Api',
    base / 'app/Services',
]:
    p.mkdir(parents=True, exist_ok=True)

(base / 'app/Enums/StatusType.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Enums;\n\nenum StatusType: string\n{\n    case ITEM = 'ITEM';\n    case PACKAGE_ITEM = 'PACKAGE_ITEM';\n    case PACKAGE = 'PACKAGE';\n    case INSPECTION = 'INSPECTION';\n}\n''', encoding='utf-8')
(base / 'app/Enums/UserStatus.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Enums;\n\nenum UserStatus: string\n{\n    case ENABLED = 'ENABLED';\n    case DISABLED = 'DISABLED';\n    case PENDING = 'PENDING';\n    case CREATED = 'CREATED';\n    case DELETED = 'DELETED';\n}\n''', encoding='utf-8')
(base / 'app/Enums/ZoneType.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Enums;\n\nenum ZoneType: string\n{\n    case ZONE_A = 'ZONE_A';\n    case ZONE_B = 'ZONE_B';\n    case ZONE_C = 'ZONE_C';\n    case EVERYWHERE = 'EVERYWHERE';\n}\n''', encoding='utf-8')

# Table metadata
TABLES = [
    ('roles', 'Role', [], False, False),
    ('zones', 'Zone', [], False, False),
    ('countries', 'Country', [], False, False),
    ('statuses', 'Status', [], False, False),
    ('users', 'User', [], True, True),
    ('user_sessions', 'UserSession', [], False, False),
    ('wallets', 'Wallet', [], False, False),
    ('wallet_transactions', 'WalletTransaction', [], False, False),
    ('visas', 'Visa', [], False, False),
    ('orders', 'Order', [], True, True),
    ('order_items', 'OrderItem', [], True, True),
    ('order_item_steps', 'OrderItemStep', [], False, False),
    ('order_item_images', 'OrderItemImage', [], False, False),
    ('packages', 'Package', [], False, False),
    ('package_steps', 'PackageStep', [], False, False),
    ('package_items', 'PackageItem', [], False, False),
    ('package_item_steps', 'PackageItemStep', [], False, False),
    ('package_item_receptions', 'PackageItemReception', [], False, False),
]

# Relationship map (simple, based on schema)
RELATION_MAP = {
    'roles': [],
    'zones': [],
    'countries': [],
    'statuses': [],
    'users': [
        ('role', 'Role', 'belongsTo', 'role_id', 'id'),
        ('zone', 'Zone', 'belongsTo', 'zone_id', 'id'),
        ('orders', 'Order', 'hasMany', 'client_id', 'id'),
        ('userSessions', 'UserSession', 'hasMany', 'user_id', 'id'),
        ('wallets', 'Wallet', 'hasMany', 'user_id', 'id'),
        ('visas', 'Visa', 'hasMany', 'user_id', 'id'),
    ],
    'user_sessions': [('user', 'User', 'belongsTo', 'user_id', 'id')],
    'wallets': [
        ('user', 'User', 'belongsTo', 'user_id', 'id'),
        ('role', 'Role', 'belongsTo', 'role_id', 'id'),
        ('walletTransactions', 'WalletTransaction', 'hasMany', 'wallet_id', 'id'),
    ],
    'wallet_transactions': [
        ('wallet', 'Wallet', 'belongsTo', 'wallet_id', 'id'),
        ('user', 'User', 'belongsTo', 'user_id', 'id'),
        ('createdBy', 'User', 'belongsTo', 'created_by', 'id'),
    ],
    'visas': [
        ('user', 'User', 'belongsTo', 'user_id', 'id'),
        ('createdBy', 'User', 'belongsTo', 'created_by', 'id'),
    ],
    'orders': [
        ('client', 'User', 'belongsTo', 'client_id', 'id'),
        ('orderItems', 'OrderItem', 'hasMany', 'order_id', 'id'),
    ],
    'order_items': [
        ('order', 'Order', 'belongsTo', 'order_id', 'id'),
        ('currentStep', 'OrderItemStep', 'belongsTo', 'current_step_id', 'id'),
        ('orderItemImages', 'OrderItemImage', 'hasMany', 'order_item_id', 'id'),
        ('steps', 'OrderItemStep', 'hasMany', 'item_id', 'id'),
        ('packageItems', 'PackageItem', 'hasMany', 'order_item_id', 'id'),
    ],
    'order_item_steps': [
        ('item', 'OrderItem', 'belongsTo', 'item_id', 'id'),
        ('status', 'Status', 'belongsTo', 'status_id', 'id'),
        ('zone', 'Zone', 'belongsTo', 'zone_id', 'id'),
        ('user', 'User', 'belongsTo', 'user_id', 'id'),
        ('createdBy', 'User', 'belongsTo', 'created_by', 'id'),
    ],
    'order_item_images': [('orderItem', 'OrderItem', 'belongsTo', 'order_item_id', 'id')],
    'packages': [
        ('currentStep', 'PackageStep', 'belongsTo', 'current_step_id', 'id'),
        ('createdBy', 'User', 'belongsTo', 'created_by', 'id'),
        ('updatedBy', 'User', 'belongsTo', 'updated_by', 'id'),
        ('gladiator', 'User', 'belongsTo', 'gladiator_id', 'id'),
        ('packageItems', 'PackageItem', 'hasMany', 'package_id', 'id'),
        ('steps', 'PackageStep', 'hasMany', 'package_id', 'id'),
    ],
    'package_steps': [
        ('package', 'Package', 'belongsTo', 'package_id', 'id'),
        ('status', 'Status', 'belongsTo', 'status_id', 'id'),
        ('zone', 'Zone', 'belongsTo', 'zone_id', 'id'),
        ('user', 'User', 'belongsTo', 'user_id', 'id'),
        ('createdBy', 'User', 'belongsTo', 'created_by', 'id'),
    ],
    'package_items': [
        ('package', 'Package', 'belongsTo', 'package_id', 'id'),
        ('orderItem', 'OrderItem', 'belongsTo', 'order_item_id', 'id'),
        ('currentStep', 'PackageItemStep', 'belongsTo', 'current_step_id', 'id'),
        ('createdBy', 'User', 'belongsTo', 'created_by', 'id'),
        ('updatedBy', 'User', 'belongsTo', 'updated_by', 'id'),
        ('steps', 'PackageItemStep', 'hasMany', 'package_item_id', 'id'),
        ('packageItemReceptions', 'PackageItemReception', 'hasMany', 'package_item_id', 'id'),
    ],
    'package_item_steps': [
        ('packageItem', 'PackageItem', 'belongsTo', 'package_item_id', 'id'),
        ('status', 'Status', 'belongsTo', 'status_id', 'id'),
        ('zone', 'Zone', 'belongsTo', 'zone_id', 'id'),
        ('user', 'User', 'belongsTo', 'user_id', 'id'),
        ('createdBy', 'User', 'belongsTo', 'created_by', 'id'),
    ],
    'package_item_receptions': [
        ('packageItem', 'PackageItem', 'belongsTo', 'package_item_id', 'id'),
        ('inspector', 'User', 'belongsTo', 'inspected_by', 'id'),
    ],
}

# Column metadata for each table
COLUMN_MAP = {
    'roles': [('code', 'string', False, 50), ('name', 'string', False, 100), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None)],
    'zones': [('code', 'string', False, 50), ('name', 'string', False, 100), ('zone_type', 'enum', False, None), ('description', 'text', True, None)],
    'countries': [('country', 'string', False, 255)],
    'statuses': [('code', 'string', False, 50), ('name', 'string', False, 100), ('type', 'enum', False, None), ('created_at', 'datetime', True, None), ('updated_at', 'datetime', True, None)],
    'users': [('public_code', 'string', False, 50), ('full_name', 'string', False, 255), ('email', 'string', False, 255), ('phone', 'string', True, 50), ('address', 'text', True, None), ('firebase_uid', 'string', True, 255), ('role_id', 'integer', False, None), ('zone_id', 'integer', True, None), ('status', 'string', False, 20), ('verified_at', 'datetime', True, None), ('last_login_at', 'datetime', True, None), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None), ('deleted_at', 'datetime', True, None)],
    'user_sessions': [('user_id', 'integer', False, None), ('created_at', 'datetime', False, None)],
    'wallets': [('user_id', 'integer', False, None), ('role_id', 'integer', False, None), ('balance', 'decimal', False, 14, 2), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None)],
    'wallet_transactions': [('wallet_id', 'integer', False, None), ('direction', 'integer', False, None), ('amount', 'decimal', False, 14, 2), ('user_id', 'integer', True, None), ('balance_before', 'decimal', False, 14, 2), ('balance_after', 'decimal', False, 14, 2), ('created_by', 'integer', True, None), ('comment', 'text', True, None), ('status', 'string', False, 20), ('created_at', 'datetime', False, None)],
    'visas': [('user_id', 'integer', False, None), ('visa_status', 'string', False, 20), ('date_from', 'datetime', False, None), ('date_to', 'datetime', False, None), ('number', 'string', False, 50), ('created_by', 'integer', False, None), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None)],
    'orders': [('client_id', 'integer', False, None), ('comment', 'text', True, None), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None), ('deleted_at', 'datetime', True, None)],
    'order_items': [('public_code', 'string', False, 50), ('order_id', 'integer', False, None), ('designation', 'string', False, 255), ('quantity_declared', 'integer', False, None), ('price_unit_declared', 'decimal', False, 14, 2), ('weight_unit_declared', 'decimal', False, 10, 3), ('weight_total', 'decimal', False, 10, 3), ('current_step_id', 'integer', False, None), ('comment', 'text', True, None), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None), ('deleted_at', 'datetime', True, None)],
    'order_item_steps': [('item_id', 'integer', False, None), ('status_id', 'integer', False, None), ('zone_id', 'integer', True, None), ('user_id', 'integer', True, None), ('comment', 'text', True, None), ('created_by', 'integer', True, None), ('created_at', 'datetime', False, None)],
    'order_item_images': [('order_item_id', 'integer', False, None), ('image_path', 'string', False, 255), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None)],
    'packages': [('qr_code', 'string', False, 255), ('items_count', 'integer', False, None), ('weight', 'decimal', False, 10, 3), ('amount', 'decimal', False, 14, 2), ('comment', 'text', True, None), ('created_by', 'integer', True, None), ('updated_by', 'integer', True, None), ('gladiator_id', 'integer', True, None), ('current_step_id', 'integer', False, None), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None)],
    'package_steps': [('package_id', 'integer', False, None), ('status_id', 'integer', False, None), ('zone_id', 'integer', True, None), ('user_id', 'integer', True, None), ('comment', 'text', True, None), ('created_by', 'integer', True, None), ('created_at', 'datetime', False, None)],
    'package_items': [('package_id', 'integer', False, None), ('order_item_id', 'integer', False, None), ('quantity_allocated', 'integer', False, None), ('weight_total_allocated', 'decimal', False, 10, 3), ('amount_total_allocated', 'decimal', False, 14, 2), ('current_step_id', 'integer', False, None), ('quantity_recupered', 'integer', False, None), ('weight_total_recupered', 'decimal', False, 10, 3), ('amount_total_recupered', 'decimal', False, 14, 2), ('created_by', 'integer', True, None), ('updated_by', 'integer', True, None), ('created_at', 'datetime', False, None), ('updated_at', 'datetime', False, None)],
    'package_item_steps': [('package_item_id', 'integer', False, None), ('status_id', 'integer', False, None), ('zone_id', 'integer', True, None), ('user_id', 'integer', True, None), ('comment', 'text', True, None), ('created_by', 'integer', True, None), ('created_at', 'datetime', False, None)],
    'package_item_receptions': [('package_item_id', 'integer', False, None), ('inspected_by', 'integer', False, None), ('expected_quantity', 'integer', False, None), ('expected_weight', 'decimal', False, 10, 3), ('received_quantity', 'integer', False, None), ('received_weight', 'decimal', False, 10, 3), ('count_reception', 'integer', True, None), ('comment', 'text', True, None), ('created_at', 'datetime', False, None)],
}

# Prepare model content

def render_model(table, singular, columns, soft_delete=False):
    fillable = [name for name, *rest in columns if name not in {'id', 'created_at', 'updated_at', 'deleted_at'}]
    casts = []
    for name, dtype, nullable, *rest in columns:
        if name in {'created_at', 'updated_at', 'deleted_at', 'verified_at', 'last_login_at', 'date_from', 'date_to'}:
            casts.append(f"        '{name}' => 'datetime',")
        elif name in {'type'}:
            casts.append("        'type' => StatusType::class,")
        elif name == 'status':
            casts.append("        'status' => UserStatus::class,")
        elif name == 'zone_type':
            casts.append("        'zone_type' => ZoneType::class,")
        elif dtype == 'decimal':
            scale = rest[1] if len(rest) > 1 else 2
            casts.append(f"        '{name}' => 'decimal:{scale}',")
    import_lines = [
        'use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;',
        'use Illuminate\\Database\\Eloquent\\Model;',
    ]
    relation_imports = []
    relation_methods = []
    for rel_name, related, rel_type, foreign_key, local_key in RELATION_MAP.get(table, []):
        if rel_type == 'belongsTo':
            relation_imports.append('use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;')
            relation_methods.append(f"    public function {rel_name}(): BelongsTo\n    {{\n        return $this->belongsTo({related}::class, '{foreign_key}');\n    }}")
        else:
            relation_imports.append('use Illuminate\\Database\\Eloquent\\Relations\\HasMany;')
            relation_methods.append(f"    public function {rel_name}(): HasMany\n    {{\n        return $this->hasMany({related}::class, '{foreign_key}', '{local_key}');\n    }}")
    if any(col[0] == 'current_step_id' for col in columns):
        step_model = {'order_items':'OrderItemStep','packages':'PackageStep','package_items':'PackageItemStep'}[table]
        foreign_key = {'order_items':'item_id','packages':'package_id','package_items':'package_item_id'}[table]
        relation_imports.append('use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;')
        relation_imports.append('use Illuminate\\Database\\Eloquent\\Relations\\HasMany;')
        relation_methods.append(f"    public function currentStep(): BelongsTo\n    {{\n        return $this->belongsTo({step_model}::class, 'current_step_id');\n    }}")
        relation_methods.append(f"    public function steps(): HasMany\n    {{\n        return $this->hasMany({step_model}::class, '{foreign_key}')->orderByDesc('created_at');\n    }}")
    # deduplicate imports
    seen = set()
    final_imports = []
    for line in import_lines + relation_imports:
        if line not in seen:
            seen.add(line)
            final_imports.append(line)
    if soft_delete:
        final_imports.append('use Illuminate\\Database\\Eloquent\\SoftDeletes;')
    # add enum imports
    if any(name in {'type'} for name, *_ in columns):
        final_imports.append('use App\\Enums\\StatusType;')
    if any(name == 'status' for name, *_ in columns):
        final_imports.append('use App\\Enums\\UserStatus;')
    if any(name == 'zone_type' for name, *_ in columns):
        final_imports.append('use App\\Enums\\ZoneType;')
    # a simple docblock
    doc_lines = [
        '    /**',
        '     * @property int $id',
    ]
    for name, dtype, nullable, *rest in columns:
        if name == 'id':
            continue
        php_type = 'string'
        if name.endswith('_id') or name in {'direction', 'quantity_declared', 'quantity_allocated', 'quantity_recupered', 'count_reception', 'items_count'}:
            php_type = 'int'
        elif name in {'created_at', 'updated_at', 'deleted_at', 'verified_at', 'last_login_at', 'date_from', 'date_to'}:
            php_type = '\\Illuminate\\Support\\Carbon'
        elif dtype == 'decimal':
            php_type = 'float|string'
        doc_lines.append(f'     * @property {php_type} ${name}')
    doc_lines.append('     */')
    if soft_delete:
        class_decl = f'class {singular} extends Model\n{{\n    use HasFactory, SoftDeletes;'
    # Use use SoftDeletes only once
    class_decl = class_decl.replace('use HasFactory, SoftDeletes;', 'use HasFactory, SoftDeletes;')
    content = ['<?php', '', 'declare(strict_types=1);', '', f'namespace App\\Models;', '', *final_imports, '', class_decl, '']
    # if soft delete imports present, good
    content.append('    protected $fillable = [')
    for name in fillable:
        content.append(f"        '{name}',")
    content.append('    ];')
    content.append('')
    if any(name in {'created_at','updated_at'} for name, *_ in columns):
        content.append('    protected $casts = [')
        for line in casts:
            content.append(line)
        content.append('    ];')
    else:
        content.append('    protected $casts = [];')
    content.append('')
    for method in relation_methods:
        content.append(method)
        content.append('')
    content.append('}')
    return '\n'.join(content) + '\n'


def render_request(table, singular, kind, columns):
    dir_path = base / f'app/Http/Requests/{singular}'
    dir_path.mkdir(parents=True, exist_ok=True)
    class_name = f'{kind}{singular}Request'
    fillable = [name for name, *rest in columns if name not in {'id', 'created_at', 'updated_at', 'deleted_at'}]
    rules = []
    for name, dtype, nullable, *rest in fillable:
        if name in {'difference_quantity', 'difference_weight'}:
            continue
        if kind == 'Update':
            prefix = 'sometimes' + ('|nullable' if nullable else '')
        else:
            prefix = 'required' if not nullable else 'sometimes|nullable'
        # simple rules map
        if name.endswith('_id'):
            target = 'users'
            if name in {'role_id', 'zone_id', 'status_id', 'user_id', 'created_by','updated_by','inspected_by','wallet_id','client_id','order_id','package_id','order_item_id','package_item_id','gladiator_id','item_id','current_step_id'}:
                target = {'role_id':'roles','zone_id':'zones','status_id':'statuses','user_id':'users','created_by':'users','updated_by':'users','inspected_by':'users','wallet_id':'wallets','client_id':'users','order_id':'orders','package_id':'packages','order_item_id':'order_items','package_item_id':'package_items','gladiator_id':'users','item_id':'order_items','current_step_id':'order_item_steps' if table=='order_items' else 'package_steps' if table=='packages' else 'package_item_steps'}[name]
            rules.append(f"            '{name}' => ['{prefix}', 'exists:{target},id'],")
        elif name in {'status', 'type', 'zone_type', 'visa_status'}:
            rules.append(f"            '{name}' => ['{prefix}', Rule::in(['ENABLED','DISABLED','PENDING','CREATED','DELETED'])],")
        elif dtype == 'decimal':
            rules.append(f"            '{name}' => ['{prefix}', 'numeric', 'min:0'],")
        elif dtype == 'integer':
            rules.append(f"            '{name}' => ['{prefix}', 'integer'],")
        elif dtype == 'datetime':
            rules.append(f"            '{name}' => ['{prefix}', 'date'],")
        elif dtype == 'text':
            rules.append(f"            '{name}' => ['{prefix}'],")
        else:
            if len(rest) and rest[0]:
                rules.append(f"            '{name}' => ['{prefix}', 'max:{rest[0]}'],")
            else:
                rules.append(f"            '{name}' => ['{prefix}'],")
    content = f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Requests\\{singular};\n\nuse Illuminate\\Foundation\\Http\\FormRequest;\nuse Illuminate\\Validation\\Rule;\n\nclass {class_name} extends FormRequest\n{{\n    public function authorize(): bool\n    {{\n        return true;\n    }}\n\n    /**\n     * @return array<string, mixed>\n     */\n    public function rules(): array\n    {{\n        return [\n{chr(10).join(rules)}\n        ];\n    }}\n}}\n'''
    (dir_path / f'{class_name}.php').write_text(content, encoding='utf-8')


def render_resource(table, singular):
    cols = [name for name, *rest in COLUMN_MAP[table] if name not in {'id', 'created_at', 'updated_at', 'deleted_at'}]
    content = f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass {singular}Resource extends JsonResource\n{{\n    /**\n     * Transform the resource into an array.\n     *\n     * @param  \\Illuminate\\Http\\Request  $request\n     * @return array<string, mixed>\n     */\n    public function toArray($request): array\n    {{\n        $data = [\n'''
    for name in cols:
        content += f"            '{name}' => $this->{name},\n"
    content += "        ];\n\n"
    if table in {'packages', 'order_items', 'package_items'}:
        step_resource = 'OrderItemStepResource' if table == 'order_items' else 'PackageStepResource' if table == 'packages' else 'PackageItemStepResource'
        content += f"        if ($this->relationLoaded('currentStep') || $this->resource->relationLoaded('currentStep')) {{\n            $data['current_step'] = $this->whenLoaded('currentStep', fn () => new {step_resource}($this->currentStep));\n        }}\n\n"
    content += "        return $data;\n    }\n}\n"
    (base / f'app/Http/Resources/{singular}Resource.php').write_text(content, encoding='utf-8')


def render_service(table, singular):
    model_class = singular
    content = f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Services;\n\nuse App\\Models\\{model_class};\nuse Illuminate\\Support\\Facades\\DB;\n\nclass {singular}Service\n{{\n    public function list(array $filters = []): mixed\n    {{\n        $query = {model_class}::query();\n\n        foreach ($filters as $column => $value) {{\n            if ($value !== null) {{\n                $query->where($column, $value);\n            }}\n        }}\n\n        return $query->get();\n    }}\n\n    public function find(int $id): ?{model_class}\n    {{\n        return {model_class}::find($id);\n    }}\n'''
    if table == 'wallet_transactions':
        content += '''\n    public function create(array $data): \App\Models\WalletTransaction\n    {\n        return DB::transaction(function () use ($data): \App\Models\WalletTransaction {\n            $wallet = \App\Models\Wallet::findOrFail($data['wallet_id']);\n            $balanceBefore = (float) $wallet->balance;\n            $amount = (float) ($data['amount'] ?? 0);\n            $direction = (int) ($data['direction'] ?? 1);\n            $balanceAfter = $balanceBefore + ($direction === 1 ? $amount : -$amount);\n\n            $wallet->balance = $balanceAfter;\n            $wallet->save();\n\n            return \App\Models\WalletTransaction::create([\n                ...$data,\n                'balance_before' => $balanceBefore,\n                'balance_after' => $balanceAfter,\n            ]);\n        });\n    }}\n'''
    elif table == 'package_item_receptions':
        content += '''\n    public function create(array $data): \App\Models\PackageItemReception\n    {\n        $payload = array_diff_key($data, array_flip(['difference_quantity', 'difference_weight']));\n\n        return \App\Models\PackageItemReception::create($payload);\n    }}\n'''
    elif table in {'order_items', 'packages', 'package_items'}:
        step_table = {'order_items':'order_item_steps','packages':'package_steps','package_items':'package_item_steps'}[table]
        parent_key = {'order_items':'item_id','packages':'package_id','package_items':'package_item_id'}[table]
        content += f'''\n    public function create(array $data): {model_class}\n    {{\n        return DB::transaction(function () use ($data): {model_class} {{\n            $parentId = DB::table('{table}')->insertGetId([\n                'current_step_id' => 0,\n                ...array_diff_key($data, array_flip(['current_step_id'])),\n            ]);\n\n            $stepId = DB::table('{step_table}')->insertGetId([\n                '{parent_key}' => $parentId,\n                'status_id' => $data['status_id'] ?? 1,\n                'created_at' => now(),\n            ]);\n\n            DB::table('{table}')->where('id', $parentId)->update(['current_step_id' => $stepId]);\n\n            return {model_class}::with(['currentStep'])->findOrFail($parentId);\n        }});\n    }}\n'''
    else:
        content += f'''\n    public function create(array $data): {model_class}\n    {{\n        return {model_class}::create($data);\n    }}\n'''
    content += f'''\n    public function update(int $id, array $data): {model_class}\n    {{\n        $model = {model_class}::findOrFail($id);\n        $model->fill($data);\n        $model->save();\n\n        return $model->fresh();\n    }}\n\n    public function delete(int $id): bool\n    {{\n        $model = {model_class}::findOrFail($id);\n\n        return (bool) $model->delete();\n    }}\n}}\n'''
    (base / f'app/Services/{singular}Service.php').write_text(content, encoding='utf-8')


def render_controller(table, singular):
    content = f'''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Controllers\\Api;\n\nuse App\\Http\\Controllers\\Controller;\nuse App\\Http\\Requests\\{singular}\\Store{singular}Request;\nuse App\\Http\\Requests\\{singular}\\Update{singular}Request;\nuse App\\Http\\Resources\\{singular}Resource;\nuse App\\Services\\{singular}Service;\nuse Illuminate\\Http\\JsonResponse;\nuse Illuminate\\Http\\Request;\n\nclass {singular}Controller extends Controller\n{{\n    public function __construct(private readonly {singular}Service $service)\n    {{\n    }}\n\n    public function index(Request $request): JsonResponse\n    {{\n        $items = $this->service->list($request->all());\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Items retrieved successfully.',\n            'data' => {singular}Resource::collection($items),\n        ]);\n    }}\n\n    public function show(int $id): JsonResponse\n    {{\n        $item = $this->service->find($id);\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item retrieved successfully.',\n            'data' => $item ? new {singular}Resource($item) : null,\n        ]);\n    }}\n\n    public function store(Store{singular}Request $request): JsonResponse\n    {{\n        $item = $this->service->create($request->validated());\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item created successfully.',\n            'data' => new {singular}Resource($item),\n        ], 201);\n    }}\n\n    public function update(int $id, Update{singular}Request $request): JsonResponse\n    {{\n        $item = $this->service->update($id, $request->validated());\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item updated successfully.',\n            'data' => new {singular}Resource($item),\n        ]);\n    }}\n\n    public function destroy(int $id): JsonResponse\n    {{\n        $this->service->delete($id);\n\n        return response()->json([\n            'success' => true,\n            'message' => 'Item deleted successfully.',\n            'data' => null,\n        ]);\n    }}\n}}\n'''
    (base / f'app/Http/Controllers/Api/{singular}Controller.php').write_text(content, encoding='utf-8')

# Generate files
for table, singular, *_ in TABLES:
    columns = COLUMN_MAP[table]
    # model
    if singular != 'User':
        (base / f'app/Models/{singular}.php').write_text(render_model(table, singular, columns, soft_delete=singular in {'Order','OrderItem','User'}), encoding='utf-8')
    # requests
    render_request(table, singular, 'Store', columns)
    render_request(table, singular, 'Update', columns)
    # resource and service/controller
    render_resource(table, singular)
    render_service(table, singular)
    render_controller(table, singular)

# Generic nested resources for steps and user
(base / 'app/Http/Resources/UserResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass UserResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'public_code' => $this->public_code,\n            'full_name' => $this->full_name,\n            'email' => $this->email,\n            'phone' => $this->phone,\n            'status' => $this->status,\n        ];\n    }\n}\n''', encoding='utf-8')
(base / 'app/Http/Resources/OrderItemStepResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass OrderItemStepResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'status_id' => $this->status_id,\n            'zone_id' => $this->zone_id,\n            'user_id' => $this->user_id,\n            'comment' => $this->comment,\n            'created_at' => $this->created_at,\n        ];\n    }\n}\n''', encoding='utf-8')
(base / 'app/Http/Resources/PackageStepResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass PackageStepResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'status_id' => $this->status_id,\n            'zone_id' => $this->zone_id,\n            'user_id' => $this->user_id,\n            'comment' => $this->comment,\n            'created_at' => $this->created_at,\n        ];\n    }\n}\n''', encoding='utf-8')
(base / 'app/Http/Resources/PackageItemStepResource.php').write_text('''<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\Http\\Resources;\n\nuse Illuminate\\Http\\Resources\\Json\\JsonResource;\n\nclass PackageItemStepResource extends JsonResource\n{\n    public function toArray($request): array\n    {\n        return [\n            'id' => $this->id,\n            'status_id' => $this->status_id,\n            'zone_id' => $this->zone_id,\n            'user_id' => $this->user_id,\n            'comment' => $this->comment,\n            'created_at' => $this->created_at,\n        ];\n    }\n}\n''', encoding='utf-8')

# Add routes
routes_path = base / 'routes/api.php'
routes_text = routes_path.read_text(encoding='utf-8')
if 'use App\\Http\\Controllers\\Api\\RoleController;' not in routes_text:
    imports = '\n'.join([f'use App\\Http\\Controllers\\Api\\{singular}Controller;' for table, singular, *_ in TABLES])
    routes_text = routes_text.replace("use Illuminate\\Support\\Facades\\Route;\n", "use Illuminate\\Support\\Facades\\Route;\n\n" + imports + "\n")
    routes_text = routes_text.rstrip() + '\n\n' + '\n'.join([f"Route::apiResource('{table}', {singular}Controller::class);" for table, singular, *_ in TABLES]) + '\n'
    routes_path.write_text(routes_text, encoding='utf-8')

print('generated', len(TABLES), 'resource sets')
