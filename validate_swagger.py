from pathlib import Path
import yaml

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
    with open(path, 'r', encoding='utf-8') as fh:
        yaml.safe_load(fh)
    print(f'OK {path}')
