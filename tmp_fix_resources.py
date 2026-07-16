from pathlib import Path

root = Path(r'c:/Laraval Projects/china-algeria-api/app/Http/Resources')
resource_map = {
    'CountryResource': 'App\\Http\\Resources\\Country\\CountryResource',
    'OrderResource': 'App\\Http\\Resources\\Order\\OrderResource',
    'OrderItemResource': 'App\\Http\\Resources\\OrderItem\\OrderItemResource',
    'OrderItemStepResource': 'App\\Http\\Resources\\OrderItemStep\\OrderItemStepResource',
    'OrderItemImageResource': 'App\\Http\\Resources\\OrderItemImage\\OrderItemImageResource',
    'PackageResource': 'App\\Http\\Resources\\Package\\PackageResource',
    'PackageItemResource': 'App\\Http\\Resources\\PackageItem\\PackageItemResource',
    'PackageItemStepResource': 'App\\Http\\Resources\\PackageItemStep\\PackageItemStepResource',
    'PackageItemReceptionResource': 'App\\Http\\Resources\\PackageItemReception\\PackageItemReceptionResource',
    'PackageStepResource': 'App\\Http\\Resources\\PackageStep\\PackageStepResource',
    'RoleResource': 'App\\Http\\Resources\\Role\\RoleResource',
    'StatusResource': 'App\\Http\\Resources\\Status\\StatusResource',
    'UserResource': 'App\\Http\\Resources\\User\\UserResource',
    'UserSessionResource': 'App\\Http\\Resources\\UserSession\\UserSessionResource',
    'VisaResource': 'App\\Http\\Resources\\Visa\\VisaResource',
    'WalletResource': 'App\\Http\\Resources\\Wallet\\WalletResource',
    'WalletTransactionResource': 'App\\Http\\Resources\\WalletTransaction\\WalletTransactionResource',
    'ZoneResource': 'App\\Http\\Resources\\Zone\\ZoneResource',
}

for path in root.rglob('*.php'):
    if path.parent.name == 'Auth':
        continue
    text = path.read_text(encoding='utf-8')
    original = text
    rel_parts = path.relative_to(root).parts
    if len(rel_parts) > 1:
        folder = rel_parts[-2]
        text = text.replace('namespace App\\Http\\Resources;', f'namespace App\\Http\\Resources\\{folder};')
    for name, fqcn in resource_map.items():
        text = text.replace(f'new {name}(', f'new \\{fqcn}(')
        text = text.replace(f'{name}::collection', f'\\{fqcn}::collection')
    if text != original:
        path.write_text(text, encoding='utf-8')
