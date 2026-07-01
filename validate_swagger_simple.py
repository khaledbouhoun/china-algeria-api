from pathlib import Path

files = [
    'swagger/openapi.yaml',
    'swagger/paths/roles.yaml',
    'swagger/paths/zones.yaml',
    'swagger/paths/countries.yaml',
    'swagger/paths/statuses.yaml',
    'swagger/paths/users.yaml',
    'swagger/paths/wallets.yaml',
    'swagger/paths/orders.yaml',
]
for path in files:
    text = Path(path).read_text(encoding='utf-8')
    assert text.strip(), f'{path} is empty'
    print(f'OK {path}')
