from pathlib import Path

root = Path('resources/views')
patterns = [
    ('class="text-lg font-bold text-slate-900 leading-tight"', 'class="text-3xl font-bold text-slate-900"'),
    ('class="text-lg font-bold text-slate-900"', 'class="text-3xl font-bold text-slate-900"'),
    ('class="text-lg font-bold text-white"', 'class="text-3xl font-bold text-white"'),
]
count = 0

for path in sorted(root.rglob('*.blade.php')):
    try:
        text = path.read_text(encoding='utf-8')
    except Exception:
        continue

    new = text
    for old, replacement in patterns:
        new = new.replace(old, replacement)

    if new != text:
        path.write_text(new, encoding='utf-8')
        count += 1

print(f'updated={count}')
